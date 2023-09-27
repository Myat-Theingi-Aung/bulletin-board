<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\PasswordReset;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;

class PasswordController extends Controller
{
    /**
     * Change the user's password.
     *
     * @param  \App\Http\Requests\ChangePasswordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(ChangePasswordRequest  $request)
    {
        $user = User::find(Auth::id());

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['errors' => ['current_password' => ['Incorrect current password']]], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['success' => 'Password is successfully updated!']);
    }

    /**
     * Send a password reset link to the given user.
     *
     * @param  \Illuminate\Http\ForgotPasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ForgotPassword(ForgotPasswordRequest $request)
    {
        $token = Str::random(20);
        $user = User::firstOrNew(['email' => $request->email]);

        $passwordReset = $user->passwordReset()->updateOrCreate([], ['token' => $token]);

        Mail::to($request->email)->send(new ResetPasswordMail($user, $token));

        return response()->json(['success' => 'Email send with password reset instructions', 'passwordReset' => $passwordReset]);
    }

    /**
     * Reset the user's password.
     *
     * This function resets the password for a user based on a token and the provided new password.
     *
     * @param  \\App\Http\Requests\ResetPasswordRequest  $request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ResetPassword(ResetPasswordRequest $request)
    {
        $passwordReset = PasswordReset::where('token', $request->token)->first();

        $passwordReset->user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['success' => 'Password has been reset!']);
    }
}
