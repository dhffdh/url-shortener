<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Url;


class RedirectController extends Controller
{
    public function redirect(Request $request, $shortCode){
        $item = Url::getByCode($shortCode);
        if($item)
            return redirect($item->href);
        else
            return abort(404);
    }
}
