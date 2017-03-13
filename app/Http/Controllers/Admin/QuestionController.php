<?php

namespace App\Http\Controllers\Admin;

use App\Http\Model\PaperInfo;
use App\Http\Model\PaperQuestion;
use App\Http\Model\Question;
use App\Http\Model\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class QuestionController extends CommonController
{
	//
	public function index(Request $request)
	{

		$category = $request->get('category');
		if (isset($category)) {

			switch ($category) {
				case 0:
					$datas = Question::where('question_is_quest_bank', 0)->orderBy('question_order', 'DESC')
						->paginate(10);
					break;
				case 1:
					$datas = Question::where('question_is_quest_bank', 1)->orderBy('question_order', 'DESC')
						->paginate(10);
					break;
				default:
					$datas = Question::orderBy('question_order', 'DESC')
						->paginate(10);

			}

		} else {
			$category = 2;
			$datas = Question::orderBy('question_order', 'DESC')
				->paginate(10);
		}

		//分值计算
		$sumScore = Question::where('question_is_quest_bank', 1)->sum('question_score');
		//题库数量
		$sumQuestion = Question::where('question_is_quest_bank', 1)->count();


		//dd($datas->links());
		return view('admin.question.index', compact('datas', 'sumScore', 'sumQuestion', 'category'));
	}

	public function ajax()
	{
		$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : 0;
		if ($act == 'laid_in') {
			$datas = $_POST['data'];
			foreach ($datas as $data) {
				$question = Question::find($data);
				$question->question_is_quest_bank = 1;
				$question->save();
			}
		}
		if ($act == 'out_put') {
			$datas = $_POST['data'];
			foreach ($datas as $data) {
				$question = Question::find($data);
				$question->question_is_quest_bank = 0;
				$question->save();
			}
		}

		echo json_encode(1);
		die;
	}

	public function create()
	{
		$data = [];
		return view('admin.question.add', compact('data'));

	}

	public function analysis()
	{
		//判断是否有人没有交卷
		$usersHasExamTime = User::whereRaw("start_exam > ? ", [0])
			->get();
		if ($usersHasExamTime) {
			$userNames = "";
			foreach ($usersHasExamTime as $user) {
				$userNames .= $user->user_neckname . ", ";
			}
			$userNames = substr($userNames,0,-1);
			echo "<script>alert('有学生没有交卷！请先敦促学生交卷再导出！ 没有交卷的同学是：' + " . $userNames . ")</script>";
			return new \Response();
		}
		$questions = Question::where('question_is_quest_bank', 1)->orderBy('question_order', 'DESC')->get();


		$users = User::whereRaw('user_id>? and user_check=?', [1, 1])->orderBy('updated_at', 'DESC')->get();


		//二维数组  学员的答题情况
		$aTable = array();


		foreach ($users as $k => $user) {
			$aTable[$k] = array();
			array_push($aTable[$k], $user->user_id . "-" . $user->user_neckname);
			foreach ($questions as $key => $question) {

				$paper = PaperInfo::where('user_id', $user->user_id)
					->orderBy('paper_id', 'DESC')
					->first();

				if (count($paper)) {

					$paperQuests = PaperQuestion::where('paper_id', $paper->paper_id)
						->get();
					$cell = null;
					foreach ($paperQuests as $paperQuest) {
						if ($paperQuest->question_id == $question->question_id) {
							$cell = ($paperQuest->quest_answer == $paperQuest->question_answer) ? 1 : 0;
							array_push($aTable[$k], $cell);
						}
					}
					if ($cell === null) {
						array_push($aTable[$k], $cell);
					}



				} else {
					array_push($aTable[$k], "从未");
				}


			}

			//$sumPapers = PaperInfo::where('user_id', $user_id)->count();
		}




		require_once 'resources/org/phpexcel/PHPExcel.php';
		$objPHPExcel = new \PHPExcel(); //生成一个sheet
		$objSheet = $objPHPExcel->getActiveSheet();//当前活动sheet
		$paperName = "report" . date("YmdHis", time());
		$objSheet->setTitle($paperName);

		foreach($questions as $key => $question) {
			//写行数据
			$objSheet->setCellValue(\PHPExcel_Cell::stringFromColumnIndex($key+1)."1", $question->question_order . "(" . $question->question_title . ")");

		}

		foreach ($aTable as $k => $col) {
			//写列数据
			foreach ($col as $key => $value ) {

				$objSheet->setCellValue(\PHPExcel_Cell::stringFromColumnIndex($key) . ($k+2), $value);


			}
		}



		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");//Excel5 2003，Excel2007
		$path = base_path('uploads') . "/excel/" . $paperName . ".xls";
		$objWriter->save($path);

		$front_path = "/uploads/excel/" . $paperName . ".xls";
		/*
		$objSheet->setCellValue("A1", $userName." 的试卷 编号：". $paper_id);
		$objSheet->setCellValue("B1", "总分：".$total_score."  题目数：".$sumQuestions);
		$objSheet->setCellValue("A2", "编号");
		$objSheet->setCellValue("B2", "标题");
		$objSheet->setCellValue("C2", "答题过程");
		$objSheet->setCellValue("D2", "正确答案");
		$objSheet->setCellValue("E2", "用户答案");
		$objSheet->setCellValue("F2", "得分");
		*/

		return view('admin.question.analysis', compact("front_path"));
	}

	public function edit($question_id)
	{
		//echo 'edit:' . $question_id;

		$field = Question::find($question_id);

		return view('admin.question.edit', compact('field'));

	}

	public function update($question_id)
	{
		$input = Input::except('_token', '_method');
		$input['question_order'] = intval($input['question_order']);
		$input['question_time'] = time();
		if ($input['question_is_quest_bank'] == '1') {
			$input['question_is_quest_bank'] = 1;
		} elseif ($input['question_is_quest_bank'] == '0' || $input['question_is_quest_bank'] == '') {
			$input['question_is_quest_bank'] = 0;
		} else {
			$input['question_is_quest_bank'] = 0;
		}
		$rules = [
			'question_title' => 'required',
			'question_content' => 'required',
			'question_time' => 'required',
			'question_order' => 'required|numeric',
			'question_answer' => 'required|numeric',
		];
		$message = [
			'question_title.required' => '题目标题不能为空!',
			'question_content.required' => '题目内容不能为空!',
			'question_order.required' => '排序不能为空!',
			'question_order.numeric' => '排序必须是数字!',
			'question_answer.required' => '答案不能为空!',
			'question_answer.numeric' => '答案必须是数字!',
		];

		$validator = Validator::make($input, $rules, $message);

		if ($validator->passes()) {
			$res = Question::where('question_id', $question_id)->update($input);

			if ($res) {
				return redirect('admin/question')->with('errors', '更新数据成功');
			} else {
				return redirect()->back()->with('errors', '更新数据失败!');
			}
		} else {
			return redirect()->back()->withErrors($validator);
		}


	}


	public function store()
	{
		$input = Input::except(['_token', 'question_thumb']);

		$input['question_order'] = intval($input['question_order']);
		$input['question_time'] = time();
		if ($input['question_is_quest_bank'] == '1') {
			$input['question_is_quest_bank'] = 1;
		} elseif ($input['question_is_quest_bank'] == '0' || $input['question_is_quest_bank'] == '') {
			$input['question_is_quest_bank'] = 0;
		} else {
			$input['question_is_quest_bank'] = 0;
		}
		$rules = [
			'question_title' => 'required',
			'question_content' => 'required',
			'question_time' => 'required',
			'question_order' => 'required|numeric',
			'question_answer' => 'required|numeric',
		];
		$message = [
			'question_title.required' => '题目标题不能为空!',
			'question_content.required' => '题目内容不能为空!',
			'question_order.required' => '排序不能为空!',
			'question_order.numeric' => '排序必须是数字!',
			'question_answer.required' => '答案不能为空!',
			'question_answer.numeric' => '答案必须是数字!',
		];
		$validator = Validator::make($input, $rules, $message);

		if ($validator->passes()) {
			$res = Question::create($input);

			if ($res) {
				return redirect('admin/question');
			} else {
				return redirect()->back()->with('errors', '数据添加失败!');
			}
		} else {
			return redirect()->back()->withErrors($validator);
		}


	}

	public function delete($question_id)
	{
		$res = Question::where('question_id', $question_id)->delete();
		if ($res) {
			$data = [
				'status' => 0,
				'msg' => '删除题目成功!',
			];
		} else {
			$data = [
				'status' => 1,
				'msg' => '删除题目失败!',
			];
		}

		return $data;
	}
}


