@extends('layouts.admin')
@section('content')
    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{url('admin/info')}}">首页</a> &raquo; 修改会员
    </div>
    <!--面包屑导航 结束-->

    <!--结果集标题与导航组件 开始-->
    <div class="result_wrap">
        <div class="result_title">
            <h3>修改会员</h3>
            @if(count($errors)>0)
                <div class="mark">
                    @if(is_object($errors))
                        @foreach($errors->all() as $error)
                            <p>{{$error}}</p>
                        @endforeach
                    @else
                        <p>{{$errors}}</p>
                    @endif
                </div>
            @endif
        </div>
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{url('admin/users/create')}}"><i class="fa fa-plus"></i>添加会员</a>
                <a href="{{url('admin/users')}}"><i class="fa fa-recycle"></i>会员列表</a>
            </div>
        </div>
    </div>
    <!--结果集标题与导航组件 结束-->

    <div class="result_wrap">
        <form action="{{url('admin/users') . '/' . $user->user_id}}" method="post">
            {{csrf_field()}}
            <table class="add_tab">
                <tbody>
                <tr>
                    <th><i class="require">*</i> 用户名：</th>
                    <td>
                        <input type="text" class="lg" name="user_name" value="{{ $user->user_name }}" placeholder="">
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i>昵称：</th>
                    <td>
                        <input type="text" class="lg" name="user_neckname" value="{{ $user->user_neckname }}" placeholder="">
                    </td>
                </tr>
                <tr>
                    <th>头像：</th>
                    <td>
                        <input type="text" size="50" name="user_avatar" value="{{ $user->user_avatar }}">
                        <input id="file_upload" name="file_upload" type="file" multiple="true">
                        <script src="{{asset('resources/org/uploadify/jquery.uploadify.min.js')}}" type="text/javascript"></script>
                        <link rel="stylesheet" type="text/css" href="{{asset('resources/org/uploadify/uploadify.css')}}">
                        <script type="text/javascript">
                            <?php $timestamp = time();?>
                            $(function() {
                                $('#file_upload').uploadify({
                                    'buttonText' : '图片上传',
                                    'formData'     : {
                                        'timestamp' : '<?php echo $timestamp;?>',
                                        '_token'     : "{{csrf_token()}}"
                                    },
                                    'swf'      : "{{asset('resources/org/uploadify/uploadify.swf')}}",
                                    'uploader' : "{{url('admin/upload')}}",
                                    'onUploadSuccess' : function(file, data, response) {
                                        $('input[name=user_avatar]').val(data);
                                        $('#art_thumb_img').attr('src','/'+data);
//                                    alert(data);
                                    }
                                });
                            });
                        </script>
                        <style>
                            .uploadify{display:inline-block;}
                            .uploadify-button{border:none; border-radius:5px; margin-top:8px;}
                            table.add_tab tr td span.uploadify-button-text{color: #FFF; margin:0;}
                        </style>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <img src="{{ preg_match('/^(http).*/', $user->user_avatar) ? $user->user_avatar : ($user->user_avatar ? ('/' . $user->user_avatar) : '/uploads/20161023222038528.png') }}"  alt="" id="art_thumb_img" style="max-width: 350px; max-height:100px;">
                    </td>
                </tr>

                <tr>
                    <th><i class="require">*</i> 手机号：</th>
                    <td>
                        <input type="text" class="lg" name="user_phone" value="{{ $user->user_phone }}" placeholder="只允许数字, 11位">
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i> 密码：</th>
                    <td>
                        <input type="password" class="lg" name="user_password" placeholder="留空表示不修改">
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i> 确认密码：</th>
                    <td>
                        <input type="password" class="lg" name="user_password_confirmation" placeholder="跟密码一致">
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i> 审核：</th>
                    <td>
                        <select name="user_check" id="">
                            <option  {{ $user->user_check ? '' : 'selected="selected"' }} value="0">未审核</option>
                            <option {{ $user->user_check ? 'selected="selected"' : ''  }} value="1">通过审核</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th></th>
                    <td>
                        <input type="submit" value="提交">
                        <input type="button" class="back" onclick="history.go(-1)" value="返回">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>

@endsection
