<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    //

    public function __construct(){
         $this->middleware('auth',[
            'except'=>['show','create','store','index','confirmEmail']
         ]);

         $this->middleware('guest',[
            'only'=>['create']
         ]);

         //限流一个小时呢只能提交10此请求

         $this->middleware('thorttle:10,60',[
            'only'=>['store']
         ]);
    }


    public function index(){
        $users = User::paginate(6);
        return view('users.index',compact('users'));
    }

    public function create(){
        return view('users.create');
    }

    public function show(User $user){
           return view('users.show',compact('user'));
    }


    public function store(Request $request){
        $this->validate($request,[
            'name'=>'required|unique:users|max:50',
            'email'=>'required|email|unique:users|max:255',
            'password'=>'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password)
        ]);

        $this->sendEmailConfirmationTo($user);
        session()->flash('success','验证邮箱已发送到你的注册邮箱上，请注意查收');
        return redirect('/');
    }

    public function edit(User $user){
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    public function update(User $user,Request $request){
        $this->authorize('update',$user);
        $this->validate($request, [
            'name'=>'required|max:50',
            'password'=>'nullable|min:6|confirmed'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        session()->flash('success','个人资料更新成功');

        return redirect()->route('users.show',$user->id);

    }

    public function destroy(User $user){
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','成功删除用户！');
        return back();
    }

    protected function sendEmailConfirmationTo($user){
        $view = 'emails.confirm';
        $data = compact('user');
        // $from = 'summer@example.com';
        // $name = 'Summer';
        $to = $user->email;
        $subject = '感谢注册Weibo应用！请确认你的邮箱。';

        Mail::send($view,$data,function($message) use ($to,$subject){
            $message->to($to)->subject($subject);
        });
    }

    public function confirmEmail($token){
        $user = User::where('activation_token',$token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success','恭喜你，激活成功！');
        return redirect()->route('users.show',[$user]);
    }
}


