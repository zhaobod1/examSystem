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
		if ($this->checkNicknameAndPhone()) {
			return redirect("usercenter")->with('errors', "请先填写姓名和手机号码再开始答题");

		}

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
			if ($oneUser && $input['user_phone'] != $currPhone) {
				return redirect('index')->with('errors', '手机号码已经存在!请换一个手机号码');
			}
			$oneUser = User::where('user_phone', $input['user_phone'])->first();
			if ($oneUser && $input['user_phone'] != $currPhone) {
				return redirect('index')->with('errors', '手机号码已经存在!请用微信方式登录');
			}
			$res = User::where('user_id', session('user')->user_id)
				->update($input);

			if ($res) {
				$session_user->user_neckname = $input['user_neckname'];
				$session_user->user_phone = $input['user_phone'];
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



	/**
	 * 开始答题
	 * @param null int $quest_id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function startExam($quest_id = null)
	{
		/* 青岛火一五信息科技有限公司huo15.com 检查是否填写个人信息，如果没有填写，转到个人中心。 日期：2017/3/13 */
		if ($this->checkNicknameAndPhone()) {
			return redirect("usercenter")->with('errors', "请先填写姓名和手机号码再开始答题");

		}

		/* 青岛火一五信息科技有限公司huo15.com 检查是否填写个人信息，如果没有填写，转到个人中心。 日期：2017/3/13 end */


		/* 青岛火一五信息科技有限公司huo15.com 检测是否关闭了答题功能 日期：2017/3/13 */
		$isclosed = $this->checkSystemStatus();
		if ($isclosed == 0) {
			$user = session('user');
			$time = $user->start_exam;
			if ($time) {
				return redirect("handin");
			}
			return redirect('/')->with('errors', "现在不是答题时间，请在考试时间内进行答题！");
		}
		//检测审核
		if (!$this->userCheck()) {
			return redirect('/');
		}
		/* 青岛火一五信息科技有限公司huo15.com 检测是否关闭了答题功能 日期：2017/3/13 end */


		/* 青岛火一五信息科技有限公司huo15.com 初始化 日期：2017/3/13 */
		//获取系统设定的考试时间
		$examTime = $this->getExamTime();
		//获取session的user数据。
		$user = session('user');
		$time = $user->start_exam;//记录考试的开始时间，0代表没有考试。
		/* 青岛火一五信息科技有限公司huo15.com 初始化 日期：2017/3/13 end */


		/* 处理提交的答案 */
		$input = Input::except('_token');
		if ($input) {
			if (!isset($input["quest_answer"])) {
				$question_id = isset($input['question_id']) ? intval($input['question_id']) : "";

				return redirect('startexam/' . $question_id)->with('errors', '提交答案不能为空！');

			}
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
				$res = PaperQuestion::whereRaw('question_id=? and paper_id=?', [$question_id, $user->paper_id])
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

		/* 青岛火一五信息科技有限公司huo15.com 判断是否存在没有答完的试卷，并获取题目列表 日期：2017/3/13 */
		if ($time == 0) {//没有未答完的试卷
			/* 记录考试开始时间，存入数据库  2017/3/13 */
			$time = time();
			$user->start_exam = $time;
			session(['user' => $user]);
			$user->update();
			/* 记录考试开始时间，存入数据库 2017/3/13 end*/

			DB::beginTransaction();
			$paper_info = new PaperInfo();
			$paper_info->user_id = $user->user_id;
			$paper_info->created_at = $user->start_exam;

			/* 创建试卷ID，把试卷题目存入数据库 2017/3/13 */
			if ($paper_info->save()) {
				//把题库存入历史试卷
				$insertArr = array();
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
				$user->paper_id = $paper_info->paper_id;
				$user->update();
				$res = DB::table('paper_questions')->insert($insertArr);
				if ($res) {
					DB::commit();
				} else {
					DB::rollback();
					return redirect('index')->with('error', '数据库更新出错(存入具体题目时),请重新考试!');
				}

			} else {//创建考试试卷出错

				DB::rollback();
				return redirect('index')->with('error', '数据库更新出错（保存paper_id时）,请重新考试!');
			}
			/* 创建试卷ID，把试卷题目存入数据库 2017/3/13 end*/


		}

		/* 青岛火一五信息科技有限公司huo15.com 判断是否存在没有答完的试卷，并获取题目列表 日期：2017/3/13 end */


		/* 获取quest_id，题目ID号 2017/3/13 */
		//初始化 题目信息
		/** @var Question $oneQuestion */
		$oneQuestion = null;
		if ($quest_id) {
			//题目ID存在时（选择固定题目答题）
			$quest_id = intval($quest_id);
			//把这道题显示出来
			//$oneQuestion = PaperQuestion::whereRaw('question_id=? and paper_id=?', [$quest_id, $user->paper_id])->first();
			$oneQuestion = Question::whereRaw('question_id=?', [$quest_id])->first();
			if (!$oneQuestion) {
				$user->start_exam = 0;
				$user->paper_id = 0;
				$user->update();
				session(['user' => $user]);
				return redirect('index')->with('error', '题库已经更改,请重新考试!');
			}

		} else {
			//把没有做的第一道考题显示出来
			$goQuestion = PaperQuestion::whereRaw('quest_answer=? and paper_id=?', ['', $user->paper_id])
				->orderBy('question_order', 'ASC')
				->first();
			if ($goQuestion) {//存在没有做完的题目
				$oneQuestion = Question::find($goQuestion->question_id);
			} else {//题已经做完，只是没有交卷
				$goQuestion = PaperQuestion::whereRaw('paper_id=?', [$user->paper_id])
					->orderBy('question_order', 'ASC')
					->first();
				$oneQuestion = Question::find($goQuestion->question_id);
			}
			$quest_id = $oneQuestion->question_id;
		}

		/* 获取quest_id，题目ID号 2017/3/13 end*/





		//前一道题ID
		$preOrder = PaperQuestion::whereRaw('question_order<? and paper_id=?', [$oneQuestion->question_order, $user->paper_id])
			->max('question_order');
		$preId = null;//前一道题的ID。
		if ($preOrder != null) {
			$thisPaperQuestion = PaperQuestion::whereRaw("question_order=? and paper_id=?",[$preOrder, $user->paper_id])
				->first();

			$preId = $thisPaperQuestion->question_id;
		} else {
			$preId = null;
		}


		//下一道题ID
		$nextId = null;

		$nextOrder = PaperQuestion::whereRaw('question_order>? and paper_id=?', [$oneQuestion->question_order, $user->paper_id])
			->min('question_order');

		if ($nextOrder != null) {
			$thisPaperQuestion = PaperQuestion::whereRaw("question_order=? and paper_id=?",[$nextOrder, $user->paper_id])
				->first();

			$nextId = $thisPaperQuestion->question_id;

		} else {
			$nextId = null;
		}


		/* 需要传递给前台的变量 2017/3/13 */
		//这道题的答案
		$thisPaperQuestion = PaperQuestion::whereRaw('question_id=? and paper_id=?', [$quest_id, $user->paper_id])
			->first();
		$quest_answer = $thisPaperQuestion->quest_answer;
		//答题过程
		$quest_process =$thisPaperQuestion->quest_process;
		//题目总数
		$totalQuestions = Question::where('question_is_quest_bank', 1)->count();
		//剩余题目
		$leftQuestions = PaperQuestion::whereRaw('quest_answer=? and paper_id=?', ['', session('paper_id')])
			->count();
		$pageTitle = '考试中...';
		/* 需要传递给前台的变量 2017/3/13 end*/



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

		$user = session("user");
		$datas = DB::table('paper_questions')->where('paper_id', $user->paper_id)->orderBy('question_order', 'ASC')
			->paginate(5);

		$totalScore = DB::table('paper_questions')->where('paper_id', $user->paper_id)->sum('question_score');
		$pageTitle = '试卷预览';

		return view('home.questionList', compact('pageTitle', 'datas', 'totalScore'));
	}

	public function handIn()
	{

		$handInTime = time();
		$user = session('user');
		if ($user->start_exam > 0) {//正在考试的时候，结束考试。


			DB::beginTransaction();
			$res2 = PaperInfo::where('paper_id', $user->paper_id)
				->update([
					'updated_at' => $handInTime
				]);
			if ($res2) {//更新交卷时间成功
				DB::commit();
				//考试得分
				$sumScore = PaperQuestion::whereRaw('paper_id=? and quest_answer=question_answer', [$user->paper_id])
					->sum('question_score');
				$paper = PaperInfo::find($user->paper_id);
				$paper->total_score = $sumScore;

				if ($paper->save()) {
					$s = intval(intval($paper->updated_at) - intval($paper->created_at));
					$sumTime = gmstrftime('%H:%M:%S', $s);;
				} else {
					DB::rollback();
					return redirect('index')->with('error', '交卷失败，失败原因可能是无法获取paper_id. 请重新考试');
				}

				//交卷成功
				$user->start_exam = 0;
				$user->paper_id = 0;
				$user->update();
				session(['user' => $user]);


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
			->orderBy("updated_at", "DESC")
			->paginate(5);
		$pageTitle = '历史试卷';
		return view('home.recentPapers', compact(
			'papers',
			'pageTitle'
		));
	}

	/**
	 * 查看题目详细信息
	 * @param int  $quest_id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public  function  getQuestion($quest_id) {
		if ($quest_id) {
			$quest_id = intval($quest_id);
			$question = Question::where("question_id", $quest_id)
				->first();

		}
		return view("home.getQuestion", compact("question"));
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
