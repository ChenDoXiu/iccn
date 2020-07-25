<?php
declare (strict_types = 1);

namespace app\index\controller;
use app\index\common\IndexBase;
use think\facade\Cache;
use app\common\Option;
use app\admin\model\Article;
use app\common\Mod;
use app\admin\model\Comment;
use app\admin\model\Plate;
use app\index\common\ConPW;
use app\index\common\Comment as Com;
class Index extends IndexBase
{
    public function index()
    {
        $this->assign([
            "body" => Option::getOpt("index-body"),
            "footer"=> Option::getOpt("index-footer"),
            "bg"    => Option::getOpt("index-background"),
        ]);
        return $this->fetch();
    }


    /**
     * 首页
     *
     */
    public function home()
    {
        $mods = Mod::getModList(10);
        $article = Article::where("state","1")->order("id","desc")->limit(6)->select();
        $this->assign("art",$article);
        $this->assign("mods",$mods);
        return $this->fetch();
    }

    /**
     * mod详情页 
     */
    public function mod($id = false,$com = 0)
    {
        if (!$id) {
            $this->error("找不到内容","home");
        }
	//dump($this->user);
	if($this->user){
            $admin = $this->user['auth'] >= 4 ? true : false;
	}else{
	    $admin = false;
	};
        $mod = Mod::getById($id,$admin);
        if (!$mod) {
            $this->error("找不到内容","home");
        }
        //判断是否有查看权限
        if ($this->user) {
            $per = $this->user["auth"];
        }else{
            $per = 1;
        }
        if ($per < $mod["permiss"]) {
            $this->error("权限不足","home");
        }
        //检测是否含有密码
        if (!$mod["pass"] == '') {
            if(ConPW::hasLock($id,1)){
                $lock = false;
            }else{
                $lock = true;
            }
        }else{
            $lock = false;
        }
        //获取评论列表
        $comment = Com::getComList($id,1);
        //获取历史版本列表
        $ver = Mod::getModAllVersion($mod["iden"]);
        //判断是否有更新
        if ($ver->toArray()) {
            if ($ver[0]["id"] > $id) {
            $this->assign("has_new",$ver[0]["id"]);
        }
        }
        
        $mods = Mod::getModList(4);
        $this->assign("comment",$comment);
        $this->assign("moddata",$mod);
        $this->assign("newMods",$mods);
        $this->assign("version",$ver);        
        $this->assign("com_id",$com);
        $this->assign("islock",$lock);

        return $this->fetch();
    }

    /**
     * 文章详情页
     *
     */
    public function article($id = false,$com = 0)
    {
        if (!$id) { 
            $this->error("找不到内容",url("index/index/home"));
        }
        $article = Article::find($id);
        if (!$article) {
            $this->error("找不到内容",url("index/index/home"));
        }
        //判断是否有查看权限
        if ($this->user) {
            $per = $this->user["auth"];
        }else{
            $per = 1;
        }
        if ($article["state"] == 4 && $per < 4) {
            $this->error('审核中',"home");
        }
        if ($per < $article["permiss"]) {
            $this->error("权限不足","home");
        }
        //检测是否含有密码
        if (!$article["pw"] == '') {
            if(ConPW::hasLock($id,2)){
                $lock = false;
            }else{
                $lock = true;
            }
        }else{
            $lock = false;
        }
        //阅读数量增加
        $article->read_num++;
        $article->isAutoWriteTimestamp(false)->save();
        //获取评论
        $comment = Com::getComList($id,2);
        $art_new = Article::where("state","1")->order("id","desc")->limit(6)->select();
        $this->assign("article",$article);
        $this->assign("comment",$comment);
        $this->assign("art_new",$art_new);
        $this->assign("com_id",$com);
        $this->assign("islock",$lock);
        return $this->fetch();
    }
    /**v
     * 模组列表
     *
     */
    public function modList($id = 0)
    {
        if ($id == 0) {
            $mods = Mod::getModList(16);
            $plate["name"] = "所有模组";
            $plate["desc"] = "全部模组内容";
        }else{
            $mods = Mod::getModList(16,1,0,true,$id);
            $plate = Plate::find($id); 
        }
        $page = $mods->render();
        $this->assign("mods",$mods);
        $this->assign("page",$page);
        $this->assign("plate",$plate);
        return $this->fetch();
    }

    /**v
     * 文章列表
     *
     */
    public function artList($id = 0)
    {
        if ($id == 0) {
            $art = Article::where("state",1)->paginate(10);
            $plate["name"] = "所有文章";
            $plate["desc"] = "全部文章内容";
        }else{
            $art = Article::where(["state" => 1,"plate" =>$id])->paginate(10);
            $plate = Plate::find($id); 
        }
        $page = $art->render();
        $this->assign("art",$art);
        $this->assign("page",$page);
        $this->assign("plate",$plate);
        return $this->fetch();
    }

    /**
     * 输入密码查看
     *
     */
    public function pw()
    {
        $post = $this->getPost("home");
        if ($post["type"] == 1) {
            //模组板块
            if(ConPW::mod($post["id"],$post["pw"])){
                $this->success("验证成功",(string)url("mod",["id" => $post["id"]]));
            }else{
                $this->success("验证失败",(string)url("mod",["id" => $post["id"]]));
            };
        }else{
            //文章板块
            if(ConPW::article($post["id"],$post["pw"])){
                $this->success("验证成功",(string)url("article",["id" => $post["id"]]));
            }else{
                $this->success("验证失败",(string)url("article",["id" => $post["id"]]));
            };
        }
    }


}
