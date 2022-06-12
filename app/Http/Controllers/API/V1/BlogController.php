<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends BaseController
{
    protected $Blog = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Blog $blog)
    {
        $this->middleware('auth:api');
        $this->blog = $blog;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogs = $this->blog->latest()->paginate(10);

        return $this->sendResponse($blogs, 'Blog list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $blogs = $this->blog->pluck('name', 'id');

        return $this->sendResponse($blogs, 'Blog list');
    }


    /**
     * Store a newly created resource in storage.
     *
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $blog = $this->blog->create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'image' => $request->get('image'),
            // 'section_id' => $request->get('section_id'),
        ]);

        return $this->sendResponse($blog, 'Blog Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $blog = $this->blog->findOrFail($id);

        $blog->update($request->all());

        return $this->sendResponse($blog, 'Blog Information has been updated');
    }

    public function destroy($id)
    {

        $this->authorize('isAdmin');

        $blog = $this->blog->findOrFail($id);

        $blog->delete();

        return $this->sendResponse($blog, 'Blog has been Deleted');
    }
    public function upload(Request $request)
    {
        $fileName = time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move(public_path('blog'), $fileName);

        return $this->sendResponse($fileName, 'FileUpload has been updated');
    }
}
