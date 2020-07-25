<?php

namespace app\index\controller;
use app\index\common\IndexBase;
use app\admin\model\User;
use app\common\Auth;
use app\common\Option;
use app\common\Mail;
/**
 * Class Regist
 * @author chenduxiu
 */
class Regist extends IndexBase
{
    /**
     * 初始化
     * 
     */
    protected function init()
    {
        $regist = Option::getOpt('regist');
        if (!$regist) {
            $this->error("站点未开启注册","index/index/home");
        }
    }
    
    /**
     * 注册-首页
     *
     * @return void
     */
    public function index()
    {
        return $this->fetch();
    }
    /**
     * 发送邮件
     *
     * @return void
     */
    public function mail()
    {
        if (!$this->request->has("submit")) {
            $this->error("无权访问此页面",url("index"));
        }
        $post = $this->request->post();
        $error = $this->validate($post,"Regist.mail");
        if (true !== $error) {
            $this->error($error,url("index"));
        } 
        $token = getRandomStr(20);
        //检测是否存在该邮箱
        $user = User::where(["mail" => strtolower($post["mail"])])->find();
        if ($user) {
            if ($user["active"] == 1) { 
                $this->error("该邮箱已注册",url("index"));
            }else{
                $user->token = $token;
                $user->save();
            }
        }else{
            $user = new User([
                "mail" => $post["mail"],
                "token"=> $token,
                "active" => 0,
            ]);
            $user->save();
        }
        
       $url = $this->request->domain().url("active",["mail" => $post["mail"],"token"=>$token]); 
        // dump($url);
        $info = Mail::mail($post["mail"],"帐号激活","<h1>激活邮件</h1><p>欢迎注册ICCN汉化组，请点击此链接激活<a href='{$url}'>{$url}</a> </p><p>如果不是你本人操作，请忽略这封邮件</p> ");
        return $this->fetch();
    }


    /**
     * 邮箱激活
     *
     * @return void
     */
    public function active($mail = 0,$token = 0)
    {
        $user = User::where(["mail" => $mail,"token" => $token,"active" => 0])->find();
        if (!$user) {
            $this->error("该邮件激活已过期",url("index"));
        }
        if ($this->request->has("submit")) {
            $post = $this->request->post(); 
            $info = $this->validate($post,"Regist.pw");
            if (true !== $info) {
                $this->error($info);
            }


            //检测用户名是否存在
            if(User::getByUser($post["user"])){
                $this->error("用户名已存在");
            }

            //存储用户
            $user->user = $post["user"];
            $user->pw  = $post["pw"];
            $user->active=1;
            $user->time = date("Y-m-d H:i:s");
            $user->token = "";
            $user->auth = Option::getOpt("regist-per");
            $user->save();

            $this->success("激活成功！",url("index/index/home"));
        }
        return $this->fetch();
    }
    
    
    
}
