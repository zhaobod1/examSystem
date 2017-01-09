@extends('layouts.admin')
@section('content')
    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{url('admin/info')}}">首页</a> &raquo; 题目管理
    </div>
    <!--面包屑导航 结束-->

    <!--结果集标题与导航组件 开始-->
    <div class="result_wrap">
        <div class="result_title">
            <h3>修改题目</h3>
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
                <a href="{{url('admin/question/create')}}"><i class="fa fa-plus"></i>添加题目</a>
                <a href="{{url('admin/question')}}"><i class="fa fa-recycle"></i>全部题目</a>
            </div>
        </div>
    </div>
    <!--结果集标题与导航组件 结束-->

    <div class="result_wrap">
        <form action="{{url('admin/question/' . $field->question_id . '/update')}}" method="post">
            {{csrf_field()}}
            <table class="add_tab">
                <tbody>
                <tr>
                    <th><i class="require">*</i> 标题：</th>
                    <td>
                        <input type="text" class="lg" name="question_title" value="{{ $field->question_title }}">
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i>排序：</th>
                    <td>
                        <input type="text" class="lg" name="question_order" placeholder="数字0-1000, 数字越大排名越靠前。"
                               value="{{ $field->question_order }}">
                    </td>
                </tr>


                <tr>
                    <th><i class="require">*</i>题目内容：</th>
                    <td>
                        <script type="text/javascript" charset="utf-8" src="{{asset('resources/org/ueditor/ueditor.config.js')}}"></script>
                        <script type="text/javascript" charset="utf-8" src="{{asset('resources/org/ueditor/ueditor.all.min.js')}}"> </script>
                        <script type="text/javascript" charset="utf-8" src="{{asset('resources/org/ueditor/lang/zh-cn/zh-cn.js')}}"></script>
                        <script id="editor" name="question_content" type="text/plain" style="width:860px;height:500px;">{!!
                        $field->question_content !!}</script>
                        <script type="text/javascript">
                            var ue = UE.getEditor('editor');
                        </script>
                        <style>
                            .edui-default{line-height: 28px;}
                            div.edui-combox-body,div.edui-button-body,div.edui-splitbutton-body
                            {overflow: hidden; height:20px;}
                            div.edui-box{overflow: hidden; height:22px;}
                        </style>
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i> 正确答案：</th>
                    <td>
                        <input type="text" class="lg" name="question_answer" placeholder="只允许数字" value="{{
                        $field->question_answer }}">
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i> 分值：</th>
                    <td>
                        <input type="text" class="lg" name="question_score" placeholder="只允许数字" value="{{
                        $field->question_score }}">
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i> 是否加入题库：</th>
                    <td>
                        <input type="text" class="lg" name="question_is_quest_bank" placeholder="填写'是', 代表入库,
                        填写'否'或者留空, 代表不入库。" value="{{ $field->question_is_quest_bank }}">
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
