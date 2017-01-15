@extends('layouts.home')
@section('content')
    <!--内容区域-->
    <div class="container">
        <div class="row" style="padding-top: 50px;">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">请您绑定手机号</h3>
                    </div>
                    <div class="panel-body">

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
                        <form action="" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <img width="100" src="{{ $user->user_avatar }}" alt="" class="img-circle">
                            </div>
                            <div class="form-group hide">
                                <label class="">用户名</label>
                                <input type="text" disabled="disabled" name="user_name" class="form-control" placeholder=""
                                       value="{{
                                $user->user_name }}">
                            </div>
                            <div class="form-group">
                                <label class="">学生姓名</label>
                                <input type="text" name="user_neckname" class="form-control" placeholder="填写正确的姓名" value="{{
                                $user->user_neckname }}">
                            </div>
                            <div class="form-group">
                                <label class="">手机号码</label>
                                <input type="text" name="user_phone" class="form-control" placeholder="请输入正确的手机号码"
                                       value="{{ $user->user_phone }}">
                            </div>
                            <div class="form-group">
                                <label class="">密码</label>
                                <input type="password" name="user_password" class="form-control" placeholder="输入新密码,留空代表不修改密码"
                                       value="">
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-block" style="background: #333;color:#fff;"
                                       value="确定">
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--内容区域 end -->
@stop


@section('style')
    <style>
        .login_wx {
            list-style: none;
        }
    </style>
@stop
