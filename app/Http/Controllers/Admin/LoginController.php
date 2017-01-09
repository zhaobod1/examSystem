<?php

namespace App\Http\Controllers\Admin;

use App\Http\Model\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;

require_once 'resources/org/code/Code.class.php';

class LoginController extends CommonController
{
    //
    public function login()
    {
        if ($input = Input::all()) {

            $userArr = $input['user'];
            $code = new \Code;
            $_code = $code->get();

            if (strtoupper($userArr['code']) != $_code) {
                return redirect()->back()->with('msg', '验证码错误!');
            }

            $user = User::first();
            if ($userArr['username'] != $user->user_name || $userArr['password'] != Crypt::decrypt
                ($user->user_password)) {
                return redirect()->back()->with('msg', '用户名或者密码错误!');
            }

            session([
                'user' => $user
            ]);

            return redirect('admin/index');

        } else {

            session(['user' => null]);
            return view('admin.login');

        }
    }

    public function code()
    {
        $code = new \Code;
        $code->make();

    }

    public function getCode()
    {
        $code = new \Code;
        return $code->get();
    }

    public function quit()
    {
        session(['user' => null]);
        return redirect('admin/login');
    }



}
