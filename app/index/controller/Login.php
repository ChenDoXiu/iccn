<?php

namespace app\index\controller;
use app\index\common\IndexBase;;
use app\admin\model\User;
/**
 * Class Login
 * @author chenduxiu
 * 登录
 */
class Login extends IndexBase
{
    
    /**
     * 登录
     *
     */
    public function index(User $user,$url = "index/index/index")
    {
        
        if($this->userModel->isLogin()){
            $this->error("用户已登录,","index/index/index");
        }
        
        $this->assign("url",$url);
        return $this->fetch();
    }
    /**
     * 开发者登录
     *
     */
    public function admin($url = "index/index/index")
    {
        if($this->userModel->isLogin()){
            $this->error("用户已登录,","index/index/index");
        }

        $this->assign("url",$url);
        return $this->fetch();
    }
    
    /**
     * 登录提交
     */
    public function login($url = "index/index/index")
    {
        $post = $this->getPost("admin"); 
        $info = $this->userModel->login($post["user"],$post["pw"]);
        if (!$info) {
            $this->error("用户名或密码错误","admin");
        }
        $this->success("登录成功",$url);
    }
    /**
     * nodebb用户登录提交
     *
     */
    public function Nblogin($url = "index/index/index")
    {
        $post = $this->getPost("index");
        $info = $this->userModel->nodeBBLogin($post["user"],$post["pw"]);
        if (!$info) {
            $this->error("用户名或密码错误","index");
        }
        $this->success("登录成功",$url);

    }
    
    /**
     * 退出登录
     *
     */
    public function out()
    {
        $this->userModel->loginOut();
        $this->success("退出登录成功","index");
    }
    

}
