<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Mail\MailShipped;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    public function index(){
    	$user = Auth::user();
    	$projects = DB::table('project')->whereRaw("id in (select project_id from user_project where user_id={$user->id})")->get();
    	foreach ($projects as $key => $value) {
    		$projects[$key]->not_read = DB::table('message')->where('project_id',$value->id)->where('is_read',0)->count();
    	}
    	return view('message/index',['projects'=>$projects]);
    }

    public function list(Request $request,$id){
    	$messages = DB::table('message')->where('project_id',$id)->orderBy('is_read','asc')->orderBy('create_time','desc')->paginate(15);
    	$count = DB::table('message')->where('project_id',$id)->count();
    	return view('message/list',['messages'=>$messages, 'count'=>$count]);
    }

    public function update($id){
    	$res = DB::table('message')->where('id',$id)->update(['is_read'=>1]);
    	if($res){
    		return 1;
    	}else{
    		return 0;
    	}
    }

}
