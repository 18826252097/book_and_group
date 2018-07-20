<?php
/**
 * 角色表model
 * User: zhouzeken
 * Date: 2018/3/7
 * Time: 10:22
 */
namespace app\auth\model;
use think\Db;
use think\Model;
use app\auth\validate\GroupVal;#验证规则
use app\auth\validate\ValidateFun;#验证方法

class AuthGroup extends Model{

    /**
     * 获取列表
     */
    public function _getList($data=[]){
        $no_page = isset($data['no_page'])?intval($data['no_page']):'';
        $curr = isset($data['curr'])?intval($data['curr']):1;
        $limits = isset($data['limits'])?intval($data['limits']):10;
        //排序
        $sort = isset($data['sort'])?intval($data['sort']):1;
        switch ($sort){
            case 1:
                $sort = 'id desc';
                break;
            case 2:
                $sort = 'id asc';
                break;
            case 3:
                $sort = 'sort desc,id desc';
                break;
            case 4:
                $sort = 'sort asc,id desc';
                break;
            default:
                $sort = 'id desc';
                break;
        }

        $keyword = isset($data['keyword'])?$data['keyword']:'';
        $title = isset($data['title'])?$data['title']:'';
        $mtype = isset($data['mtype'])?intval($data['mtype']):'';
        $status = isset($data['status'])?intval($data['status']):'';
        $content = isset($data['content'])?trim($data['content']):'';

        //搜索条件
        $where = 'del=1 and shows=1';
        $where .= $keyword?' and CONCAT(title,content) like "%'.$keyword.'%"':'';
        $where .= $title?' and title like "%'.$title.'%"':'';
        $where .= $mtype?' and mtype='.$mtype:'';
        $where .= $status?' and status='.$status:'';
        $where .= $content?' and content like "%'.$content.'%"':'';

        //返回字段
        $field = 'id,no_edit,title,mtype,content,rules,sort,status';
        switch ($no_page){
            case 1:
                //关闭分页
                $list  = $this->field($field)->where($where)->order($sort)->select();
                foreach ($list as $k=>$v){
                    $list[$k]['rules_cnn'] = $this->_tfRules($v['rules']);
                    $list[$k]['status_cnn'] = $this->_tfStatus($v['status']);
                    $list[$k]['mtype_cnn'] = $this->_tfMtype($v['mtype']);
                }
                return ['code'=>200,'msg'=>config('msg.200'),'data'=>$list];
                break;
            default:
                //开启分页
                $total = $this->where($where)->count();
                $pages = ceil(intval($total)/$limits);
                if($curr > $pages){
                    $curr = $pages;
                }
                $list  = $this->field($field)->where($where)->page($curr,$limits)->order($sort)->select();
                foreach ($list as $k=>$v){
                    $list[$k]['rules_cnn'] = $this->_tfRules($v['rules']);
                    $list[$k]['status_cnn'] = $this->_tfStatus($v['status']);
                    $list[$k]['mtype_cnn'] = $this->_tfMtype($v['mtype']);
                }
                $result = [
                    'list'     => $list,
                    'total'    => $total,
                    'curr'     => $curr,
                    'limits'   => $limits,
                    'pages'    => $pages
                ];
                return ['code'=>200,'msg'=>config('msg.200'),'data'=>$result];
                break;
        }
    }

    /**
     * 获取单个信息
     */
    public function _getInfo($id=''){
        $id = intval($id);
        if(empty($id)){
            return ['code'=>10062,'msg'=>config('msg.10062')];
        }
        $field = 'id,no_edit,title,mtype,content,rules,sort,status';
        $info = $this->field($field)->where('del=1 and id='.$id)->find();
        if(!$info){
            return ['code'=>10005,'msg'=>config('msg.10005')];
        }
        $info['rules_cnn'] = $this->_tfRules($info['rules']);
        $info['status_cnn'] = $this->_tfStatus($info['status']);
        $info['mtype_cnn'] = $this->_tfMtype($info['mtype']);
        return ['code'=>200,'msg'=>config('msg.200'),'data'=>$info];
    }

    /**
     * 根据条件获取单个信息
     */
    public function _getWinfo($where='id=0'){

    }

