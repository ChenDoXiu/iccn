<?php
namespace app\admin\validate;
use think\Validate;

class User extends Validate
{
    protected $rule = [
        "user"  => "require",
        "pw"    => "require",
        "name"  => "max:20",
        "info"  => "max:255",
        "pws"   => "require|confirm:pw",
        "auth" => "number|require",
        "mail"  => "email",
    ];
    protected $message = [
        "user.require"  => "请输入用户名",
        "pw.require"    => "请输入密码",
        "name.max"      => "名称最大20个字符",
        "info.max"      => "简介最大255个字符",
        "pws.require"   => "请输入确认密码",
        "pws.confirm"   => "两次输入密码不一致",
        "mail"          => "邮箱格式不正确",
        "auth"     => "用户组参数错误",
    ];
    protected $scene = [
        "login"   => ["user","pw"],
        "info"    => ["name","info"],
        "pw"      => ["pw","pws"],
        "add"     => ["user","name","pw","pws","usergroup","mail"],
        "update"  => ["user","mail","auth"],
    ];
}
