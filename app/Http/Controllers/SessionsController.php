<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{


    public function create(){
        return view('sessions/create');
    }


    public function store(Request $request){
        $this->validate($request,[
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];
        if (Auth::attempt($credentials,$request->has('remember'))) {
            // 登录成功后的相关操作
            session()->flash('success', '欢迎回来！');
            return redirect()->route('users.show', [Auth::user()]);
        } else {
            // 登录失败后的相关操作
            session()->flash('danger', '用户名或密码错误');
            return redirect()->back();
        }
        return;
    }


    public function destroy(){
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }
}
