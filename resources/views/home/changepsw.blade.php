@extends('layouts.home')

@section('content')
    <!--内容区域-->
    <style>
        .registerArea {
            margin-bottom: 50px;
        }

        .register {
            float: right;
        }
    </style>
    <div class="container">
        <div class="row" style="padding-top: 10px;">
            <div class="col-xs-4">
                <a href="/" class="btn btn-primary">返回首页</a>
            </div>
        </div>
        <div class="row" style="padding-top:10px;">

            <div class="col-xs-12">
                <div class="panel panel-default" style="border-color: #333">
                    <div class="panel-heading">
                        <h3 class="panel-title">找回密码</h3>
                    </div>
                    <div class="panel-body">
                        @if(session('msg'))
                            <p style="color:red">{{ session('msg') }}</p>
                        @endif
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
                        <form action="{{ url('changepswdone') }}">
                            <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <label class="">1、手机号</label>
                                <input type="text" id="user_phone" name="user_phone" class="form-control" placeholder="手机号">
                            </div>

                            <div class="form-group">
                                <label class="">2、验证手机</label>
                                <div class="input-group">
                                    <input type="text" name="user_captcha" class="form-control" placeholder="验证码">
                                    <div class="input-group-btn">
                                        <button id="sendSmsBtn" class="btn primary">获取验证码</button>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                <label class="">3、新的密码</label>
                                <input type="password" name="user_password" class="form-control" placeholder="密码">
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-block btn-primary" style="background: #333;border: none;"  value="提交">
                            </div>

                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--内容区域 end -->
@stop

@section('nav')

    <nav class="navbar navbar-inverse" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">微信答题</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    @include('home.common.support')
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
@stop

@section('style')
    <style>
        .login_wx {
            list-style: none;
        }
    </style>
@stop

@section('javascript')
    <script>
        $(function () {
            var t = 60; //倒计时秒数
            function countDown() {
                t--;
                var txt = "倒计时 " + t + " 秒";
                $("#sendSmsBtn").text(txt);
                if (t<=0) {
                    clearInterval(tt);
                    var txt = "获取验证码";
                    $("#sendSmsBtn").text(txt);
                    $("#sendSmsBtn").removeAttr('disabled');

                }
            }
            //发送短信
            $("#sendSmsBtn").click(function (e) {
                e.preventDefault();
                var txt = "倒计时 " + t + " 秒";
                $(this).text(txt);
                $(this).attr('disabled', 'disabled');
                tt = setInterval(countDown, 1000);
                $.post(
                        '/findpsw',
                        {
                            "mobile":$("#user_phone").val(),
                            "_token":$("#_token").val()
                        },
                        function (res) {
                            if (res.error_code == 0) {
                                if (res.reason == "4006") {
                                    alert("手机号码不存在。");
                                } else {
                                    alert("手机号码输入错误，或者发送短信失败！错误代号：" + res.reason +". 请联系管理员解决");

                                }
                            } else {
                                alert("验证码发送成功，请注意查收！");
                            }

                        },
                        "json"

                );
            });
        });
        

    </script>
@stop


