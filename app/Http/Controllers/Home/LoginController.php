<?php

namespace App\Http\Controllers\Home;

use App\Http\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;

class LoginController extends CommonController
{
	public function login()
	{
		if ($input = Input::all()) {

			if (!isset($input['user_name']) || !isset($input['user_password'])) {
				return redirect()->back()->with('msg', '用户名或者密码不能为空!');
			}
			if ($input['user_name'] == '' || $input['user_password'] == '') {
				return redirect()->back()->with('msg', '用户名或者密码不能为空!');

			}

			$user = User::where('user_phone', $input['user_name'])->first();
			if ($user) {
				$user->user_name = $input['user_name'];
				if ($input['user_password'] != Crypt::decrypt($user->user_password)) {
					return redirect()->back()->with('msg', '密码错误!');
				} else {
					session([
						'user' => $user
					]);
					$user->update();
					return redirect('index');
				}
			}
			$user = User::where('user_name', $input['user_name'])->get();
			if (count($user)) {
				$user = $user[0];
				if ($input['user_name'] != $user->user_name || $input['user_password'] != Crypt::decrypt($user->user_password)) {
					return redirect()->back()->with('msg', '用户名或者密码错误!');
				}

				session([
					'user' => $user
				]);
			} else {
				return redirect()->back()->with('msg', '用户名或者密码错误!');

			}


			return redirect('index');

		}
		$pageTitle = '登录';
		return view('home.login', compact('pageTitle'));
	}

	public function changepsw()
	{
		$pageTitle = '修改密码';
		return view('home.changepsw', compact('pageTitle'));
	}

	public function changepswdone()
	{
		if ($input = Input::all()) {

			$user_captcha = trim($input['user_captcha']);
			if ($user_captcha != session('captcha') || session('captcha') == null) {
				return redirect()->back()->with('msg', '验证码不正确！');
			}

			$rules = [
				'user_phone' => 'required|numeric',
				'user_captcha' => 'required|numeric',
				'user_password' => 'required|between:6,20'
			];

			$message = [
				'user_captcha.required' => '验证码不能为空!',
				'user_password.required' => '密码不能为空!',
				'user_phone.numeric' => '手机号码必须是数字!',
				'user_password.between' => '新密码必须6到20位之间!',
			];
			$validator = Validator::make($input, $rules, $message);

			if ($validator->passes()) {

				$user = User::where('user_phone', $input['user_phone'])
					->first();
				if ($user) {
					$user->user_password = Crypt::encrypt(trim($input['user_password']));
					$r = $user->update();
					if ($r) {
						return redirect('login')->with('msg',$user->user_phone .' 密码修改成功!');

					} else {
						return redirect()->back()->with('msg',"数据库更新错误");

					}
				} else {
					return redirect()->back()->with('msg',$user->user_phone .' 不存在!');
				}


			} else {

				return redirect()->back()->withErrors($validator);
			}
		} else {
			return redirect()->back()->with('msg', '请输入注册信息');

		}
	}
	public function register()
	{
		$pageTitle = '注册';
		return view('home.register', compact('pageTitle'));
	}

	public function registerdone()
	{

		if ($input = Input::all()) {
			$user_captcha = trim($input['user_captcha']);
			if ($user_captcha != session('captcha') || session('captcha') == null) {
				return redirect()->back()->with('msg', '验证码不正确！');
			}

			$rules = [
				'user_phone' => 'required|numeric',
				'user_phone' => 'required|numeric',
				'user_captcha' => 'required|between:2,60'
			];

			$message = [
				'user_captcha.required' => '验证码不能为空!',
				'user_password.required' => '密码不能为空!',
				'user_phone.numeric' => '手机号码必须是数字!',
				'user_password.between' => '新密码必须2到60位之间!',
			];
			$validator = Validator::make($input, $rules, $message);

			if ($validator->passes()) {

				$user = new User();
				$user->user_name = $input['user_phone'];
				$user->user_phone = $input['user_phone'];
				$user->user_password = Crypt::encrypt($input['user_password']);
				$user->save();
				session([
					'user' => $user
				]);
				return redirect('index')->with('msg',$user->user_phone .' 注册成功!');

			} else {

				return redirect()->back()->withErrors($validator);
			}
		} else {
			return redirect()->back()->with('msg', '请输入注册信息');

		}

	}

	public function sendSms()
	{
		$mobile = trim(Input::get('mobile'));
		$user = User::where('user_phone', $mobile)->first();
		if ($user) {
			$res["count"] = 0;
			$res["error_code"] = 0;
			$res["reason"] = "4005"; //电话号码已经存在
			return response(json_encode($res));
		}
		$captcha = $this->make_rand(4);
		$key = trim("15yj1xdaxt0vrcbjrhi5zhwagc36yw");
		session(['captcha'=> $captcha]);
		$content = urlencode(trim("【爱学生】您的验证码是" . $captcha . "，请妥善保管。"));
		$url = "http://121.40.119.148/api.php?mobile=" . $mobile . "&content=" . $content . "&key=" . $key;
		$res = $this->curlHttpGet($url);
		$res = json_decode($res);
		return response(json_encode($res));

	}
	public function findpsw()
	{
		$mobile = trim(Input::get('mobile'));
		$user = User::where('user_phone', $mobile)->first();
		if (!$user) {
			$res["count"] = 0;
			$res["error_code"] = 0;
			$res["reason"] = "4006"; //电话号码不存在
			return response(json_encode($res));
		}
		$captcha = $this->make_rand(4);
		$key = trim("15yj1xdaxt0vrcbjrhi5zhwagc36yw");
		session(['captcha'=> $captcha]);
		$content = urlencode(trim("【爱学生】您的验证码是" . $captcha . "，请妥善保管。"));
		$url = "http://121.40.119.148/api.php?mobile=" . $mobile . "&content=" . $content . "&key=" . $key;
		$res = $this->curlHttpGet($url);
		$res = json_decode($res);
		return response(json_encode($res));

	}

	public function quit()
	{
		session(['user' => null]);
		return redirect('login');
	}
}
