<?php

namespace App\Http\Controllers\Admin;

use App\Http\Model\PaperInfo;
use App\Http\Model\PaperQuestion;
use App\Http\Model\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PaperController extends Controller
{
    //

	public function index($user_id)
	{

		$papers = PaperInfo::where('user_id', $user_id)
			->orderBy('created_at', 'DESC')
			->paginate(10);
		$sumPapers = PaperInfo::where('user_id', $user_id)->count();
		return view('admin.paper.index', compact('papers','sumPapers'));
	}

	/**
	 * 题目列表
	 * @param int $paper_id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
	 */
	public function questionList($paper_id)
	{

		if ($paper_id) {
			$paper_id = intval($paper_id);
			$questions = PaperQuestion::where('paper_id', $paper_id)
				->paginate(10);

			$sumQuestions = PaperQuestion::where('paper_id', $paper_id)->count();
			$total_score = PaperInfo::find($paper_id)->total_score;
		} else {
			//未知意外
			return redirect()->back();
		}

		return view('admin.paper.paper', compact('questions', 'sumQuestions', 'total_score'));
	}
	public function exportExcel($paper_id)
	{

		if ($paper_id) {
			$paper_id = intval($paper_id);


			/*$questions = PaperQuestion::where('paper_id', $paper_id)
				->paginate(10);

			$sumQuestions = PaperQuestion::where('paper_id', $paper_id)->count();
			$total_score = PaperInfo::find($paper_id)->total_score;*/

			if ($paper_id) {
				$paper_id = intval($paper_id);
				$questions = PaperQuestion::where('paper_id', $paper_id)
					->paginate(100);

				$sumQuestions = PaperQuestion::where('paper_id', $paper_id)->count();
				$total_score = PaperInfo::find($paper_id)->total_score;
				$userId = PaperInfo::find($paper_id)->user_id;
				$userName = User::find($userId)->user_name;
				$paperName = $userName."-".$paper_id;
			} else {
				//未知意外
				return redirect()->back();
			}

			require_once 'resources/org/phpexcel/PHPExcel.php';
			$objPHPExcel = new \PHPExcel(); //生成一个sheet
			$objSheet = $objPHPExcel->getActiveSheet();//当前活动sheet
			$objSheet->setTitle($paperName);
			$objSheet->setCellValue("A1", $userName." 的试卷 编号：". $paper_id);
			$objSheet->setCellValue("B1", "总分：".$total_score."  题目数：".$sumQuestions);
			$objSheet->setCellValue("A2", "编号");
			$objSheet->setCellValue("B2", "标题");
			$objSheet->setCellValue("C2", "答题过程");
			$objSheet->setCellValue("D2", "正确答案");
			$objSheet->setCellValue("E2", "用户答案");
			$objSheet->setCellValue("F2", "得分");

			foreach ($questions as $key=>$question) {
				//echo "key:".$key."-----"."question:".$question;
				$objSheet->setCellValue("A".($key+3), $question->question_order);
				$objSheet->setCellValue("B".($key+3), $question->question_title);
				$objSheet->setCellValue("C".($key+3), $question->quest_process);
				$objSheet->setCellValue("D".($key+3), $question->question_answer);
				$objSheet->setCellValue("E".($key+3), $question->quest_answer);
				if ($question->question_answer==$question->quest_answer) {
					$objSheet->setCellValue("F".($key+3), $question->question_score);
				} else {
					$objSheet->setCellValue("F".($key+3), 0);
				}

			}

			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");//Excel5 2003，Excel2007
			$path = base_path('uploads') . "/excel/".$paperName.".xls";
			$objWriter->save($path);

			$front_path = "/uploads/excel/".$paperName.".xls";

		} else {
			//未知意外
			return redirect()->back();
		}

		return view('admin.paper.exportexcel', compact("front_path"));
	}
}
