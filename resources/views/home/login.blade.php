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
        <div class="row" style="padding-top: 50px;">
            <div class="col-xs-12">
                <div class="panel panel-default" style="border-color: #333">
                    <div class="panel-heading">
                        <h3 class="panel-title">登录</h3>
                    </div>
                    <div class="panel-body">
                        @if(session('msg'))
                            <p style="color:red">{{ session('msg') }}</p>
                        @endif
                        <form action="{{ url('login') }}">
                            <div class="form-group">
                                <label class="">手机号</label>
                                <input type="text" name="user_name" class="form-control" placeholder="用户名/手机号">
                            </div>
                            <div class="form-group">
                                <label class="">密码</label>
                                <input type="password" name="user_password" class="form-control" placeholder="">
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-block btn-primary"
                                       style="background: #333;border: none;"
                                       value="登录">
                            </div>
                            <div class="form-group registerArea">
                                <div class="col-xs-5">
                                    <a href="/changepsw">忘记密码？</a>
                                </div>
                                <div class="col-xs-7">
                                    <a class="register" href="/register">注册</a>
                                </div>
                            </div>
                        </form>

                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <h5>合作账号登录</h5>
                                <hr>
                                <ul class="login_wx">
                                    <li>
                                        <a href="{{ url('wechat/getUserDetail') }}">
                                            <img width="50" class="img-responsive img-circle"
                                                 src="{{ asset('public/img/weixin.png')}}" alt="">
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>


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
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
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


