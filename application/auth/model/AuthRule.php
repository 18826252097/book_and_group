<?php
/**
 * 权限表model
 * User: zhouzeken
 * Date: 2018/3/7
 * Time: 10:22
 */
namespace app\auth\model;
use think\Db;
use think\Model;
use app\auth\validate\RuleVal;#验证规则
use app\auth\validate\ValidateFun;#验证方法

class AuthRule extends Model{

    /**
     * 获取列表
     * @param int $is_top 是否获取顶级，传1获取顶级
     * @param int $no_page 关闭分页 传1关闭
     * @param int $curr 当前页
     * @param int $limits 每页显示数量
     * @param string $keyword 关键字，可匹配多个字段
     */
    public function _getList($data=[]){
        $is_top = isset($data['is_top'])?intval($data['is_top']):'';
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
        $name = isset($data['name'])?$data['name']:'';
        $title = isset($data['title'])?$data['title']:'';
        $pid = isset($data['pid'])?intval($data['pid']):0;
        $type = isset($data['type'])?intval($data['type']):'';
        $status = isset($data['status'])?intval($data['status']):'';

        //搜索条件
        $where = 'del=1 and shows=1';
        $where .= $keyword?' and CONCAT(name,title) like "%'.$keyword.'%"':'';
        $where .= $name?' and name like "%'.$name.'%"':'';
        $where .= $title?' and title like "%'.$title.'%"':'';
        if($is_top == 1){
            $where .= ' and pid=0';
        }else{
            $where .= $pid?' and pid='.$pid:'';
        }
        $where .= $type?' and type='.$type:'';
        $where .= $status?' and status='.$status:'';

        //返回字段
        $field = 'id,name,title,pid,auth_path,level,icon,type,status,sort,desc';
        switch ($no_page){
            case 1:
                //关闭分页
                $list  = $this->field($field)->where($where)->order($sort)->select();
                foreach ($list as $k=>$v){
                    $list[$k]['status_cnn'] = $this->_tfStatus($v['status']);
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
                    $list[$k]['status_cnn'] = $this->_tfStatus($v['status']);
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
            return ['code'=>10062,'msg'=>config('msg.10062')];#ID不能为空
        }
        $field = 'id,name,title,pid,auth_path,level,icon,type,status,sort,desc';
        $info = $this->field($field)->where('del=1 and id='.$id)->find();
        if(!$info){
            return ['code'=>10005,'msg'=>config('msg.10005')];#数据不存在
        }
        $info['status_cnn'] = $this->_tfStatus($info['status']);
        return ['code'=>200,'msg'=>config('msg.200'),'data'=>$info];
    }


    /**
     * 添加数据
     */
    public function _addinfo($data=[]){
        //规则验证
        $vali_data = RuleVal::add();
        $validate = new ValidateFun($vali_data['rule'], $vali_data['message']);#实例化验证类
        if(!$validate->check($data)){
            $err = $validate->getError();
            return ['code'=>$err,'msg'=>config('msg.'.$err)];
        }

        $arr = [
            'title'         => trim($data['title']),
            'name'          => trim($data['name']),
            'pid'           => intval($data['pid']),
            'icon'          => isset($data['icon'])?trim($data['icon']):'',
            'desc'          => isset($data['desc'])?trim($data['desc']):'',
            'status'        => isset($data['status'])?intval($data['status']):1,
            'sort'          => isset($data['sort'])?intval($data['sort']):0,
            'update_time'   => time()
        ];

        $id = $this->insertGetId($arr);
        if($id > 0){
            //添加成功
            $pinfo = $this->find($arr['pid']);
            if(!$pinfo){
                $nData = [
                    'pid'       => 0,
                    'auth_path' => $id,
                    'level'     => 1
                ];
            }else{
                $nData = [
                    'auth_path'  => $pinfo['auth_path'].'-'.$id,
                    'level'      => $pinfo['level']+1
                ];
            }
            $this->where('id',$id)->update($nData);

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
        $vali_data = RuleVal::edit();
        $validate = new ValidateFun($vali_data['rule'], $vali_data['message']);#实例化验证类
        if(!$validate->check($data)){
            $err = $validate->getError();
            return ['code'=>$err,'msg'=>config('msg.'.$err)];
        }
        $id = intval($data['id']);

        $arr = [
            'title'         => isset($data['title'])?trim($data['title']):null,
            'name'          => isset($data['name'])?trim($data['name']):null,
            'pid'           => isset($data['pid'])?intval($data['pid']):null,
            'icon'          => isset($data['icon'])?trim($data['icon']):null,
            'desc'          => isset($data['desc'])?trim($data['desc']):null,
            'status'        => isset($data['status'])?intval($data['status']):null,
            'sort'          => isset($data['sort'])?intval($data['sort']):null,
            'update_time'   => time()
        ];

        if($arr['pid']){
            $pinfo = $this->find($arr['pid']);
            if(!$pinfo){
                $arr['pid'] = 0;
                $arr['auth_path'] = $id;
                $arr['level'] = 1;
            }else{
                $arr['auth_path'] = $pinfo['auth_path'].'-'.$id;
                $arr['level'] = $pinfo['level']+1;
            }
        }
        $arr = eliminateArrNull($arr);#把数组元素值为null的剔除
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
            return ['code'=>10010,'msg'=>config('msg.10010')];#缺少参数
        }

        $idarr = explode(',',$ids);
        $idstr = implode(',',array_unique($idarr));#去重
        if(!empty($idstr)){
            $this->where('id in('.$idstr.')')->update(['del'=>2]);
        }

        return ['code'=>200,'msg' => config('msg.200')];
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

}
