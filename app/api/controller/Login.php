<?php

namespace app\api\controller;
use app\api\common\ApiBase;
use app\admin\model\User;

/**
 * Class Login
 * @author chenduxiu
 * 用户登录相关API
 */
class Login extends ApiBase
{
    /**
     * nodebb登录
     *
     */
    public function nodebb()
    {
        $this->getUser();
        $post = $this->getPost();
        if (!$post) {
            return $this->error($this->error);
        }
        $info = $this->userModel->nodeBBLogin($post["user"],$post["pw"]);
        if (!$info) {
            return $this->error("用户名或密码错误");
        }
        return $this->success("登录成功");
       
    }
    /**
     * 判断是否登录
     *
     */
    public function isLogin()
    {
        $this->getUser();
        if ($this->user) {
            return $this->success("已登录");
        }else{
            return $this->error("未登录");
        }
        
    }
    
    
}
