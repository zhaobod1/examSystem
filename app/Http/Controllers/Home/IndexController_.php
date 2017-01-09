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
		if (!$this->userCheck()) {
			$userCheck = '管理员审核通过才可以答题! 右上角菜单按钮联系管理员。';
		} else {
			$userCheck = '您已经通过审核! 右上角菜单按钮开始答题。';

		}
		return view('home.index', compact('userCheck'));
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


			$res = User::where('user_name', session('user')->user_name)
				->update($input);

			if ($res) {
				return redirect('index')->with('errors', '个人信息更新成功!');
			} else {
				return redirect()->back()->with('errors', '个人信息更新失败!');
			}

		}


		$user = User::where('user_name', session('user')->user_name)->first();
		session(['user' => $user]);
		$pageTitle = '用户中心';
		return view('home.userCenter', compact('user', 'pageTitle'));
	}

	public function startExam($quest_id = null)
	{

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
						'quest_answer' => $input['quest_answer']
					]);
				if ($res) {
					return redirect('startexam/' . $question_id)->with('errors', '提交答案成功! 请继续答题');
				}
			} else {
				return redirect()->back()->withErrors($validator);
			}


		}
		/* 处理提交的答案 end */



		/* 获取并记录考试开始时间$time */
		$user = session('user');
		if ($user->start_exam == 0) {

			//记录开始考试时间
			$time = time();
			$user->start_exam = $time;
			DB::beginTransaction();
			$res = User::where('user_name', $user->user_name)->update([
				'start_exam' => $time
			]);
			if ($res) {
				//更新session
				session('paper_id', null);
				session(['user' => $user]);
				$paper_info = new PaperInfo();
				$paper_info->user_id = $user->user_id;
				$paper_info->created_at = $user->start_exam;
				if ($paper_info->save()) {
					//记录paper_id
					session('paper_id', $paper_info->paper_id);
					//考试开始
					//把题库存入历史试卷
					$questions = DB::table('question')->where('question_is_quest_bank', 1)->get();
					foreach ($questions as $key => $question) {
						$insertArr[$key]['question_answer'] = $question->question_answer;
						$insertArr[$key]['question_id'] = $question->question_id;
						$insertArr[$key]['question_title'] = $question->question_title;
						$insertArr[$key]['question_score'] = $question->question_score;
						$insertArr[$key]['question_order'] = $question->question_order;
						$insertArr[$key]['paper_id'] = session('paper_id');
					}

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


			} else {
				DB::rollback();
				return redirect('index')->with('error', '数据库更新出错,请重新考试!');
			}
		} else {
			//重新记录考试开始时间,从session('user')中取得
			if (session('paper_id')) {
				$time = $user->start_exam;
			} else {
				$user->start_exam = 0;
				session('user', $user);
				return redirect('index')->with('error', '数据库更新出错,请重新考试!');

			}


		}
		/* 获取并记录考试开始时间$time end */


		/*如果存在,把当前题目的答案取出*/
		if (isset($quest_id)) {
			$quest_id = intval($quest_id);
			//把这道题显示出来
			$oneQuestion = Question::find($quest_id);
			//这道题的答案
			$quest_answer = PaperQuestion::whereRaw( 'question_id=? and paper_id=?', [$quest_id, $this->getSessionPaperId()])->first();

			$quest_answer = $quest_answer->quest_answer;
		} else {
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

		}

		/*如果存在,把当前题目的答案取出 end*/

		//题目总数
		$totalQuestions = Question::where('question_is_quest_bank', 1)->count();
		//剩余题目
		$leftQuestions = PaperQuestion::whereRaw('quest_answer=? and paper_id=?', ['', session('paper_id')])
			->count();


		//前一道题ID
		$preOrder = PaperQuestion::whereRaw('question_order<?', [$oneQuestion->question_order])
			->max('question_order');
		if ($preOrder != null) {
			$preQuest = Question::where('question_order', $preOrder)->first();
			$preId = $preQuest->question_id;
		} else {
			$preId = null;
		}


		//下一道题ID
		$nextOrder = PaperQuestion::whereRaw('question_order>?', [$oneQuestion->question_order])
			->min('question_order');

		if ($nextOrder != null) {
			$nextQuest = Question::where('question_order', $nextOrder)->first();
			$nextId = $nextQuest->question_id;
		} else {
			$nextId = null;
		}


		$pageTitle = '考试中...';
		return view('home.startExam', compact(
				'pageTitle',
				'time',
				'oneQuestion',
				'preId',
				'nextId',
				'quest_answer',
				'totalQuestions',
				'leftQuestions'
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
			DB::beginTransaction();
			$res = DB::table('user')->where('user_name', $user->user_name)->update([
				'start_exam' => 0
			]);

			if ($res) {

				//更新session
				session(['user' => $user]);

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


					session('paper_id', null);
					//交卷成功
					return view('home.handIn', compact(
						'sumTime',
						'sumScore',
						'paper'
					));
				}




			} else {

				return redirect('index');
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
