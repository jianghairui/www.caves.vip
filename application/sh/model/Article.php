<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/7
 * Time: 17:17
 */
namespace app\sh\model;
use think\Model;

class Article extends Model
{
    protected $pk = 'id';
    protected $table = 'mp_article';

    protected static function init()
    {
//        self::beforeInsert(function ($data) {
//            //控制需要用save或create方法触发,不可用insert
////            halt($data);
//            return true;
//        });
//
//        self::beforeUpdate(function ($data) {
//            //控制需要用save或update方法触发
//            halt($data);
//            return false;
//        });
//
//        self::beforeDelete(function ($data) {
//            //控制需要用destroy方法触发,不可用delete
//            halt($data);
//            return false;
//        });

        self::afterDelete(function ($data) {
            //控制需要用destroy方法触发,不可用delete
            @unlink($data['pic']);
        });

    }



}