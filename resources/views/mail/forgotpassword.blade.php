<!DOCTYPE html>
<html>
<head>
    <title>Notification</title>
</head>
<body>
    Hello,<br/>
    Follow this link to reset your equal-partners password for your 
    {{ $details['email'] }} account. >> <a href="{{ $details['url'] }}?key={{$details['api_key']}}&email={{$details['email']}}">Click here</a>
   
    <p>If you didn't ask to reset your password, you can ignore this email.</p>
    <p>Thanks</p>
    <p>Your </p>
</body>
</html>