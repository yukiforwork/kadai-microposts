@if (count($microposts) > 0)
    <ul class="list-unstyled">
        @foreach ($microposts as $micropost)
            <li class="media mb-3">
                {{-- 投稿の所有者のメールアドレスをもとにGravatarを取得して表示 --}}
                <img class="mr-2 rounded" src="{{ Gravatar::get($micropost->user->email, ['size' => 50]) }}" alt="">
                <div class="media-body">
                    <div>
                        {{-- 投稿の所有者のユーザ詳細ページへのリンク --}}
                        {!! link_to_route('users.show', $micropost->user->name, ['user' => $micropost->user->name]) !!}
                        <span class="text-muted">posted at {{ $micropost->created_at }}</span>
                    </div>
                    <div>
                        {{-- 投稿内容 --}}
                        <p class="mb-0">{!! nl2br(e($micropost->content)) !!}</p>
                    </div>
                <div class="row">
                <div class="col-md">
                <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                <div class="btn-group mr-2" role="group" aria-label="First group">   
                    
                    <div>
                        @if (Auth::id() == $micropost->user_id)
                            {{-- 投稿削除ボタンのフォーム --}}
                            {!! Form::open(['route' => ['microposts.destroy', $micropost->id], 'method' => 'delete']) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-secondary btn-sm']) !!}
                            {!! Form::close() !!}
                        @endif
                        </div>
                          {{-- お気に入りボタン --}}
                        @include('user_favorite.favorite_button')
                        </div>
</div>
</div>
</div>
</div>
            </li>
        @endforeach 
      
    </ul>
    {{-- ページネーションのリンク --}}
    {{ $microposts->links() }}
@endif