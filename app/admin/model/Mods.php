<?php
declare (strict_types = 1);

namespace app\admin\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Mods extends Model
{
    
    //开启写入时间戳
    protected $autoWriteTimestamp = "datetime";
    protected $createTime = "time";
    protected $updateTime = false;
    //设置JSON字段
    protected $json = ["pic"];
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
            $con = $data["desc"];
            $con = strip_tags($con);
            return mb_substr($con,0,30);
        }else{
            return $val;
        }
    }


    /**
     * 关联到user表
     *
     */
    public function userinfo()
    {
        return $this->beLongsTo(User::class,"user");
    }
    
    /**
     * 关联到板块
     *
     */
    public function plateinfo()
    {
        return $this->beLongsTo(Plate::class,"plate");
    }
    /**
     * 关联评论
     *
     */
    public function comment()
    {
        return $this->hasMany(Comment::class,"con");
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
    

}
