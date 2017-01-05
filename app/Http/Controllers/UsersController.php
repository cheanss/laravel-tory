<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'only' => ['edit', 'update', 'destroy', 'followings', 'followers'],
        ]);

        $this->middleware('guest', [
            'only' => ['create'],
        ]);
    }

    public function index()
    {
        $users = User::paginate(30);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $statuses = $user->statuses()
            ->orderBy('created_at', 'desc')
            ->paginate(30);
        return view('users.show', compact('user', 'statuses'));
    }

    public function store(Request $request)
    {
        // 验证用户提交数据
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed',
        ]);

        // 创建用户并写入数据库
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // 用户注册成功后自动登录
//        Auth::login($user);
        // 用户注册成功后发送邮件已激活账号
        $this->sendEmailConfirmationTo($user);
        // 提示语，flash 第一个参数和 bootstrap 的类进行结合提示成功样式语句
//        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        // 跳转到用户详情页
//        return redirect()->route('users.show', [$user]);
        return redirect('/');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'confirmed|min:6',
        ]);

        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $data = array_filter([
            'name' => $request->name,
            'password' => $request->password,
        ]);

        $user->update($data);

        session()->flash('success', '个人资料更新成功！');
        return redirect()->route('users.show', $id);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }

    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'jack.shi@keerle.net';
        $name = 'Jack';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜您，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

    public function followings($id)
    {
        $user = User::findOrFail($id);
        $users = $user->followings()->paginate(30);
        $title = '我关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    public function followers($id)
    {
        $user = User::findOrFail($id);
        $users = $user->followers()->paginate(30);
        $title = '我的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
