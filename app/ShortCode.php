<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Url;
use Exception;

class ShortCode extends Model
{

    /**
     * allowed characters for short code
     *
     * @var string
     */
    protected static $chars = "abcdefghijklmnopqrstuvwxyz|ABCDEFGHIJKLMNOPQRSTUVWXYZ|0123456789";

    protected static $lengthCode = 6;

    /**
     * Validate and clear request code from unavaleble chars
     *
     * @param $userCode
     * @return string
     * @throws Exception
     */
    public static function validateCode($userCode){
        $codeArray = str_split($userCode);
        $avalebleChars = [];
        foreach(explode('|', self::$chars) as $set){
            $avalebleChars = array_merge($avalebleChars, str_split($set));
        }
        $resCode = "";
        foreach ($codeArray as $char){
            if(in_array($char,$avalebleChars))
                $resCode .= $char;
        }

        if(strlen($resCode) < self::$lengthCode)
            throw new Exception("The code must be at least 6 characters.");

        return $resCode;
    }


    /*
     * Create random shortCode
     *
     * if length = 4 exist 57^4 = 10.556.001 versions
     * if length = 5 exist 57^5 = 601.692.057 versions
     * if length = 6 exist 57^6 = 34.296.447.249 versions
     * if length = 7 exist 57^7 = 1.954.897.493.193 versions
     *
     */
    public static function generateShortCode($length = 6){
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
    public static function createShortCode($length = 6){
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
    public static function isUniqueShortCode($code){
        return !Url::where('code', $code)->exists();
    }
}
