<?php

namespace app\admin\model;
use think\Model;
/**
 * Class Article
 * @author chenduxiu
 * 文章模型
 */
class Article extends Model
{
    //开启写入时间戳
    protected $autoWriteTimestamp = "datetime";
    protected $createTime = "time";
    protected $updateTime = "up_time";
    /**
     * 自动文章摘要
     *
     * @return string
     */
    public function setAbsrtactAttr($val,$data)
    {
        //去掉字符串头尾空格
        $val = trim($val);
        if ($val == "") {
            $con = $data["con"];
            $con = strip_tags($con);
            return mb_substr($con,0,30);
        }else{
            return $val;
        }
    }
    /**
     * 获取用户名
     */
    // public function getUserAttr($id)
    // {
    //      $user = User::find($id);
    //     return $user;
    // }
    /**
     * undocumented function
     *
     * @return void
     */
    public function userinfo()
    {
        return $this->beLongsTo(User::class,"user");
    }
     
    /**
     * 获取板块名称
     *
     * @return void
     */
    // public function getPlateAttr($id)
    // {
    //     if ($id == 0) {
    //         return "未分类";
    //     }
        // return Plate::find($id)["name"];
    // v}
     
    /**
     * undocumented function
     *
     * @return void
     */
    public function plateinfo()
    {
        return $this->beLongsTo(Plate::class,"plate");
    }
    
    /**
     * 获取时间
     *
     */
    public function getTimeAttr($time)
    {
        $time = strtotime($time);
        $timenow = time();
        $time3 = $timenow - $time;
        if ($time3 < 60) {
            return "刚刚";
        }
        if ($time3 < 3600) {
            $i = floor($time3/60);
            return "{$i} 分钟前";
        }
        if ($time3 < 86400) {
            $i = floor($time3/3600);
            return "{$i} 小时前";
        }
        if ($time3 < 2592000) {
            $i = floor($time3/86400);
            return "{$i} 天前";
        }
        return date("Y-m-d",$time);
    }
    /**
     * 评论信息
     *
     */
    public function comment()
    {
       return $this->hasMany(Comment::class,"con"); 
    }
    
    
}
