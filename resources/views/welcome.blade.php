<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Administración | Restaurante Capitán Chino</title>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
</head>

<body>
    <div id="app"></div>
    <div id="alerts" style="position: fixed; top: 2.5rem; right: 1.5rem; z-index: 2500 !important;"></div>
    @vite('resources/js/app.js')
    <script async src="//www.instagram.com/embed.js"></script>
</body>

</html>
