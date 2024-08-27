<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function index(){
     
       $user = Auth::user();
        //$user = User::find(Auth::id());
        //dd($user->role);
        return view('user.dashboard',compact('user'));
    }
}
