<?php

namespace App\Http\Controllers\Home;

use App\Http\Model\PaperInfo;
use App\Http\Model\PaperQuestion;
use App\Http\Model\Question;
use App\Http\Model\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class IndexController extends CommonController
{


	//
	public function index()
	{
		$isChecked = false;
		if (!$this->userCheck()) {
			$userCheck = '管理员审核通过才可以答题! 右上角菜单按钮联系管理员。';
			$isChecked = false;
		} else {
			$userCheck = '您好！ ';
			$isChecked = true;

		}
		return view('home.index', compact('userCheck', 'isChecked'));
	}

	public function userCenter()
	{

		//dd(session('user'));
		if (Input::all()) {
			$input = Input::except('_token', '_method');
			if ($input['user_password'] == '') {

				unset($input['user_password']);
			} else {
				$input['user_password'] = Crypt::encrypt($input['user_password']);
			}
			$session_user = session('user');
			$currPhone = $session_user->user_phone;
			$oneUser = User::where('user_name', $input['user_phone'])->first();
			if ($oneUser && $input['user_phone']!=$currPhone) {
				return redirect('index')->with('errors', '手机号码已经存在!');
			}
			$oneUser = User::where('user_phone', $input['user_phone'])->first();
			if ($oneUser && $input['user_phone']!=$currPhone) {
				return redirect('index')->with('errors', '手机号码已经存在!');
			}
			$res = User::where('user_id', session('user')->user_id)
				->update($input);
			if ($res) {
				return redirect('index')->with('errors', '个人信息更新成功!');
			} else {
				return redirect()->back()->with('errors', '个人信息更新失败! 可能原因：您的信息未做任何修改');
			}

		}

		if (!session('user')) {
			return redirect('index');
		}

		$user = User::where('user_name', session('user')->user_name)->first();
		session(['user' => $user]);
		$pageTitle = '用户中心';
		return view('home.userCenter', compact('user', 'pageTitle'));
	}

	//全新答题
	public function exam($user)
	{
		//记录开始考试时间
		$time = time();
		$user->start_exam = $time;
		session(['user' => $user]);
		DB::beginTransaction();
		$paper_info = new PaperInfo();
		$paper_info->user_id = $user->user_id;
		$paper_info->created_at = $user->start_exam;
		/* 题目存入数据库 */
		if ($paper_info->save()) {
			//把题库存入历史试卷
			$questions = DB::table('question')->where('question_is_quest_bank', 1)->get();
			foreach ($questions as $key => $question) {
				$insertArr[$key]['question_answer'] = $question->question_answer;
				$insertArr[$key]['question_id'] = $question->question_id;
				$insertArr[$key]['question_title'] = $question->question_title;
				$insertArr[$key]['question_score'] = $question->question_score;
				$insertArr[$key]['question_order'] = $question->question_order;
				$insertArr[$key]['paper_id'] = $paper_info->paper_id;
			}
			//记录paper_id
			session(['paper_id' => $paper_info->paper_id]);
			$res3 = DB::table('paper_questions')->insert($insertArr);
			if ($res3) {
				DB::commit();
				//回到主作用域返回第一道没有做的题目
			} else {
				DB::rollback();
				return redirect('index')->with('error', '数据库更新出错,请重新考试!');
			}
		} else {
			DB::rollback();
			return redirect('index')->with('error', '数据库更新出错,请重新考试!');
		}
		/* 题目存入数据库 end */

		//把没有做的第一道考题显示出来
		$goQuestion = PaperQuestion::whereRaw('quest_answer=? and paper_id=?', ['', $this->getSessionPaperId()])
			->orderBy('question_order', 'ASC')
			->first();
		if ($goQuestion) {
			$oneQuestion = Question::find($goQuestion->question_id);
		} else {
			$goQuestion = PaperQuestion::whereRaw('paper_id=?', [$this->getSessionPaperId()])
				->orderBy('question_order', 'ASC')
				->first();
			$oneQuestion = Question::find($goQuestion->question_id);
		}
		$res["oneQuestion"] = $oneQuestion;
		//这道题的答案
		$res["quest_answer"] = $quest_answer = $goQuestion->quest_answer;
		//这道题的答题过程
		$res["quest_process"] = $quest_process = $goQuestion->quest_process;
		//题目总数
		$res["totalQuestions"] = $totalQuestions = Question::where('question_is_quest_bank', 1)->count();
		//剩余题目
		$res["leftQuestions"] = $leftQuestions = PaperQuestion::whereRaw('quest_answer=? and paper_id=?', ['', session('paper_id')])
			->count();


		//前一道题ID
		$preOrder = PaperQuestion::whereRaw('question_order<? and paper_id=?', [$oneQuestion->question_order, session('paper_id')])
			->max('question_order');
		if ($preOrder != null) {
			$preQuest = Question::where('question_order', $preOrder)->first();
			$preId = $preQuest->question_id;
		} else {
			$preId = null;
		}
		$res["preId"] = $preId;

		//下一道题ID
		$nextOrder = PaperQuestion::whereRaw('question_order>? and paper_id=?', [$oneQuestion->question_order, session('paper_id')])
			->min('question_order');

		if ($nextOrder != null) {
			$nextQuest = Question::where('question_order', $nextOrder)->first();
			$nextId = $nextQuest->question_id;
		} else {
			$nextId = null;
		}
		$res["nextId"] = $nextId;


		return $res;
	}

	public function startExam($quest_id = null)
	{

		//检测是否关闭了答题功能
		$isclosed = $this->checkSystemStatus();
		if ($isclosed == 0) {
			return redirect('/')->with('errors',"现在不是答题时间，请在考试时间内进行答题！");
		}
		//考试时间
		$examTime = $this->getExamTime();

		//检测审核
		if (!$this->userCheck()) {
			return redirect('/');
		}

		/* 处理提交的答案 */
		$input = Input::except('_token');
		if ($input) {
			$input['quest_answer'] = preg_replace('#[^(0-9\.)|\s]+#', '', $input['quest_answer']);

			$rules = [
				'quest_answer' => 'required'
			];

			$message = [
				'quest_answer.required' => '答案不能为空!',
				//'quest_answer.numeric' => '答案必须是数字!',
			];
			$validator = Validator::make($input, $rules, $message);

			if ($validator->passes()) {
				$question_id = intval($input['question_id']);
				$res = PaperQuestion::whereRaw('question_id=? and paper_id=?', [$question_id, $this->getSessionPaperId()])
					->update([
						'quest_answer' => $input['quest_answer'],
						'quest_process' => $input['quest_process']
					]);
				if ($res) {
					return redirect('startexam/' . $question_id)->with('errors', '提交答案成功! 请继续答题');
				}
			} else {
				return redirect()->back()->withErrors($validator);
			}


		}
		/* 处理提交的答案 end */

		//获取session的user数据。
		$user = session('user');
		$time = $user->start_exam;
		if ($quest_id) {
			//题目ID存在时（选择固定题目答题）
			$quest_id = intval($quest_id);
			//把这道题显示出来
			$oneQuestion = Question::find($quest_id);
			//这道题的答案
			$quest = PaperQuestion::whereRaw('question_id=? and paper_id=?', [$quest_id, $this->getSessionPaperId()])->first();
			if (!$quest) {
				return redirect('index')->with('error', '题库已经更改,请重新考试!');
			}
			$quest_answer = $quest->quest_answer;
			//答题过程
			$quest_process = $quest->quest_process;
			//题目总数
			$totalQuestions = Question::where('question_is_quest_bank', 1)->count();
			//剩余题目
			$leftQuestions = PaperQuestion::whereRaw('quest_answer=? and paper_id=?', ['', session('paper_id')])
				->count();


			//前一道题ID
			$preOrder = PaperQuestion::whereRaw('question_order<? and paper_id=?', [$oneQuestion->question_order, session('paper_id')])
				->max('question_order');
			if ($preOrder != null) {
				$preQuest = Question::where('question_order', $preOrder)->first();
				$preId = $preQuest->question_id;
			} else {
				$preId = null;
			}


			//下一道题ID
			$nextOrder = PaperQuestion::whereRaw('question_order>? and paper_id=?', [$oneQuestion->question_order, session('paper_id')])
				->min('question_order');

			if ($nextOrder != null) {
				$nextQuest = Question::where('question_order', $nextOrder)->first();
				$nextId = $nextQuest->question_id;
			} else {
				$nextId = null;
			}

		} else {

			//开始答题
			// 答题的第一种情况 还没有答完题目继续答题
			if ($user->start_exam) {
				if ($this->getSessionPaperId()) {
					//存在没有答完的试卷
					//把没有做的第一道考题显示出来
					$goQuestion = PaperQuestion::whereRaw('quest_answer=? and paper_id=?', ['', $this->getSessionPaperId()])
						->orderBy('question_order', 'ASC')
						->first();
					if ($goQuestion) {
						$oneQuestion = Question::find($goQuestion->question_id);
					} else {
						$goQuestion = PaperQuestion::whereRaw('paper_id=?', [$this->getSessionPaperId()])
							->orderBy('question_order', 'ASC')
							->first();
						$oneQuestion = Question::find($goQuestion->question_id);

					}
					//这道题的答案
					$quest_answer = $goQuestion->quest_answer;
					//这道题的答题过程
					$quest_process = $goQuestion->quest_process;
					//题目总数
					$totalQuestions = Question::where('question_is_quest_bank', 1)->count();
					//剩余题目
					$leftQuestions = PaperQuestion::whereRaw('quest_answer=? and paper_id=?', ['', session('paper_id')])
						->count();


					//前一道题ID
					$preOrder = PaperQuestion::whereRaw('question_order<? and paper_id=?', [$oneQuestion->question_order, session('paper_id')])
						->max('question_order');
					if ($preOrder != null) {
						$preQuest = Question::where('question_order', $preOrder)->first();
						$preId = $preQuest->question_id;
					} else {
						$preId = null;
					}


					//下一道题ID
					$nextOrder = PaperQuestion::whereRaw('question_order>? and paper_id=?', [$oneQuestion->question_order, session('paper_id')])
						->min('question_order');
					if ($nextOrder != null) {
						$nextQuest = Question::where('question_order', $nextOrder)->first();

						$nextId = $nextQuest->question_id;
					} else {
						$nextId = null;
					}

				} else {
					dd("o:".$user->start_exam);
					$user->start_exam = 0;
					$res = $this->exam($user);
					$oneQuestion = $res["oneQuestion"];

					//这道题的答案
					$quest_answer = $res["quest_answer"];
					//这道题的答题过程
					$quest_process = $res["quest_process"];
					//题目总数
					$totalQuestions = $res["totalQuestions"];
					//剩余题目
					$leftQuestions = $res["leftQuestions"];
					$preId = $res["preId"];
					$nextId = $res["nextId"];

				}
			} else {// 答题的第二种情况 开始全新答题
				//记录开始考试时间
				$time = time();
				$user->start_exam = $time;
				session(['user' => $user]);
				DB::beginTransaction();
				$paper_info = new PaperInfo();
				$paper_info->user_id = $user->user_id;
				$paper_info->created_at = $user->start_exam;
				/* 题目存入数据库 */
				if ($paper_info->save()) {
					//把题库存入历史试卷
					$questions = DB::table('question')->where('question_is_quest_bank', 1)->get();
					foreach ($questions as $key => $question) {
						$insertArr[$key]['question_answer'] = $question->question_answer;
						$insertArr[$key]['question_id'] = $question->question_id;
						$insertArr[$key]['question_title'] = $question->question_title;
						$insertArr[$key]['question_score'] = $question->question_score;
						$insertArr[$key]['question_order'] = $question->question_order;
						$insertArr[$key]['paper_id'] = $paper_info->paper_id;
					}
					//记录paper_id
					session(['paper_id' => $paper_info->paper_id]);
					$res3 = DB::table('paper_questions')->insert($insertArr);
					if ($res3) {
						DB::commit();
						//回到主作用域返回第一道没有做的题目
					} else {
						DB::rollback();
						return redirect('index')->with('error', '数据库更新出错,请重新考试!');
					}
				} else {
					DB::rollback();
					return redirect('index')->with('error', '数据库更新出错,请重新考试!');
				}
				/* 题目存入数据库 end */

				//把没有做的第一道考题显示出来
				$goQuestion = PaperQuestion::whereRaw('quest_answer=? and paper_id=?', ['', $this->getSessionPaperId()])
					->orderBy('question_order', 'ASC')
					->first();
				if ($goQuestion) {
					$oneQuestion = Question::find($goQuestion->question_id);
				} else {
					$goQuestion = PaperQuestion::whereRaw('paper_id=?', [$this->getSessionPaperId()])
						->orderBy('question_order', 'ASC')
						->first();
					$oneQuestion = Question::find($goQuestion->question_id);
				}
				//这道题的答案
				$quest_answer = $goQuestion->quest_answer;
				//这道题的答题过程
				$quest_process = $goQuestion->quest_process;
				//题目总数
				$totalQuestions = Question::where('question_is_quest_bank', 1)->count();
				//剩余题目
				$leftQuestions = PaperQuestion::whereRaw('quest_answer=? and paper_id=?', ['', session('paper_id')])
					->count();


				//前一道题ID
				$preOrder = PaperQuestion::whereRaw('question_order<? and paper_id=?', [$oneQuestion->question_order, session('paper_id')])
					->max('question_order');
				if ($preOrder != null) {
					$preQuest = Question::where('question_order', $preOrder)->first();
					$preId = $preQuest->question_id;
				} else {
					$preId = null;
				}


				//下一道题ID
				$nextOrder = PaperQuestion::whereRaw('question_order>? and paper_id=?', [$oneQuestion->question_order, session('paper_id')])
					->min('question_order');

				if ($nextOrder != null) {
					$nextQuest = Question::where('question_order', $nextOrder)->first();
					$nextId = $nextQuest->question_id;
				} else {
					$nextId = null;
				}


			}

			//session()->forget('user');

		}
		$pageTitle = '考试中...';
		return view('home.startExam', compact(
				'pageTitle',
				'time',
				'oneQuestion',
				'preId',
				'nextId',
				'quest_answer',
				'quest_process',
				'totalQuestions',
				'leftQuestions',
				'examTime'
			)
		);


	}

	public function questionList()
	{

		$datas = DB::table('paper_questions')->where('paper_id', $this->getSessionPaperId())->orderBy('question_order', 'ASC')
			->paginate(5);

		$totalScore = DB::table('question')->sum('question_score');
		$pageTitle = '试卷预览';

		return view('home.questionList', compact('pageTitle', 'datas', 'totalScore'));
	}

	public function handIn()
	{

		$handInTime = time();
		$user = session('user');
		if ($user->start_exam > 0) {

			$user->start_exam = 0;
			//用户表更新交卷状态

			//更新session
			session(['user' => $user]);
			DB::beginTransaction();
			$res2 = PaperInfo::where('paper_id', session('paper_id'))
				->update([
					'updated_at' => $handInTime
				]);
			if ($res2) {
				DB::commit();
				//考试得分
				$sumScore = PaperQuestion::whereRaw('paper_id=? and quest_answer=question_answer', [$this->getSessionPaperId()])
					->sum('question_score');
				$paper = PaperInfo::find($this->getSessionPaperId());
				$paper->total_score = $sumScore;

				if ($paper->save()) {
					$s = intval(intval($paper->updated_at) - intval($paper->created_at));

					$sumTime = gmstrftime('%H:%M:%S', $s);;
				}


				session()->forget('paper_id');
				session('paper_id', null);
				//交卷成功
				return view('home.handIn', compact(
					'sumTime',
					'sumScore',
					'paper'
				));
			}

		} else {
			$pageTitle = '考试结束';
			return view('home.handIn', [
				'handinFail' => true,
				'pageTitle' => $pageTitle,
			]);

		}

	}

	public function recentPapers()
	{


		$user = session('user');
		$papers = PaperInfo::where('user_id', $user->user_id)
			->paginate(5);
		$pageTitle = '历史试卷';
		return view('home.recentPapers', compact(
			'papers',
			'pageTitle'
		));
	}

	public function paper($paper_id)
	{
		if ($paper_id) {
			$paper_id = intval($paper_id);
			$questions = PaperQuestion::where('paper_id', $paper_id)
				->paginate(5);
			$paperInfo = PaperInfo::find($paper_id);
			$pageTitle = '查看试卷: ' . $paperInfo->paper_id;
			return view('home.paper', compact('pageTitle', 'questions', 'paperInfo'));
		} else {
			//意外
			redirect('index');
		}
	}

}
