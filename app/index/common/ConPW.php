<?php

namespace app\index\common;
use app\admin\model\Mods; 
use app\admin\model\Article;
use think\facade\Session;
/**
 * Class ConPW
 * @author chenxuiu
 * 文章和模组的密码验证
 */
class ConPW
{
    /**
     * 模组密码验证
     *
     * @return boolean
     */
    public static function mod($id,$pw)
    {
        $mod = Mods::find($id);
        if (!$mod) {
            return false;
        }
        $modpw = $mod["pass"];
        if ($pw == $modpw) {
            self::add($id,1);
            return true;
        }
    }
    /**
     * 文章密码验证 
     *
     * @return boolean
     */
    public static function article($id,$pw)
    {
        $article = Article::find($id);
        if (!$article) {
            return false;
        }
        $artpw = $article["pw"];
        if ($pw == $artpw) {
            self::add($id,2);
            return true;
        } 
    }
    
    /**
     * 存储已经解锁的文章
     *
     */
    protected static function add($id,$type)
    {
        $list = Session::get("_pw_list",[]);
        $list[$type][$id] = true;
        Session::set("_pw_list",$list);
    }
    /**
     * 检测文章是否解锁
     *
     * @return boolean
     */
    public static function hasLock($id,$type)
    {
        if (isset(Session::get("_pw_list")[$type][$id])) {
            return true;
        }else{
            return false;
        }
    }
    
    
}
