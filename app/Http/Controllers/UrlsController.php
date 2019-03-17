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

    /**
     * @return mixed
     */
    public function all()
    {
        return Url::getAllByUser();
    }

    /**
     * @param Request $request
     * @param $urlId
     * @return mixed
     */
    public function show(Request $request, $urlId)
    {
        return Url::getOneByUser($urlId);
    }


    /**
     * @param Request $request
     * @param $urlId
     * @return mixed
     * @throws \Exception
     */
    public function statistics(Request $request, $urlId)
    {
        return Url::getStatsOneByUser($urlId);
    }


    /**
     * @param StoreUrlPost $request
     * @return Url|mixed|null
     * @throws \Exception
     */
    public function save(StoreUrlPost $request)
    {
        return Url::createOneByUser($request);
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
