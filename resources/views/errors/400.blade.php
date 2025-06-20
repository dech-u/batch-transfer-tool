<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Error Page</title>
    <style>
        body {
            /* background-image: url("{{ url('images/404 error.svg') }}"); */
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            position: relative;
        }

        body img {
            margin: 10% 0;
        }

        h1 {
            position: fixed;
            top: 3%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 2rem;
            color: #205678;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1 class="">Currently you don't have the website management feature. First, buy a package that includes this feature or purchase an add-on, and then you will get access to it.</h1>
    <img class="" src="{{ url('images/400.svg') }}" alt="400 error">
</body>

</html>
