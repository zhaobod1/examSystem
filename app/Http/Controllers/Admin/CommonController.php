<?php

namespace App\Http\Controllers\Admin;

use App\Http\Model\PaperInfo;
use App\Http\Model\PaperQuestion;
use App\Http\Model\QuestConfig;
use App\Http\Model\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class CommonController extends Controller
{
	//
	public function upload()
	{
		$file = Input::file('Filedata');
		if ($file->isValid()) {
			$entension = $file->getClientOriginalExtension(); //上传文件的后缀.
			if ($entension == 'png' || $entension == 'gif' || $entension == 'jpg' || $entension == 'jpeg') {
				$newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
				$path = $file->move(base_path() . '/uploads', $newName);
				$filepath = 'uploads/' . $newName;
				return $filepath;
			} else {
				return '图片格式只允许: png/jpg/jpeg/gif!';
			}

		}


	}

	public function getExamTime()
	{

		$examTime = QuestConfig::where("conf_name", "examTime")->value('field_value');
		return $examTime;
	}

	/**
	 * 检测失误的操作导致的0分试卷
	 */
	public function checkOnLineExamTime()
	{

		/* 检测是否存在没有答完的试卷 2017/3/15 */
		$sysExamTime = intval($this->getExamTime()) * 60;
		$currentTime = time();
		//现在的时间减去系统规定的时间
		$startTime = $currentTime - $sysExamTime;
		$users = User::whereRaw("user_id>? and paper_id>? and start_exam <?", [1, 0, $startTime])
			->get();
		if (count($users))
			/** @var User $user */
			foreach ($users as $user) {
				$paper = PaperInfo::where("paper_id", $user->paper_id)->first();
				if ($paper->total_score == 0 && $paper->updated_at == 0) {

					PaperQuestion::where("paper_id", $user->paper_id)->delete();
					$paper->delete();
					$paper->update();
					$user->paper_id = 0;
					$user->start_exam = 0;
					$user->update();
				}

			}


		/* 检测是否存在没有答完的试卷 2017/3/15 end*/
	}




}
