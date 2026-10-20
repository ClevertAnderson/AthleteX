<?php

namespace App\Services;

use App\Mail\CoachAccountMail;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class CoachOnboarding
{
    /**
     * Create the login account for a new coach and email the credentials.
     * Never throws - returns a short message describing what happened.
     */
    public static function onboard(Coach $coach): ?string
    {
        $email = trim((string) $coach->coach_email);

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'No valid email on file, so no login account was created.';
        }

        if (User::where('email', $email)->exists()) {
            return 'A user account with this email already exists, so no new account was created.';
        }

        try {
            $temp = Str::password(12);

            // forceFill: works even if role / coach_id / coach_sport are not in User::$fillable
            $user = new User();
            $user->forceFill([
                'name'        => trim($coach->coach_first_name . ' ' . $coach->coach_last_name),
                'email'       => $email,
                'password'    => Hash::make($temp),
                'role'        => 'coach',
                'coach_id'    => $coach->id,
                'coach_sport' => $coach->coach_sport_event,
            ])->save();
        } catch (\Throwable $e) {
            Log::error('Coach account creation failed: ' . $e->getMessage());
            return 'Coach saved, but the login account could not be created: ' . $e->getMessage();
        }

        try {
            $url = URL::temporarySignedRoute(
                'password.setup',
                now()->addDays(7),
                ['user' => $user->id, 'h' => sha1($user->password)]
            );

            Mail::to($user->email)->send(new CoachAccountMail($user, $temp, $url));

            return 'Login account created and credentials emailed to ' . $user->email . '.';
        } catch (\Throwable $e) {
            Log::error('Coach credentials email failed: ' . $e->getMessage());
            return 'Login account created, but the email could not be sent: ' . $e->getMessage();
        }
    }
}
