<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <link rel="stylesheet" href="{{ asset('resources/views/admin/style/css/ch-ui.admin.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/views/admin/style/font/css/font-awesome.min.css') }}">
</head>
<body>
	<!--面包屑导航 开始-->
	<div class="crumb_warp">
		<!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
		<i class="fa fa-home"></i> <a href="{{ url('admin/info')}}">首页</a> &raquo; huo15question系统信息
	</div>
	<!--面包屑导航 结束-->

	<!--结果集标题与导航组件 开始-->
	<div class="result_wrap">
        <div class="result_title">
            <h3>快捷操作</h3>
        </div>
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{ url('admin/question/create') }}"><i class="fa fa-plus"></i>新增题目</a>
                <a href="{{ url('admin/users/create') }}"><i class="fa fa-user-plus"></i>新增会员</a>
                <a href="{{ url('admin/users') }}"><i class="fa fa-users"></i>会员列表</a>
                <a id="clearErrorPapers" href="{{ url('admin/sys/clearerrorpapers') }}"><i class="fa fa-fw fa-cubes"></i>清理错误试卷</a>
            </div>
            <script>
                $("#clearErrorPapers").click(function () {
                  $(this).attr("disabled", "disabled");
                  $str='<i class="fa fa-fw fa-cubes"></i>清理中,请耐心等待....';
                  $(this).text($str);
                })
            </script>
        </div>
    </div>
    <!--结果集标题与导航组件 结束-->


    <div class="result_wrap">
        <div class="result_title">
            <h3>系统基本信息</h3>
        </div>
        <div class="result_content">
            <ul>
                <li>
                    <label>操作系统</label><span>{{ PHP_OS }}</span>
                </li>
                <li>
                    <label>运行环境</label><span>{{ $_SERVER['SERVER_SOFTWARE'] }} </span>
                </li>
                <li>
                    <label>PHP运行方式</label><span>apache2handler</span>
                </li>
                <li>
                    <label>Huo15Question-版本</label><span>v-1.0</span>
                </li>
                <li>
                    <label>上传附件限制</label><span>{{ get_cfg_var('upload_max_filesize') ? get_cfg_var
                    ('upload_max_filesize') : '不允许上传附件' }}</span>
                </li>
                <li>
                    <label>北京时间</label><span>{{ date('Y年m月d日 H:i:s', time()) }}</span>
                </li>
                <li>
                    <label>服务器域名/IP</label><span>{{ $_SERVER['SERVER_NAME'] }} </span>
                </li>
                <li>
                    <label>版权</label>
                    <span>
                    <a href="http://www.huo15.com">火一五信息科技有限公司</a>
                    </span>
                </li>
            </ul>
        </div>
    </div>


    <div class="result_wrap">
        <div class="result_title">
            <h3>使用帮助</h3>
        </div>
        <div class="result_content">
            <ul>
                <li>
                    <label>官方交流网站：</label><span><a href="http://bbs.huo15.com">http://bbs.huo15.com</a></span>
                </li>
                <li>
                    <label>官方交流QQ群：</label><span>292522055</span>
                </li>
            </ul>
        </div>
    </div>
	<!--结果集列表组件 结束-->

</body>
</html>