<?php

namespace app\user\model;
use think\Db;
use think\Model;
use app\user\validate\BatchVal;#验证规则类
use app\user\validate\ValidateFun;#验证方法类
class Member extends Model{

    /**
     * 根据条件查询单条用户
     * @param string $where
     * @return array
     */
    public function _getWinfo($where='m.id=0'){
        $where = $where?$where:'m.id=0';
        $field = 'm.id,m.username,m.password,m.encrypt,m.create_time,m.status,m.sort';
        $field .= ',aga.group_id';
        $field .= ',mc.realname,mc.nickname,mc.icon,mc.sex,mc.birthday,mc.content,mc.email,mc.phone,mc.wechat,mc.tencent,mc.msn,mc.province_id,mc.city_id,mc.district_id,mc.address,mc.remark';
        $field .= ',mtp.third_id,mtp.openid,mtp.content as mtp_content';
        $info = $this->alias('m')->field('m.id,aga.group_id')
            ->join('auth_group_access aga','aga.uid=m.id','LEFT')
            ->join('member_content mc','mc.uid=m.id','LEFT')
            ->join('member_third_party mtp','mtp.uid=m.id','LEFT')
            ->where($where)->find();
        if(empty($info['id'])){
            return ['code'=>10034,'msg'=>config('msg.10034')];#用户不存在
        }

        //此处可以判断类型，返回不一样的字段
        switch ($info['group_id']){
            default:
                $arr = $this->alias('m')->field($field)
                    ->join('auth_group_access aga','aga.uid=m.id','LEFT')
                    ->join('member_content mc','mc.uid=m.id','LEFT')
                    ->join('member_third_party mtp','mtp.uid=m.id','LEFT')
                    ->where($where)->find();
                break;
        }

        $arr['group_cnn'] = $this->_tfGroup($arr['group_id']);
        $arr['mtp_content'] = unserialize($arr['mtp_content']);
        $arr['status_cnn'] = $this->_tfStatus($arr['status']);
        $arr['sex_cnn'] = $this->_tfSex($arr['sex']);
        $arr['province_cnn'] = $this->_tfArea($arr['province_id']);
        $arr['city_cnn'] = $this->_tfArea($arr['city_id']);
        $arr['district_cnn'] = $this->_tfArea($arr['district_id']);
        //判断账号状态
        switch ($arr['status']){
            case 1:
                return ['code'=>200,'msg'=>config('msg.200'),'data'=>$arr];
                break;
            case 2:
                return ['code'=>10032,'msg'=>config('msg.10032')];#禁用
                break;
            case 3:
                return ['code'=>10033,'msg'=>config('msg.10033')];#未激活
                break;
            default:
                return ['code'=>10056,'msg'=>config('msg.10056')];#账号异常
                break;
        }
    }

    //获取用户详细信息
    public function get_info($uid=''){
        $uid = intval($uid);
        if(empty($uid)){
            return ['code'=>10010,'msg'=>config('msg.10010')];#缺少参数
        }
        $info = $this->alias('m')->field('m.id,aga.group_id')
            ->join('auth_group_access aga','aga.uid=m.id','LEFT')
            ->where('m.id='.$uid)->find();
        if(empty($info['id'])){
            return ['code'=>10034,'msg'=>config('msg.10034')];#用户不存在
        }
        $where['m.id'] = $uid;
        $field = 'm.id,m.username,m.password,m.encrypt,m.create_time,m.status,m.sort';
        $field .= ',aga.group_id';
        $field .= ',mc.realname,mc.nickname,mc.icon,mc.sex,mc.birthday,mc.content,mc.email,mc.phone,mc.wechat,mc.tencent,mc.msn,mc.province_id,mc.city_id,mc.district_id,mc.address,mc.remark';

        //根据身份类型查询不同数据
        switch ($info['group_id']){
            default:
                //默认基本信息
                $arr = $this->alias('m')
                    ->join('auth_group_access aga','aga.uid=m.id','LEFT')
                    ->join('member_content mc','m.id=mc.uid','LEFT')
                    ->where($where)
                    ->field($field)
                    ->find();
                break;
        }

        $arr['group_cnn'] = $this->_tfGroup($arr['group_id']);
        $arr['status_cnn'] = $this->_tfStatus($arr['status']);
        $arr['sex_cnn'] = $this->_tfSex($arr['sex']);
        $arr['province_cnn'] = $this->_tfArea($arr['province_id']);
        $arr['city_cnn'] = $this->_tfArea($arr['city_id']);
        $arr['district_cnn'] = $this->_tfArea($arr['district_id']);

        return ['code'=>200,'msg'=>config('msg.200'),'data'=>$arr];
    }

