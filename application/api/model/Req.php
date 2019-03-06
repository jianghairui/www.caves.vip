<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/7
 * Time: 17:17
 */
namespace app\index\model;
use think\Model;

class Req extends Model
{
    protected $pk = 'id';
    protected $table = 'mp_req';

    protected static function init()
    {

    }

    public static function sortlist($condition = [],$lon = "117.04712",$lat = "39.064491",$page = 0,$perpage = 10) {
        $sql = "";
        $sql .= " AND re.pay_status=1 AND re.status=1 AND re.show=1 AND re.world=1 ";

        if($condition['perpage']) {
            $perpage = $condition['perpage'];
        }

        if($condition['page']) {
            $page = ($condition['page'] - 1)*$perpage;
        }

        if($condition['lon']) {
            $lon = $condition['lon'];
        }

        if($condition['lat']) {
            $lat = $condition['lat'];
        }
        $cityinfo = self::getCityinfo($lon,$lat);
        $city = $cityinfo['city'];
        $county = $cityinfo['district'];

        if($condition['city']) {
            $sql .= " AND re.city='{$city}'";
        }

        if($condition['county']) {
            $sql .= " AND re.county='{$county}'";
        }

        if($condition['gender']) {
            $gender = $condition['gender'];
            $sql .= " AND u.gender='{$gender}'";
        }
        $res = self::query("SELECT COUNT(*) AS `count` FROM mp_req re LEFT JOIN mp_user u ON re.f_openid=u.openid WHERE 1 " .$sql);
        $data['count'] = $res[0]['count'];
        $data['list'] = self::query("SELECT *,(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*({$lon}-r.lon)/360),2)+COS(PI()*{$lat}/180)* COS(r.lat * PI()/180)*POW(SIN(PI()*({$lat}-r.lat)/360),2)))) AS juli
FROM (SELECT re.*,u.nickname,u.avatar,u.credit,u.vip,u.gender FROM mp_req re LEFT JOIN mp_user u ON re.f_openid=u.openid WHERE 1 " .$sql. ") r ORDER BY juli ASC LIMIT {$page},{$perpage};");
        return $data;

    }


    protected static function getCityinfo($long,$lat) {
        $info = \my\Geocoding::getAddressComponent($long,$lat);
        $city = $info['result']['addressComponent'];
        return $city;
    }





}