<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use App\Http\Requests\RequestLogin;
use Mail;
use App\Mail\RegisterSuccess;

class LoginController extends Controller 
{
    public function getFormLogin(){
        return view('auth.login');
    }
    public function postLogin(RequestLogin $request){
        $data =$request->only('email','password');
        if(Auth::attempt($data) || Auth::attempt(['user'=> $request->email , 'password' => $request->password]) ||
        Auth::attempt(['phone'=> $request->email , 'password' => $request->password])
        ){ 
            if(Auth::user()->is_active ==1){
                return redirect()->to('/');
            }
            else if(Auth::user()->is_active ==0){
                Auth::logout();
                \Session::flash('toastr',[
                    'type'=>'error',
                    'messages'=>'Tài khoản của bạn chưa được xác thực . Chúng tôi đã gửi một email đến '.$request->email.' với một liên kết để xác thực tài khoản của bạn.'
                ]);
                Mail::to($request->email)->send(new RegisterSuccess($request->c_name,$request->user));
                return redirect()->route('get.login');
            }
            else if(Auth::user()->is_active ==2){
                Auth::logout();
                \Session::flash('toastr',[
                    'type'=>'error',
                    'messages'=>'Tài khoản của bạn đã bị khóa do vi phạm chính sách của chúng tôi! '
                ]);
                return redirect()->route('get.login');
            }
    }
        else{
            \Session::flash('toastr',[
                'type'=>'error',
                'messages'=>'Sai tài khoản hoặc mật khẩu'
            ]);
        }
        return redirect()->back();
    }
    protected function getLogout(){
        Auth::logout();
        return redirect()->route('get.login');
    }
}