    //获取列表
    public function get_user_list($data=[]){
        $curr = isset($data['curr'])?intval($data['curr']):1;#当前页
        $limit = isset($data['limits'])?intval($data['limits']):10;#每页显示数量
        $sort = isset($data['sort'])?intval($data['sort']):1;  //排序
        switch ($sort){
            case 1:
                $sort = 'm.create_time desc,m.id desc';
                break;
            case 2:
                $sort = 'm.create_time asc,m.id asc';
                break;
            case 3:
                $sort = 'm.sort desc,m.id desc';
                break;
            case 4:
                $sort = 'm.sort asc,m.id desc';
                break;
        }

        //查询条件
        $keyword = isset($data['keyword'])?$data['keyword']:'';
        $username = isset($data['username'])?$data['username']:'';
        $status = isset($data['status'])?intval($data['status']):'';
        $realname = isset($data['realname'])?$data['realname']:'';
        $nickname = isset($data['nickname'])?$data['nickname']:'';
        $sex = isset($data['sex'])?intval($data['sex']):'';
        $birthday = isset($data['birthday'])?$data['birthday']:'';
        if(!empty($birthday)){
            $birthday = strtotime($birthday);
            $birthday = date('Ymd',$birthday);
        }
        $start_birthday = isset($data['start_birthday'])?$data['start_birthday']:'';#生日日期范围搜索-开始
        $end_birthday = isset($data['end_birthday'])?$data['end_birthday']:'';#生日日期范围搜索-结束

        $email = isset($data['email'])?$data['email']:'';
        $phone = isset($data['phone'])?$data['phone']:'';

        $wechat = isset($data['wechat'])?$data['wechat']:'';
        $tencent = isset($data['tencent'])?intval($data['tencent']):'';
        $msn = isset($data['msn'])?intval($data['msn']):'';
        $province_id = isset($data['province_id'])?intval($data['province_id']):'';
        $city_id = isset($data['city_id'])?intval($data['city_id']):'';
        $district_id = isset($data['district_id'])?intval($data['district_id']):'';
        $address = isset($data['address'])?$data['address']:'';
        $remark = isset($data['remark'])?$data['remark']:'';
        $where = '1=1 and m.del=1';
        $where .= $keyword?' and CONCAT(m.username,mc.realname,mc.nickname,mc.email,mc.phone,mc.address,mc.remark) like "%'.$keyword.'%"':'';
        $where .= $username?' and m.username like "%'.$username.'%"':'';
        $where .= $status?' and m.status="'.$status.'"':'';
        $where .= $realname?' and mc.realname like "%'.$realname.'%"':'';
        $where .= $nickname?' and mc.nickname like "%'.$nickname.'%"':'';
        $where .= $sex?' and mc.sex="'.$sex.'"':'';

        $where .= $birthday?' and date_format(from_unixtime(mc.birthday),"%Y%m%d")="'.$birthday.'"':'';

        if(empty($birthday)){
            if($start_birthday && empty($end_birthday)){
                $where .= ' and mc.birthday>="'.strtotime($start_birthday).'"';
            }
            if($end_birthday && empty($start_birthday)){
                $where .= ' and mc.birthday<="'.strtotime($end_birthday).'"';
            }
            if($start_birthday && $end_birthday){
                $where .= ' and mc.birthday between "'.strtotime($start_birthday).'" and "'.strtotime($end_birthday).'"';
            }
        }

        $where .= $email?' and mc.email like "%'.$email.'%"':'';
        $where .= $phone?' and mc.phone like "%'.$phone.'%"':'';
        $where .= $wechat?' and mc.wechat like "%'.$wechat.'%"':'';
        $where .= $tencent?' and mc.tencent like "%'.$tencent.'%"':'';
        $where .= $msn?' and mc.msn like "%'.$msn.'%"':'';
        $where .= $province_id?' and mc.province_id="'.$province_id.'"':'';
        $where .= $city_id?' and mc.city_id="'.$city_id.'"':'';
        $where .= $district_id?' and mc.district_id="'.$district_id.'"':'';
        $where .= $address?' and mc.address like "%'.$address.'%"':'';
        $where .= $remark?' and mc.remark like "%'.$remark.'%"':'';

        $field = 'm.id,m.username,m.status';#member主表字段
        $field .= ',aga.group_id';
        $field .= ',mc.realname,mc.nickname,mc.icon,mc.sex,mc.birthday,mc.content,mc.email,mc.phone,mc.wechat,mc.tencent,mc.msn,mc.province_id,mc.city_id,mc.district_id,mc.address,mc.remark';#member_content附表字段

        $total = $this->alias('m')
            ->join('auth_group_access aga','aga.uid=m.id','LEFT')
            ->join('member_content mc','mc.uid=m.id','LEFT')
            ->where($where)
            ->count();
        $list = $this->alias('m')
            ->join('auth_group_access aga','aga.uid=m.id','LEFT')
            ->join('member_content mc','mc.uid=m.id','LEFT')
            ->field($field)
            ->where($where)
            ->page($curr,$limit)
            ->order($sort)
            ->select();
        foreach ($list as $k=>$v){
            $list[$k]['group_cnn'] = $this->_tfGroup($v['group_id']);
            $list[$k]['status_cnn'] = $this->_tfStatus($v['status']);
            $list[$k]['sex_cnn'] = $this->_tfSex($v['sex']);
            $list[$k]['province_cnn'] = $this->_tfArea($v['province_id']);
            $list[$k]['city_cnn'] = $this->_tfArea($v['city_id']);
            $list[$k]['district_cnn'] = $this->_tfArea($v['district_id']);
        }


        $result = [
            'list'  =>  $list,
            'total' =>  intval($total),
            'curr'  =>  intval($curr),
            'limits' =>  intval($limit),
            'pages' => ceil(intval($total)/intval($limit))  //获取分页总数
        ];
        return ['code'=>200,'msg'=>config('msg.200'),'data'=>$result];
    }

