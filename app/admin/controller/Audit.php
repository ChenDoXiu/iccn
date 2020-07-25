<?php

namespace app\admin\controller;
use app\admin\common\AdminBase;
use app\admin\model\Mods;
use app\admin\model\Article;
/**
 * Class Audit
 * @author chenduxiu
 */
class Audit extends AdminBase
{
    /**
     * 审核首页
     *
     */
    public function index()
    {
        $mods = Mods::where("state",4)->select();
        $article = Article::where("state",4)->select();
        $this->assign("mods",$mods);
        $this->assign("article",$article);
        return $this->fetch();
    }
    /**
     * 审核通过
     *
     */
    public function approved($id,$type)
    {
        if ($type == "mod") {
            $mod = Mods::find($id);
            if (!$mod) {
                $this->error("资源不存在","index");
            }
            $mod->state = 1;
            $mod->save();
        }elseif ($type == "article") {
            $mod = Article::find($id);
            if (!$mod) {
                $this->error("资源不存在","index");
            }
            $mod->state = 1;
            $mod->save();
        }else{
        $this->setAlert("参数错误","index");
        }
        $this->setAlert("已通过","index");
    }
    
    /**
     * 驳回
     *
     */
    public function dis($id,$type)
    {
        $post = $this->getPost();

        if ($type == "mod") {
            $mod = Mods::find($id);
            if (!$mod) {
                $this->error("资源不存在","index");
            }
            $mod->state = 5;
            $mod->dis = $post["dis"];
            $mod->save();
        }elseif ($type == "article") {
            $mod = Article::find($id);
            if (!$mod) {
                $this->error("资源不存在","index");
            }
            $mod->state = 5;
            $mod->dis = $post["dis"];
            $mod->save();
        }else{
        $this->setAlert("参数错误","index");
        }
        $this->setAlert("已驳回","index");
    }
    
    
} 
