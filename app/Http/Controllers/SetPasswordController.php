<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class SetPasswordController extends Controller
{
    /** The link is valid only while the password is still the temporary one. */
    private function guard(Request $request, User $user): void
    {
        abort_unless(hash_equals(sha1($user->password), (string) $request->query('h')), 403, 'This link has already been used or is no longer valid.');
    }

    public function show(Request $request, User $user)
    {
        $this->guard($request, $user);

        return view('auth.set-password', ['user' => $user, 'done' => false]);
    }

    public function update(Request $request, User $user)
    {
        $this->guard($request, $user);

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user->forceFill(['password' => $validated['password']])->save();

        return view('auth.set-password', ['user' => $user, 'done' => true]);
    }
}
