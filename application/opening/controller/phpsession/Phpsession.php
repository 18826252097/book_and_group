<?php

/**
 * 批量公共实现类
 * @Author zhouzeken（1454740914@qq.com）
 * @Date 2018/0209
 */
namespace app\opening\controller\phpsession;
use app\common\api\opening\IPhpsession;
use think\Db;
use app\opening\validate\PhpsessionVal;#验证规则
use app\opening\validate\ValidateFun;

class Phpsession implements IPhpsession
{

    //保存session
    private $set = [
        'phpsession' => '',
        'list'       => [],
        'times'      => 86400 #保存的秒数,默认24小时
    ];

    //获取session
    private $get = [
        'phpsession' => ''
    ];

    //删除单个/多个session里的字段
    private $delete = [
        'phpsession' => '',
        'name'       => [] #要删除的字段['aa','bb']，可同时删除单个或多个
    ];

    //清空session
    private $clear = [
        'phpsession' => ''
    ];
    
    public function __construct(array $config = []){
        $this->set = array_merge($this->set,$config);
        $this->get = array_merge($this->get,$config);
        $this->delete = array_merge($this->delete,$config);
        $this->clear = array_merge($this->clear,$config);
    }

    /**
     * 保存session
     * @return mixed
     */
    public function phpsession_set(){
        $data = $this->set;
        $m_phpsession = new \app\opening\model\Phpsession();
        return $m_phpsession->_set($data);
    }

    /**
     * 获取session
     * @return mixed
     */
    public function phpsession_get(){
        $data = $this->get;
        $m_phpsession = new \app\opening\model\Phpsession();
        return $m_phpsession->_get($data['phpsession']);
    }

    /**
     * 删除单个/多个session里的字段
     * @return mixed
     */
    public function phpsession_delete(){
        $data = $this->delete;
        $m_phpsession = new \app\opening\model\Phpsession();
        return $m_phpsession->_delete($data);
    }

    /**
     * 清空所有session
     * @return mixed
     */
    public function phpsession_clear(){
        $data = $this->clear;
        $m_phpsession = new \app\opening\model\Phpsession();
        return $m_phpsession->_clear($data['phpsession']);
    }

}