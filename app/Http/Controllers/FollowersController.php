<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FollowersController extends Controller
{
    public function __contruct(){
        $this->middleware('auth');
    }

    public function store(User $user){
        $this->authorize('follow',$user);
        if(!Auth::user()->isFollowing($user->id)){
            Auth::user()->follow($user->id);
        }
        return redirect()->route('users.show',$user->id);
    }

    public function destroy(User $user){
        $this->authorize('follow',$user);

        if(Auth::user()->isFollowing($user->id)){
            Auth::user()->unfollow($user->id);
        }
        return redirect()->route('users.show',$user->id);
    }

}
