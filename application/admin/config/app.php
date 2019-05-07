<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/9/25
 * Time: 13:57
 */

return array(
    'layout_on'     =>  true,
    'layout_name'   =>  'layout',
    'page'   =>  1,
    'perpage'   =>  5,
    'login_key' => 'jiang',
    'app_trace' => false,

    'trace'     =>  [
        //支持Html,Console
        'type'  =>  'html',
    ],
    'superman'  => 'jianghairui',
    'auth'  => [
        'auth_on' => true,
        'auth_type'         => 1, // 认证方式，1为实时认证；2为登录认证。
        'auth_out'          => [
            'Index/index'
        ]
    ]
);