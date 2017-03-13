@extends('layouts.home')



@section('content')
    <!--内容区域-->
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h4>试卷编号:{{ $paperInfo->paper_id }}</h4>
                <h5>试卷总分: <span style="color:red">{{ $paperInfo->total_score }}</span></h5>

                <table class="table table-responsive table-striped">
                    <thead>
                    <tr>
                        <th>编号</th>
                        <th>标题</th>
                        <th>分值</th>
                        <th>成绩</th>
                    </tr>
                    </thead>
                    @foreach($questions as $question)
                        <tr onclick="javascript:window.location.href='{{ url('getQuestion') }}' + '/' + '{{ $question->question_id }}'">
                            <td>{{ $question->question_order }}</td>
                            <td>{{ $question->question_title }}</td>
                            <td><span style="color:red">
                                    {{ $question->question_score }}
                                </span>
                            </td>
                            <td>
                                <span class="glyphicon glyphicon-{{ $question->question_answer==$question->quest_answer ? 'ok' : 'remove' }}"></span>
                            </td>
                        </tr>
                    @endforeach
                </table>
                <nav>
                    <ul class="pagination">
                        {{ $questions->links() }}
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!--内容区域 end -->

@stop




