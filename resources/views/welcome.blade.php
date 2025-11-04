<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Welcome</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <h1>Welcome to Elim Church Platform</h1>
        <h2>Features</h2>
        <ul>
            <li>Your gateway to sermons, articles, and apologetic resources</li>
            <li>Connect with your church community</li>
            <li>Receive notifications about upcoming events</li>
            <li>Access the Bible and study materials</li>
        </ul>

        <h2>Get Started</h2>
        <a href="{{ route('home') }}">Visit as guest</a>
        <p>
            <a href="{{ route('login') }}">Login</a> |
            <a href="{{ route('register') }}">Register</a>
        </p>
    </body>
</html>
