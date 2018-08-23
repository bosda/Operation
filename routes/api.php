<?php

use Illuminate\Http\Request;
use \App\Utils\Util;
use App\Utils\ReportHelper;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/* 微信开发者接入 */
Route::get('/wx',function(Request $request){
	$util = new Util();
	return $util->wechat_check();
});

/* 微信消息处理 */
Route::post('/wx',function(Request $request){
	$util = new Util();
	$obj = simplexml_load_string(file_get_contents("php://input"),null,LIBXML_NOCDATA);
	if($obj->Event == 'subscribe' || $obj->Event == 'SCAN'){
		if(isset($obj->EventKey) && $obj->Event == 'subscribe'){
			$user_id = substr($obj->EventKey,8);
		}else{
			$user_id = $obj->EventKey;
		}

		/* 绑定微信 */
		if(isset($user_id)){
			$res = DB::table('users')->where('id',$user_id)->value('bind');
			if(!$res){
				DB::table('users')->where('id',$user_id)->update([
					'bind' => 1,
					'openid' => $obj->FromUserName
				]);
				
				return $util->send_msg($obj->FromUserName,$obj->ToUserName,"绑定成功");
			}else{
				return $util->send_msg($obj->FromUserName,$obj->ToUserName,"您已绑定，请勿重复绑定");
			}
		}
	}
	
	return 'success';

});

Route::get('/get_access_token',function(){return url('/get_access_token');
	$util = new Util();
	return $util->get_access_token();
});

/* 微信二维码 */
Route::get('/get_wechat_ticket/{id}',function($id){
	$util = new Util();
	$url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$util->get_wechat_ticket($id)['ticket'];
	return $url;
});

/* 日志处理 */
Route::post('/log',function(Request $request){
	$util = new Util();
	$project = DB::table('project')->where('token',$request->input('project_token'))->first();

	if(!$project){
		$result['status'] = 400000;
		$result['msg'] = 'token错误';
		return json_encode($result);
	}

	if($request->input('sign') != sha1(md5($project->token))){
		$result['status'] = 40001;
		$result['msg'] = '签名错误';
		return json_encode($result);	
	}
	
	$users = DB::select('select u.id,u.notify,u.openid,u.email from user_project as up left join users as u on u.id=up.user_id where up.project_id=?',[$project->id]);

	$message = json_decode($request->input('message'),true);
	foreach ($message as $key => $value) {
		$res = DB::table('message')->insert([
			'project_id' => $project->id,
			'title' => $value['message'],
			'type' => $value['type'],
			'file' => $value['file'],
			'line' => $value['line'],
			'code' => $value['code'],
			'content' => json_encode($value['trace']),
			'create_time' => $request->input('timestamp')
		]);

		/* 发送通知限制，避免频繁发送消息，1小时内同一消息只能发送5次 */
		$count = DB::select("select count(*) as count from message where title = ? and create_time between ? and ?",[$value['message'], ($request->input('timestamp')-60*60) ,$request->input('timestamp')]);

		if($count[0]->count > 5){
			continue;
		}

		/* 发送通知 */
		if(!empty($users)){
			foreach($users as $v){
				if($v->notify == 1 || $v->notify == 3){//微信通知
					$template_id = 'uTgLN1ez9GW228dstbFsyptKrd8u3y9U8lMHe4UUD-M';
					$data = [
						'first' => [
							'value' => '你负责的项目出错啦！',
							'color' => '#888'
						],
						'keyword1' => [
							'value' => $project->project_name,
							'color' => '#FF5722'
						],
						'keyword2' => [
							'value' => $value['type'],
							'color' => '#888'
						],
						'keyword3' => [
							'value' => $value['file'].' in line '.$value['line'],
							'color' => '#888'
						],
						'keyword4' => [
							'value' => $value['message'],
							'color' => '#1E9FFF'
						],
						'keyword5' => [
							'value' => date('Y-m-d H:i:s',$request->input('timestamp')),
							'color' => '#888'
						],
						'remark' => [
							'value' => '查看详情请登陆operation.world',
							'color' => '#888'
						]
					];
					if($v->openid){
						$msg = $util->send_tpl($v->openid,$template_id,$data);
					}
				}

				if($v->notify == 2 || $v->notify == 3){//邮件通知
					$subject = "你负责的项目出错啦！";
					$message = "
					<html>
					<head>
						<title>email</title>
					</head>
						<body>
							<h2>{$project->project_name}</h2>
							<h3>错误信息：{$value['type']} {$value['message']}</h3>
							<p>错误定位：{$value['file']}".' in line '."{$value['line']}</p>
							<div>错误追踪：".json_encode($value['trace'])."</div>
						</body>
					</html>
					";
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=utf-8" . "\r\n";

					if($v->email){
						$email = mail($v->email,$subject,$message,$headers);
					}
					
				}
			}
		}
	}

	/* 文件上传 */
	foreach($_FILES["files"]['error'] as $k=>$v){
		if($v > 0){
			$result['status'] = 30001;
			$result['msg'] = '文件上传失败';
			return json_encode($result);
		}else{
			$path = storage_path().'/upload/logs/' ;
			if(!is_dir($path)){
				mkdir($path,0777,true);
			}
			$file = move_uploaded_file($_FILES["files"]["tmp_name"][$k],$path. $_FILES["files"]["name"][$k]);
		}
	}

	if(!$res){
		$result['status'] = 30002;
		$result['msg'] = '数据库操作失败，重试或联系管理员';
		return json_encode($result);	
	}
	
	if(!empty($msg) && $msg['errcode'] != 0){
		$result['status'] = 20001;
		$result['msg'] = '微信发送失败'.$msg['errmsg'].$msg['msgid'];
		return json_encode($result);
	}

	if(!empty($email)){
		$result['status'] = 20002;
		$result['msg'] = '邮件发送失败';
		return json_encode($result);
	}

	if($file){
		if(!$res){
			$result['status'] = 30001;
			$result['msg'] = '文件上传失败';
			return json_encode($result);	
		}
	}

	$result['status'] = 10000;
	$result['msg'] = 'ok';
	return json_encode($result);
	
});