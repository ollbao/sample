<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{

    public function __construct(){
        //除了这几个动作,其他动作都需要授权
        $this->middleware('auth', [
            'except' => ['create', 'store'. 'index']
        ]);
        //只允许未登录用户访问的动作
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }


    //用户页面
    public function index(){
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }


    //注册页面
    public function create(){
        return view('users/create');
    }

    //注册提交页面
    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        Auth::login($user);
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show',[$user]);
    }

    //用户信息页面
    public function show(User $user){
        return view('users/show',compact('user'));
    }

    //用户信息编辑页面
    public function edit(User $user){
        $this->authorize('update', $user);//防止编辑他人信息
        return view('users/edit',compact('user'));
    }

    //用户信息编辑提交
    public function update(User $user, Request $request){

        $this->authorize('update', $user);//防止编辑他人信息

        $this->validate($request,[
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);
        $data = [];
        $data['name'] = $request->name;
        if($request->password){
            $data['password'] = $request->password;
        }
        $user->update($data);
        session()->flash('success', '用户信息修改成功');
        return redirect()->route('users.show', $user->id);
    }

    //删除用户
    public function destroy(User $user){
        $this->authorize('destroy', $user);//只允许管理员操作
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }
}
