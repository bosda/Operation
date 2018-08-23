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
        <div style="width:550px;margin:80px auto 0 auto;">
            <form class="layui-form" action="{{ action('ProjectController@store') }}" method="post">
                {{ csrf_field() }}
                <div class="layui-form-item">
                    <label class="layui-form-label">项目名*</label>
                    <div class="layui-input-block">
                        <input type="text" name="project_name" required lay-verify="required" placeholder="请输入项目名" autocomplete="on" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">项目token*</label>
                    <div class="layui-input-block">
                        <input type="text" name="token" required lay-verify="required" placeholder="请输入项目token(用于签名校对)" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">项目成员</label>
                    <div class="layui-input-block">
                        @foreach ($users as $user)
                        <input type="checkbox" name="users[]" value="{{$user->id}}" title="{{$user->name}}">
                        @endforeach
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="add">添加</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>

            </form>
        </div>

        <script src="{{ asset('plugins/layui/layui.js') }}"></script>
        
        <script>
            layui.use('form',function(){
                var form = layui.form;
            });
        </script>
    </body>
</html>
