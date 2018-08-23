<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>list</title>
        <link href="{{ asset('plugins/layui/css/layui.css') }}" rel="stylesheet" type="text/css">
    </head>

    <body>
        <div style="text-align:center;margin-top:12px;">
            <h1>操作失败</h1>
        </div>

        <script src="{{ asset('plugins/layui/layui.js') }}"></script>
        <script>
            setTimeout(function(){
                document.location.href = "{{ $url }}";
            },1300);
        </script>
    </body>
</html>
