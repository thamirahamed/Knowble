<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Admin Verification</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        p {
            color: #555;
            margin-bottom: 20px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ asset('images/logo-gray.webp') }}" alt="logo" style="width: 100px;">
    </div>
    <h1>Tutor Needs Your Verification</h1>
    <p>A new tutor has registered and is awaiting approval:</p>
    <ul>
        <li>CB Number: {{ $profile->cb_number }}</li>
        <li>Status: {{ $tutor->status }}</li>
    </ul>
    <p>Go to Admin Dashboard using the link below:</p>
    <button><a href="{{ route('admin.dashboard') }}" style="color: #fff; text-decoration: none;">Admin Dashboard</a></button>
</div>
</body>
</html>
