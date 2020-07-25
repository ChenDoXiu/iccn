<?php

namespace app\index\common;
use app\BaseController;
use app\common\model\Menu;
use app\common\Option;
use app\admin\model\User;
/**
 * Class IndexBase
 * @author chenduxiu
 * 前台控制器基类
 */
class IndexBase extends BaseController
{
    protected $user;
    protected $userModel;
    /**
     * 初始化方法
     *
     */
    protected function initialize()
    {
        //初始化顶部菜单
        $this->getMenu();
        //初始化底部页脚
        $this->getFooter();
        //获取用户信息
        $this->getUser();
        //获取所有选项
        $this->getOpt();
        //执行应用初始化
        $this->init();
    }
    /**
     * 初始化占位
     */
    protected function init()
    {
    }
    
    /**
     * 获取顶部菜单
     *
     */
    protected function getMenu()
    {
        $list = Menu::select(); 
        $str = "";
        foreach ($list as $val) {
           $str .= " <li class='nav-item'><a class='nav-link' href='{$val["url"]}'>{$val["name"]}</a></li>";
        }
        $this->assign("menu_list",$str);
    }
    
    /**
     * 获取页脚菜单
     *
     */
    protected function getFooter()
    {
        $f = Option::getOpt("front-footer");
        $this->assign("footer_menu",$f);
    }
     
    /**
     * 注入所有选项
     *
     */
    protected function getOpt()
    {
        $f = Option::getAll();
        $this->assign("option",$f);
    }
    /**
     * 获取用户信息$
     *
     */
    protected function getUser()
    {
        $user = new User();
        $this->userModel = $user;
        $user = $user->isLogin();
        // dump($user);
        $this->user = $user;
        $this->assign("user_info",$user);
    }
    
    
}
