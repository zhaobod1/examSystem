<?php

namespace App\Http\Controllers\Admin;

use App\Http\Model\PaperInfo;
use App\Http\Model\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class UserController extends CommonController
{
	//

	/**
	 * 会员列表
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index(Request $request)
	{


		/* 青岛火一五信息科技有限公司huo15.com 计数初始化 日期：2017/3/13 */
		//输入请求。

		$input = Input::except('_token');
		$category = $request->get('category');//审核类别，0 未审核，1已经审核，2是全部

		$datas = array();
		//全部会员
		$sumUser = User::where('user_id', '>', 1)->count();
		//已审核会员
		$sumCheckedUser = User::whereRaw('user_id>? and user_check=?', [1, 1])->count();
		/* 青岛火一五信息科技有限公司huo15.com 计数初始化 日期：2017/3/13 end */


		/* 青岛火一五信息科技有限公司huo15.com 搜索键入关键字 日期：2017/3/13 */
		if (isset($input["keywords"]) && strlen($input["keywords"]) >= 1) {
			$keyWord = htmlspecialchars($input["keywords"]);
			$categoryId = $input["category"];//审核类别，0 未审核，1已经审核 , 2 是全部。
			switch ($categoryId) {
				case "0":
					$datas = User::whereRaw('user_id>? and user_check=? and user_neckname like ?', [1, 0, "%{$keyWord}%"])
						->orderBy('updated_at', 'DESC')
						->paginate(10);
					break;
				case "1":
					$datas = User::whereRaw('user_id>? and user_check=? and user_neckname like ?', [1, 1, "%{$keyWord}%"])->orderBy('updated_at', 'DESC')
						->paginate(10);
					break;
				default:
					$datas = User::whereRaw('user_id>? and user_neckname like ?', [1, "%{$keyWord}%"])
						->orderBy('updated_at', 'DESC')
						->paginate(10);
					break;
			}

		}
		if (isset($input["userTel"]) && strlen($input["userTel"]) >= 1) {
			$userTel = htmlspecialchars($input["userTel"]);

			$categoryId = $input["category"];//审核类别，0 未审核，1已经审核 , 2 是全部。
			switch ($categoryId) {
				case "0":
					$datas = User::whereRaw('user_id>? and user_check=? and user_phone like ?', [1, 0, "%{$userTel}%"])
						->orderBy('updated_at', 'DESC')
						->paginate(10);
					break;
				case "1":
					$datas = User::whereRaw('user_id>? and user_check=? and user_phone like ?', [1, 1, "%{$userTel}%"])->orderBy('updated_at', 'DESC')
						->paginate(10);
					break;
				default:
					$datas = User::whereRaw('user_id>? and user_phone like ?', [1, "%{$userTel}%"])
						->orderBy('updated_at', 'DESC')
						->paginate(10);
					break;
			}

		}
		/* 青岛火一五信息科技有限公司huo15.com 搜索键入关键字 日期：2017/3/13 end */


		/* 青岛火一五信息科技有限公司huo15.com 下拉选择审核或者未审核筛选 日期：2017/3/13 */
		if (!((isset($input["keywords"]) && strlen($input["keywords"]) >= 1)||(isset($input["userTel"]) && strlen($input["userTel"]) >= 1))) {

			switch ($category) {
				case "0":
					$datas = User::whereRaw('user_id>? and user_check=? and user_neckname like ?', [1, 0, "%%"])
						->orderBy('updated_at', 'DESC')
						->paginate(10);
					break;
				case "1":
					$datas = User::whereRaw('user_id>? and user_check=? and user_neckname like ?', [1, 1, "%%"])->orderBy('updated_at', 'DESC')
						->paginate(10);
					break;
				default:
					$datas = User::whereRaw('user_id>? and user_neckname like ?', [1, "%%"])->orderBy('updated_at', 'DESC')
						->paginate(10);
					break;
			}
		}


		/* 青岛火一五信息科技有限公司huo15.com 下拉选择审核或者未审核筛选 日期：2017/3/13 end */


		return view('admin.users.index', compact('datas', 'sumUser', 'sumCheckedUser', 'category'));
	}


	public function create()
	{

		return view('admin.users.add');
	}

	public function store()
	{

		$input = Input::except(['_token']);
		$input['created_at'] = time();
		$input['updated_at'] = time();


		$rules = [
			'user_name' => 'required',
			'user_phone' => 'required|numeric',
			'user_password' => 'required|between:2,60|confirmed',
			'user_check' => 'required|boolean',
		];
		$message = [
			'user_name.required' => '用户名不能为空!',
			'user_phone.required' => '题目内容不能为空!',
			'user_password.required' => '密码不能为空!',
			'user_check.required' => '审核状态不能为空!',
			'user_check.boolean' => '审核状态有误!',
			'user_password.confirmed' => '确认密码与密码不一致!',
			'user_phone.numeric' => '手机号必须是数字!',
		];

		$validator = Validator::make($input, $rules, $message);

		if ($validator->passes()) {
			$count = User::where('user_name', $input['user_name'])->count();

			if ($count) {
				return redirect()->back()->with('errors', '用户名已经存在, 请换一个用户名试试。');

			}

			unset($input['user_password_confirmation']);

			$input['user_password'] = Crypt::encrypt($input['user_password']);
			$res = User::create($input);
			if ($res) {
				return redirect('admin/users')->with('success', '添加用户成功');
			} else {
				return redirect()->back()->with('errors', '添加用户失败,存入数据库过程中失败!稍后再试');
			}
		} else {
			return redirect()->back()->withErrors($validator);
		}


	}

	public function edit($user_id)
	{
		if (intval($user_id) == 1) {
			return redirect('admin/users');
		}

		$user = User::find($user_id);
		return view('admin.users.edit', compact('user'));
	}

	public function delete($user_id)
	{
		$user_id = intval($user_id);
		if ($user_id == 1) {
			$data = [
				'status' => 1,
				'msg' => '管理员不能删除!',
			];
			return $data;
		}

		$paperInfo = PaperInfo::where("user_id", $user_id)->first();
		if ($paperInfo) {
			$data = [
				'status' => 1,
				'msg' => '请先删除会员的考卷记录，再删除会员!',
			];
			return $data;
		}
		$res = User::where('user_id', $user_id)->delete();
		if ($res) {
			$data = [
				'status' => 0,
				'msg' => '删除会员成功!',
			];
		} else {
			$data = [
				'status' => 1,
				'msg' => '删除会员失败!',
			];
		}

		return $data;

	}

	//put.admin/users/{user_id}    更新文章
	public function update($user_id)
	{
		if (intval($user_id) == 1) {
			return redirect('admin/users');
		}
		$input = Input::except('_token', '_method');

		if ($input['user_password'] == '') {

			unset($input['user_password']);

		} else {

			$input['user_password'] = Crypt::encrypt($input['user_password']);
		}
		unset($input['user_password_confirmation']);
		if ($input['user_avatar'] == '') {

			unset($input['user_avatar']);
		}


		$re = User::where('user_id', $user_id)->update($input);
		if ($re) {
			return redirect('admin/users');
		} else {
			return back()->with('errors', '会员更新失败，请稍后重试！');
		}
	}
}
