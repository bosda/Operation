<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>登录</title>
        <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
    </head>

    <body>
        <div class="container">
            <div class="row" style="margin-top:15%">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">用户登录</div> 
                        <div class="panel-body">
                            <form method="POST" action="{{ action('UserController@store') }}" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="email" class="col-md-4 control-label">用户名</label> 
                                    <div class="col-md-6">
                                        <input type="text" name="user_name" value="" required="required" autofocus="autofocus" class="form-control">
                                    </div>
                                </div> 
                                
                                <div class="form-group">
                                    <label for="password" class="col-md-4 control-label">密码</label>
                                    <div class="col-md-6">
                                        <input id="password" type="password" name="password" required="required" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="col-md-8 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">登录</button> 
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
