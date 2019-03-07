<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/7
 * Time: 17:17
 */
namespace app\admin\model;
use think\Model;

class Slideshow extends Model
{
    protected $pk = 'id';
    protected $table = 'mp_slideshow';

    protected static function init()
    {

        self::afterDelete(function ($data) {
            //控制需要用destroy方法触发,不可用delete
            @unlink($data['pic']);
        });

    }



}