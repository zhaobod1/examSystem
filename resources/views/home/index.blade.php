@extends('layouts.home')

@section('content')


    <!--巨幕-->
    <div class="jumbotron">
        <div class="p10">
            <div class="mark">
                @if(session('msg'))
                    <p style="color:red">{{ session('msg') }}</p>
                @endif
                @if(is_object($errors))
                    @foreach($errors->all() as $error)
                        <p>{{$error}}</p>
                    @endforeach
                @else
                    <p>{{$errors}}</p>
                @endif
            </div>
            <p>{{ $userCheck }}</p>
            @if($isChecked)
                @if($bContinueExam == 1)
                    <p>
                        上次考试用时： <span style="color:red;">{{ $sumTime }}</span>
                    </p>
                    <p>
                        <a href="/startexam" class="btn btn-primary">继续考试</a>
                    </p>
                @elseif($bContinueExam == 2)
                    <p>
                        上次考试已经结束，您还没有交卷。
                    </p>
                    <p>
                        <a href="/handin" class="btn btn-primary">交卷</a>
                    </p>
                @else
                    <p>
                        <a href="/startexam" class="btn btn-primary">开始考试</a>
                    </p>
                @endif

            @endif
            <p>{{ date('Y 年 m 月 d 日', time()) }}</p>
        </div>
        <script src="https://cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
        <script>
            $(".btn-primary").click(function (e) {
              e.preventDefault();
              if (confirm("确定 " + $(this).text() + " 吗？")) {
                window.location.href = $(this).attr("href");
              }
            })
        </script>
    </div>
    <!--巨幕 end -->


@stop


