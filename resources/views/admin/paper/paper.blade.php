@extends('layouts.admin')

@section('content')

    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{ url('admin/info') }}">首页</a> &raquo; 试卷题目列表
    </div>
    <!--面包屑导航 结束-->


    <!--搜索结果页面 列表 开始-->
    <form action="#" method="post">
        <div class="result_wrap">
            <!--快捷导航 开始-->
            <div class="result_content">
                <div class="short_wrap">
                    <a href="javascript:void(0);">
                        试题数量: <span style="color: #f254b6;">{{ $sumQuestions }}</span>
                    </a>
                    <a href="javascript:void(0);">
                        总分: <span style="color: #f254b6;">{{ $total_score }}</span>
                    </a>

                </div>
            </div>
            <!--快捷导航 结束-->
        </div>

        <div class="result_wrap">
            <div class="result_content">
                <table class="list_tab">
                    <tr>
                        <th>编号</th>
                        <th>标题</th>
                        <th>答题过程</th>
                        <th>正确答案</th>
                        <th>用户答案</th>
                        <th>得分</th>
                    </tr>
                    @foreach($questions as $question)
                        <tr>

                            <td>{{ $question->question_order }}</td>
                            <td>
                                <a href="{{ url('admin/question') . '/' . $question->question_id . '/edit'}}">
                                    <span style="">
                                        {{ $question->question_title }}
                                    </span>
                                </a>
                            </td>
                            <td>
                                <span style="color:green">
                                    {{ $question->quest_process }}
                                </span>
                            </td>
                            <td>
                                <span style="color:green">
                                    {{ $question->question_answer }}
                                </span>
                            </td>
                            <td>
                                <span style="color:#177bb1">
                                    {{ $question->quest_answer }}
                                </span>
                            </td>
                            <td>
                                <span style="color:{{ $question->quest_answer==$question->question_answer ? 'green' : 'red' }}">
                                    {{ $question->quest_answer==$question->question_answer ? $question->question_score : 0 }}
                                </span>
                            </td>


                        </tr>

                    @endforeach
                </table>


                <div class="page_list">
                    {{ $questions->links() }}
                </div>
                <style>
                    .result_content ul li span {
                        font-size: 15px;
                        padding: 6px 12px;
                    }
                </style>
            </div>
        </div>
    </form>
    <!--搜索结果页面 列表 结束-->

    <script>
        function delQuest(question_id) {
            layer.confirm('您确定要删除此试卷吗？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.post("{{url('admin/papers/')}}/"+question_id+"/delete",{'_method':'delete','_token':"{{csrf_token()
                }}"},
                        function
                                (data) {
                            if(data.status==0){
                                location.href = location.href;
                                layer.msg(data.msg, {icon: 6});
                            }else{
                                layer.msg(data.msg, {icon: 5});
                            }
                        });
//            layer.msg('的确很重要', {icon: 1});
            }, function(){

            });
        }
    </script>

@endsection