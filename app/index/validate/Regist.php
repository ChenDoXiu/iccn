<?php

namespace app\index\validate;
use think\Validate;
/**
 * Class Regist
 * @author chenduxiu
 */
class Regist extends Validate
{
    protected $rule = [
        "mail"   => "email|require|token",
        "pw"    => "require",
        "pws"   => "require|confirm:pw",       
        "user"  => "require",
    ];
    protected $message = [
        "mail"   => "邮件输入错误",
        "pws.require"   => "请输入确认密码",
        "pws.confirm"   => "两次输入密码不一致",
        "user"    => "请输入用户名",
    ];
    protected $scene = [
        "mail"  => ["mail"],
        "pw"      => ["pw","pws","user"],
    ];
}
