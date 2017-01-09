@extends('layouts.admin')

@section('content')

    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{ url('admin/info') }}">首页</a> &raquo; 历史试卷
    </div>
    <!--面包屑导航 结束-->


    <!--搜索结果页面 列表 开始-->
    <form action="#" method="post">
        <div class="result_wrap">
            <!--快捷导航 开始-->
            <div class="result_content">
                <div class="short_wrap">
                    <a href="javascript:void(0);">
                        试卷数量: <span style="color: #f254b6;">{{ $sumPapers }}</span>
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
                        <th>得分</th>
                        <th>用时</th>
                        <th>交卷时间</th>
                        <th>操作</th>
                    </tr>
                    @foreach($papers as $paper)
                        <tr>

                            <td>{{ $paper->paper_id }}</td>
                            <td>
                                <a href="#">
                                    <span style="color:red">
                                        {{ $paper->total_score }}
                                    </span>
                                </a>
                            </td>
                            <td>
                                {{ gmstrftime('%H:%M:%S',intval($paper->updated_at) - intval($paper->created_at)) }}
                            </td>

                            <td>{{ date('Y-m-d H:i:s', $paper->updated_at) }}</td>
                            <td>
                                <a href="{{ url('admin/paper') . '/' . $paper->paper_id }}">题目列表</a>
                                <a href="{{ url('admin/paper') . '/' . $paper->paper_id .'/export'}}" target="_blank">导出EXCEL</a>
                                <a href="javascript:void(0);" onclick="delQuest(0)">删除</a>
                            </td>
                        </tr>

                    @endforeach
                </table>


                <div class="page_list">
                    {{ $papers->links() }}
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
            layer.confirm('您确定要删除此题吗？', {
                btn: ['确定','取消'] //按钮
            }, function(){

                alert('暂时不允许删除!');return;
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