<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set your password</title>
    @vite(['resources/css/app.css'])
    <style>
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#2e4e1f,#4c8a2b); font-family:Arial,sans-serif; }
        .card { background:#fff; width:100%; max-width:420px; border-radius:12px; padding:28px; box-shadow:0 10px 30px rgba(0,0,0,.25); margin:16px; }
        h1 { margin:0 0 6px; font-size:22px; color:#2e4e1f; }
        p { color:#555; font-size:14px; }
        label { display:block; font-size:13px; font-weight:bold; margin:14px 0 4px; color:#333; }
        input { width:100%; box-sizing:border-box; padding:10px; border:1px solid #ccc; border-radius:6px; font-size:14px; }
        button, .btn { display:block; width:100%; box-sizing:border-box; text-align:center; text-decoration:none; margin-top:20px; padding:11px; background:#2e7d32; color:#fff; border:0; border-radius:6px; font-weight:bold; font-size:15px; cursor:pointer; }
        .err { color:#c62828; font-size:13px; margin-top:4px; }
    </style>
</head>
<body>
<div class="card">
@if($done)
    <h1>Password updated</h1>
    <p>Your password has been set. You can now log in with your email and new password.</p>
    <a class="btn" href="{{ route('login') }}">Go to login</a>
@else
    <h1>Set your password</h1>
    <p>Hello {{ $user->name }}, choose a password for <strong>{{ $user->email }}</strong>.</p>
    <form method="POST" action="{{ request()->fullUrl() }}">
        @csrf
        <label for="password">New password</label>
        <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password">
        @error('password') <div class="err">{{ $message }}</div> @enderror

        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">

        <p style="font-size:12px;margin-top:10px;">At least 8 characters, with letters and numbers.</p>
        <button type="submit">Save password</button>
    </form>
@endif
</div>
</body>
</html>
