@extends('layouts.home')

@section('content')
    <div class="container">
        <h4>微信答题系统</h4>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">{{ isset($handinFail) ? '交卷失败' : '交卷成功' }}</h3>
            </div>
            <div class="panel-body">
                @if(!isset($handinFail))
                    <ul class="list-group">
                        <li class="list-group-item">
                            试卷编号: <a href="#">{{ $paper->paper_id }} &nbsp;&nbsp;<span
                                        class="glyphicon glyphicon-hand-left"></span></a>
                        </li>
                        <li class="list-group-item">
                            考试用时: {{ $sumTime }}
                        </li>
                        <li class="list-group-item">
                            考试得分: {{ $sumScore }}分
                        </li>
                    </ul>
                @else
                    <ul class="list-group">
                        <li class="list-group-item">
                            请先开始答题!
                        </li>

                    </ul>

                @endif

            </div>
            <div class="panel-footer">
                <a href="{{ url('recentPapers') }}" class="btn btn-primary">历史答题</a>
                <a href="{{ url('index') }}" class="btn btn-primary">返回首页</a>
            </div>

        </div>
    </div>

@stop
