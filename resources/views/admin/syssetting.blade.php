@extends('layouts.admin')
@section('content')
    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{url('admin/info')}}">首页</a> &raquo; 系统设置
    </div>
    <!--面包屑导航 结束-->

    <!--结果集标题与导航组件 开始-->
    <div class="result_wrap">
        <div class="result_title">
            <h3>快捷菜单</h3>
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




    <!-- 青岛火一五信息科技有限公司huo15.com 日期：2017/1/14 解决admin后台switch样式的错位 -->
    <style>
        label {
            margin-right: 0px;
            vertical-align: middle;
            font-family: tahoma;
        }
        table.add_tab tr td span {
            margin-left: 0px;
            color: #666;
        }
    </style>
    <script>
        $(function () {
            $('.switch').on('switch-change', function (e, data) {
                var $el = $(data.el)
                        , value = data.value;
                console.log(e, $el, value);
                $("input[name='isCloseSystem']").val(value);
            });
        })
    </script>
    <!-- 青岛火一五信息科技有限公司huo15.com 日期：2017/1/14 end -->
    <div class="result_wrap">
        <form action="{{url('admin/syssetting')}}" method="post">
            {{csrf_field()}}
            <table class="add_tab">
                <tbody>
                <tr>
                    <th><i class="require">*</i> 是否开启答题：</th>
                    <td>
                        <div class="switch has-switch" data-on="primary" data-off="info">
                            <input name="isCloseSystem" type="checkbox" {{ $aSet['isCloseSystem'] ? "checked":'' }} value="{{ $aSet['isCloseSystem'] ? "true":'false' }}"/>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i>考试时间设置：</th>
                    <td>
                        <input type="text" class="lg" name="examTime" placeholder="60" value="{{ isset($aSet['examTime']) ? $aSet['examTime']:'' }}">分钟
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
