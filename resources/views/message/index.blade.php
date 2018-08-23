<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>list</title>
        <link href="{{ asset('plugins/layui/css/layui.css') }}" rel="stylesheet" type="text/css">
        <style>
            .font-right{text-align: right;}
            tbody>tr:hover{cursor: pointer;}
        </style>
    </head>

    <body>
        <div style="margin-top:42px;">
            <p style="margin-left:32px;">
                <span lay-separator=">">
                    <a href="javascript:;">项目列表</a>
                </span>
            </p>
            <div style="width:980px;margin:0 auto;">
                <table class="layui-table" lay-skin="nob">
                    <colgroup>
                        <col width="120">
                        <col>
                        <col width="200">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>项目名称</th>
                            <th style="text-align:right">未读消息</th>
                        </tr> 
                    </thead>

                    <tbody>
                        @foreach ($projects as $p)
                        <tr data-id="{{$p->id}}">
                            <td>{{$p->id}}</td>
                            <td>{{$p->project_name}}</td>
                            <td class="font-right"><span class="layui-badge">{{$p->not_read}}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <script src="{{ asset('plugins/layui/layui.js') }}"></script>

        <script>
            layui.use('jquery',function(){
                var $ = layui.jquery;
                $('tbody tr').click(function(){
                    var id = $(this).data('id');
                    document.location.href = "{{action('MessageController@index')}}"+'/'+id;
                });
            });
        </script>
    </body>
</html>
