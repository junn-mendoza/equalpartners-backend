<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #50baf3;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .button {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #50baf3;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777777;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>You're Invited to Join Equal Partners!</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Hi {{ $recipientName }},</p>
            <p>We are excited to invite you to join the Equal Partners app. It's a space where equality, collaboration, and partnership are prioritized.</p>
            <p>With Equal Partners, you can:</p>
            <ul>
                <li>Collaborate efficiently with your team.</li>
                <li>Manage projects seamlessly.</li>
                <li>Foster a community of shared values.</li>
            </ul>
            <p>Click one of the buttons below to download the app and join us!</p>

            <!-- Download Buttons -->
            <a href="{{ $playStoreLink }}" class="button">Download on Play Store</a>
            <a href="{{ $appStoreLink }}" class="button">Download on App Store</a>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>If you have any questions, feel free to reply to this email.</p>
            <p>&copy; {{ date('Y') }} Equal Partners. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
