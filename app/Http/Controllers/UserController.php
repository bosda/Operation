<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::check()){
            return redirect('/');
        }else{
            return view('user/login');
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user/create');
    }

    public function add(Request $request){
        $user_name = $request->input('user_name');
        $real_name = empty($request->input('real_name')) ? '' : $request->input('real_name');
        $email = empty($request->input('email')) ? '' : $request->input('email');
        $password = Hash::make($request->input('password'));
        $res = DB::table('users')->insert([
            'user_name'=>$user_name, 
            'real_name'=>$real_name, 
            'email'=>$email, 
            'password'=>$password,
            'create_time'=>time()
        ]);
        if($res){
            return view('success',['url'=>action('UserController@create')]);
        }else{
            return view('error',['url'=>action('UserController@create')]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_name = $request->input('user_name');
        $password = $request->input('password');
        if(Auth::attempt(['user_name'=>$user_name,'password'=>$password])){
            return redirect()->intended('/');
        }else{
            return redirect()->intended('/user');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    public function getlist(Request $request)
    {
        $count = DB::table('users')->count();
        $offset = ($request->input('page')-1) * $request->input('limit');
        $users = DB::table('users')->offset($offset)->limit($request->input('limit'))->get();
        foreach ($users as $key => $value) {
            $users[$key]->type = $value->type == 1 ? '管理员' : '普通用户';
        }
        $data = [
            'code' => 0,
            'msg' => 'ok',
            'count' => $count,
            'data' => $users
        ];
        return json_encode($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = DB::table('users')->where('id',$id)->first();
        //var_dump($user);
        return view('user/edit',['user'=>$user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = [
            'user_name' => $request->input('user_name'),
        ];
        if($request->input('real_name')){
            $data['real_name'] = $request->input('real_name');
        }
        if($request->input('email')){
            $data['email'] = $request->input('email');
        }
        if($request->input('password')){
            $data['password'] = Hash::make($request->input('password'));
        }
        if($request->input('wechat_notify') && $request->input('email_notify')){
            $data['notify'] = 3;
        }else if($request->input('wechat_notify')){
            $data['notify'] = 1;
        }else if($request->input('email_notify')){
            $data['notify'] = 2;
        }else{
            $data['notify'] = 0;
        }

        $res = DB::table('users')->where('id',$id)->update($data);
        if($res){
            return view('success',['url'=>route('user.edit',['id'=>$id])]);
        }else{
            return view('error',['url'=>route('user.edit',['id'=>$id])]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = DB::table('users')->where('id',$id)->delete();
        return $res ? 1 : 0;
    }

    public function list(){
        return view('user/list');
    }

    public function logout(){
        Auth::logout();
        return redirect()->action('UserController@index');
    }
}