    /**
     * 添加数据
     */
    public function _addinfo($data=[]){
        //规则验证
        $vali_data = GroupVal::add();
        $validate = new ValidateFun($vali_data['rule'], $vali_data['message']);#实例化验证类
        if(!$validate->check($data)){
            $err = $validate->getError();
            return ['code'=>$err,'msg'=>config('msg.'.$err)];
        }

        $arr = [
            'title'         => trim($data['title']),
            'mtype'         => isset($data['mtype'])?intval($data['mtype']):1,
            'content'       => isset($data['content'])?$data['content']:'',
            'status'        => isset($data['status'])?intval($data['status']):1,
            'sort'          => isset($data['sort'])?intval($data['sort']):0,
            'update_time'   => time()
        ];
        //权限规则处理
        $rules = isset($data['rules'])?strMtions($data['rules']):'';
        $rulearr = explode(',',$rules);
        $rulestr = implode(',',array_unique($rulearr));#去重

        $arr['rules'] = $rulestr;
        $id = $this->insertGetId($arr);
        if($id > 0){
            //添加成功
            return ['code'=>200,'msg'=>config('msg.200'),'data'=>['id'=>$id]];
        }else{
            //添加失败
            return ['code'=>10006,'msg'=>config('msg.10006')];
        }


    }

    /**
     * 更新数据
     */
    public function _editInfo($data=[]){
        //规则验证
        $vali_data = GroupVal::edit();
        $validate = new ValidateFun($vali_data['rule'], $vali_data['message']);#实例化验证类
        if(!$validate->check($data)){
            $err = $validate->getError();
            return ['code'=>$err,'msg'=>config('msg.'.$err)];
        }
        $id = intval($data['id']);
        $info = $this->field('id,no_edit')->where('del=1 and id='.$id)->find();
        if(empty($info['id'])){
            return ['code'=>10005,'msg'=>config('msg.10005')];#数据不存在
        }
        if($info['no_edit'] == 2){
            return ['code'=>10064,'msg'=>config('msg.10064')];#不可编辑
        }

        $arr = [
            'title'         => isset($data['title'])?trim($data['title']):null,
            'mtype'         => isset($data['mtype'])?intval($data['mtype']):null,
            'content'       => isset($data['content'])?trim($data['content']):null,
            'status'        => isset($data['status'])?intval($data['status']):null,
            'sort'          => isset($data['sort'])?intval($data['sort']):null,
            'update_time'   => time()
        ];
        //权限规则处理
        $rules = isset($data['rules'])?strMtions($data['rules']):null;
        if($rules !== null){
            $rulearr = explode(',',$rules);
            $rulestr = implode(',',array_unique($rulearr));#去重
            $arr['rules'] = $rulestr?$rulestr:'';
        }
        $arr = eliminateArrNull($arr);#剔除元素为null的
        $this->where('id',$id)->update($arr);
        return ['code'=>200,'msg'=>config('msg.200')];
    }

    /**
     * 删除数据
     * @param string $ids id字符串，格式：1,2,3
     */
    public function _delArr($ids=''){
        $ids = strMtions($ids);#把不符合规则的剔除
        if(empty($ids)){
            return ['code'=>10010,'msg'=>config('msg.10010')];
        }

        $idarr = explode(',',$ids);
        //检测哪些不能删除的，剔除出来
        foreach ($idarr as $k=>$v){
            $ifedit = '';
            $ifedit = $this->_checkEdit($v);
            if($ifedit == false){
                unset($idarr[$k]);
            }
        }

        $idstr = implode(',',array_unique($idarr));#去重
        if(!empty($idstr)){
            $this->where('id in('.$idstr.')')->update(['del'=>2]);
        }

        return ['code'=>200,'msg' => config('msg.200')];
    }

    /**
     * 检测能否操作
     * @param int $id 角色id
     * @return bool
     */
    public function _checkEdit($id=''){
        if(!empty($id)){
            $no_edit = $this->where('id',$id)->value('no_edit');
            if(empty($no_edit)){
                return false;
            }elseif($no_edit == 2){
                return false;
            }
            return true;
        }
        return false;
    }

    //转换角色类型
    public function _tfMtype($type=0){
        switch ($type){
            case 1:
                $str = '管理员';
                break;
            case 2:
                $str = '普通用户';
                break;
            default:
                $str = '未知';
                break;
        }
        return $str;
    }

    //转换状态
    public function _tfStatus($type=0){
        switch ($type){
            case 1:
                $str = '启用';
                break;
            case 2:
                $str = '禁用';
                break;
            default:
                $str = '未知';
                break;
        }
        return $str;
    }

    /**
     * 转换权限为中文
     * @param $rules string 权限id 格式:1,2,3
     */
    public function _tfRules($rules=''){
        $rules = strMtions($rules);
        $idarr = explode(',',$rules);
        $idstr = implode(',',array_unique($idarr));#去重
        $res = [];
        if(!empty($idstr)){
            $list = Db::name('auth_rule')->field('title')->where('id in('.$idstr.')')->select();
            foreach ($list as $v){
                $res[] = $v['title'];
            }
        }
        $str = implode('、',$res);
        return $str?$str:'';
    }


}
