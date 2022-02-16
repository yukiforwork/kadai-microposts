
     @if (Auth::User()->is_favorite($micropost->id))
    {{-- お気に入り解除ボタンのフォーム --}}
    {!! Form::open(['route' => ['user.unfavorite', $micropost->id], 'method' => 'delete']) !!}
        {!! Form::submit('Unfavorite', ['class' => 'btn btn-danger btn-secondary btn-sm']) !!}
    {!! Form::close() !!}
    @else
    {{-- お気に入りボタンのフォーム --}}
    {!! Form::open(['route' => ['user.makefavorite', $micropost->id]]) !!}
        {!! Form::submit('MakeFavorite', ['class' => 'btn btn-success btn-secondary btn-sm']) !!}
    {!! Form::close() !!}
　  @endif
