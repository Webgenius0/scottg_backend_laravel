<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Models\Blog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class BlogController extends Controller
{

    /* public function getActiveBlogs()
    {
        try {
            
            $blogs = Blog::where('status', 'active')
                ->with('user:id,name,image')
                ->select(['title', 'blog_category', 'slug', 'content', 'image', 'created_at', 'updated_at'])
                ->get()
                ->map(function ($blog) {
                    $blog->content = strip_tags($blog->content);
                    $blog->image = asset($blog->image);
                    $blog->created_at = $blog->created_at->diffForHumans();
                    $blog->user->image = asset($blog->user->image ?? '');
                    $blog->user = [
                        'name' => $blog->user->name,
                        'image' => $blog->user->image ?? '',
                    ];
                    return $blog;
                });

            return ApiResponse::success('Active blogs fetched successfully', $blogs);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    } */

    public function getActiveBlogs()
    {
        try {
            $blogs = Blog::where('status', 'active')
                ->where('user_id', auth()->id())
                ->select(['title', 'blog_category', 'slug', 'content', 'image', 'created_at', 'updated_at'])
                ->get()
                ->map(function ($blog) {
                    $blog->content = strip_tags($blog->content);
                    $blog->image = asset($blog->image);
                    $blog->time_info = [
                        'date' => $blog->created_at->format('d M Y'),
                        'time' => $blog->created_at->format('h:i A'),
                    ];
                    $blog->user_info = [
                        'name' => auth()->user()->first_name ?? 'Unknown',
                        'image' => auth()->user()->image ? asset(auth()->user()->image) : asset('backend/assets/img/avatars/man.png'),
                    ];
                    return $blog;
                });

            return ApiResponse::success('Active blogs fetched successfully', $blogs);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }


    public function getBlogBySlug($slug)
    {
        try {
            $blog = Blog::where('slug', $slug)
                ->where('status', 'active')
                ->where('user_id', auth()->id())
                ->select(['title', 'blog_category', 'slug', 'content', 'image', 'created_at', 'updated_at'])
                ->firstOrFail();

            $blog->content = strip_tags($blog->content);
            $blog->image = asset($blog->image);
            $blog->time_info = [
                'date' => $blog->created_at->format('d M Y'),
                'time' => $blog->created_at->format('h:i A'),
            ];
            $blog->user_info = [
                'name' => auth()->user()->first_name ?? 'Unknown',
                'image' => auth()->user()->image ? asset(auth()->user()->image) : asset('backend/assets/img/avatars/man.png'),
            ];

            return ApiResponse::success('Blog fetched successfully', $blog);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), 404);
        }
    }
}
