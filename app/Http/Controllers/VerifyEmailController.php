<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Notifications\CustomVerifyEmail;

use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Handle the email verification link.
     */
    public function __invoke(Request $request, $id, $hash)
    {
        $user = User::find($id);
        Log::info('Login attempt', [
            'user' => $user
        ]);
        // Check hash validity
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        // Mark as verified if not yet verified
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        // Optional: auto-login after verification
        Auth::login($user);

        // Redirect to your desired Inertia page
        return redirect()->route('login')->with('verified', true);
    }

    /**
     * Send a verification email.
     */
    public function sendVerificationEmail(Request $request, User $user)
    {
        Log::info('Login attempt', [
            'user' => $user->hasVerifiedEmail()
        ]);
        if ($user->hasVerifiedEmail()) {
            return redirect()->route(Route::currentRouteName());
        }

        $user->notify(new CustomVerifyEmail);

        return redirect()->back()->with('success', 'Email verification link sent.');
    }
}
