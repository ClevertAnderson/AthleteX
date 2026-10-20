<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f4f6f1; padding:24px; color:#222;">
    <div style="max-width:520px; margin:0 auto; background:#fff; border-radius:10px; overflow:hidden; border:1px solid #dde3d5;">
        <div style="background:#2e4e1f; color:#fff; padding:18px 24px; font-size:18px; font-weight:bold;">
            Sports Development Office
        </div>
        <div style="padding:24px; line-height:1.55;">
            <p>Hello Coach <strong>{{ $user->name }}</strong>,</p>
            <p>An account has been created for you. Here are your login details:</p>
            <table style="background:#f4f6f1; border-radius:6px; padding:12px 16px; width:100%;">
                <tr><td style="color:#666;">Login page</td><td><a href="{{ route('login') }}">{{ route('login') }}</a></td></tr>
                <tr><td style="color:#666;">Email</td><td><strong>{{ $user->email }}</strong></td></tr>
                <tr><td style="color:#666;">Temporary password</td><td><strong>{{ $tempPassword }}</strong></td></tr>
            </table>
            <p>For your security, please set your own password using the button below.</p>
            <p style="text-align:center; margin:28px 0;">
                <a href="{{ $setupUrl }}" style="background:#2e7d32; color:#fff; text-decoration:none; padding:12px 26px; border-radius:6px; font-weight:bold;">Set my password</a>
            </p>
            <p style="font-size:12px; color:#777;">This link expires in 7 days and stops working once your password is set. If the button does not work, copy this address into your browser:<br>{{ $setupUrl }}</p>
        </div>
    </div>
</body>
</html>
