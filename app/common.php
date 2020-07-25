<?php
use think\facade\Request;
use think\facade\Session;
// 应用公共文件
function get_url(){
    //获取应用名称
    $app = app("http")->getName(); 
    //获取控制器名称
    $con = Request::controller("true");
    //获取操作名称
    $act = Request::action("true");
    //组合
    $str = $app ."/" .$con ."/" .$act;
    return $str;
}
//设置表单填充信息
function set_full($arr){
    $full = session("auto_full");
    if ($full) {
        $arr = $full + $arr;
    }
    Session::flash("auto_full",$arr);
}
//清除填充内容
function clean_full(){
    Session::flash("auto_full",null);
}
function has_full(){
    $full = Session::get("auto_full");
    if ($full) {
        return true;
    }else{
        return false;
    } 
}
//表单填充
//name 查找的内容，例如 name.admin
//default 找不到默认填充
function full($name,$default = ""){
    $arr = explode(".",$name);
    $full = Session::get("auto_full");
    foreach ($arr as $val) {
        if (!isset($full[$val])) {
            return $default;
        }
        $full = $full[$val];
    }
    return $full;
}


//获取异步发送邮件js脚本
function getMailJs(){
    $url = url("admin/mail/index");
    $js = <<<a
<script>
var xmlhttp;
if (window.XMLHttpRequest)
{
    //  IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
}
else
{
    // IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.open("GET","{$url}");
xmlhttp.send();
</script>
a;
    return $js;
}



/**
 * 获得随机字符串
 * @param $len             需要的长度
 * @param $special        是否需要特殊符号
 * @return string       返回随机字符串
 */
function getRandomStr($len, $special=false){
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );

    if($special){
        $chars = array_merge($chars, array(
            "!", "@", "#", "$", "?", "|", "{", "/", ":", ";",
            "%", "^", "&", "*", "(", ")", "-", "_", "[", "]",
            "}", "<", ">", "~", "+", "=", ",", "."
        ));
    }

    $charsLen = count($chars) - 1;
    shuffle($chars);                            //打乱数组顺序
    $str = '';
    for($i=0; $i<$len; $i++){
        $str .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
    }
    return $str;
}