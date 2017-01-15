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
                    <li><a href="javascript:void(0);" id="timeKeeper"></a></li>
                    <li><a id="handIn" href="{{ url('handin') }}">交卷</a></li>
                    <li><a href="{{ url('questionlist') }}">全部习题</a></li>

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
                    <li>编号: {{ $oneQuestion->question_order }}</li>
                    <li>分值: {{ $oneQuestion->question_score }} 分</li>
                    <li>共 {{ $totalQuestions }} 题-剩余 <span id="surplusQuest">{{ $leftQuestions }}</span> 题</li>
                </ol>
            </div>
        </div>
        <!--题目内容区域-->
        <div class="row">
            <div class="col-xs-12">
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

        </div>
        <div class="row">
            <div class="col-xs-12 amplifyImg" id="hasImgDiv">
                {!! $oneQuestion->question_content !!}
            </div>
        </div>
        <!--题目内容区域 end-->
    </div>
    <div class="container bg-black" id="btnArea">
        <div class="row" style="margin-bottom: 5px;padding-top:10px;">
            <div class="col-xs-12 col-md-12" style="">
                <form class="" id="submitForm" action="">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input type="hidden" value="{{ $examTime }}" id="examTime">
                        <textarea name="quest_process" id="quest_process" cols="30" rows="5" class="form-control" placeholder="请输入答题过程">{{ $quest_process }}</textarea>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="question_id" value="{{ $oneQuestion->question_id }}">
                        <input id="inputTxt"  name="quest_answer"  style="width: 100%;" value="{{ $quest_answer }}"  class="form-control" type="text" placeholder="请输入最终答案，只能输入数字">
                    </div>
                </form>

            </div>

        </div>
        <div class="row">
            <div class="col-xs-2 col-md-2">
                <btn-group class="btn-group hand-in-wrapper">
                    <a href="{{ url('handin') }}" class="btn btn-success">交卷</a>
                </btn-group>
            </div>
            <div class="col-xs-10 col-md-10">
                <div class="btn-group btn-group-lg" style="float: right;">
                    <a  href="{{ $preId ? url('startexam') . '/' . $preId : 'javacript:void(0);' }}" class="btn
                    btn-default">{{ $preId ? '上一题' : '无上题' }}</a>
                    <a id="submitBtn" class="btn btn-primary">提交</a>
                    <a href="{{ $nextId ? url('startexam') . '/' . $nextId : 'javacript:void(0);' }}"  class="btn
                    btn-default">{{ $nextId ? '下一题' : '无下题' }}</a>
                </div>
            </div>
        </div>
        <div class="row hide">
            <div class="col-xs-12">
                <div class="btn-toolbar" role="toolbar">
                    <div class="btn-group btn-group-lg" style="float: right;">
                        <button type="button" class="btn btn-default btn-lg">1</button>
                        <button type="button" class="btn btn-lg btn-default">2</button>
                        <button type="button" class="btn btn-lg btn-default">3</button>
                        <button type="button" class="btn btn-lg btn-default">4</button>
                        <button type="button" class="btn btn-lg btn-default">5</button>
                        <button type="button" class="btn btn-lg btn-default">6&nbsp;&nbsp;</button>
                    </div>
                    <div class="btn-group btn-group-lg" style="float: right;">
                        <button type="button" class="btn btn-lg btn-default">7</button>
                        <button type="button" class="btn btn-lg btn-default">8</button>
                        <button type="button" class="btn btn-lg btn-default">9</button>
                        <button type="button" class="btn btn-lg btn-default">0</button>
                        <button type="button" class="btn btn-lg btn-default">.</button>
                        <button type="button" class="btn btn-lg btn-default">←</button>
                    </div>
                </div>
            </div>
        </div>

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
    <!--答题页面js-->
    <script>

        $(function () {

            /* 青岛火一五信息科技有限公司huo15.com 日期：2017/1/9 */
            var surplusQuest = $("#surplusQuest").text();
            if (surplusQuest == '0') {
                $(".hand-in-wrapper").show();
            }
            /* 青岛火一五信息科技有限公司huo15.com 日期：2017/1/9 end */

            /* 按钮事件 */

            //提交按钮
            $('#submitBtn').click(function (e) {
                if (confirm('确定提交答案吗?')) {
                    $('#submitForm').submit();
                    if (surplusQuest == '0') {
                        $(".hand-in-wrapper").show();
                    }
                }
            });

            //获取所有按钮
            $('.btn-toolbar button').each(function (index, element) {

                //按钮动作
                $(element).click(function (e) {
                    console.log('按键动作: ' + $(element).text());
                    var btnTxt = $(element).text();
                    var inputTxt = $('#inputTxt').val();
                    var updateTxt = '';
                    if (btnTxt != '←') {
                        updateTxt = inputTxt + btnTxt;
                    } else {
                        updateTxt = inputTxt.slice(0, -1);
                    }

                    updateTxt.replace(/\s/g, "")
                    $('#inputTxt').val(updateTxt);

                });
            })

            /* 按钮事件 end */


            /*检测答案输入字符*/
            $('#inputTxt').keyup(function (e) {
                var inputTxt = $(this).val();
                var reg = /[^0-9|^\.]/g;
                inputTxt = inputTxt.replace(reg, '');
                $(this).val(inputTxt);
            });
            /*检测答案输入字符 end*/


            /* 倒计时 */
            var examTime = parseInt($("#examTime").val());
            var start_time = {{ $time }}*1000;
            $('#timeKeeper').text('60:00');

            var totalTime = examTime*60*1000;
            var nowTimestamp = new Date().getTime();


            var leftTime = totalTime - (nowTimestamp - start_time);
            var t = setInterval(timeKeeper, 1000);
            function timeKeeper() {

                var g = Math.floor(leftTime / 3600000.0);
                var e = Math.floor((leftTime - g * 3600000) / 60000.0);
                var f = Math.floor((leftTime - g * 3600000) % 60000 / 1000.0);
                var html = "还剩 <b>" + g + "</b> 时 <b>" + e + "</b> 分 <b>" + f + "</b> 秒";
                document.getElementById("timeKeeper").innerHTML = html;//这个id是你想要显示的div的id

                leftTime = leftTime - 1000;
                if (leftTime <=0) {
                    window.location.href = '{{ url("handin") }}';
                }
            }

            /* 倒计时 end */

            /* 答题区域图片处理 */

            var phoneWith = $('.breadcrumb').width();
            $('#hasImgDiv img').each(function (index, element) {
                //调整大小
                if ($(element).width() > phoneWith) {
                    $(element).width(phoneWith);
                }
                //点击放大


            });
            /* 答题区域图片处理 */

        });




    </script>
@stop



@section('style')
    <!--答题页面css-->
    <link rel="stylesheet" href="{{ asset('public/css/dt.css') }}">
@stop
