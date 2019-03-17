<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Url;
use App\Stat;

class StatsController extends Controller
{
    public function redirect(Request $request, $shortCode){
        $item = Url::getByCode($shortCode);
        if($item && !empty($item->href)){
            if($item->timeout){
                $dateNow = date("Y-m-d H:i:s");
                if($dateNow > $item->timeout){
                    return abort(404,'Link has expired');
                }
            }
            Stat::Add($request,$item->id);
            return redirect($item->href);
        }
        else
            return abort(404);
    }
}
