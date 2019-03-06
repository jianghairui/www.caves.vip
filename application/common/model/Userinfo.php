<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/10
 * Time: 12:53
 */
namespace app\common\model;
use think\Model;

class Userinfo extends Model {
    protected $pk = 'id';
    protected $table = 'mp_cate';

    protected static function init()
    {
//        self::beforeInsert(function ($data) {
//            //控制需要用save或create方法触发,不可用insert
////            halt($data);
//            return true;
//        });
//

//
//        self::beforeDelete(function ($data) {
//            //控制需要用destroy方法触发,不可用delete
//            halt($data);
//            return false;
//        });

        self::beforeUpdate(function ($data) {
            //控制需要用save或update方法触发
            halt($data);
            return false;
        });

//        self::afterUpdate(function ($data) {
//            //控制需要用save或update方法触发
//            halt($data);
//            return false;
//        });

        self::afterDelete(function ($data) {
            //控制需要用destroy方法触发,不可用delete
            @unlink($data['cover']);
        });

    }
}
