<?php

namespace app\index\common;
use app\admin\model\Comment as Com;
use app\common\NodeBB;
/**
 * Class Comment
 * @author chenduxiu 
 * 处理评论列表
 */
class Comment
{
    /**
     * 获取评论列表
     * $id 内容的ID
     * $type 内容的类型
     * @return void
     */
    public static function getComList($id=false,$type = false,$user = false)
    {
        
        $com = Com::with(["userinfo","fatherMod","fatherArticle"])->where("state",1)->order("id","desc");
        if($id){
            $com = $com->where("con",$id);
        }
        if ($type) {
            $com = $com->where("type",$type);
        }
        if ($user) {
            $com = $com->where("user",$user);
        }
        $com = $com->select()->toArray();

        //建立id索引
        $com = self::creatListById($com);
        $list = [];
        foreach ($com as $val) {
            $data = self::createComArr($val);
            //处理回复的消息
            if ($val["com_id"] != 0) {
                //获取评论的数组
                if (isset($com[$val["com_id"]])) {
                    $fa = $com[$val["com_id"]];
                    $data["com"] = self::createComArr($fa);                   
                }

            }
            $list[] = $data;
        }

        return $list;
    }
    /**
     * 根据id建立数组
     *
     * @return array
     */
    protected static function creatListById($arr)
    {
        $list = [];
        foreach ($arr as $val) {
            $list[$val["id"]] = $val; 
        }
        return $list;
    }
    
    /**
     * 获取nodebb用户信息 
     *
     * @return array
     */
    protected static function getNBuser($uid)
    {
        $user = NodeBB::getUserInfo($uid);
        if (!$user) {

            $arr = [
                "uid" => 0,
                "name"=> "用户已注销",
                "photo"=> "/static/imges/logo.png",
            ];
            return $arr;   
        }
        $arr = [
            "uid" => $user["uid"],
            "name"=> $user["username"],
            "photo"=> $user["picture"]?"https://forum.adodoz.cn/" . $user["picture"]:"/static/imges/logo.png",
        ];
        return $arr;
    }
    
    /**
     * 获取系统用户信息
     * @return array 
     */
    protected static function getSysUser($user)
    {
        
        if (!$user) {

            $arr = [
                "uid" => 0,
                "name"=> "用户已注销",
                "photo"=> "/static/imges/logo.png",
                "mail" => ""
            ];
            return $arr;   
        }
         return [
                    "uid" => $user["id"], 
                    "name"=> $user["name"],
                    "photo"=> $user["photo"],
                    "mail" => $user["mail"],
         ];
    }
    

    /**
     * 根据数据创建单个回复数组
     *
     * @return array
     */
    protected static function createComArr($arr)
    {
           $data = [];
           //评论id
           $data["id"] = $arr["id"];
            //处理评论发送者信息
            if ($arr["usertype"] === "nodebb") {
                //当用户来自nodebb时
                $data["user"] = self::getNBuser($arr["user"]);
                //添加一个空邮件防止报错
                $data["user"]["mail"] = "";
            }elseif($arr["usertype"] === "system"){
                //当用户来者本地系统时
                $data["user"] = self::getSysUser($arr["userinfo"]);
            }
            //处理评论消息
            $data["text"] = $arr["text"];
            //处理发送时间
            $data["date"] = $arr["time"];
            //回复类型
            $data["type"] = $arr["type"];
            //获取被评论的文章
            $data["con"]  = self::getFatherCon($arr);
            return $data;
    }
    /**
     * 获取评论所属的文章或模组信息
     *
     */
    protected static function getFatherCon($arr)
    {
        if ($arr["type"] == 1) {

            $data = $arr["fatherMod"];
            $data["type"] = 1;
            return $data;
        }elseif($arr["type"] == 2){

            $data = $arr["fatherArticle"];
            $data["type"] = 2;
            return $data;
        }
    }
    
    
    
}
