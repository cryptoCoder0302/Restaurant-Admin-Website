<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends BaseController
{
    protected $Gallery = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Gallery $gallery)
    {
        $this->middleware('auth:api');
        $this->gallery = $gallery;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $galleries = $this->gallery->latest()->paginate(10);

        return $this->sendResponse($galleries, 'Blog list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $galleries = $this->gallery->pluck('name', 'id');

        return $this->sendResponse($galleries, 'Gallery list');
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
        $gallery = $this->gallery->create([
            'name' => $request->get('name'),
            'image' => $request->get('image'),
            // 'section_id' => $request->get('section_id'),
        ]);

        return $this->sendResponse($gallery, 'Gallery Created Successfully');
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
        $gallery = $this->gallery->findOrFail($id);

        $gallery->update($request->all());

        return $this->sendResponse($gallery, 'Gallery Information has been updated');
    }

    public function destroy($id)
    {

        $this->authorize('isAdmin');

        $gallery = $this->gallery->findOrFail($id);

        $gallery->delete();

        return $this->sendResponse($gallery, 'Gallery has been Deleted');
    }
    public function upload(Request $request)
    {
        $fileName = time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move(public_path('gallery'), $fileName);

        return $this->sendResponse($fileName, 'FileUpload has been updated');
    }
}
