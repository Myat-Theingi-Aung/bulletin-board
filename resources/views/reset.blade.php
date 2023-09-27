<!DOCTYPE html>
<html>
<head>
    <title>Bulletin Board</title>
</head>
<body>
  <h1 class="mb-4">Welcome to Bulletin_Board, {{ $user->name }}</h1>
  <p>Your name is: {{ $user->name }}</p>
  <a href="http://127.0.0.1:5173/reset/{{ $token }}">http://127.0.0.1:5173/reset/{{ $token }}</a>
  <p>Click the link and reset password</p>
  <p>Thanks for joining and have a great day!</p>
</body>
</html>
