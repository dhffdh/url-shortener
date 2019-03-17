<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * @property integer $id
 * @property integer $url_id
 *
 * @property string $user_agent
 * @property string $user_refer
 * @property string $user_ip
 *
 * Class Stat
 * @package App
 */
class Stat extends Model
{

    /**
     * @param Request $request
     * @param $url_id
     * @return Stat
     */
    public static function Add(Request $request, $url_id){
        $item = new Stat;
        $item->url_id = $url_id;
        $item->user_ip = $request->ip();
        $item->user_agent = $request->userAgent();
        $item->user_refer = $request->server('HTTP_REFERER');
        $item->save();
        return $item;
    }

    /**
     * All stats
     *
     * @param $url_id
     * @return mixed
     */
    public static function getAll($url_id)
    {
        return self::where('url_id',$url_id)->get(['id','user_ip','created_at']);
    }


    /**
     * Count of clicks
     *
     * @param $url_id
     * @return mixed
     */
    public static function getCounter($url_id){
        return self::where('url_id',$url_id)->count();
    }


    /**
     * На основе истории переходов посчитать
     * общее количество уникальных посетителей
     * за последние 14 дней.
     *
     * @param $url_id
     * @return mixed
     * @throws \Exception
     */
    public static function getCountOfLastUsers($url_id){

        $date = new \DateTime();
        $date->sub(new \DateInterval('P14D'));

        $count = self::where('url_id',$url_id)
            ->where('created_at', '>', $date)
            ->get(['id','user_ip','created_at'])
            ->groupBy('user_ip')
            ->count()
        ;
        return $count;
    }


}
