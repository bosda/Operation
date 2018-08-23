<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>盈道科技</title>
        <link href="{{ asset('plugins/layui/css/layui.css') }}" rel="stylesheet" type="text/css">

        <style>
            .layui-nav-tree .layui-nav-child dd{
                padding-left: 1rem;
            }
        </style>
    </head>

    <body class="layui-layout-body">
        <div class="layui-layout layui-layout-admin">
            <div class="layui-header">
                <div class="layui-logo">盈道科技</div>
                <ul class="layui-nav layui-layout-right">
                    <li class="layui-nav-item">
                        <a href="javascript:;">
                            <img src="{{ $user['avatar'] or asset('images/head.jpg') }}" class="layui-nav-img">{{ $user['real_name'] or $user['user_name'] }}
                        </a>
                    </li>
                    <li class="layui-nav-item"><a href="{{ action('UserController@logout') }}">注销</a></li>
                </ul>
            </div>

            <div class="layui-side layui-bg-black">
                <div class="layui-side-scroll">
                    <ul class="layui-nav layui-nav-tree" lay-filter="side">
                        @if ($user['type'] == 1)
                        <li class="layui-nav-item layui-this" data-url="{{ action('MessageController@index') }}">
                            <a href="javascript:;"><i class="layui-icon layui-icon-dialogue"></i> 消息列表</a>
                        </li>
                        <li class="layui-nav-item">
                            <a href="javascript:;"><i class="layui-icon layui-icon-template-1"></i> 项目管理</a>
                            <dl class="layui-nav-child">
                                <dd data-url="{{ action('ProjectController@index') }}"><a href="javascript:;">项目列表</a></dd>
                                <dd data-url="{{ action('ProjectController@create') }}"><a href="javascript:;">添加项目</a></dd>
                            </dl>
                        </li>
                        @else
                        <li class="layui-nav-item layui-this" data-url="{{ action('MessageController@index') }}">
                            <a href="javascript:;"><i class="layui-icon layui-icon-dialogue"></i> 消息列表</a>
                        </li>
                        @endif

                        <li class="layui-nav-item">
                            <a class="" href="javascript:;"><i class="layui-icon layui-icon-username"></i> 用户管理</a>
                            <dl class="layui-nav-child">
                                @if ($user['type'] == 1)
                                <dd data-url="{{ action('UserController@list') }}"><a href="javascript:;">用户列表</a></dd>
                                <dd data-url="{{ action('UserController@create') }}"><a href="javascript:;">添加用户</a></dd>
                                @endif
                                <dd data-url="{{ action('UserController@edit',['id'=>$user->id]) }}"><a href="javascript:;">修改信息</a></dd>
                            </dl>
                        </li>

                        <li class="layui-nav-item">
                            <a class="" href="javascript:;"><i class="layui-icon layui-icon-app"></i> 应用说明</a>
                            <dl class="layui-nav-child">
                                <dd data-url="{{ url('sdk') }}"><a href="javascript:;">SDK说明</a></dd>
                                <dd data-url="{{ url('api') }}"><a href="javascript:;">API说明</a></dd>
                                <dd data-url="{{ url('getDocument') }}"><a href="javascript:;">文档下载</a></dd>
                            </dl>
                        </li>

                    </ul>
                </div>
            </div>

            <div class="layui-body">
                <iframe src="{{ action('MessageController@index') }}" style="border:0;width:100%;height:100%;"></iframe>
            </div>

            <div class="layui-footer">
                2018 &copy; operation.world &reg; 盈道科技
            </div>
        </div>

        <script src="{{ asset('plugins/layui/layui.js') }}"></script>
        <script>
            layui.use('element',function(){
                var element = layui.element,$ = layui.jquery;
                var height = $('.layui-body').height();
                var iframe = $('iframe');
                iframe.height(height-3);
                element.on('nav(side)',function(elem){
                    if(elem.parent().data('url')){
                        iframe.attr('src',elem.parent().data('url'));
                    }
                });
            });
        </script>
    </body>
</html>
