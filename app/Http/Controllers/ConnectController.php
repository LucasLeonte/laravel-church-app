<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FriendRequest;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConnectController extends Controller
{
    public function index(Request $request): Factory|View
    {
        $perPage = 12;
        $q = trim((string) $request->query('q', ''));

        if (Auth::check()) {
            $authId = Auth::id();

            // 1) Friends (accepted)
            $acceptedRows = FriendRequest::where(function ($query) use ($authId) {
                $query->where('sender_id', $authId)->orWhere('receiver_id', $authId);
            })->where('status', 'accepted')->get();

            $friendIds = $acceptedRows->map(function ($r) use ($authId) {
                return $r->sender_id === $authId ? $r->receiver_id : $r->sender_id;
            })->unique()->values()->toArray();

            $friends = collect();
            if (! empty($friendIds)) {
                $friendsQuery = User::query()->with(['favoriteVerses' => function ($qv) { $qv->limit(3); }])->whereIn('id', $friendIds)->orderBy('name');
                if ($q !== '') {
                    $friendsQuery->where(function ($wf) use ($q) {
                        $wf->where('name', 'like', "%$q%")->orWhere('bio', 'like', "%$q%");
                    });
                }
                $friends = $friendsQuery->get();
            }

            // 2) Received pending requests (others who sent me a request) - newest first
            // Use the DB query builder here to avoid static-analyzer confusion with latest()/orderByDesc signatures.
            $receivedRows = \Illuminate\Support\Facades\DB::table('friend_requests')
                ->where('receiver_id', $authId)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();
            $receivedSenderIds = $receivedRows->pluck('sender_id')->unique()->toArray();

            $received = collect();
            $receivedMap = []; // userId => requestId
            if (! empty($receivedSenderIds)) {
                // fetch users keyed by id to preserve the order from receivedRows (newest first)
                $userMap = User::query()->with(['favoriteVerses' => function ($qv) { $qv->limit(3); }])->whereIn('id', $receivedSenderIds)->get()->keyBy('id');

                foreach ($receivedRows as $r) {
                    $u = $userMap->get($r->sender_id);
                    if (! $u) {
                        continue;
                    }

                    // apply search filter if present
                    if ($q !== '') {
                        $nameMatch = mb_stripos($u->name, $q) !== false;
                        $bioMatch = $u->bio && mb_stripos($u->bio, $q) !== false;
                        if (! $nameMatch && ! $bioMatch) {
                            continue;
                        }
                    }

                    $received->push($u);
                    $receivedMap[$u->id] = $r->id;
                }
            }

            // 3) Others: exclude auth, friends, and received senders
            $excludeIds = array_merge([$authId], $friendIds, $receivedSenderIds);

            $othersQuery = User::query()->with(['favoriteVerses' => function ($qv) { $qv->limit(3); }])->orderBy('name');
            if (! empty($excludeIds)) {
                $othersQuery->whereNotIn('id', $excludeIds);
            }
            if ($q !== '') {
                $othersQuery->where(function ($wo) use ($q) {
                    $wo->where('name', 'like', "%$q%")->orWhere('bio', 'like', "%$q%");
                });
            }

            $others = $othersQuery->paginate($perPage)->withQueryString();

            // Build statusMap and idMap for the paginated others
            $statusMap = [];
            $idMap = [];

            $otherIds = $others->pluck('id')->toArray();
            if (! empty($otherIds)) {
                $requests = FriendRequest::where(function ($qreq) use ($authId, $otherIds) {
                    $qreq->where('sender_id', $authId)->whereIn('receiver_id', $otherIds);
                })->orWhere(function ($qreq) use ($authId, $otherIds) {
                    $qreq->where('receiver_id', $authId)->whereIn('sender_id', $otherIds);
                })->get();

                foreach ($requests as $r) {
                    $otherId = $r->sender_id === $authId ? $r->receiver_id : $r->sender_id;
                    if ($r->status === 'accepted') {
                        $statusMap[$otherId] = 'accepted';
                    } elseif ($r->status === 'pending') {
                        $statusMap[$otherId] = $r->sender_id === $authId ? 'sent' : 'received';
                        $idMap[$otherId] = $r->id;
                    } else {
                        $statusMap[$otherId] = $r->status;
                    }
                }
            }

            return view('connect.index', compact('friends', 'received', 'receivedMap', 'others', 'statusMap', 'idMap', 'q'));
        }

        // Not authenticated: show public paginated users with optional search
        $query = User::query()->with(['favoriteVerses' => function ($qv) { $qv->limit(3); }])->orderBy('name');
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")->orWhere('bio', 'like', "%$q%");
            });
        }
        $users = $query->paginate($perPage)->withQueryString();

        return view('connect.index', ['users' => $users, 'q' => $q]);
    }

    public function sendRequest($receiverId): RedirectResponse
    {
        $user = Auth::user();
        /** @var User $user */
        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->id == $receiverId) {
            return back()->withErrors(['receiver' => trans('You cannot send a friend request to yourself.')]);
        }

        // check existing
        $existing = FriendRequest::where(function ($q) use ($user, $receiverId) {
            $q->where('sender_id', $user->id)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($user, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $user->id);
        })->first();

        if ($existing) {
            if ($existing->status === 'accepted') {
                return back()->with('status', 'You are already friends.');
            }

            if ($existing->status === 'pending') {
                // if the other user already sent a request to me, accept it
                if ($existing->sender_id === $receiverId && $existing->receiver_id === $user->id) {
                    $existing->status = 'accepted';
                    $existing->save();
                    return back()->with('status', 'Friend request accepted automatically.');
                }

                return back()->with('status', 'Friend request already pending.');
            }

            // otherwise recreate or update
            $existing->update(['sender_id' => $user->id, 'receiver_id' => $receiverId, 'status' => 'pending']);
            return back()->with('status', 'Friend request sent.');
        }

        FriendRequest::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Friend request sent.');
    }

    public function accept($id): RedirectResponse
    {
        $user = Auth::user();
        /** @var User $user */
        if (! $user) {
            return redirect()->route('login');
        }

        $fr = FriendRequest::findOrFail($id);
        if ($fr->receiver_id !== $user->id) {
            abort(403);
        }

        if ($fr->status !== 'pending') {
            return back()->with('status', 'Cannot accept this request.');
        }

        $fr->status = 'accepted';
        $fr->save();

        return back()->with('status', 'Friend request accepted.');
    }

    public function decline($id): RedirectResponse
    {
        $user = Auth::user();
        /** @var User $user */
        if (! $user) {
            return redirect()->route('login');
        }

        $fr = FriendRequest::findOrFail($id);
        if ($fr->receiver_id !== $user->id) {
            abort(403);
        }

        if ($fr->status !== 'pending') {
            return back()->with('status', 'Cannot decline this request.');
        }

        $fr->status = 'declined';
        $fr->save();

        return back()->with('status', 'Friend request declined.');
    }

    public function cancel($id): RedirectResponse
    {
        $user = Auth::user();
        /** @var User $user */
        if (! $user) {
            return redirect()->route('login');
        }

        $fr = FriendRequest::findOrFail($id);
        if ($fr->sender_id !== $user->id) {
            abort(403);
        }

        if ($fr->status !== 'pending') {
            return back()->with('status', 'Cannot cancel this request.');
        }

        $fr->status = 'cancelled';
        $fr->save();

        return back()->with('status', 'Friend request cancelled.');
    }

    /**
     * Remove an existing friendship (accepted friend request) between the authenticated user and the given user id.
     */
    public function removeFriend($otherUserId): RedirectResponse
    {
        $user = Auth::user();
        /** @var User $user */
        if (! $user) {
            return redirect()->route('login');
        }

        $authId = $user->id;

        // Find accepted friend request between the two users (either direction)
        $fr = FriendRequest::where(function ($q) use ($authId, $otherUserId) {
            $q->where('sender_id', $authId)->where('receiver_id', $otherUserId);
        })->orWhere(function ($q) use ($authId, $otherUserId) {
            $q->where('sender_id', $otherUserId)->where('receiver_id', $authId);
        })->where('status', 'accepted')->first();

        if (! $fr) {
            return back()->with('status', 'No friendship found to remove.');
        }

        // Delete the friendship record so users are no longer friends
        try {
            $fr->delete();
        } catch (Exception) {
            return back()->with('status', 'Could not remove friend at this time.');
        }

        return back()->with('status', 'Friend removed.');
    }
}
