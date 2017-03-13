@extends('layouts.admin')

@section('content')

    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{ url('admin/info') }}">首页</a> &raquo; 题目列表
    </div>
    <!--面包屑导航 结束-->

    <!--结果页快捷搜索框 开始-->
    <div class="search_wrap">
        <form action="" method="post">
            <table class="search_tab">
                <tr>
                    <th width="120">选择分类:</th>
                    <td>
                        <select onchange="javascript:location.href=this.value;">
                            <option {{ $category == 2 ?  'selected = "selected"' : '' }} value="{{ url
                            ('admin/question') }}">全部</option>
                            <option {{ $category == 1 ?  'selected = "selected"' : '' }} value="{{ url
                            ('admin/question') }}?category=1">已入库题目</option>
                            <option {{ $category == 0 ?  'selected = "selected"' : '' }} value="{{ url
                            ('admin/question') }}?category=0">未入库题目</option>
                        </select>
                    </td>
                    {{--<th width="70">关键字:</th>
                    <td><input type="text" name="keywords" placeholder="关键字"></td>
                    <td><input type="submit" name="sub" value="查询"></td>--}}
                </tr>
            </table>
        </form>
    </div>
    <!--结果页快捷搜索框 结束-->

    <!--搜索结果页面 列表 开始-->
    <form action="#" method="post">
        <div class="result_wrap">
            <!--快捷导航 开始-->
            <div class="result_content">
                <div class="short_wrap">
                    <a id="exportReport" href="{{ url('admin/question/exportAnalysis') }}"><i class="fa fa-bar-chart"></i>导出报表</a>
                    <a style="display: none;" id="waitBtn" href="javascript:;"><i class="fa fa-bar-chart"></i>正在导出...</a>
                    <a href="{{ url('admin/question/create') }}"><i class="fa fa-plus"></i>新增题目</a>
                    <a id="out_put" href="#"><i class="fa fa-recycle"></i>批量出库</a>
                    <a id="laid_in" href="#"><i class="fa fa-refresh"></i>批量入库</a>
                    <a href="javascript:void(0);">
                        题库数量: <span style="color: #f254b6;">{{ $sumQuestion }}</span>
                    </a>
                    <a href="javascript:void(0);">
                        题库总分: <span style="color: #f254b6;">{{ $sumScore }}</span>
                    </a>
                </div>
            </div>
            <!--快捷导航 结束-->
        </div>

        <div class="result_wrap">
            <div class="result_content">
                <input type="hidden" name="getCategory" id="getCategory" value="{{ $category }}">
                <table class="list_tab">
                    <tr>
                        <th class="tc" width="5%"><input type="checkbox" id="checkAll" name=""></th>
                        <th class="tc">排序</th>
                        <th class="tc">ID</th>
                        <th>标题</th>
                        <th>分值</th>
                        <th>是否入库</th>
                        <th>正确答案</th>
                        <th>更新时间</th>
                        <th>操作</th>
                    </tr>
                    <script>
                        $(function () {

                          //导出报表
                          $('#exportReport').click(function (e) {
                            $(this).hide();
                            $("#waitBtn").show();
                            setTimeout(openExport, 30000);
                          });
                          function openExport() {
                            $('#exportReport').show();
                            $("#waitBtn").hide();
                          }




                            $("input[type='checkbox']").unbind('click');

                            //全选
                            $("#checkAll").click(function (e) {
                                if ($(this).prop("checked")) {
                                    $("input[name='id_check']").prop("checked", true);
                                } else {
                                    $("input[name='id_check']").prop("checked", false);

                                }
//                                $("input[name='id_check']").prop("checked", !$(this).prop("checked"))
                            });

                            //批量入库
                            $("#laid_in").click(function (e) {
                                e.preventDefault();
                                var data = [];
                                $("input[name='id_check']:checked:checked").each(function (index, element) {
                                    data.push($(this).val());

                                });
                                console.log('data:', data);
                                $.post(
                                        'question/ajax?act=laid_in',
                                        {
                                            data:data,
                                            '_token':"{{csrf_token() }}"
                                        },
                                        function (res) {
                                            if (res) {
                                                window.location.href = 'question';
                                            } else {
                                                alert("批量更新出错")
                                            }
                                        },
                                        'json'
                                );
                            });
                            //批量出库
                            $("#out_put").click(function (e) {
                                e.preventDefault();
                                var data = [];
                                $("input[name='id_check']:checked:checked").each(function (index, element) {
                                    data.push($(this).val());

                                });
                                console.log('data:', data);
                                $.post(
                                        'question/ajax?act=out_put',
                                        {
                                            data:data,
                                            '_token':"{{csrf_token() }}"
                                        },
                                        function (res) {
                                            if (res) {
                                                window.location.href = 'question';
                                            } else {
                                                alert("批量更新出错")
                                            }
                                        },
                                        'json'
                                );
                            });
                        });
                    </script>
                    @foreach($datas as $data)
                    <tr>
                        <td class="tc">
                            <input type="checkbox" name="id_check" value="{{ $data->question_id }}">
                        </td>
                        <td class="tc">
                            <input type="text" name="ord[]" value="{{ $data->question_order }}">
                        </td>
                        <td class="tc">{{ $data->question_id }}</td>
                        <td>
                            <a href="#">{{ $data->question_title }}</a>
                        </td>
                        <td>
                            {{ $data->question_score }}
                        </td>
                        <td><span style="color:{{ $data->question_is_quest_bank ? 'green' : 'red' }};">{{
                        $data->question_is_quest_bank ? '是' : '否' }}</span></td>
                        <td>{{ $data->question_answer }}</td>
                        <td>{{ date('Y-m-d H:i:s', $data->question_time) }}</td>
                        <td>
                            <a href="{{ url('admin/question/'. $data->question_id .'/edit') }}">修改</a>
                            <a href="javascript:void(0);" onclick="delQuest({{$data->question_id}})">删除</a>
                        </td>
                    </tr>

                  @endforeach
                </table>





                <div class="page_list">
                    {{ $datas->links() }}
                </div>
                <script>
                    var getCategory = $("#getCategory").val();
                    $(".pagination li a").each(function (index, element) {
                      $(element).attr("href", $(element).attr("href") + "&category=" + getCategory);
                    })
                </script>
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
                $.post("{{url('admin/question/')}}/"+question_id+"/delete",{'_method':'delete','_token':"{{csrf_token()
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