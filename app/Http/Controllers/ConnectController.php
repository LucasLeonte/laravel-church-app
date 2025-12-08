<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FriendRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConnectController extends Controller
{
    public function index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $perPage = 12;
        $query = User::query()->with(['favoriteVerses' => function ($q) {
            $q->limit(3);
        }])->orderBy('name');

        if (Auth::check()) {
            $query->where('id', '!=', Auth::id());
        }

        $users = $query->paginate($perPage);

        $statusMap = [];
        $idMap = []; // map otherUserId => friend_request_id for pending requests

        if (Auth::check()) {
            $authId = Auth::id();
            $otherIds = $users->pluck('id')->toArray();

            $requests = FriendRequest::where(function ($q) use ($authId, $otherIds) {
                $q->where('sender_id', $authId)->whereIn('receiver_id', $otherIds);
            })->orWhere(function ($q) use ($authId, $otherIds) {
                $q->where('receiver_id', $authId)->whereIn('sender_id', $otherIds);
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

        return view('connect.index', compact('users', 'statusMap', 'idMap'));
    }

    public function sendRequest(Request $request, $receiverId): \Illuminate\Http\RedirectResponse
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

    public function accept(Request $request, $id): \Illuminate\Http\RedirectResponse
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

    public function decline(Request $request, $id): \Illuminate\Http\RedirectResponse
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

    public function cancel(Request $request, $id): \Illuminate\Http\RedirectResponse
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
}
