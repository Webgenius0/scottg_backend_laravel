<?php

namespace App\Http\Controllers\Web\Backend;

use Exception;
use App\Models\Blog;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class BlogController extends Controller
{
    
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Blog::latest()->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('image', function ($data) {
                        $image = $data->image;
                        $url = asset($image);
                        return '<img src="' . $url . '" alt="image" width="100px" height="100px" style="margin-left:20px;">';
                    })
                    ->addColumn('content', function ($data) {
                        // Strip HTML tags and truncate the content
                        $content = strip_tags($data->content);
                        return Str::limit($content, 100);
                    })
                    ->addColumn('status', function ($data) {
                        $status = ' <div class="form-check form-switch" style="margin-left:40px;">';
                        $status .= ' <input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" getAreaid="' . $data->id . '" name="status"';
                        if ($data->status == 'active') {
                            $status .= "checked";
                        }
                        $status .= '><label for="customSwitch' . $data->id . '" class="form-check-label" for="customSwitch"></label></div>';

                        return $status;
                    })
                    ->addColumn('action', function ($data) {
                        $editUrl = route('admin.blogs.edit', $data->id);
                        return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                        <a href="' . $editUrl . '" class="btn btn-primary text-white" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="#" onclick="deleteAlert(' . $data->id . ')" class="btn btn-danger text-white" title="Delete">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>';
                    })

                    ->rawColumns(['image', 'content', 'status', 'action'])
                    ->make(true);
            }
            return view('web.backend.layout.blogs.index');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong! Please try again.');
        }
    }

    public function create()
    {
        return view('web.backend.layout.blogs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'blog_category' => 'required|string',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        try {
            $blog = new Blog();
            $blog->title = $request->title;
            $blog->blog_category = $request->blog_category;
            $blog->content = $request->content;
            $blog->slug = Helper::makeSlug($blog, $request->title . '-' . time());
            $blog->image = Helper::fileUpload($request->file('image'), 'blog', $request->file('image')->getClientOriginalName());

            $blog->save();

            return redirect()->route('admin.blogs')->with('t-success', 'Blog created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong! Please try again.');
        }
    }

    public function edit($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            return view('web.backend.layout.blogs.edit', compact('blog'));
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong! Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'blog_category' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $blog = Blog::findOrFail($id);
            $blog->title = $request->title;
            $blog->blog_category = $request->blog_category;
            $blog->content = $request->content;
            $blog->slug = Helper::makeSlug($blog, $request->title . '-' . time());

            if ($request->hasFile('image')) {
                // Delete the old image
                if ($blog->image) {
                    Helper::fileDelete($blog->image);
                }
                // Upload the new image
                $image = Helper::fileUpload($request->file('image'), 'blog', $request->file('image')->getClientOriginalName());
                $blog->image = $image;
            }

            $blog->save();

            return redirect()->route('admin.blogs')->with('t-success', 'Blog updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong! Please try again.');
        }
    }

    public function destroy(string $id)
    {
        
        try {

            $data = Blog::find($id);
            $data->delete();

            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);

        }
        catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong! Please try again.']);
        }

    }

    public function changeStatus($id)
    {
        
        $data = Blog::find($id);
        if ($data->status == 'active') {
            $data->status = 'inactive';
            $data->save();
            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data' => $data,
            ]);
        } else {
            $data->status = 'active';
            $data->save();
            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data' => $data,
            ]);
        }

    }

}
