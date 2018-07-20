<?php
/**
 * 日志管理
 * User: zhouzeken
 * Date: 2018/3/28
 * Time: 15:35
 */

namespace app\common\controller;

use think\Db;

class Log
{

    /**
     * @param array $data['uid']  用户id 可选
     * @param array $data['type'] 类型 可选，默认1
     * @param array $data['msg'] 内容
     */
    public static function addlog($data=[]){
        $add = [
            'uid'      => isset($data['uid'])?intval($data['uid']):0,
            'ip'       => get_ip(),
            'msg'      => isset($data['msg'])?$data['msg']:'未知',
            'url'      => $_SERVER['PATH_INFO'],
            'type'      => isset($data['type'])?intval($data['type']):1,
        ];
        Db::name('log')->insert($add);
    }
}