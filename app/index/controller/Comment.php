<?php

namespace app\index\controller;;
use app\index\common\IndexBase;
use app\admin\model\Comment as ComModel;
use app\common\Mail;
use app\admin\model\Mods;
use app\admin\model\Article;
/**
 * Class Comment
 * @author chenduxiu
 */
class Comment extends IndexBase
{
    /**
     * 发送评论 
     */
    public function post()
    {
        if ($this->user == false) {
            $this->error("请登录","index/login/index");
        }
        $post = $this->getPost("index");
        $info = $this->validate($post,"Comment");
        if (true !== $info) {
            $this->error($info,url("index/index/mod",["id" => $post["id"]]));
        }
        if (!isset($post["com"]) || empty($post["com"])) {
            $post["com"] = 0;
        }
        
        if ($post["type"] == 1) {
            $type = "mod";
        }else{
            $type = "article";
        }

        $com = new ComModel([
            "text"  => $post["comment"],
            "type"  => $post["type"],
            "user"  => $this->user["id"], 
            "con"   => $post["id"],
            "com_id"=> $post["com"],
            "time"  => date("Y-m-d H:i:s"),
            "usertype"=> $this->user["usertype"],
        ]);
        $com->save();
        $url = url("index/index/{$type}",["id" => $post["id"]]);
        $this->sendMail($url,$post["type"],$post["id"],$post["comment"]);
        $this->success("发送成功",$url);

    }
    /**
     * 给作者发送邮件
     *
     * $url 评论的地址
     * $type 类型
     * $id id
     * $com 评论内容
     */
    protected function sendMail($url,$type,$id,$com)
    {
        if ($type == 1) {
            $con = Mods::find($id);
        }else{
            $con = Article::find($id);
        }
        $usermail = $con->userinfo->mail;
        dump($usermail);
        $url = $this->request->domain() . $url;
        Mail::mail($usermail,"你收到了一条新的回复","{$this->user["name"]}:<br/>{$com}<br/><br/>{$url}");
        
    }
    
    
}
