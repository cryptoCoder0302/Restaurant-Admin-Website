<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends BaseController
{
    protected $News = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(News $news)
    {
        $this->middleware('auth:api');
        $this->news = $news;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $news = $this->news->latest()->paginate(10);

        return $this->sendResponse($news, 'News list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $news = $this->news->pluck('name', 'id');

        return $this->sendResponse($news, 'News list');
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
        $news = $this->news->create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'image' => $request->get('image'),
            // 'section_id' => $request->get('section_id'),
        ]);

        return $this->sendResponse($news, 'News Created Successfully');
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
        $news = $this->news->findOrFail($id);

        $news->update($request->all());

        return $this->sendResponse($news, 'News Information has been updated');
    }

    public function destroy($id)
    {

        $this->authorize('isAdmin');

        $news = $this->news->findOrFail($id);

        $news->delete();

        return $this->sendResponse($news, 'News has been Deleted');
    }
    public function upload(Request $request)
    {
        $fileName = time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move(public_path('news'), $fileName);

        return $this->sendResponse($fileName, 'FileUpload has been updated');
    }
}
