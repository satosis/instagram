<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\Follow; 
use App\Models\Post;
use DB;
class HomeController extends Controller
{ 
    public function index(Request $request)
    {     
        //tất cả bài viết của những người bạn theo dõi
        $posts =Post::join('follows','follows.followed','posts.p_user')
                    ->where('follows.user_id',\Auth::id())
                    ->where('posts.p_type','profile')
                    ->select('posts.*')
                    ->orderBy('created_at','desc')
                    ->simplePaginate(5);  

        $count_post =Post::join('follows','follows.followed','posts.p_user')
            ->where('follows.user_id',\Auth::id())
            ->where('posts.p_type','profile')
            ->count();

        $areFollow =Follow::where(['user_id'=>\Auth::id()])->get();
        $user=[];
        if(!count($areFollow)){
            $user =User::where('id','!=',\Auth::id())
            ->orderBy('picture','desc')
            ->limit(5)
            ->get(); 
        } 
        else{
            foreach($areFollow as $list){
            $user =User::where('id','!=',$list->id)
                        ->where('id','!=',\Auth::id())
                        ->orderBy('picture','desc')
                        ->inRandomOrder()
                        ->limit(5)
                        ->get(); 
        } 
    }
    
    $output = '';
    if ($request->ajax()) {
            foreach ($posts as $key =>$val) {
                $output.= view('layout.Home.post',compact('key','val'))->render();
            }   
        return $output;
    }
        $data=[
            'posts' => $posts, 
            'count_post' => $count_post, 
            'user'  => $user,     
            'title' => 'Instagram'
        ];
        return view('welcome',$data);
    }
    public function search(Request $request){
        // full text search 
        // $val = User::where('id','!=',\Auth::id())
        //             ->search($request->value)
        //             ->get();

        $val =  User::where('id','!=',\Auth::id())
                ->where('user','like','%'.$request->value.'%')
                ->orwhere('c_name','like','%'.$request->value.'%')
                ->get();
        if(!$val->isEmpty())
            return view('layout.header.data',compact('val'))->render();
        else{
            return 0;
        }
    }
    public function data(){
        DB::statement("ALTER TABLE users ADD FULLTEXT search('c_name', 'user')");
        DB::statement("ALTER TABLE users ENGINE = MyISAM");
    }
}
