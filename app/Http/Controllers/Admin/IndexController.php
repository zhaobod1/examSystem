<?php

namespace App\Http\Controllers\Admin;

use App\Http\Model\QuestConfig;
use App\Http\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class IndexController extends CommonController
{
    //
    public function index()
    {
        return view('admin.index');
    }

    public function info()
    {
        return view('admin.info');
    }

    public function password()
    {

        if ($input = Input::all()) {

            $rules = [
                'password' => 'required|between:2,60|confirmed'
            ];

            $message = [
                'password.required' => '新密码不能为空!',
                'password.between' => '新密码必须2到60位之间!',
                'password.confirmed' => '新密码与确认密码不一致!',
            ];
            $validator = Validator::make($input, $rules, $message);

            if ($validator->passes()) {

                $user = User::first();
                $_password = Crypt::decrypt($user->user_password);

                if ($_password == $input['password_o']) {

                    $user->user_password = Crypt::encrypt($input['password']);
                    $user->update();
                    return redirect()->back()->with('errors', '密码修改成功!');
                } else {
                    return redirect()->back()->with('errors', '原密码错误!');
                }
            } else {

                return redirect()->back()->withErrors($validator);
            }
        } else {
            return view('admin.password');

        }
    }

	public function sysSetting()
	{
		if (Input::all()) {
			$input = Input::except('_token', '_method');
			if(!isset($input['isCloseSystem'])) {
				$input['isCloseSystem'] = 0;
			} else {
				if ($input['isCloseSystem'] == 'true') {
					$questLib = DB::table('question')->where('question_is_quest_bank', 1)
						->count();
					if (!$questLib) {
						$input['isCloseSystem'] =0;
						QuestConfig::find(1)->update([
							"field_value" => $input['isCloseSystem']
						]);
						QuestConfig::find(2)->update([
							"field_value" => $input['examTime']
						]);
						return back()->with('errors', '题库的入库数量是0，不能开启系统!');
					}
					$input['isCloseSystem'] =1;
				}
			}
			QuestConfig::find(1)->update([
				"field_value" => $input['isCloseSystem']
			]);
			QuestConfig::find(2)->update([
				"field_value" => $input['examTime']
			]);

			return back()->with('errors', '系统设置成功!');
		}
		$aSet['isCloseSystem'] = QuestConfig::where('conf_name', 'isCloseSystem')->value('field_value');
		$aSet['examTime'] = QuestConfig::where('conf_name', 'examTime')->value('field_value');
		return view("admin.syssetting",compact('aSet'));
    }
}
