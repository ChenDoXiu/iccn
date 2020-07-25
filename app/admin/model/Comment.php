<?php
declare (strict_types = 1);

namespace app\admin\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Comment extends Model
{
    /**
     * 对应用户表
     *
     * @return void
     */
    public function userinfo()
    {
        return $this->beLongsTo(User::class,"user");
    }
    /**
     * 父评论
     *
     */
    public function fatherCom()
    {
        return $this->beLongsTo(self::class,"com_id");
    }
    /**
     * 回复的mod
     *
     */
    public function fatherMod()
    {
        return $this->beLongsTo(Mods::class,"con");
    }
    
    /**
     * 回复的文章
     *
     */
    public function fatherArticle()
    {
        return $this->beLongsTo(Article::class,"con");
    }
    /**
     * 评论内容获取器
     *
     */
    public function getTextAttr($text,$com)
    {
        if ($com["state"] == 2) {
           return "该评论已被删除"; 
        }else{
            return $text;
        }
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
