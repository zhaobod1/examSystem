@extends('layouts.admin')

@section('content')

    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{ url('admin/info') }}">首页</a> &raquo; 会员列表
    </div>
    <!--面包屑导航 结束-->
    @include("common.msgOrError")
    <!--结果页快捷搜索框 开始-->
    <div class="search_wrap">
        <form action="?" method="post">
            {{ csrf_field() }}
            <table class="search_tab">
                <tr>
                    <th width="120">选择分类:</th>
                    <td>
                        <select name="category">
                            <option {{ $category == 2 ?  'selected = "selected"' : '' }} value="{{ url
                            ('admin/users') }}">全部会员
                            </option>
                            <option {{ $category == 1 ?  'selected = "selected"' : '' }} value="{{ url
                            ('admin/users') }}?category=1">已审核会员
                            </option>
                            <option {{ $category == 0 ?  'selected = "selected"' : '' }} value="{{ url
                            ('admin/users') }}?category=0">未审核会员
                            </option>
                        </select>
                    </td>
                    <th width="70">关键字:</th>
                    <td><input type="text" name="keywords" placeholder="输入学生姓名的关键字"></td>
                    <td><input type="submit" name="sub" value="查询"></td>
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
                    <a href="{{ url('admin/users/create') }}"><i class="fa fa-plus"></i>新增会员</a>
                    <a href="#"><i class="fa fa-recycle"></i>批量删除</a>
                    <a href="#"><i class="fa fa-refresh"></i>更新排序</a>
                    <a href="javascript:void(0);">
                        会员数量: <span style="color: #f254b6;">{{ $sumUser }}</span>
                    </a>
                    <a href="javascript:void(0);">
                        通过审核: <span style="color: #f254b6;">{{ $sumCheckedUser }}</span>
                    </a>
                </div>
            </div>
            <!--快捷导航 结束-->
        </div>

        <div class="result_wrap">
            <div class="result_content">
                <table class="list_tab">
                    <tr>
                        <th class="tc" width="5%"><input type="checkbox" name=""></th>
                        <th class="tc">ID</th>
                        <th>用户名</th>
                        <th>昵称</th>
                        <th>头像</th>
                        <th>是否审核</th>
                        <th>手机</th>
                        <th>更新时间</th>
                        <th>操作</th>
                    </tr>
                    @foreach($datas as $data)
                        <tr>
                            <td class="tc"><input type="checkbox" name="id[]" value="59"></td>

                            <td class="tc">{{ $data->user_id }}</td>
                            <td>
                                <a href="#">{{ $data->user_name }}</a>
                            </td>
                            <td>
                                {{ $data->user_neckname }}
                            </td>
                            <td>
                                <img src="{{ preg_match('/^(http).*/', $data->user_avatar) ? $data->user_avatar : ($data->user_avatar ?'/' . $data->user_avatar : '/uploads/20161023222038528.png')  }}" width="70" alt=""
                                     class="round">
                            </td>
                            <td><span style="color:{{ $data->user_check ? 'green' : 'red' }};">{{
                        $data->user_check ? '是' : '否' }}</span></td>
                            <td>{{ $data->user_phone }}</td>
                            <td>{{ date('Y-m-d H:i:s', $data->created_at) }}</td>
                            <td>
                                <a href="{{ url('admin/papers') . '/' . $data->user_id }}">答题记录</a>
                                <a href="{{ url('admin/users/'. $data->user_id .'/edit') }}">修改</a>
                                <a href="javascript:void(0);" onclick="delQuest({{$data->user_id}})">删除</a>
                            </td>
                        </tr>

                    @endforeach
                </table>


                <div class="page_list">
                    {{ $datas->links() }}
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
      function delQuest(user_id) {
        layer.confirm('您确定要删除此会员吗？', {
          btn: ['确定', '取消'] //按钮
        }, function () {
          $.post("{{url('admin/users/')}}/" + user_id + "/delete", {
              '_method': 'delete', '_token': "{{csrf_token()
                }}"
            },
            function
              (data) {
              if (data.status == 0) {
                location.href = location.href;
                layer.msg(data.msg, {icon: 6});
              } else {
                layer.msg(data.msg, {icon: 5});
              }
            });
//            layer.msg('的确很重要', {icon: 1});
        }, function () {

        });
      }
    </script>

@endsection