    //修改基本信息
    public function update_info($data=[]){
        if(empty($data['uid'])){
            return ['code'=>10010,'msg'=>config('msg.10010')];#缺少参数
        }
        $uid = intval($data['uid']);
        $info_id = $this->where('id='.$uid)->value('id');
        if(empty($info_id)){
            return ['code'=>10034,'msg'=>config('msg.10034')];#用户不存在
        }
        $vali_data =\app\user\validate\Update::info(); #获取验证规则
        $validate = new ValidateFun($vali_data['rule'], $vali_data['message']);#实例化验证类
        if(!$validate->check($data)){
            return ['code'=>$validate->getError(),'msg'=>config('msg.'.$validate->getError())];
        }

        $m_member_content = Db::name('member_content');
        #验证邮箱、手机号是否被使用
        $email = isset($data['mc_email'])?$data['mc_email']:null;
        $phone = isset($data['mc_phone'])?$data['mc_phone']:null;
        if($email){
            $email_info = $m_member_content->field('email')->where('uid!='.$uid.' and email="'.$email.'"')->find();
            if($email_info['email']){
                return ['code'=>10036,'msg'=>config('msg.10036')];
            }
        }
        if($phone){
            $phone_info = $m_member_content->field('phone')->where('uid!='.$uid.' and phone="'.$phone.'"')->find();
            if($phone_info['phone']){
                return ['code'=>10037,'msg'=>config('msg.10037')];
            }
        }

        /**
         * member主表
         */
        $member_data = [
            'sort'         => isset($data['m_sort'])?intval($data['m_sort']):null,
            'status'       => isset($data['m_status'])?intval($data['m_status']):null,
        ];
        $member_data = eliminateArrNull($member_data);#把数组元素为null的剔除
        if(!empty($member_data)){
            $this->where('id='.$uid)->update($member_data);
        }

        /**
         * member_content表
         */
        $member_content_data = [
            'realname'   => isset($data['mc_realname'])?$data['mc_realname']:null,
            'nickname'   => isset($data['mc_nickname'])?$data['mc_nickname']:null,
            'icon'       => isset($data['mc_icon'])?$data['mc_icon']:null,
            'sex'        => isset($data['mc_sex'])?intval($data['mc_sex']):null,
            'birthday'   => isset($data['mc_birthday'])?strtotime($data['mc_birthday']):null,
            'content'    => isset($data['mc_content'])?$data['mc_content']:null,
            'email'      => $email,
            'phone'      => $phone,
            'wechat'     => isset($data['mc_wechat'])?$data['mc_wechat']:null,
            'tencent'    => isset($data['mc_tencent'])?$data['mc_tencent']:null,
            'msn'        => isset($data['mc_msn'])?intval($data['mc_msn']):null,
            'province_id'=> isset($data['mc_province_id'])?intval($data['mc_province_id']):null,
            'city_id'    => isset($data['mc_city_id'])?intval($data['mc_city_id']):null,
            'district_id'=> isset($data['mc_district_id'])?intval($data['mc_district_id']):null,
            'address'    => isset($data['mc_address'])?$data['mc_address']:null,
            'remark'     => isset($data['mc_remark'])?$data['mc_remark']:null,
        ];
        $member_content_data = eliminateArrNull($member_content_data);#把数组元素为null的剔除
        if(!empty($member_content_data)){
            $m_member_content->where('uid='.$uid)->update($member_content_data);
        }

        return ['code'=>200,'msg'=>config('msg.200')];
    }

