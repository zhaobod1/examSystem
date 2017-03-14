<?php

namespace App\Http\Controllers\Admin;

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
        if($file -> isValid()){
            $entension = $file -> getClientOriginalExtension(); //上传文件的后缀.
            if ($entension == 'png' || $entension == 'gif' || $entension == 'jpg' || $entension == 'jpeg') {
                $newName = date('YmdHis').mt_rand(100,999).'.'.$entension;
                $path = $file -> move(base_path().'/uploads',$newName);
                $filepath = 'uploads/'.$newName;
                return $filepath;
            } else {
                return '图片格式只允许: png/jpg/jpeg/gif!';
            }

        }


    }


}
