<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{

    public function index()
    {
        return BlogResource::collection(Blog::query()->paginate(10));
    }

    public function store(CreateBlogRequest $request)
    {

        $blog = Blog::create(array_merge($request->all(), [
            'created_by_id' => Auth::id(),
            'thumbnail_path' => $request->file('thumbnail')->storePublicly('', ['disk' => 'public'])
        ]));

        return new BlogResource($blog);
    }


    public function show(Blog $blog)
    {
        $blog->load('createdBy');
        return new BlogResource($blog);
    }

    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        foreach ($request->all() as $key => $value) {
            if(!$blog->isFillable($key) && $key !== 'thumbnail'){
                continue;
            }

            if($key === 'thumbnail' && $request->hasFile('thumbnail')) {
                $blog->thumbnail_path = $request->file('thumbnail')->storePublicly();
                continue;
            }

            $blog->$key = $value ?? $blog->$key;
        }

        $blog->save();

        return new BlogResource($blog);
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
    }

}
