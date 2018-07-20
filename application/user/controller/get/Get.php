<?php

/**
 * 获取信息类，默认类
 * @Author zhouzeken（1454740914@qq.com）
 * @Date 2018/0209
 */
namespace app\user\controller\get;
use app\common\api\user\IGet;
use think\Db;
use app\user\validate\GetVal;#验证规则
use app\user\validate\ValidateFun;#验证方法

class Get implements IGet
{

    //获取用户详细信息参数
    private $info = [
        'uid' => 0
    ];

    //获取列表参数
    private $list = [
        'curr'    => 1,#当前页
        'limits'   => 10,#每页显示数量
        'sort'    => 1,#排序
        'keyword' => '',#搜索关键字
    ];

    public function __construct(array $config = []){
        $this->info = array_merge($this->info,$config);
        $this->list = array_merge($this->list,$config);
    }

    /**
     * 获取用户详细信息
     * @return mixed
     */
    public function get_info(){
        //编写公共框架文档
        $data = $this->info;
        $update = new \app\user\model\Member();
        \app\common\controller\Log::addlog(['msg'=>'获取用户详细信息']);#添加系统日志
        return   $update->get_info($data['uid']);
    }

    /**
     * 获取用户列表
     * @return mixed
     */
    public function get_list(){
        $data = $this->list;
        $update = new \app\user\model\Member();
        \app\common\controller\Log::addlog(['msg'=>'获取用户列表']);#添加系统日志
        return   $update->get_user_list($data);
    }

}