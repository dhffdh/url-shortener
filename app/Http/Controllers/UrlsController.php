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
        return Url::getAllByUser();
    }

    public function show(Request $request, $urlId)
    {
        $item = Url::getOneByUser($urlId);
        return $item;
    }

    public function store(StoreUrlPost $request)
    {
        $item = Url::createOneByUser($request);
        return $item;
    }

    public function update(Request $request, $urlId)
    {
        $item = Url::getOneByUser($urlId);
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
        return Url::deleteOneByUser($urlId);
    }

}
