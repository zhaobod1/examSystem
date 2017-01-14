<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>微信答题 - {{ isset($pageTitle) ? $pageTitle : '首页' }}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

    <link rel="stylesheet" href="{{ asset('public/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/main.css') }}">
    <script src="{{ asset('public/js/vendor/modernizr-2.6.2.min.js') }}"></script>
    <!-- Bootstrap -->
    <link href="{{ asset('public/static/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- 可选的Bootstrap主题文件（一般不用引入） -->
    <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap-theme.min.css">





</head>
<body>
<!--[if lt IE 7]>
<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to
    improve your experience.</p>
<![endif]-->

<!-- Add your site or application content here -->

<!--导航条-->
@section('nav')
<nav class="navbar navbar-inverse" role="navigation">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">微信答题</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="active"><a href="/">首页</a></li>
                <li><a href="{{ url('usercenter') }}">个人中心</a></li>
                <li ><a href="{{ url('recentPapers') }}">历史答题</a></li>
                <li><a href="{{ url('startexam') }}">开始答题</a></li>
                @include('home.common.support')
                <li><a href="{{ url('quit') }}">退出</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
@show
<!--导航条 end-->
<ol class="breadcrumb">
    <li><a href="{{ url("usercenter") }}">{{ session('user')->user_neckname ? session('user')->user_neckname : "匿名" }}</a></li>
    <li><a href="{{ url("usercenter") }}">{{ session('user')->user_phone? session('user')->user_phone : "未设置手机号" }}</a></li>
</ol>
@yield('content')

<!--尾部-->
@section('footer')
<div class="jumbotron" style=" margin-bottom:0;margin-top:0px;">
    <div class="container">
        <a href="http://www.huo15.com">
            <span>© 2016 火一五信息科技有限公司</span>
        </a>
    </div>
</div>
@show
<!--尾部 end-->

<link rel="stylesheet" href="{{ asset('public/static/css/common.css') }}">
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>-->
<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="{{ asset('public/static/js/jquery-3.1.0.min.js') }}"></script>

<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="{{ asset('public/static/js/bootstrap.min.js') }}"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.9.0.min.js"><\/script>')</script>
<script src="{{ asset('public/js/plugins.js') }}"></script>
<script src="{{ asset('public/js/main.js') }}"></script>
<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->

@yield('style')
@yield('javascript')
<script>
    function isWeiXin(){
        var ua = window.navigator.userAgent.toLowerCase();
        if(ua.match(/MicroMessenger/i) == 'micromessenger'){
            return true;
        }else{
            return false;
        }
    }

    var openPc = 1;
    if (!openPc) {
        if(!isWeiXin()) {
            $('body').empty();
            alert('请用微信浏览器打开!!');
            $('body').empty();

        }
    }

    $(function () {
        $('#handIn').click(function (ev) {
            ev.preventDefault();
            if (confirm('确认交卷?')) {
                window.location.href=$(this).attr('href');
            }
        })
    })

</script>
</body>
</html>
