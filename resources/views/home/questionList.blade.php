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
                    <li><a id="handIn" href="{{ url('handin') }}">交卷</a></li>
                    <li><a href="{{ url('startexam') }}">按顺序答题</a></li>

                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    <!--导航条 end-->
@stop


@section('content')
    <!--内容区域-->
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h4>题目列表</h4>
                <h5>试卷总分: <span style="color:red">{{ $totalScore }}</span></h5>
                <ul class="list-group">
                    @foreach($datas as $data)
                        <li class="list-group-item" onclick="javascript:window.location.href='{{ url('startexam') }}'
                                + '/' + '{{ $data->question_id }}'">
                            <a href="{{ url('startexam') . '/' . $data->question_id }}">
                                {{ $data->question_order }}.
                                <span style="{{ $data->quest_answer ? 'color:#cbdee4' : '' }}">
                                     标题：{{ strlen($data->question_title)>15 ? substr($data->question_title,0,15) . "..." :  $data->question_title}}
                                </span>
                            </a>
                            &nbsp;&nbsp;&nbsp;&nbsp;分值:<span style="color:red">{{ $data->question_score }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <nav>
                    <ul class="pagination">
                        {{ $datas->links() }}
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!--内容区域 end -->

@stop




