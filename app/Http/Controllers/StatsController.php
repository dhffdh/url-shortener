<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Url;


class StatsController extends Controller
{
    public function redirect(Request $request, $shortCode){
        $item = Url::getByCode($shortCode);
        if($item){
            return redirect($item->href);
        }
        else
            return abort(404);
    }
}
