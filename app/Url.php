<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class Url extends Model
{
    protected $fillable = ['href', 'code', 'user_id', 'active', 'timeout'];

    protected static $viewColumns = ['id','href', 'code', 'active', 'created_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    protected $appends = ['short_href'];

    public function getShortHrefAttribute()
    {
        return Config::get('app.url')."/r/".$this->code;
    }


    public static function getAll()
    {
        $item = self::where('active', true)
            ->where('user_id', Auth::id())
            ->get(self::$viewColumns);

        return $item;
    }


    public static function getOne($urlId){
        return self::where('id', $urlId)
            ->where('active', true)
            ->where('user_id', Auth::id())
            ->first(self::$viewColumns);
    }

    public static function getByCode($shortCode){
        return self::where('code', $shortCode)
            ->where('active', true)
            ->first(self::$viewColumns);
    }


    public static function deleteOne($urlId){
        $item = self::where('id', $urlId)
            ->where('active', true)
            ->where('user_id', Auth::id())
            ->first();
        $item->active = false;
        $item->save();

        return $item;
    }

    public static function createOne($href){
        $item = new Url;
        $item->href = $href;
        $item->code = self::generateRandomString();
        $item->user_id = Auth::id();
        $item->active = true;
        $item->save();
    }



    protected static $chars = "abcdfghjkmnpqrstvwxyz|ABCDFGHJKLMNPQRSTVWXYZ|0123456789|~-_!*'()";

    protected static function generateRandomString($length = 6){
        $sets = explode('|', self::$chars);
        $all = '';
        $randString = '';
        foreach($sets as $set){
            $randString .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++){
            $randString .= $all[array_rand($all)];
        }
        $randString = str_shuffle($randString);
        return $randString;
    }

}
