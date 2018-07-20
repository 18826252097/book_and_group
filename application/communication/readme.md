#通信模块
## 目录结构
~~~
communication 通信模块
├─controller           控制器目录
│  ├─check             验证功能目录
│  │  ├─Email.php       邮箱验证
│  │  ├─Phone.php       手机短信验证
│  ├─send              发送功能目录
│  │  ├─Email.php       邮箱发送
│  │  ├─Phone.php       手机短信发送
│  ├─Api.php            接口-调用工厂类
│  ├─Unit.php           接口-单元测试
├─model                验证器目录
│  ├─CommCheck.php      验证功能目录
├─validate             验证器目录
│  ├─check              验证功能目录
│  │  ├─Email.php       邮箱验证-验证器类
│  │  ├─Phone.php       手机短信验证-验证器类
│  ├─send              发送功能目录
│  │  ├─Email.php       邮箱发送-验证器类
│  │  ├─Phone.php       手机短信发送-验证器类
│  ├─ValidateFactory.php验证器-工厂类
│  ├─common.php         通信模块-公共函数文件
│  ├─config.php         通信模块-公共配置文件
├─view                 模板目录
│  ├─Unit              单元目录
│  │  ├─test.html       测试模板
模块基于tp引入第三方类库：PhoneCode、PHPMailer
www 项目目录
├─application        应用目录
│  ├─communication     通信模块目录
├─extend             类库目录
│  ├─PhoneCode.php     短信发送
├─vendor             类库目录
│  ├─PHPMailer         邮箱发送
~~~

## 调用方法 具体参考application\index\controller\Sends.php
### 邮箱
```
//使用公共函数getApi调用此模块
$data = array(
            'type'      =>  'email',              //类型 默认是email
            'name'      =>  '名称',               //邮件名称
            'to'        =>  '502510773@qq.com',  //对方邮件地址
            'body'      =>  'hello world',       //内容 
            'subject'   =>  '主题',               //主题
        );
$res = getapi($data,'/communication/Api/send');//调用邮箱注册接口
//使用公共函数decodeData解析返回结果
$res = decodeData($res);//解析返回参数
//成功返回
array(2) {
  ["data"] => array(4) {
    ["code"] => int(200)
    ["msg"] => string(6) "成功"
    ["data"] => array(0) {
    }
    ["time"] => string(19) "2018-02-10 09:55:45"
  }
  ["sign"] => string(32) "3a5229b958b9f823a605fb9697c33643"
}
//其他返回
array(2) {
  ["data"] => array(3) {
    ["code"] => int(400)
    ["msg"] => string(84) "请求失败错误信息为：You must provide at least one recipient email address."
    ["time"] => string(19) "2018-02-10 09:59:41"
  }
  ["sign"] => string(32) "a03e84fee9f1939889a0d3494df01449"
}
```
### 短信
```
//发送-使用公共函数getApi调用此模块
$data = array(
          'type'      => 'phone',           //类型
          'mobile'    => '18819493724',     //手机号码
          'len'       => '6'                //验证码长度
      );
$res = getapi($data,'/communication/Api/send');//调用通信发送接口
dump($res);
//使用公共函数decodeData解析返回结果
$res = decodeData($res);//解析返回参数
//成功返回
array(2) {
  ["data"] => array(4) {
    ["code"] => int(200)
    ["msg"] => string(6) "成功"
    ["data"] => array(3) {
      ["verify_code"] => string(6) "381472"         //发送的验证码
      ["mobile"] => string(11) "18819493724"        //手机号码
      ["time"] => string(19) "2018-02-10 10:11:32"
    }
    ["time"] => string(19) "2018-02-10 10:11:32"
  }
  ["sign"] => string(32) "8ba1fa6efafe32554a6637b538b794df"
}
//其他返回
array(2) {
  ["data"] => array(3) {
    ["code"] => int(400)
    ["msg"] => string(12) "请求失败"
    ["time"] => string(19) "2018-02-10 10:13:06"
  }
  ["sign"] => string(32) "487b16f28449ad09eb531889ec3eda02"
}
//验证-使用公共函数getApi调用此模块
$data = array(
    'type'=>'phone',
    'mobile'=>'18819493724',
    'codes' =>$code,  //需要验证的验证码
);
$res = getapi($data,'/communication/Api/check');
$res = decodeData($res);//解析返回参数
//成功
array(2) {
  ["data"] => array(4) {
    ["code"] => int(200)
    ["msg"] => string(6) "成功"
    ["data"] => array(2) {
      ["verify_code"] => string(6) "495989"
      ["mobile"] => string(11) "18819493724"
    }
    ["time"] => string(19) "2018-02-10 15:01:18"
  }
  ["sign"] => string(32) "f02d4d2318a491b2baca773780a73b64"
}
//时间过期
array(2) {
  ["data"] => array(3) {
    ["code"] => int(400)
    ["msg"] => string(39) "请求失败！错误：时间已过期"
    ["time"] => string(19) "2018-02-10 15:06:15"
  }
  ["sign"] => string(32) "1d799995431d4edb7f46144a553817aa"
}
//验证码错误
array(2) {
  ["data"] => array(3) {
    ["code"] => int(10004)
    ["msg"] => string(15) "验证码错误"
    ["time"] => string(19) "2018-02-10 15:06:42"
  }
  ["sign"] => string(32) "1cf3252bd7673a35ecb1e313a52cdaf8"
}

```
