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
        <div style="margin-top:12px;">
            <div style="width:900px;margin:0 auto;">
                <table class="layui-table" id="list" lay-filter="user"></table>
            </div>
        </div>

        <script src="{{ asset('plugins/layui/layui.js') }}"></script>

        <script type="text/html" id="barDemo">
            <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
        </script>

        <script>
            layui.use(['table','layer'],function(){
                var table = layui.table, layer = layui.layer, $ = layui.jquery;
                table.render({
                    elem: '#list',
                    width:837,
                    url: '{{ action('ProjectController@show',['id'=>1]) }}',
                    cols: [[
                        {field:'id', title:'ID', width:80},
                        {field:'project_name', title:'项目名称', width:300},
                        {field:'url', title:'项目地址', width:300},
                        {fixed: 'right', width:150, align:'center', toolbar: '#barDemo'}
                    ]],
                    page:true
                });

                table.on('tool(user)',function(obj){
                    if(obj.event == 'edit'){
                        document.location.href = "{{ action('ProjectController@index') }}"+'/'+obj.data.id+'/edit';
                    }else if(obj.event == 'del'){
                        layer.confirm('Are you sure delete?',function(index){
                            $.ajax({
                                url: "{{ action('ProjectController@index') }}"+'/'+obj.data.id,
                                type: 'DELETE',
                                dataType: 'text',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(data){
                                    if(data){
                                        layer.msg('删除成功！',{time:1200},function(){
                                            document.location.reload();
                                        });
                                    }else{
                                        layer.msg('删除失败！');
                                    }
                                    layer.close(index);
                                    
                                }
                            });
                            
                        })
                    }
                });
            });
        </script>
    </body>
</html>