    /**
     * 修改密码
     * @param int $data['uid'] 用户id
     * @param string $data['old_passwd'] 旧密码
     * @param string $data['new_passwd'] 新密码
     * @return array
     */
    public function update_pwd($data=[]){
        $uid = isset($data['uid'])?intval($data['uid']):0;
        $old_passwd = isset($data['old_passwd'])?$data['old_passwd']:'';#旧密码
        $new_passwd = isset($data['new_passwd'])?$data['new_passwd']:'';#新密码
        if(empty($uid) || empty($old_passwd) || empty($new_passwd)){
            return ['code'=>10010,'msg'=>config('msg.10010')];#缺少参数
        }
        $info = $this->field('id,username,password,encrypt')->where('del=1 and id='.$uid)->find();
        if(empty($info['id'])){
            return ['code'=>10034,'msg'=>config('msg.10034')];#用户不存在
        }
        $old_passwd = getMd5($old_passwd,$info['username'],$info['encrypt']);
        if($old_passwd != $info['password']){
            return ['code'=>10057,'msg'=>config('msg.10057')];#旧密码错误
        }

        $new_passwd = getMd5($new_passwd,$info['username'],$info['encrypt']);
        $flg = $this->where('id',$uid)->update(['password'=>$new_passwd,'update_time'=>time()]);
        if($flg > 0){
            return ['code'=>200,'msg'=>config('msg.200')];#成功
        }else{
            return ['code'=>10007,'msg'=>config('msg.10007')];#数据更新失败
        }
    }

    //找回密码
    public function back_pwd($data=[]){
        $code = session('code');
//        $code =1234;
        if(empty($data['code'])||$code!=$data['code']){
            return ['code'=>10045,'msg'=>config('msg.10045')];
        }
        $arr = Db::name('member')->alias('m')->join('member_content mc','m.id=mc.uid')->where('phone',$data['phone'])
            ->field('m.id,username,password,encrypt')->find();
        if(empty($arr)){
            return ['code'=>10034,'msg'=>config('msg.10034')];
        }
        $passwd=getMd5($data['password'],$arr['username'],$arr['encrypt']);  //加密
        $res = Db::name('member')->where('id',$arr['id'])->update(['password'=>$passwd]);
        if($res){
            return ['code'=>200,'msg'=>config('msg.200')];
        }else{
            return ['code'=>10007,'msg'=>config('msg.10007')];
        }
    }

    //删除一个或多个用户
    public function delUser($ids=[],$del=2){
        $ids = strMtions($ids);
        if(empty($ids)){
            return ['code'=>10010,'msg' => config('msg.10010')];
        }
        $idarr = explode(',',$ids);
        $idstr = implode(',',array_unique($idarr));
        if(!empty($idstr)){
            $this->where('id in('.$idstr.')')->update(['del'=>$del]);
        }

        return ['code'=>200,'msg' => config('msg.200')];
    }

