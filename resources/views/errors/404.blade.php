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
            /* height: 100vh;  */
            margin: 0; 
            display: flex;
            align-items: center;
            justify-content: center;
        }
        body img {
            margin: 10%  0px;
        }
    </style>
</head>

<body>
    <img class="" src="{{ url('images/404 error.svg') }}" alt="404 error">
</body>

</html>