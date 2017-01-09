@extends('layouts.admin')

@section('content')
    <!--头部 开始-->
    <div class="top_box">
        <div class="top_left">
            <div class="logo">HUO15管理系统</div>
            <ul>
                <li><a href="/" target="_blank">首页</a></li>
                <li><a href="{{ url('admin/info') }}" class="active" target="main" >管理页</a></li>
            </ul>
        </div>
        <div class="top_right">
            <ul>
                <li>管理员：admin</li>
                <li><a href="{{ url('admin/password') }}" target="main">修改密码</a></li>
                <li><a href="{{ url('admin/quit') }}">退出</a></li>
            </ul>
        </div>
    </div>
    <!--头部 结束-->

    <!--左侧导航 开始-->
    <div class="menu_box">
        <ul>
            <li>
                <h3><i class="fa fa-fw fa-clipboard"></i>常用操作</h3>
                <ul class="sub_menu">
                    <li><a href="{{ url('admin/question/create') }}" target="main"><i class="fa fa-fw
                    fa-plus-square"></i>添加题目</a></li>
                    <li><a href="{{ url('admin/question') }}" target="main"><i class="fa fa-fw fa-list-ul"></i>题目列表</a></li>
                    <li><a href="{{ url('admin/users/create') }}" target="main"><i class="fa fa-fw
                    fa-user-plus"></i>添加会员</a></li>
                    <li><a href="{{ url('admin/users') }}" target="main"><i class="fa fa-fw
                    fa-users"></i>会员列表</a></li>
                </ul>
            </li>
            <li>
                <h3><i class="fa fa-fw fa-cog"></i>技术支持</h3>
                <ul class="sub_menu">
                    <li><a href="http://bbs.huo15.com/forum.php?mod=forumdisplay&fid=61&page=1" target="_blank"><i class="fa fa-fw
                    fa-cubes"></i>有问必答</a></li>

                    <li>
                        <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=3186355915&site=qq&menu=yes">
                            <i class="fa fa-server"></i>
                            联系开发者
                        </a>
                    </li>

                </ul>
            </li>

        </ul>
    </div>
    <!--左侧导航 结束-->

    <!--主体部分 开始-->
    <div class="main_box">
        <iframe src="{{ url('admin/info') }}" frameborder="0" width="100%" height="100%" name="main"></iframe>
    </div>
    <!--主体部分 结束-->

    <!--底部 开始-->
    <div class="bottom_box">
        CopyRight © 2016. Powered By <a href="http://www.huo15.com">http://www.huo15.com</a>.
    </div>
    <!--底部 结束-->
@endsection