    //判断密码是否正确
    public function checkUserPwd($data){
        $password = getMd5($data['password'],$data['username'],$data['encrypt']);
        $check_where['username'] = $data['username'];
        $check_where['password'] = $password;
        $this_user_data = db('member')->where($check_where)->field('id')->find();
        if(empty($this_user_data['id'])){
            return false;
        }else{
            return true;
        }
    }


    /**
     * 批量导入
     * @param array $data 批量导入的用户数组
     */
    public function _daoruDefault(array $data){
        $list = isset($data['list'])?$data['list']:[];
        if(!is_array($list) || count($list) == 0 || empty($list)){
            return ['code'=>10048,'msg'=>config('msg.10048')];#导入数据错误
        }

        #判断模板是否正确
        $hea = $list[1];
        if($hea['A'] != '账号'
            || $hea['B'] != '手机号'
            || $hea['C'] != '邮箱'
            || $hea['D'] != '真实姓名'
            || $hea['E'] != '昵称'
            || $hea['F'] != '头像地址'
            || $hea['G'] != '性别'
            || $hea['H'] != '生日'
            || $hea['I'] != '简介'
            || $hea['J'] != '微信号'
            || $hea['K'] != 'qq号'
            || $hea['L'] != 'msn'
            || $hea['M'] != '备注'){
            return ['code'=>10051,'msg'=>config('msg.10051')];
        }

        $m_group_access = Db::name('auth_group_access');#用户-角色关联表
        $m_member_content = Db::name('member_content');#member_content表
        $m_member_third_party = Db::name('member_third_party');#member_third_party第三方表

        //打开锁文件
        $fp = fopen('./public/lock/add_user.lock','r');
        flock($fp,LOCK_SH);

        //启动事务
        $err_msg = '';
        $err_no = true;
        Db::startTrans();

        $excel_list = array_splice($list,2);#把excel得到的数据，从第3行开始读起
        if(count($excel_list) == 0 || empty($excel_list)){
            return ['code'=>10048,'msg'=>config('msg.10048')];#导入数据错误
        }

        #重新组合数组
        $new_list = [];
        foreach ($excel_list as $k=>$v){
            $new_list[$k]['username'] = $v['A'];
            $new_list[$k]['phone'] = $v['B'];
            $new_list[$k]['email'] = $v['C'];
            $new_list[$k]['realname'] = $v['D'];
            $new_list[$k]['nickname'] = $v['E'];
            $new_list[$k]['icon'] = $v['F'];
            $new_list[$k]['sex'] = $this->_tfCnnSex($v['G']);
            $new_list[$k]['birthday'] = $v['H'];
            $new_list[$k]['content'] = $v['I'];
            $new_list[$k]['wechat'] = $v['J'];
            $new_list[$k]['tencent'] = $v['K'];
            $new_list[$k]['msn'] = $v['L'];
            $new_list[$k]['remark'] = $v['M'];
        }

        foreach ($new_list as $k=>$v){
            $Knum = '';
            $Knum = $k+1;

            $username = '';
            $username = isset($v['username'])?$v['username']:'';
            $email = '';
            $email = isset($v['email'])?$v['email']:'';
            $phone = '';
            $phone = isset($v['phone'])?$v['phone']:'';
            $sex = '';
            $sex = isset($v['sex'])?$v['sex']:'未知';


            #验证username，email，phone字段是否正确
            $vali_data = BatchVal::importCheck();
            $validate = new ValidateFun($vali_data['rule'], $vali_data['message']);#实例化验证类
            if(!$validate->check(['username'=>$username,'email'=>$email,'phone'=>$phone])){
                $err_no = false;
                $err_msg = "发生错误，错误位置为第".$Knum."条、".config('msg.'.$validate->getError());
                break;
            }

            /**
             * member主表数据
             */
            $member_arr = [];

            $member_arr['username'] = $username;
            $create_pwd = '';
            $create_pwd = createPwd($username);#生成默认密码
            $member_arr['password'] = $create_pwd['password'];
            $member_arr['encrypt'] = $create_pwd['encrypt'];
            $member_arr['create_time'] = time();
            $member_flg = $this->insertGetId($member_arr);#添加主表数据
            if($member_flg > 0){
                $err_msg = '导入成功，导入数量:'.$Knum.'条';

                /**
                 * 角色-用户管理表
                 */
                $group_access_arr = [];
                $group_access_arr = [
                    'uid'        => $member_flg,
                    //'group_id'   => $v['group_id']?$v['group_id']:0
                ];
                $group_access_flg = $m_group_access->insertGetId($group_access_arr);#添加附表数据
                if($group_access_flg <= 0){
                    $err_no = false;
                    $err_msg = "发生错误，错误位置为第".$Knum."条、".config('msg.10047');
                    break;
                }

                /**
                 * member_content信息表数据
                 */
                $content_arr = [];
                $content_arr = [
                    'uid'               => $member_flg,
                    'realname'          => isset($v['realname'])?$v['realname']:'',
                    'nickname'          => isset($v['nickname'])?$v['nickname']:'',
                    'icon'              => isset($v['icon'])?$v['icon']:config('images.user_default_img'),
                    'sex'               => $v['sex'],
                    'birthday'          => isset($v['birthday'])?verDates($v['birthday']):0,
                    'content'           => isset($v['content'])?$v['content']:'',
                    'email'             => $email,
                    'phone'             => $phone,
                    'wechat'            => isset($v['wechat'])?$v['wechat']:'',
                    'tencent'           => isset($v['tencent'])?$v['tencent']:'',
                    'msn'               => isset($v['msn'])?$v['msn']:'',
                    'province_id'       => isset($v['province_id'])?intval($v['province_id']):0,
                    'city_id'           => isset($v['city_id'])?intval($v['city_id']):0,
                    'district_id'       => isset($v['district_id'])?intval($v['district_id']):0,
                    'address'           => isset($v['address'])?$v['address']:'',
                    'remark'            => isset($v['remark'])?$v['remark']:'',
                ];
                $content_flg = $m_member_content->insertGetId($content_arr);#添加附表数据
                if($content_flg <= 0){
                    $err_no = false;
                    $err_msg = "发生错误，错误位置为第".$Knum."条、".config('msg.10047');
                    break;
                }

                /**
                 * member_third_party第三方信息表
                 */
                $third_party_arr = [];
                $third_party_arr = [
                    'uid'=>$member_flg
                ];
                $third_party_flg = $m_member_third_party->insertGetId($third_party_arr);
                if($third_party_flg <= 0){
                    $err_no = false;
                    $err_msg = "发生错误，错误位置为第".$Knum."条、".config('msg.10047');
                    break;
                }


            }else{
                $err_no = false;
                $err_msg = "发生错误，错误位置为第".$Knum."条、".config('msg.10047');
                break;
            }
        }

        if($err_no == false){
            //发生错误，回滚事务
            Db::rollback();
            //关闭锁
            flock($fp,LOCK_UN);
            fclose($fp);
            return ['code'=>400,'msg'=>$err_msg];
        }else{
            //成功，提交事务
            Db::commit();
            //关闭锁
            flock($fp,LOCK_UN);
            fclose($fp);
            return ['code'=>200,'msg'=>$err_msg];
        }
    }

