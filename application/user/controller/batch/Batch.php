<?php

/**
 * 批量公共实现类，默认类
 * @Author zhouzeken（1454740914@qq.com）
 * @Date 2018/0209
 */
namespace app\user\controller\batch;
use app\common\api\user\IBatch;
use think\Db;
use app\user\validate\BatchVal;#验证规则
use app\user\validate\ValidateFun;#验证方法

class Batch implements IBatch
{

    //导入
    private $import = [
        'file_path'=>'' #Excel本地文件路径
    ];

    //导出
    private $export = [
        #搜索条件
        'map' => []
    ];

    public function __construct(array $config = []){
        $this->import = array_merge($this->import,$config);
        $this->export = array_merge($this->export,$config);
    }

    /**
     * 批量导入
     * @return mixed
     */
    public function import(){
        $data = $this->import;
        $file_path = $data['file_path'];
        if(empty($file_path)){
            return ['code'=>10010,'msg'=>config('msg.10010')];
        }


        //#获取excel表数据
        $obj = new \app\common\controller\Export(['file_path'=>$file_path]);
        $res = $obj->import();
        #得到数组
        $list = isset($res['list'][1])?$res['list'][1]:[];
        $m_member = new \app\user\model\Member();#批量导入
        \app\common\controller\Log::addlog(['msg'=>'批量导入用户数据']);#添加系统日志
        return $m_member->_daoruDefault(['list'=>$list]);
    }

    /**
     * ps:此方法是get方式请求
     * 批量导出
     * @return mixed
     */
    public function export(){
        $data = $this->export;
        #设置导出表头
        $table_hea = ['账号','状态','真实姓名','昵称','头像','性别','生日','简介','邮箱','手机','微信号','qq号','msn','省份','所在市','所在区','详细地址','备注'];
        
        $m_member = new \app\user\model\Member();
        $res = $m_member->_getDaochuList($data['map']);
        if($res['code'] != 200){
            echo json_encode($res);exit;
        }
        $list = [];
        foreach ($res['data'] as $k=>$v){
            $list[$k]['username'] = $v['username'];
            $list[$k]['status_cnn'] = $v['status_cnn'];
            $list[$k]['realname'] = $v['realname'];
            $list[$k]['nickname'] = $v['nickname'];
            $list[$k]['icon'] = $v['icon'];
            $list[$k]['sex_cnn'] = $v['sex_cnn'];
            $list[$k]['birthday'] = date('Y/m/d',$v['birthday']);
            $list[$k]['content'] = $v['content'];
            $list[$k]['email'] = $v['email'];
            $list[$k]['phone'] = $v['phone'];
            $list[$k]['wechat'] = $v['wechat'];
            $list[$k]['tencent'] = $v['tencent'];
            $list[$k]['msn'] = $v['msn'];
            $list[$k]['province_cnn'] = $v['province_cnn'];
            $list[$k]['city_cnn'] = $v['city_cnn'];
            $list[$k]['district_cnn'] = $v['district_cnn'];
            $list[$k]['address'] = $v['address'];#详细地址
            $list[$k]['remark'] = $v['remark'];#备注
        }
        //#导出excel表数据
        $obj = new \app\common\controller\Export(['table_data'=>$list,'table_header'=>$table_hea]);
        $obj->export();
    }
}