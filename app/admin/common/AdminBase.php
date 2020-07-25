<?php

namespace app\admin\common;
use app\BaseController;
use app\admin\model\User;
use think\facade\Session;
use think\facade\Db;
use app\common\Xss;
/**
 * Class AdminBase
 * @author chenduxiu
 * 后台基础类
 */
class AdminBase extends BaseController
{ 
    //权限
    protected $authList = [];
    //板块
    protected $plateList = [];

    //用户
    protected $user;
    /**
     * 初始化
     *
     * @return void
     */
    protected function initialize()
    { 
        //判断用户是否登录
        $this->isLogin();
        //获取提示信息
        $this->alert();
        //加载菜单
        $this->loadMenu();
        //判断当前是否有访问权限
        $this->auth();
        //获取权限列表
        // $this->getAuthGroup();
        //获取板块列表
        // $this->getPlateGroup();
        //其他初始化
        $this->init();
    } 

    /**
     * 空的初始化方法，占位
     *
     * @return void
     */
    protected function init()
    {
    }
    

    /**
     * 判断是否登录
     *
     */
    protected function isLogin()
    {
        $user = new User();
        $info = $user->isLogin();
        $this->user = $info;
        if ($info) {
            $this->assign("user_info",$info);
        }else{
            $this->error("请登录",url("index/login/admin",["url" => "admin/index/index"]));
        }
    }


    /**
     * 显示信息框
     *    
     */
    protected function alert()
    {
        $this->assign("alert_text",Session::get("alert_text"));
        $this->assign("alert_type",Session::get("alert_type"));
    }
    /**
     * 设置信息框
     *
     */
    protected function setAlert($info,$url,$type = "alert-primary")
    {
        Session::flash("alert_text",$info);
        Session::flash("alert_type",$type);
        $this->redirect($url,302);  
    } 


    /**
     * 加载菜单选项
     *
     */
    protected function loadMenu()
    {
        $str = LoadMenu::load(); 
        $this->assign("menu",$str);
    }


    /**
     * 判断是否有访问权限
     *
     */
    protected function auth()
    {
        $auth = LoadMenu::getAuth(get_url()); 
        if (!$auth) {
            $this->error("你没有访问权限","index/index/index");
        }
    }


    /**
     * 获取权限列表
     *
     */
    protected function getAuthGroup()
    {
        $auth = Db::name("auth_group")->select()->toArray(); 
        $this->authList = $auth;
        $this->assign("auth_list",$auth);
    }
    /**
     * 获取板块列表
     *
     */
    protected function getPlateGroup()
    {
        $list = Plate::getPlateList();
        $this->assign("plate_list",$list);
        $this->plateList = $list;
    }
    /**
     * 清除XSS注入
     *
     * @return string
     */
    protected function cleanXss($html)
    {
        $xss = new Xss();
	      return $xss->parse($html);
    }


    /**
     * 判断用户是否是管理员
     *
     * @return boolean
     */
    protected function isAdmin()
    {
        if (session("user")["auth"] == 5) {
            return true;
        }else{
            return false;
        }
    }
    
    
    
     
    
}
