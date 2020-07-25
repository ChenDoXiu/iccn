<?php

namespace app\api\controller;
use app\api\common\ApiBase;
use app\index\common\Comment as Com;
use app\admin\model\Comment as ComModel;
use app\admin\model\Mods;
use app\common\Mail;
/**
 * Class Comment
 * @author chenduxiu
 * 评论相关API
 */
class Comment extends ApiBase
{
    /**
     * 根据mod ID获取评论
     *
     */
    public function get($id = 0)
    {
        $list = Com::getComList($id,1); 
        $arr = [];
        foreach ($list as $val) {
            unset($val["con"]);
            $arr[] = $val;
        }
        return $this->success($arr);
    }
    /**
     * 发送评论 
     */
    public function post()
    {
        $this->getUser();
        if ($this->user == false) {
            return $this->error("请登录");
        }
        $post = $this->getPost("Comment");
        if (!$post) {
           return $this->error($this->error); 
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

        $info = $this->sendMail($url,$post["type"],$post["id"],$post["comment"]);
        if ($info) {
            
        return $this->success("发送成功");
        }else{
            return $this->error("发送失败");
        }

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
        if (!$con) {
            return false;
        }
        $usermail = $con->userinfo->mail;
        // dump($usermail);
        $url = request()->domain() . $url;
        Mail::mail($usermail,"你收到了一条新的回复","{$this->user["name"]}:<br/>{$com}<br/><br/>{$url}");
        Mail::sendEvent();
        return true;
        
    }
    
}
