<?php

namespace app\common;
use app\admin\model\Mods;

/**
 * Class Mod
 * @author chenduxiu
 * 模组查询工具
 */
class Mod
{
    /**
     * 获取模组列表
     * $num 数量
     * $state 状态
     * $user 作者
     * $page 分页，默认分页
     * @return array
     */
    public static function getModList($num,$state = 1,$user = 0,$page = true,$plate = false)
    {
        $id = Mods::field("max(id)")->group("iden")->select();
        $arr = [];
        foreach ($id as $val) {
            $arr[] = $val["max(id)"];
        }
        
        $res = Mods::with(["userinfo","plateinfo"])->whereIn("id",$arr)->where("state",$state)->order("id","desc");
        if ($user != 0) {
            $res = $res->where("user",$user);
        }
        if ($plate) {
        	
            $res = $res->where("plate",$plate);
        }
        if ($page) {
            $res = $res->paginate($num); 
        }else{
            $res = $res->select();
        }
        return $res;
    }
    /**
     * 删除模组及其历史版本
     *
     */
    public static function delMod($iden)
    {
        $mod = Mods::with("comment")->where("iden",$iden)->select();
        foreach ($mod as $val) {
            $val->together(["comment"])->delete();
        }
    }
    
    /**
     * 删除模组的某个版本
     *
     */
    public static function delVer($id)
    {
        $mod = Mods::with("comment")->find($id);
        $mod->together(["comment"])->delete();
        //think PHP 的bug，无法删除
        //替代方案
        // Comment::where("con",$id)->delete();
    }
    
    /**
     * 获取模组标识的所有版本
     *
     */
    public static function getModAllVersion($iden,$state = 1)
    {
        return Mods::where(["iden" =>$iden,"state" => $state])->order("id","desc")->select();
    }
    /**
     * 根据id获取版本
     *
     */
    public static function getById($id,$admin = false)
    {
        $mod = Mods::where('id' , $id);
        if (!$admin) {
            $mod = $mod->where("state",1);
        }
        return $mod->find();
    }
    
    
}
