@extends('layouts.sidebar')

@section('content')
<div class="board_area w-100 border m-auto d-flex">
  <div class="post_view w-75 mt-5">
    <p class="w-75 m-auto">投稿一覧</p>
    @if(!empty($posts))
    @foreach($posts as $post)
    <div class="post_area border w-75 m-auto p-3">
      <p><span>{{ $post->user->over_name }}</span><span class="ml-3">{{ $post->user->under_name }}</span>さん</p>
      <p><a href="{{ route('post.detail', ['id' => $post->id]) }}">{{ $post->post_title }}</a></p>
      <div class="post_bottom_area d-flex">
        <div class="d-flex post_status">
         <div class="mr-5">
            <!-- postからsub_categoryを取得 -->
            <?php
              $post_sub_categories = DB::select(
                'SELECT sub_category_id FROM posts inner join post_sub_categories ON posts.id = post_sub_categories.post_id WHERE posts.id =:id', ['id'=>$post->id]);
              $sub_category_id = $post_sub_categories[0]->sub_category_id;
              $post_category = DB::table('sub_categories')->where('id',$sub_category_id)->first();
            ?>
            <span class="category_btn">{{$post_category->sub_category}}</span>
           </div>
          <div class="mr-5">
            <i class="fa fa-comment"></i><span class=""></span>
            <?php $comment_count = DB::table('post_comments')->where('post_id', $post->id)->count() ?>
            <span class="comment_counts{{ $post->id }}">{{ $comment_count }}</span>
          </div>
          <div>
            <?php $like_count = DB::table('likes')->where('like_post_id', $post->id)->count() ?>
            @if(Auth::user()->is_Like($post->id))
            <p class="m-0"><i class="fas fa-heart un_like_btn" post_id="{{ $post->id }}"></i>
            <span class="like_counts{{ $post->id }}">{{ $like_count }}</span>
            </p>
            @else
            <p class="m-0"><i class="fas fa-heart like_btn" post_id="{{ $post->id }}"></i>
            <span class="like_counts{{ $post->id }}">{{ $like_count }}</span>
          </p>
            @endif
          </div>
        </div>
      </div>
    </div>
    @endforeach
    @endif
  </div>
  <div class="other_area border w-25">
    <div class="border m-4">
      <div class=""><a href="{{ route('post.input') }}">投稿</a></div>
      <div class="">
        <input type="text" placeholder="キーワードを検索" name="keyword" form="postSearchRequest">
        <input type="submit" value="検索" form="postSearchRequest">
      </div>
      <input type="submit" name="like_posts" class="category_btn" value="いいねした投稿" form="postSearchRequest">
      <input type="submit" name="my_posts" class="category_btn" value="自分の投稿" form="postSearchRequest">
      <ul>
        @foreach($categories as $category)
        <li class="main_categories" category_id="{{ $category->id }}"><span>{{ $category->main_category }}<span></li>
          @foreach($sub_categories as $sub_category)
            @continue($sub_category->main_category_id !== $category->id)
            <li class="main_categories" category_id="{{ $sub_category->id }}"><span>
              <input type="submit" name="category_word" class="category_btn" value="{{ $sub_category->sub_category }}" form="postSearchRequest">
              <span>
            </li>
          @endforeach
        @endforeach
      </ul>
    </div>
  </div>
  <form action="{{ route('post.show') }}" method="get" id="postSearchRequest"></form>
</div>
@endsection
