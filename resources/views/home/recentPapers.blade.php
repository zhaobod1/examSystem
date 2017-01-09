@extends('layouts.home')



@section('content')
    <!--内容区域-->
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h4>考试记录</h4>
                <ul class="list-group">
                    @foreach($papers as $paper)
                        <li class="list-group-item" onclick="javascript:window.location.href='{{ url('paper') }}' + '/' + '{{ $paper->paper_id }}'">
                            试卷编号: <a href='{{ url('paper') . '/' . $paper->paper_id }}'  >{{ $paper->paper_id }}</a>&nbsp;
                            成绩:<span style="color:red">{{ $paper->total_score }}</span> 分&nbsp;&nbsp;&nbsp;&nbsp;
                            用时: {{ gmstrftime('%H:%M:%S',intval($paper->updated_at) - intval($paper->created_at)) }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <nav>
                    <ul class="pagination">
                        {{ $papers->links() }}
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!--内容区域 end -->

@stop




