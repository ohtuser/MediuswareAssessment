<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function userForm(){
        return view('admin.user_management');
    }
    public function user(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$request->row_id,
            'account_type' => 'required',
        ]);
        if(!$request->row_id){
            $request->validate([
                'password' => 'required|same:confirm_password'
            ]);
        }
        if($request->row_id){ // for update
            $user = User::find($request->row_id);
            $user->updated_at = now();
        }else{
            $user = new User();
            $user->created_at = now();
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->account_type = $request->account_type;
        $user->password = Hash::make($request->password);
        $user->save();
        return back()->with('success', 'User '. ($request->row_id ? 'updated' : 'added') .' successfully');
    }
}
