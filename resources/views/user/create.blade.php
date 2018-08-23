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
        <div style="width:500px;margin:80px auto 0 auto;">
            <form class="layui-form" action="{{ action('UserController@add') }}" method="post">
                {{ csrf_field() }}
                <div class="layui-form-item">
                    <label class="layui-form-label">用户名*</label>
                    <div class="layui-input-block">
                        <input type="text" name="user_name" required lay-verify="required" placeholder="请输入用户名" autocomplete="on" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">姓名</label>
                    <div class="layui-input-block">
                        <input type="text" name="real_name" placeholder="请输入姓名" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">邮箱</label>
                    <div class="layui-input-block">
                        <input type="email" name="email" placeholder="请输入邮箱" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">密码*</label>
                    <div class="layui-input-block">
                        <input type="password" name="password" required lay-verify="required" placeholder="请输入密码" class="layui-input">
                    </div>
                </div>
            
                <div class="layui-form-item">
                    <label class="layui-form-label">确认密码*</label>
                    <div class="layui-input-block">
                        <input type="password" name="password2" required lay-verify="required" placeholder="请输入密码" class="layui-input">
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
                form.on('submit(add)',function(data){
                    if(data.field.password != data.field.password2){
                        layer.msg('两次密码不一致！');
                        return false;
                    }
                });
            });
        </script>
    </body>
</html>
