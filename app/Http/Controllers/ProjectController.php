<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('project/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = DB::table('users')->where('id','!=',1)->get();
        foreach ($users as $key => $value) {
            $users[$key]->name = $value->real_name ? $value->real_name : $value->user_name;
        }

        return view('project/create',['users'=>$users]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $project_name = $request->input('project_name');
        $token = $request->input('token');
        $id = DB::table('project')->insertGetId([
            'project_name' => $project_name, 
            'token' => $token, 
            'create_time'=>time()
        ]);
        $users = $request->users;
        if(!empty($users)){
            array_push($users, 1);
            foreach ($users as $key => $value) {
                DB::table('user_project')->insert([
                    'user_id' => $value,
                    'project_id' => $id,
                    'create_time' => time()
                ]);
            }
        }
        
        if($id){
            return view('success',['url'=>action('ProjectController@index')]);
        }else{
            return view('error',['url'=>action('ProjectController@create')]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $count = DB::table('project')->count();
        $offset = ($request->input('page')-1) * $request->input('limit');
        $projects = DB::table('project')->offset($offset)->limit($request->input('limit'))->get();

        $data = [
            'code' => 0,
            'msg' => 'ok',
            'count' => $count,
            'data' => $projects
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
        $project = DB::table('project')->where('id',$id)->first();
        $users = DB::table('users')->where('id','!=',1)->get();
        foreach ($users as $key => $value) {
            $users[$key]->name = $value->real_name ? $value->real_name : $value->user_name;
        }
        $user_project = DB::table('user_project')->where('project_id',$id)->get();
        if(count($user_project)){
            foreach ($user_project as $key => $value) {
                $userid[] = $value->user_id;
            }
        }else{
            $userid = [];
        }

        return view('project/edit',['project'=>$project,'users'=>$users,'userid'=>$userid]);
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
            'project_name' => $request->input('project_name'),
            'token' => $request->input('token'),
        ];
        $res = DB::table('project')->where('id',$id)->update($data);

        $res = DB::table('user_project')->where('project_id',$id)->delete();
        $users = $request->input('users');
        if(!empty($users)){
            array_push($users, 1);
            foreach ($users as $key => $value) {
                $res = DB::table('user_project')->insert([
                    'user_id' => $value,
                    'project_id' => $id,
                    'create_time' => time()
                ]);
            }
        }

        if($res){
            return view('success',['url'=>route('project.index')]);
        }else{
            return view('error',['url'=>route('project.index')]);
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
        $res = DB::table('project')->where('id',$id)->delete();
        DB::table('user_project')->where('project_id',$id)->delete();
        return $res ? 1 : 0;
    }

}
