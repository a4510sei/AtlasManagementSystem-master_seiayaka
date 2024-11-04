<?php

namespace App\Http\Controllers\Authenticated\BulletinBoard;


use \Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories\MainCategory;
use App\Models\Categories\SubCategory;
use App\Models\Posts\Post;
use App\Models\Posts\PostComment;
use App\Models\Posts\Like;
use App\Models\Users\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\BulletinBoard\PostFormRequest;
use Auth;
use DB;

class PostsController extends Controller
{
    public function show(Request $request){
        $posts = Post::with('user', 'postComments')->get();
        $categories = MainCategory::get();
        $sub_categories = SubCategory::get();
        $like = new Like;
        $post_comment = new Post;
        if(!empty($request->keyword)){
            $posts = Post::with('user', 'postComments')
            ->where('post_title', 'like', '%'.$request->keyword.'%')
            ->orWhere('post', 'like', '%'.$request->keyword.'%')->get();
        }else if($request->category_word){
            // $sub_category = $request->category_word;
            $sub_category_id = SubCategory::where('sub_category', $request->category_word)->value('id');
            $sub_category = SubCategory::where('id', $sub_category_id )->first();
            $post_sub_categories = $sub_category->posts->first();
            if(!empty($post_sub_categories)){
                $post_sub_categories = $sub_category->posts->first()->pivot;
                $posts = Post::with('user', 'postComments')
                ->whereIn('id', [$post_sub_categories->post_id])
                ->get();
            }else{
            // サブカテゴリに紐づく投稿がnullだった時は投稿を表示しない
                $posts = NULL;
            }
        }else if($request->like_posts){
            $likes = Auth::user()->likePostId()->get('like_post_id');
            $posts = Post::with('user', 'postComments')
            ->whereIn('id', $likes)->get();
        }else if($request->my_posts){
            $posts = Post::with('user', 'postComments')
            ->where('user_id', Auth::id())->get();
        }
        return view('authenticated.bulletinboard.posts', compact('posts', 'categories', 'sub_categories', 'like', 'post_comment'));
    }

    public function postDetail($post_id){
        $post = Post::with('user', 'postComments')->findOrFail($post_id);
        return view('authenticated.bulletinboard.post_detail', compact('post'));
    }

    public function postInput(){
        $main_categories = MainCategory::get();
        $sub_categories = SubCategory::get();
        return view('authenticated.bulletinboard.post_create', compact('main_categories', 'sub_categories'));
    }

    public function postCreate(PostFormRequest $request){
        DB::beginTransaction();
        try{
            $post = Post::create([
                'user_id' => Auth::id(),
                'post_title' => $request->post_title,
                'post' => $request->post_body
            ]);
            // 中間テーブル作成
            // 紐づくカテゴリーを登録
            $sub_category_id = $request->post_category_id;
            $sub_category = Post::orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->first();
            $sub_category->subCategories()->attach($sub_category_id);
            return redirect()->route('post.show');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('post.input');
        }
    }

    public function postEdit(Request $request){
        // バリデーション実行
        $validator = Validator::make($request->all(), [
          'post_title' => ['string', 'min:4', 'max:100'],
          'post_body' => ['string', 'min:10', 'max:5000'],
        ]);
        // バリデーションが通らなかった時の処理（ここではリダイレクト）
        if ($validator->fails()) {
            $messages = $validator->messages();
            if($messages->has('post_title')){
                $error_message = $messages->first('post_title');
            }else if($messages->has('post_body')){
                $error_message = $messages->first('post_body');
            }
            return response()->json($error_message, Response::HTTP_UNPROCESSABLE_ENTITY);
        }else{
            Post::where('id', $request->post_id)->update([
                'post_title' => $request->post_title,
                'post' => $request->post_body,
            ]);
            return redirect()->route('post.detail', ['id' => $request->post_id]);
        }
    }

    public function postDelete($id){
        Post::findOrFail($id)->delete();
        return redirect()->route('post.show');
    }
    public function mainCategoryCreate(PostFormRequest $request){
        MainCategory::create(['main_category' => $request->main_category_name]);
        return redirect()->route('post.input');
    }
    public function subCategoryCreate(PostFormRequest $request){
        SubCategory::create([
            'main_category_id' => $request->main_category_id,
            'sub_category' => $request->sub_category_name]);
        return redirect()->route('post.input');
    }

    public function commentCreate(Request $request){
        PostComment::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment
        ]);
        return redirect()->route('post.detail', ['id' => $request->post_id]);
    }

    public function myBulletinBoard(){
        $posts = Auth::user()->posts()->get();
        $like = new Like;
        return view('authenticated.bulletinboard.post_myself', compact('posts', 'like'));
    }

    public function likeBulletinBoard(){
        $like_post_id = Like::with('users')->where('like_user_id', Auth::id())->get('like_post_id')->toArray();
        $posts = Post::with('user')->whereIn('id', $like_post_id)->get();
        $like = new Like;
        return view('authenticated.bulletinboard.post_like', compact('posts', 'like'));
    }

    public function postLike(Request $request){
        $user_id = Auth::id();
        $post_id = $request->post_id;

        $like = new Like;

        $like->like_user_id = $user_id;
        $like->like_post_id = $post_id;
        $like->save();

        return response()->json();
    }

    public function postUnLike(Request $request){
        $user_id = Auth::id();
        $post_id = $request->post_id;

        $like = new Like;

        $like->where('like_user_id', $user_id)
             ->where('like_post_id', $post_id)
             ->delete();

        return response()->json();
    }


public function postSearchRequest(Request $request){

}
}