    /**
     * 根据条件获取数据
     * @param array $map 条件数组
     */
    public function _getDaochuList($map=[]){
        $username = isset($map['username'])?$map['username']:false;
        $status = isset($map['status'])?intval($map['status']):false;
        $realname = isset($map['realname'])?$map['realname']:false;
        $nickname = isset($map['nickname'])?$map['nickname']:false;
        $sex = isset($map['sex'])?intval($map['sex']):false;
        $birthday = isset($map['birthday'])?$map['birthday']:false;
        $email = isset($map['email'])?$map['email']:false;
        $phone = isset($map['phone'])?$map['phone']:false;
        $wechat = isset($map['wechat'])?$map['wechat']:false;
        $tencent = isset($map['tencent'])?intval($map['tencent']):false;
        $msn = isset($map['msn'])?intval($map['msn']):false;
        $province_id = isset($map['province_id'])?intval($map['province_id']):false;
        $city_id = isset($map['city_id'])?intval($map['city_id']):false;
        $district_id = isset($map['district_id'])?intval($map['district_id']):false;
        $address = isset($map['address'])?$map['address']:false;
        $remark = isset($map['remark'])?$map['remark']:false;

        $where = '1=1';
        $where .= $username?' and m.username like "%'.$username.'%"':'';
        $where .= $status?' and m.status="'.$status.'"':'';
        $where .= $realname?' and mc.realname like "%'.$realname.'%"':'';
        $where .= $nickname?' and mc.nickname like "%'.$nickname.'%"':'';
        $where .= $sex?' and mc.sex="'.$sex.'"':'';
        //$where .= $birthday?' and mc.birthday="'.$birthday.'"':'';#生日筛选暂时忽略
        $where .= $email?' and mc.email like "%'.$email.'%"':'';
        $where .= $phone?' and mc.phone like "%'.$phone.'%"':'';
        $where .= $wechat?' and mc.wechat like "%'.$wechat.'%"':'';
        $where .= $tencent?' and mc.tencent like "%'.$tencent.'%"':'';
        $where .= $msn?' and mc.msn like "%'.$msn.'%"':'';
        $where .= $province_id?' and mc.province_id="'.$province_id.'"':'';
        $where .= $city_id?' and mc.city_id="'.$city_id.'"':'';
        $where .= $district_id?' and mc.district_id="'.$district_id.'"':'';
        $where .= $address?' and mc.address like "%'.$address.'%"':'';
        $where .= $remark?' and mc.remark like "%'.$remark.'%"':'';

        $field = 'm.id,m.username,m.status';#member主表字段
        $field .= ',mc.realname,mc.nickname,mc.icon,mc.sex,mc.birthday,mc.content,mc.email,mc.phone,mc.wechat,mc.tencent,mc.msn,mc.province_id,mc.city_id,mc.district_id,mc.address,mc.remark';#member_content附表字段
        $list = $this->alias('m')
            ->join(config('prefix').'member_content mc','mc.uid=m.id','LEFT')
            ->field($field)
            ->where($where)
            ->select();
        foreach ($list as $k=>$v){
            $list[$k]['status_cnn'] = $this->_tfStatus($v['status']);
            $list[$k]['sex_cnn'] = $this->_tfSex($v['sex']);
            $list[$k]['province_cnn'] = $this->_tfArea($v['province_id']);
            $list[$k]['city_cnn'] = $this->_tfArea($v['city_id']);
            $list[$k]['district_cnn'] = $this->_tfArea($v['district_id']);
        }
        return ['code'=>200,'msg'=>config('msg.200'),'data'=>$list];
    }

