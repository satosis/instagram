<?php

namespace App\Repositories;
 
use App\Models\User;
use App\Models\Post;
use App\Models\Follow;
class HomePageRepository
{
    public function get($id){
        //$id là username có thể là Auth->user hoặc không  
        $user = User::where('user',$id)->first();  
        $post = Post::where(['p_user'=>$user->id ,
        'p_type'=>'profile'
       ])
        ->orderBy('created_at','desc')
        ->get();
        $countPost = Post::where(['p_user'=>$user['id'],'p_type'=>'profile'])->count();                   
        $video = Post::where(['p_user'=>$user['id'],
                           'p_type'=>'video'])
                    ->orderBy('created_at','desc')
                    ->get();
        
        //số người đang theo dõi
        $areFollow =Follow::where(['user_id'=>$user['id']])->orderBy('created_at','desc')->get();
        //kiểm tra xem có theo dõi người khác hay không
        $isFollow =Follow::where(['user_id'=>\Auth::id(),'followed'=>$user['id']])->count();
        //đang theo dõi ai
        $userFollow =Follow::where('followed',$user['id'])->orderBy('created_at','desc')->get();
        $viewData =[
            'user'       => $user,
            'countPost'  => $countPost, 
            'title'      => '',
            'post'       => $post, 
            'video'      => $video, 
            'followed'   => $isFollow,
            'areFollow'  => $areFollow,
            'userFollow' => $userFollow
        ];  
        return view('home-page',$viewData);
    }
}