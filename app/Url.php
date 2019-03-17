<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Exception;
use \DateTime;
use App\ShortCode;

/**
 * @property integer $id
 * @property integer $user_id
 *
 * @property string $href
 * @property string $code
 *
 * @property boolean $active
 * @property string $timeout
 *
 * Class Url
 * @package App
 */
class Url extends Model
{

    protected static $viewColumns = ['id','href', 'code', 'active', 'created_at', 'timeout'];

    protected $fillable = ['href', 'code', 'user_id', 'active', 'timeout'];

    protected $casts = [];

    protected $appends = ['short_href','date'];

    /**
     * new computed attr: short_href
     *
     * @return string
     */
    public function getShortHrefAttribute()
    {
        return $this->code ? Config::get('app.url')."/i/".$this->code : null;
    }

    /**
     * new computed attr: date
     *
     * @return null
     */
    public function getDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : null;
    }



    /**
     * Get all links by current User
     *
     * @return mixed
     */
    public static function getAllByUser()
    {
        $item = self::where('active', true)
            ->where('user_id', Auth::id())
            ->get(self::$viewColumns);
        return $item;
    }


    /**
     * Get one link by current User
     *
     * @param $urlId
     * @return mixed
     */
    public static function getOneByUser($urlId){
        return self::where('id', $urlId)
            ->where('active', true)
            ->where('user_id', Auth::id())
            ->first(self::$viewColumns);
    }

    /**
     * Get one link and stats by current User
     *
     * @param $urlId
     * @return mixed
     * @throws Exception
     */
    public static function getStatsOneByUser($urlId){
        $item = self::getOneByUser($urlId);
        $info = [];
        if($item){
            $info['id'] = $item->id;
            $info['counter'] = Stat::getCounter($urlId);
            $info['clicks'] = Stat::getAll($urlId);
            $info['lastusers'] = Stat::getCountOfLastUsers($urlId);
        }
        return $info;
    }


    /**
     * @param $shortCode
     * @return mixed
     */
    public static function getByCode($shortCode){
        return self::where('code', $shortCode)
            ->where('active', true)
            ->first(self::$viewColumns);
    }


    /**
     * deleting by current User
     *
     * @param $urlId
     * @return mixed
     */
    public static function deleteOneByUser($urlId){
        $item = self::where('id', $urlId)
            ->where('active', true)
            ->where('user_id', Auth::id())
            ->first();
        $item->delete();
        return $item;
    }

    /**
     * Adding link by current User
     *
     * @param Request $request
     * @return Url|mixed|null
     * @throws Exception
     */
    public static function createOneByUser(Request $request){

        $href = $request['href'];
        if(empty($href)){
            throw new Exception("Href is required");
        }
        if(!self::isUniqueHref($href)){
            throw new Exception("This link already exists");
        }

        $code = null;
        if(!empty($request['code'])){
            $code = ShortCode::validateCode($request['code']);
            if(!ShortCode::isUniqueShortCode($code))
                throw new Exception("This short-code already exists");
        }else{
            $code = ShortCode::createShortCode();
        }


        $dateTimeout = null;
        $timeout = ( !empty($request['timeout']) && in_array($request['timeout'],['day','week','month','year'])) ? $request['timeout'] : null;

        if(!empty($timeout)){
            $dateNow = new DateTime();
            $interval = 'PT0S';
            //P 7Y 5M 4D T 4H3M2S
            switch ($timeout){
                case "day":
                    $interval = 'P1D';
                    break;
                case "week":
                    $interval = 'P7D';
                    break;
                case "month":
                    $interval = 'P1M';
                    break;
                case "year":
                    $interval = 'P1Y';
                    break;
            }
            //Add interval to nowtime
            $dateTimeout = $dateNow->add(new \DateInterval($interval));
        }

        $item = new Url;
        $item->href = $href;
        $item->code = $code;
        $item->user_id = Auth::id();
        $item->active = true;
        $item->timeout = $dateTimeout;
        $item->save();

        return $item;
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


}