    //新增一条用户
    public function _addInfo($data=[]){

        //打开锁文件
        $fp = fopen('./public/lock/add_user.lock','r');
        flock($fp,LOCK_SH);

        //启动事务
        Db::startTrans();

        /**
         * 1、member主表
         */
        $member_data = [
            'username'     => isset($data['m_username'])?$data['m_username']:'',
            'password'     => isset($data['m_password'])?$data['m_password']:'',
            'encrypt'      => isset($data['m_encrypt'])?$data['m_encrypt']:'',
            'create_time'  => isset($data['m_create_time'])?strtotime($data['m_create_time']):time(),
            'status'       => isset($data['m_status'])?$data['m_status']:1,
            'del'       => isset($data['m_del'])?$data['m_del']:1,
        ];
        $uid = $this->insertGetId($member_data);
        if($uid <= 0){
            //发生错误，回滚事务
            Db::rollback();
            //关闭锁
            flock($fp,LOCK_UN);
            fclose($fp);
            return ['code'=>10067,'msg'=>config('msg.10067'),'uid'=>''];
        }

        /**
         * 2、auth_group_access用户-角色关联表
         */
        $m_group_access = [
            'uid'       => $uid,
            'group_id'  => isset($data['aga_group_id'])?$data['aga_group_id']:0
        ];
        $aga_id = Db::name('auth_group_access')->insert($m_group_access);
        if($aga_id <= 0){
            //发生错误，回滚事务
            Db::rollback();
            //关闭锁
            flock($fp,LOCK_UN);
            fclose($fp);
            return ['code'=>10067,'msg'=>config('msg.10067'),'uid'=>''];
        }

        /**
         * 3、member_content表
         */
        $member_content_data = [
            'uid'        => $uid,
            'realname'   => isset($data['mc_realname'])?$data['mc_realname']:'',
            'nickname'   => isset($data['mc_nickname'])?$data['mc_nickname']:'',
            'icon'       => isset($data['mc_icon'])?$data['mc_icon']:config('images.user_default_img'),
            'sex'        => isset($data['mc_sex'])?intval($data['mc_sex']):3,
            'birthday'   => isset($data['mc_birthday'])?intval($data['mc_birthday']):'',
            'content'    => isset($data['mc_content'])?$data['mc_content']:'',
            'email'      => isset($data['mc_email'])?$data['mc_email']:'',
            'phone'      => isset($data['mc_phone'])?$data['mc_phone']:'',
            'wechat'     => isset($data['mc_wechat'])?$data['mc_wechat']:'',
            'tencent'    => isset($data['mc_tencent'])?intval($data['mc_tencent']):'',
            'msn'        => isset($data['mc_msn'])?intval($data['mc_msn']):'',
            'province_id'=> isset($data['mc_province_id'])?intval($data['mc_province_id']):0,
            'city_id'    => isset($data['mc_city_id'])?intval($data['mc_city_id']):0,
            'district_id'=> isset($data['mc_district_id'])?intval($data['mc_district_id']):0,
            'address'    => isset($data['mc_address'])?intval($data['mc_address']):'',
            'remark'     => isset($data['mc_remark'])?intval($data['mc_remark']):'',
        ];
        $mc_id = Db::name('member_content')->insert($member_content_data);
        if($mc_id <= 0){
            //发生错误，回滚事务
            Db::rollback();
            //关闭锁
            flock($fp,LOCK_UN);
            fclose($fp);
            return ['code'=>10067,'msg'=>config('msg.10067'),'uid'=>''];
        }

        /**
         * 4、member_third_party第三方平台信息表
         */
        $member_third_party_data = [
            'uid'        => $uid,
            'third_id'=> isset($data['mtp_third_id'])?intval($data['mtp_third_id']):0,
            'openid'    => isset($data['mtp_openid'])?intval($data['mtp_openid']):'',
            'content'    => isset($data['mtp_content'])?intval($data['mtp_content']):'',
        ];
        $mtp_id = Db::name('member_third_party')->insert($member_third_party_data);
        if($mtp_id <= 0){
            //发生错误，回滚事务
            Db::rollback();
            //关闭锁
            flock($fp,LOCK_UN);
            fclose($fp);
            return ['code'=>10067,'msg'=>config('msg.10067'),'uid'=>''];
        }

        //成功，提交事务
        Db::commit();

        //关闭锁
        flock($fp,LOCK_UN);
        fclose($fp);

        return ['code'=>200,'msg'=>config('msg.200'),'data'=>['uid'=>$uid]];
    }


