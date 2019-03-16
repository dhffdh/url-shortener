<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Url;
use App\Http\Requests\StoreUrlPost;


class UrlsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function all()
    {
        return Url::getAll();
    }

    public function show(Request $request, $urlId)
    {
        $item = Url::getOne($urlId);
        return $item;
    }

    public function store(StoreUrlPost $request)
    {
        $item = Url::createOne($request['href']);
        return response()->json($item, 201);
    }

    public function update(Request $request, $urlId)
    {
        $item = Url::getOne($urlId);
        $item->update($request->all());

        return response()->json($item, 200);
    }


    /**
     * @param Request $request
     * @param $urlId
     * @return mixed
     */
    public function delete(Request $request, $urlId)
    {
        return Url::deleteOne($urlId);
    }

}
