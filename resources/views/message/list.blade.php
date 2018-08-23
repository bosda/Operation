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
            .pagination {
                display: inline-block;
                padding-left: 0;
                margin: 20px 0;
                border-radius: 4px;
            }
            .pagination>li {
                display: inline;
            }
            .pagination>li:first-child>a, .pagination>li:first-child>span {
                margin-left: 0;
                border-top-left-radius: 4px;
                border-bottom-left-radius: 4px;
            }
            .pagination>li>a, .pagination>li>span {
                position: relative;
                float: left;
                padding: 6px 12px;
                margin-left: -1px;
                line-height: 1.42857143;
                color: #337ab7;
                text-decoration: none;
                background-color: #fff;
                border: 1px solid #ddd;
            }
            .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover {
                z-index: 3;
                color: #fff;
                cursor: default;
                background-color: #337ab7;
                border-color: #337ab7;
            }
        </style>
    </head>

    <body>
        <div style="margin-top:42px;">
            <p style="margin-left:32px;">
                <span lay-separator=">">
                    <a href="{{action('MessageController@index')}}">项目列表&nbsp;&gt;&nbsp;</a>
                    <a href="javascript:;">项目列表</a>
                    <i style="margin-left:32px;">共{{$count}}条数据</i>
                </span>
            </p>
            <div style="width:85%;margin:0 auto;">
                <table class="layui-table" lay-skin="nob">
                    <colgroup>
                        <col width="120">
                        <col>
                        <col width="100">
                        <col width="250">
                        <col width="80">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>标题</th>
                            <th>处理</th>
                            <th>时间</th>
                            <th style="text-align:right">操作</th>
                        </tr> 
                    </thead>

                    <tfoot>
                        <tr>
                            <td colspan="5">{{ $messages->links() }}</td>
                        </tr>
                    </tfoot>

                    <tbody>
                        @foreach ($messages as $m)
                        <tr>
                            <td>{{$m->id}}</td>
                            <td>{{$m->title}}</td>
                            <td>
                                @if($m->is_read == 0)
                                    <button class="layui-btn layui-btn-danger layui-btn-xs">未读</button>
                                @else
                                    <button class="layui-btn layui-btn-norma layui-btn-xs">已读</button>
                                @endif
                            <td>{{date('Y-m-d H:i:s',$m->create_time)}}</td>
                            <td class="font-right">
                                <button data-id="{{$m->id}}" class="layui-btn layui-btn-warm layui-btn-xs">查看</button>
                            </td>
                            <td style="display:none;">
                                {{$m->type}} {{$m->title}}
                                {{$m->file}} in line {{$m->line}}
                            </td>
                            <td style="display:none;">
@foreach (explode('","',$m->content) as $c)<p>{{$c}}</p>@endforeach 
                            </td>
                            <td style="display:none;">{{url('getLog')}}/laravel-{{date('Y-m-d',$m->create_time)}}.log</td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
        

        <!--<div style="padding:12px;">
            <blockquote class="layui-elem-quote">

            </blockquote>
            <fieldset class="layui-elem-field layui-field-title">
                <legend>详情 - 错误追踪</legend>
                <div class="layui-field-box">
                    <pre class="layui-code">
                        
                    </pre>  
                </div>
            </fieldset>
            
            <div>
                <a href="">
                    <i class="layui-icon layui-icon-read" style="font-size:80px; color: #1E9FFF;"></i> 
                    <p>附件列表下载</p>
                </a>
            </div>
        </div>-->

        <script src="{{ asset('plugins/layui/layui.js') }}"></script>

        <script>
            layui.use(['layer','jquery'],function(){
                var layer = layui.layer, $ = layui.jquery;
                $('button.layui-btn-warm').click(function(){
                    var id = $(this).data('id');
                    $.ajax({
                        url: "{{action('MessageController@index')}}"+'/'+id,
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function(data){
                            //console.log(data);
                        }
                    });
                    var title = $(this).parent().next().html();
                    var content = $(this).parent().next().next().html();
                    var url = $(this).parent().next().next().next().text();
                    var html = '<div style="padding:12px;">';
                        html +=    '<blockquote class="layui-elem-quote">';

                        html += title;

                        html +=     '</blockquote>';
                        html +=     '<fieldset class="layui-elem-field layui-field-title">';
                        html +=         '<legend>详情 - 错误追踪</legend>';
                        html +=         '<div class="layui-field-box">';
                        html +=         '<pre class="layui-code">';

                        html += content;

                        html +=         '</pre>';
                        html +=         '</div>';
                        html +=     '</fieldset>';

                        html +=     '<div><a href="'+url+'">';
                        html +=     '<i class="layui-icon layui-icon-read" style="font-size:80px; color: #1E9FFF;"></i><p>附件列表下载</p></a></div>';
                        html += '</div>';
        
                    var index = layer.open({
                        type: 1,
                        title: '详情',
                        content: html
                    });
                    layer.full(index);
                });
            });
        </script>
    </body>
</html>
