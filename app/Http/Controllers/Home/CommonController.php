<?php

namespace App\Http\Controllers\Home;

use App\Http\Model\PaperInfo;
use App\Http\Model\QuestConfig;
use App\Http\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CommonController extends Controller
{
	public function checkNicknameAndPhone()
	{
		$user = session('user');
		if ($user->user_neckname=="" || strlen($user->user_phone)< 11) {
			return true;
		}
		return false;
	}

	public function userCheck()
	{
		/*判断审核*/
		$sessionUser = session('user');
		$sessionUser = User::where("user_id", $sessionUser->user_id)->first();
		if ($sessionUser) {
			return $sessionUser->user_check;
		}
		return false;
		/*判断审核 end*/
	}

	public function checkSystemStatus()
	{
		$isClosed = QuestConfig::where("conf_name", "isCloseSystem")->value('field_value');
		return $isClosed;
	}
	public function getExamTime()
	{
		$examTime = QuestConfig::where("conf_name", "examTime")->value('field_value');
		return $examTime;
	}



	public function curlHttpGet($url)
	{
		$url = trim($url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); //执行后不打印出来。
		curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);

		//设置https
		date_default_timezone_set('PRC'); //使用cookies时，必须设置时区
		$output = curl_exec($ch);
		if ($output == false) {
			return curl_error($ch);
		}
		curl_close($ch);
		return $output;
	}

	public function curlHttpsGet($url)
	{
		$url = trim($url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);

		//设置https
		date_default_timezone_set('PRC'); //使用cookies时，必须设置时区
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //终止从服务器端进行验证
		$output = curl_exec($ch);
		if ($output == false) {
			return curl_error($ch);
		}
		curl_close($ch);
		return $output;
	}

	function make_rand($length = "4")
	{
		$str = "0123456789";
		$result = "";
		for ($i = 0; $i < $length; $i++) {
			$num[$i] = rand(0, 9);
			$result .= $str[$num[$i]];
		}
		return $result;
	}
	public function checkQuestLibCount()
	{
		$questLib = DB::table('question')->where('question_is_quest_bank', 1)
			->count();

		if ($questLib == 0) {
			QuestConfig::find(1)->update([
				"field_value" => 0
			]);
			return false;
		} else {
			return true;
		}
	}
}
