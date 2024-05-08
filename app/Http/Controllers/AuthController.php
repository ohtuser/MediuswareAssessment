<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request){
        return view('auth.login');
    }
    public function login_post(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $userInfo = User::where('email', $request->email)->first();
        if ($userInfo) {
            if (Hash::check($request->password, $userInfo->password)) {
                Auth::guard('auth')->login($userInfo);
                return redirect()->route('dashboard')->with('success', 'Welcome '. $userInfo->name);
                
            } else {
                return back()->with('error', 'Invalid Password');
            }
        } else {
            return back()->with('error', 'Invalid Email');
        }
        
    }
    public function logout(Request $request){
        Auth::guard('auth')->logout();
        return redirect()->route('login');
    }
}
