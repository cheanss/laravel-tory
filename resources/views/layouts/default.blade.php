<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="/css/app.css">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Sample App') - Laravel 入门教程</title>
</head>
<body>
    @include('layouts._header')
    <div class="container">
        <div class="col-md-offset-1 col-md-10">
            @include('shared.messages')
            @yield('content')
            @include('layouts._footer')
        </div>
    </div>

    <script src="/js/app.js"></script>
</body>
</html>