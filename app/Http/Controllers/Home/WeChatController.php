<?php

namespace App\Http\Controllers\Home;

use App\Http\Model\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Ixudra\Curl\Facades\Curl;

class WeChatController extends CommonController
{

	private $appid = 'wxc5ee40750ec3fd1a';
	private $appsecret = 'dee2649fc3c43f61e929f0bdbfec6949';
	public $curlObj = '';
	//获取用户的openid
	public function baseInfo()
	{

		//1.获取code
		$redirect_uri = urlencode('http://q.huo15.com/wechat/getCode');
		$scope = 'snsapi_base';
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->appid . '&redirect_uri=' . $redirect_uri. '&response_type=code&scope=' . $scope . '&state=STATE#wechat_redirect';
		header('location:' . $url);
	}

	//获取openId  getCode()
	public function getUserOpenId()
	{
		//2.获取网页授权的access_token

		$code = trim($_GET['code']);
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret=' .$this->appsecret. '&code='.$code.'&grant_type=authorization_code';

		//3.拉取用户的openid

		$response = $this->curlHttpsGet($url);
		/*
		 * { "access_token":"ACCESS_TOKEN",
			 "expires_in":7200,
			 "refresh_token":"REFRESH_TOKEN",
			 "openid":"OPENID",
			 "scope":"SCOPE" }
		*/

		$response = json_decode($response);
		return $response->openid;
	}

	public function getUserDetail()
	{

		//2-1.获取code
		$redirect_uri = urlencode('http://q.huo15.com/wechat/getUserInfo');
		$scope = 'snsapi_userinfo';
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->appid . '&redirect_uri=' . $redirect_uri. '&response_type=code&scope=' . $scope . '&state=STATE#wechat_redirect';
		header('location:' . $url);
	}

	public function getUserInfo(Request $request)
	{
		//2-2.获取网页授权的access_token

		$code = $_GET['code'];
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->appid . '&secret=' . $this->appsecret . '&code=' . $code . '&grant_type=authorization_code';

		$response = $this->curlHttpsGet($url);
		$response = json_decode($response);
		$access_token = $response->access_token;
		$openid = $response->openid;
		//2-3.拉取用户的详细信息
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
		$response = $this->curlHttpsGet($url);
		$response = json_decode($response);


		$userInfo = User::where('openid', $response->openid)->get();
		if (count($userInfo)) {
			$userInfo = $userInfo[0];
			session([
				'user' => $userInfo
			]);

			return redirect('index');


		} else {
			$userInfo = array();
			$userInfo['user_name'] = $response->openid;
			$userInfo['user_neckname'] = $response->nickname;
			$userInfo['user_password'] = 'eyJpdiI6IktOSWRlY09lZE11b29XUUhmeVdrUkE9PSIsInZhbHVlIjoiVVwvVFFtdlo2bldEZWFhaDVGK3RPYmc9PSIsIm1hYyI6IjZiZTk2YjQ0OTJkODBjYTYzY2E3NDM0NjM3ZTk5ZTU3ZTkxNmQyZGFmNzAzY2QzOTZiOWRkZjFlZmU3NWEwOTcifQ==';
			$userInfo['user_check'] = 0;
			$userInfo['user_phone'] = '0';
			$userInfo['user_avatar'] = $response->headimgurl;
			$userInfo['created_at'] = time();
			$userInfo['updated_at'] = time();
			$userInfo['start_exam'] = 0;
			$userInfo['openid'] = $response->openid;
			$res = User::create($userInfo);
			if ($response) {
				$temp = $userInfo;
				$userInfo = new User();
				$userInfo['user_name'] = $response->openid;
				$userInfo['user_neckname'] = $response->nickname;
				$userInfo['user_password'] = 'eyJpdiI6IktOSWRlY09lZE11b29XUUhmeVdrUkE9PSIsInZhbHVlIjoiVVwvVFFtdlo2bldEZWFhaDVGK3RPYmc9PSIsIm1hYyI6IjZiZTk2YjQ0OTJkODBjYTYzY2E3NDM0NjM3ZTk5ZTU3ZTkxNmQyZGFmNzAzY2QzOTZiOWRkZjFlZmU3NWEwOTcifQ==';
				$userInfo['user_check'] = 0;
				$userInfo['user_phone'] = '0';
				$userInfo['user_avatar'] = $response->headimgurl;
				$userInfo['created_at'] = $temp['created_at'];
				$userInfo['updated_at'] = $temp['updated_at'];
				$userInfo['start_exam'] = 0;
				$userInfo['openid'] = $response->openid;
				session([
					'user' => $userInfo
				]);
				return redirect('index');
			} else {
				return redirect('index');
			}

		}





		/*
		 * {    "openid":" OPENID",
				 "nickname": NICKNAME,
				 "sex":"1",
				 "province":"PROVINCE"
				 "city":"CITY",
				 "country":"COUNTRY",
				 "headimgurl":    "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/46",
				"privilege":[ "PRIVILEGE1" "PRIVILEGE2"     ],
				 "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
				}
		 */

	}



}
