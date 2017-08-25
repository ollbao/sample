<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{

    public function __construct(){
        //除了这几个动作,其他动作都需要授权
        $this->middleware('auth', [
            'except' => ['create', 'store', 'index', 'confirmEmail']
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
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
    }

    //用户信息页面
    public function show(User $user){
        $statuses = $user->statuses()->orderBy('created_at', 'desc')->paginate(30);
        return view('users/show',compact('user','statuses'));
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

    //发送邮件
    protected function sendEmailConfirmationTo($user){
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'aufree@yousails.com';
        $name = 'Aufree';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }


    public function confirmEmail($token){
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }


    //关注列表
    public function followings(User $user){
        $users = $user->followings()->paginate(30);
        $title = '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }


    //粉丝列表
    public function followers(User $user){
        $users = $user->followers()->paginate(30);
        $title = '粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
