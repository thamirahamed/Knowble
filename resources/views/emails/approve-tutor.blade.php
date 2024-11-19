<!DOCTYPE html>
<html>
<head>
    <title>Tutor Approval Required</title>
</head>
<body>
<p>A new tutor has registered and is awaiting approval:</p>
<ul>
    <li>User ID: {{ $tutor->user_id }}</li>
    <li>Status: {{ $tutor->status }}</li>
</ul>
<p><a href="{{ route('admin.dashboard') }}">Go to Admin Dashboard</a></p>
</body>
</html>
