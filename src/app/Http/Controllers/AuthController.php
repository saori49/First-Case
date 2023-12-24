<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //loginページ表示
    public function showLogin()
    {
        return view('login');
    }

    //login
    public function login(LoginFormRequest $request)
    {
        $credentials = $request->only( 'email','password');
        //成功
        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
            return redirect('/')->with('login_success','ログイン成功しました');
        }

        //失敗
        return redirect('/login')->with(
            'login_error' ,'メールアドレスかパスワードが間違っています。',
        );
    }

    //logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('logout','ログアウトしました');
    }

    //registerページ表示
    public function showRegister()
    {
        return view('register');
    }

    // ユーザーを作成してデータベースに保存
    public function register(RegisterFormRequest $request)
    {
        $user=User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect('/login')->with('success', 'ユーザーが登録されました。');
    }

}
