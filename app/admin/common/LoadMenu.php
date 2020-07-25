<?php

namespace app\admin\common;
use think\facade\Session;
use think\facade\Request;
/**
 * Class LoadMenu
 * @author chenduxiu
 * 加载后台菜单
 */
class LoadMenu
{
    //存储每个模块的权限信息
    protected static $auth = []; 
    /**
     * 载入
     *
     * @return string
     */
    public static function load()
    {
        $menu = include "menu.php";
        $str = "";
        foreach ($menu as $val) {
            if ($val["type"] == "link") {
               if ($a = self::createLinkType($val)) {
                   $str .= self::createLiTag($a);
               }
            } 
            if ($val["type"] == "group") {
               if ($a = self::createGroupType($val)) {
                   $str .= self::createLiTag($a);
               } 
            }
        }
        //加载其他功能的权限列表
        $auth = include "fun_auth.php";
        self::$auth += $auth;
        return $str;
    }
    /**
     * 判断是否有权限
     *
     * @return void
     */
    public static function getAuth($url,$a = 3)
    {
        if (!isset(self::$auth[$url])) {
           $auth = $a; 
        }else{
            $auth = self::$auth[$url];
        }
        return self::isShow($auth);
    }
    
    /**
     * 生成类型为link的选项
     *
     * @return string
     */
    protected static function createLinkType($arr)
    {
        self::$auth[$arr["link"]] = $arr["auth"];
        if (self::isShow($arr["auth"])) {
            $act = self::isActive($arr["link"]) ? "active" : "";
            $url = url($arr["link"]);
            return "<a href='{$url}'  class='{$act}'><span class='oi {$arr["icon"]}'></span>{$arr["name"]}</a>";
        }else{
            return false;
        }

    }

    /**
     * 创建类型为group的选项卡
     *
     * @return string
     */
    protected static function createGroupType($arr)
    {
        $child = "";
        $count = 0;
        //循环创建子菜单
        foreach ($arr["child"] as $val) {
           if ($str = self::createGroupLink($val)) {
               $child .= $str;
               $count ++;
           } 
        }
        if ($count !== 0) {
           $child = self::createUlTag($child,$arr["id"]); 
           $child = self::createGroupCard($arr,$child);
           return $child;
        }else{
            return false;
        }
        
    }
    

    /**
     * 创建group的选项
     *
     * @return string
     */
    protected static function createGroupLink($arr)
    {
        self::$auth[$arr["link"]] = $arr["auth"];
        if (self::isShow($arr["auth"])) {            
            $act = self::isActive($arr["link"]) ? "active" : "";
            $url = url($arr["link"]);
            return "<li  class='{$act}'><a href='{$url}'>{$arr["name"]}</a></li>";
        }else{
            return false;
        }
    }


    /**
     * 创建group的折叠部分
     *
     * @return string
     */
    protected static function createGroupCard($arr,$child)
    {
        $str = "<a href='#{$arr["id"]}' data-toggle='collapse'><span class='oi {$arr["icon"]}'></span>{$arr["name"]}</a>";
        $str .= $child;
        $str .= "<span class='oi oi-chevron-right'></span>";
        return $str;

    }
    
    
    /**
     * 将字符串包裹上li标签
     *
     * @return string
     */
    protected static function createLiTag($str)
    {
        return "<li>{$str}</li>";
    }
    
    /**
     * 创建group子选项的ul标签
     *
     * @return string
     */
    protected static function createUlTag($str,$id)
    {
        return "<ul class='child collapse' id='{$id}' data-parent='.left'>{$str}</ul>";
    }
    

    /**
     * 判断选项是否显示
     *
     * @return boolean
     */
    protected static function isShow($auth)
    {
        if (Session::get("user")["auth"] >= $auth) {
            return true;
        }else{
            return false;
        }
    }
    /**
     * 判断选项是否高亮
     *
     * @return boolean
     */
    protected static function isActive($url)
    {
        $str = get_url();
        if ($url == $str) {
            return true;
        }else{
            return false;
        }
    }
    
    

}
