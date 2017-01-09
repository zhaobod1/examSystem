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
            <p>
                <a href="/startexam" class="btn btn-primary">开始考试</a>
            </p>
            @endif
            <p>{{ date('Y 年 m 月 d 日', time()) }}</p>
        </div>

    </div>
    <!--巨幕 end -->


@stop