    //转换状态
    public function _tfStatus($status=0){
        switch ($status){
            case 1:
                $str = '正常';
                break;
            case 2:
                $str = '禁用';
                break;
            case 3:
                $str = '未激活';
                break;
            default:
                $str = '未知状态';
                break;
        }
        return $str;
    }

    //转换性别
    public function _tfSex($sex=0){
        switch ($sex){
            case 1:
                $str = '男';
                break;
            case 2:
                $str = '女';
                break;
            default:
                $str = '未知';
                break;
        }
        return $str;
    }

    //转换性别-中文数字
    public function _tfCnnSex($sex_name=''){
        if($sex_name == '男'){
            return 1;
        }elseif($sex_name == '女'){
            return 2;
        }else{
            return 3;
        }
    }


    //转换地区
    public function _tfArea($area_id=0){
        $area_id = intval($area_id);
        if(!empty($area_id)){
            $name = Db::name('area')->where('id='.$area_id)->value('name');
            if(empty($name)) return '未知';
            return $name;
        }
        return '未知';
    }

    //转换身份
    public function _tfGroup($id=0){
        if(!empty($id)){
            $title = Db::name('auth_group')->where('id',$id)->value('title');
            if(!empty($title)){
                return $title;
            }
        }
        return '未知';
    }

    //根据身份中文名称，获取id
    public function _tfCnnGroup($title=''){
        $res = 0;
        if(!empty($title)){
            $res = Db::name('auth_group')->where('title="'.$title.'"')->value('id');
            if(!empty($res)){
                return $res;
            }
        }
        return $res;
    }

}