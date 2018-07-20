<?php
/**
 * 邮件发送
 * User: linweixin(502510773@qq.com)
 * Date: 2018/2/3
 * Time: 10:54
 */
namespace app\common\controller\send;
use app\common\api\common\ISend;
use think\Config;

class Email implements ISend{
    // 配置参数
    protected static $config = [
        'to'          => '',     //发送地址
        'name'        => '',     //发送名称
        'subject'     => '',      //邮箱主题
        'body'        => '',      //邮寄主要内容
        'attachment'  => null,    //附件
    ];
    /**
     * 初始化配置
     * @param $config
     */
    public function __construct(array $config = []){
        /*if (empty($config)) {
            $config = Config::get('smtp.email');
        }*/
        $email_config = Config::get('smtp.mail');
        self::$config = array_merge(self::$config, $email_config,$config);
    }
    /**
     * 发送
     * @return mixed
     */
    public function send(){
        $send_config = self::$config;
        import('PHPMailerAutoload','vendor/PHPMailer');
        $mail = new \PHPMailer(); //PHPMailer对象
        $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP();  // 设定使用SMTP服务
        $mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
        $mail->SMTPSecure = 'ssl';                 // 使用安全协议
        $mail->Host       = $send_config['SMTP_HOST'];  // SMTP 服务器
        $mail->Port       = $send_config['SMTP_PORT'];  // SMTP服务器的端口号
        $mail->Username   = $send_config['SMTP_USER'];  // SMTP服务器用户名
        $mail->Password   = $send_config['SMTP_PASS'];  // SMTP服务器密码
        $mail->SetFrom($send_config['FROM_EMAIL'], $send_config['FROM_NAME']);
        $replyEmail       = $send_config['REPLY_EMAIL']?$send_config['REPLY_EMAIL']:$send_config['FROM_EMAIL'];
        $replyName        = $send_config['REPLY_NAME']?$send_config['REPLY_NAME']:$send_config['FROM_NAME'];
        $mail->AddReplyTo($replyEmail, $replyName);
        $mail->Subject    = $send_config['subject'];
        $mail->MsgHTML($send_config['body']);
        $mail->AddAddress($send_config['to'], $send_config['name']);
        if(is_array($send_config['attachment'])){ // 添加附件
            foreach ($send_config['attachment'] as $file){
                is_file($file) && $mail->AddAttachment($file);
            }
        }
        if ($mail->Send()) {
            //发送成功
            $json = [
                'code'  =>200,
                'msg'   => config('msg.200'),
                'data'  =>[]
            ];
        }else{
            //发送失败
            $json = [
                'code'  =>400,
                'msg'   => $mail->ErrorInfo,
            ];
        }
        return $json;
    }
    /**
     * 验证
     * @param $code
     * @return mixed
     */
    public function check($code){

        return '邮箱验证';
    }
}