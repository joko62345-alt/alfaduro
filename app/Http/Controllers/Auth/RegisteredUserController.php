<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $verificationEmailStatus = config('mail.default') === 'log' ? 'logged' : 'sent';

        try {
            event(new Registered($user));
        } catch (Throwable $exception) {
            Log::warning('Verification email could not be sent after registration.', [
                'user_id' => $user->id,
                'exception' => $exception,
            ]);

            $verificationEmailStatus = 'failed';
        }

        Auth::login($user);

        try {
            Mail::to($user->email)->queue(
                (new WelcomeMail($user))
                    ->delay(now()->addSeconds(5))
                    ->afterCommit()
            );

            $welcomeEmailStatus = 'queued';
        } catch (Throwable $exception) {
            Log::warning('Welcome email could not be sent after registration.', [
                'user_id' => $user->id,
                'exception' => $exception,
            ]);

            $welcomeEmailStatus = 'failed';
        }

        return redirect()
            ->route('verification.notice')
            ->with('verification_email_status', $verificationEmailStatus)
            ->with('welcome_email_status', $welcomeEmailStatus);
    }
}
