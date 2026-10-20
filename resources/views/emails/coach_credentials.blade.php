<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { background-color: #16a34a; color: white; padding: 15px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .credentials { background-color: #f8fafc; padding: 15px; border-left: 4px solid #16a34a; margin: 20px 0; }
        .btn { display: inline-block; background-color: #16a34a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Welcome to AthleteX!</h2>
        </div>
        <div class="content">
            <p>Hello Coach <strong>{{ $coachData['name'] }}</strong>,</p>
            <p>Your official coaching account has been successfully created by the Administrator. You can now log in to manage your athletes, track attendances, and record achievements.</p>
            
            <div class="credentials">
                <p style="margin: 0 0 10px 0;"><strong>Your Login Credentials:</strong></p>
                <p style="margin: 0;"><strong>Email:</strong> {{ $coachData['email'] }}</p>
                <p style="margin: 5px 0 0 0;"><strong>Temporary Password:</strong> {{ $coachData['password'] }}</p>
            </div>

            <p><em>For security purposes, please log in and change your password immediately from your profile settings.</em></p>

            <center>
                <a href="{{ url('/login') }}" class="btn">Login to Your Account</a>
            </center>
        </div>
    </div>
</body>
</html>