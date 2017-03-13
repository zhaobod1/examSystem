@extends('layouts.home')

@section('nav')
    <!--导航条-->
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

                    <li><a id="handIn" href="{{ url('index') }}">首页</a></li>
                    <li><a href="Javascript:history.back();">返回</a></li>

                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    <!--导航条 end-->
@stop

@section('content')
    <div class="container bg-info">
        <div class="row">
            <div class="col-xs-12 p0">
                <ol class="breadcrumb">
                    <li>编号: {{ $question->question_order }}</li>
                    <li>分值: {{ $question->question_score }} 分</li>
                </ol>
            </div>
        </div>
        <!--题目内容区域-->

        <div class="row">
            <div class="col-xs-12 amplifyImg" id="hasImgDiv">
                {!! $question->question_content !!}
            </div>
        </div>
        <!--题目内容区域 end-->
    </div>

@stop

@section('footer')
@stop

@section('javascript')
    @include('home.common.masklayer')
    <style>
        .hand-in-wrapper {
            display: none;
        }
        .amplifyImg {
            margin-bottom: 250px;
        }
    </style>

@stop



@section('style')
    <!--答题页面css-->
    <link rel="stylesheet" href="{{ asset('public/css/dt.css') }}">
@stop
