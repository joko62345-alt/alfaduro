<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (Throwable $exception) {
            Log::warning('Verification email could not be resent.', [
                'user_id' => $request->user()->id,
                'exception' => $exception,
            ]);

            return back()->with('verification_email_status', 'failed');
        }

        return back()
            ->with('status', 'verification-link-sent')
            ->with('verification_email_status', config('mail.default') === 'log' ? 'logged' : 'sent');
    }
}
