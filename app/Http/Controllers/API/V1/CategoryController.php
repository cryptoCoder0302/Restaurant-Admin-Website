<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    protected $category = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Category $category)
    {
        $this->middleware('auth:api');
        $this->category = $category;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = $this->category->latest()->paginate(10);

        return $this->sendResponse($categories, 'Category list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $categories = $this->category->pluck('name', 'id');

        return $this->sendResponse($categories, 'Category list');
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
        $tag = $this->category->create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'section_id' => $request->get('section_id'),
            // 'section_id' => $request->get('section_id'),
        ]);

        return $this->sendResponse($tag, 'Category Created Successfully');
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
        $category = $this->category->findOrFail($id);

        $category->update($request->all());

        return $this->sendResponse($category, 'Category Information has been updated');
    }

    public function destroy($id)
    {

        $this->authorize('isAdmin');

        $category = $this->category->findOrFail($id);

        $category->delete();

        return $this->sendResponse($category, 'Section has been Deleted');
    }
}
