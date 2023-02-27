<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{ $userData['title'] }}</title>
</head>
<body>
  {{ $userData['firstName'] }} {{ $userData['lastName'] }}
  <hr>
  Email: {{ $userData['email'] }}
  <hr>
  <img src="{{ $userData['avatar'] }}" alt="{{ $userData['title'] }}" style="width:50px; height:50px">
</body>
</html>