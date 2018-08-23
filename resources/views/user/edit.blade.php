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
            <form class="layui-form" action="{{ action('UserController@update',['id'=>$user->id]) }}" method="post">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <div class="layui-form-item">
                    <label class="layui-form-label">用户名*</label>
                    <div class="layui-input-block">
                        <input type="text" name="user_name" value="{{ $user->user_name }}" required lay-verify="required" placeholder="请输入用户名" autocomplete="on" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">姓名</label>
                    <div class="layui-input-block">
                        <input type="text" name="real_name" value="{{ $user->real_name }}" placeholder="请输入姓名" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">邮箱</label>
                    <div class="layui-input-block">
                        <input type="email" name="email" value="{{ $user->email }}" placeholder="请输入邮箱" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">类型</label>
                    <label class="layui-form-label" style="text-align:left">{{ $user->type == 1 ? '管理员' : '普通用户'}}</label>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">微信</label>
                    <label class="layui-form-label" style="text-align:left;width:150px;">{{$user->bind == 0 ? '未绑定' : '已绑定'}}</label>
                    <label class="layui-form-label" id="bind-wechat" style="text-align:left;width:35px;"><span class="layui-btn layui-btn-xs layui-btn-radius layui-btn-warn">绑定</span></label>
                    <label class="layui-form-label" id="show-account" style="text-align:left;width:35px;"><span class="layui-btn layui-btn-xs layui-btn-radius layui-btn-normal">关注公众号</span></label>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">通知设置</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="wechat_notify" value="1" lay-skin="switch" lay-text="开启|关闭" 
                            @if ($user->notify==1 || $user->notify==3)
                                checked
                            @endif
                            ><span style="margin-right:32px;position:relative;top:5px;left:8px;">微信通知</span>
                        <input type="checkbox" name="email_notify" value="2" lay-skin="switch" lay-text="开启|关闭"
                            @if ($user->notify==2 || $user->notify==3)
                                checked
                            @endif
                        ><span style="position:relative;top:5px;left:8px;">邮件通知</span>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">创建时间</label>
                    <label class="layui-form-label" style="text-align:left">{{ date('Y-m-d',$user->create_time) }}</label>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">密码</label>
                    <div class="layui-input-block">
                        <input type="password" name="password" placeholder="******" class="layui-input">
                    </div>
                </div>
            
                <div class="layui-form-item">
                    <label class="layui-form-label">确认密码</label>
                    <div class="layui-input-block">
                        <input type="password" name="password2" placeholder="******" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="update">提交</button>
                    </div>
                </div>

            </form>
        </div>

        <script src="{{ asset('plugins/layui/layui.js') }}"></script>

        <script>
            layui.use('form',function(){
                var form = layui.form, $ = layui.jquery;
                form.on('submit(update)',function(data){
                    if(data.field.password != data.field.password2){
                        layer.msg('两次密码不一致！');
                        return false;
                    }
                });

                $('#bind-wechat').click(function(){

                    $.get("{{url('/api/get_wechat_ticket',['id'=>$user->id])}}",function(data){
                        var html = '<img style="width:260px;height:260px;" src="'+data+'">';
                        layer.open({
                            type:1,
                            content:html
                        });
                    });
                });

                $("#show-account").click(function(){
                    var html = '<img src="{{asset('images/account.jpg')}}">';
                    layer.open({
                        type:1,
                        content:html
                    });
                });
            });
        </script>
    </body>
</html>
