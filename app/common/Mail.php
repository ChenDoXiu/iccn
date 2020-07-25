<?php

namespace app\common;
use app\common\Option;
use phpmailer\PHPMailer;
use think\facade\Session;
/**
 * Class Mail
 * @author chenduxiu
 * 邮件发送工
 */
class Mail
{

    /**
     * 加入邮件发送队列
     */
    public static function mail($to,$title,$content)
    {
        $arr = [
            "to" => $to,
            "title"=> $title,
            "content"=> $content,
        ];
        if (Session::has("__mail_list")) {
            //session存在
            $list = Session::get("__mail_list");
            $list[] = $arr;
            Session::set("__mail_list",$list);
        }else{
            //不存在
            $list = [];
            $list[] = $arr;
            Session::set("__mail_list",$list);
        }
        
    }

    /**
     * 发送事件
     *
     */
    public static function sendEvent()
    {
        $list = Session::get("__mail_list");
        // dump($list);
        if (!$list) {
            return;
        }
        foreach ($list as $mail) {
            $info = self::send($mail["to"],$mail["title"],$mail["content"]);
        }
        Session::set("__mail_list",[]);
    }
    
    /**
     * 发送
     *
     */
    protected static function send($to,$title,$content)
    {
        // 加载发送邮件的扩展类库
        $mail = new PHPMailer();
        // var_dump($mail);
        // 设置字符集
        $mail->CharSet = "utf-8";
        // 设置采用SMTP方式发送邮件
        $mail->IsSMTP();
        // 设置邮件服务器地址
        $mail->Host = Option::getOpt("mail-smtp");// qq
        // 设置邮件服务器的端口   163端口25
        $mail->Port = Option::getOpt("mail-port");
        // 设置发件人的邮箱地址
        $mail->From = Option::getOpt("mail-mail");
        // 设置发送方名称
        $mail->FromName = Option::getOpt("mail-name");
        // 设置SMTP是否需要密码验证
        $mail->SMTPAuth = true;
        // 发送方
        $mail->Username = Option::getOpt("mail-user");// 发件方邮箱
        // $mail->Password = "xxx";// 163客户端的授权密码
        $mail->Password = Option::getOpt("mail-pass");// qq客户端的授权密码
        $mail->SMTPSecure = "ssl";// qq才需要使用ssl协议方式，163不需要
        // 发送邮件的主题
        $mail->Subject = $title;
        // 内容类型 文本型
        $mail->AltBody = "text/html";
        // 发送的内容
        $mail->Body = $content;
        // 设置内容是否为html格式
        $mail->IsHTML(true);
        // 设置接收方
        $mail->AddAddress(trim($to));
        return $mail->Send();
    }

}
