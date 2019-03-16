<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Exception;

class Url extends Model
{

    protected static $viewColumns = ['id','href', 'code', 'active', 'created_at'];

    protected $fillable = ['href', 'code', 'user_id', 'active', 'timeout'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    protected $appends = ['short_href'];


    /**
     * computed attr: ShortHref
     *
     * @return string
     */
    public function getShortHrefAttribute()
    {
        return Config::get('app.url')."/i/".$this->code;
    }

    /**
     * Get list of links for user
     *
     * @return mixed
     */
    public static function getAll()
    {
        $item = self::where('active', true)
            ->where('user_id', Auth::id())
            ->get(self::$viewColumns);
        return $item;
    }

    /**
     * @param $urlId
     * @return mixed
     */
    public static function getOne($urlId){
        return self::where('id', $urlId)
            ->where('active', true)
            ->where('user_id', Auth::id())
            ->first(self::$viewColumns);
    }

    /**
     * @param $shortCode
     * @return mixed
     */
    public static function getOneByCode($shortCode){
        return self::where('code', $shortCode)
            ->where('active', true)
            ->first(self::$viewColumns);
    }


    /**
     * @param $urlId
     * @return mixed
     */
    public static function deleteOne($urlId){
        $item = self::where('id', $urlId)
            ->where('active', true)
            ->where('user_id', Auth::id())
            ->first();
        //$item->active = false;
        //$item->save();
        $item->delete();
        return $item;
    }


    /**
     * Create item in DB
     *
     * @param $href
     * @throws Exception
     */
    public static function createOne($href){
        if(!self::isUniqueHref($href)){
            throw new Exception("This link already exists");
        }
        $item = new Url;
        $item->href = $href;
        $item->code = self::createShortCode();
        $item->user_id = Auth::id();
        $item->active = true;
        $item->save();
    }

    /**
     * Check unique URL for user
     *
     * @param $href
     * @return bool
     */
    protected static function isUniqueHref($href){
        return !self::where('href', $href)
            ->where('user_id', Auth::id())
            ->exists();
    }

    /**
     * avalible chars
     *
     * @var string
     */
    protected static $chars = "abcdfghjkmnpqrstvwxyz|ABCDFGHJKLMNPQRSTVWXYZ|0123456789|!*()";

    /*
     * Create random shortCode
     *
     * if length = 4 exist 57^4 = 10.556.001 versions
     * if length = 5 exist 57^5 = 601.692.057 versions
     * if length = 6 exist 57^6 = 34.296.447.249 versions
     * if length = 7 exist 57^7 = 1.954.897.493.193 versions
     *
     */
    protected static function generateShortCode($length = 6){
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

    /**
     * Create !unique shortCode
     *
     * @param int $length
     * @return string
     * @throws Exception
     */
    protected static function createShortCode($length = 6){
        $count = 0;
        $time_start = microtime(true);
        do {
            $count++;
            $code = self::generateShortCode($length);
            $isExist = !self::isUniqueShortCode($code);
            if($count > pow(10, $length-1)){
                $time_end = microtime(true);
                $time = $time_end - $time_start;
                throw new Exception("generateShortCode error, count= $count, time= $time");
                break;
            }
        } while ($isExist);
        return $code;
    }


    /**
     * Check uniqum shortCode
     *
     * @param $code
     * @return mixed
     */
    protected static function isUniqueShortCode($code){
        return !self::where('code', $code)->exists();
    }


}
