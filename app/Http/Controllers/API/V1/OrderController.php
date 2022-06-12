<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    protected $order = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->middleware('auth:api');
        $this->order = $order;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $order = $this->order->latest()->paginate(10);

        return $this->sendResponse($order, 'Order list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $order = $this->order->pluck('name', 'id');

        return $this->sendResponse($order, 'Order list');
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

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */

   
}
