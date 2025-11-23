<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('contact');
    }

    public function send(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        $adminEmail = env('MAIL_ADMIN', config('mail.from.address'));

        try {
            Mail::to($adminEmail)->send(new ContactFormMail($data));

            // Some drivers populate failures; log if any
            if (method_exists(Mail::class, 'failures')) {
                $failures = Mail::failures();
                if (!empty($failures)) {
                    Log::error('Contact mail failures', ['failures' => $failures, 'admin' => $adminEmail]);
                    return redirect()->route('contact.index')->with('status', 'Er is een fout opgetreden bij het versturen van uw bericht.');
                }
            }

            return redirect()->route('contact.index')->with('status', 'Bedankt — uw bericht is verzonden.');
        } catch (\Throwable $e) {
            Log::error('Contact mail exception: '.$e->getMessage(), [
                'exception' => $e,
                'admin' => $adminEmail,
                'data' => $data,
            ]);

            return redirect()->route('contact.index')->with('status', 'Er is een fout opgetreden bij het versturen van uw bericht.');
        }
    }
}
