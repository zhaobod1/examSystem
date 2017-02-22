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