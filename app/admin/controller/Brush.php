<?php

namespace app\admin\controller;
use app\admin\common\AdminBase;
use app\common\Option;
use app\admin\common\Plate;
use app\common\model\Menu;
/**
 * Class Brush
 * @author chenduxiu
 */
class Brush extends AdminBase
{
    use \app\admin\common\RelTool;
    /**
     * 首页
     *
     */
    public function index()
    {
        $body = Option::getOpt("index-body"); 
        $footer = Option::getOpt("index-footer");
        $bg = Option::getOpt("index-background");
        $this->assign([
            "body" => $body, 
            "footer"  => $footer,
            "bg"  => $bg,
        ]);
        return $this->fetch();
    }
    /**
     * 首页数据更新
     *
     */
    public function indexUpdate()
    {
        $data = $this->getPost("index");
        Option::setOpt("index-body",$data["body"]);
        Option::setOpt("index-footer",$data["footer"]);
        if ($url = $this->saveImg("bg",false)) {
            Option::setOpt("index-background",$url);
        }
        $this->setAlert("更新成功","index");
    }
    
    /**
     * 首页banana设定
     *
     */
    public function banana()
    {
        $data["title"] = Option::getOpt("banana-title");
        $data["text"] = Option::getOpt("banana-text");
        $data["url"] = Option::getOpt("banana-url");
        $data["bg"] = Option::getOpt("banana-bg");
        $this->assign("data",$data);
        return $this->fetch();
    }
    /**
     * 更新banana数据
     *
     */
    public function bananaUpdate()
    {
        $data = $this->getPost();;
           Option::setOpt("banana-title",$data["title"]);
           Option::setOpt("banana-text",$data["text"]);
           Option::setOpt("banana-url",$data["url"]);
           if ($url = $this->saveImg("bg",false)) {
               Option::setOpt("banana-bg",$url);
           }        
           $this->setAlert("更新成功","banana");
    }
    /**
     * 菜单选项
     *
     */
    public function menu($edit = false)
    {
        if ($edit) {
            $data = Menu::find($edit);
            if (!$data) {
                $this->error("找不到数据",url("admin/front/frontmenu"));
            }
            $this->assign("data",$data);
            $this->assign("edit",true);
        }   
        $this->assign([
            "father" => Plate::getPlate(0,"menu"),
            "plate"  => Plate::getPlateList(),
            "list"   => Plate::getPlateList(-1,"menu"),
        ]);
        return $this->fetch();
    }
    /**
     * 新增保存菜单选项
     *
     */
    public function saveMenu()
    {
       $data = $this->getPost("menu"); 
       $info = $this->vd($data,"Menu");
       $menu = new Menu([
           "name" => $data["title"],
           "url"  => $data["url"],
           "pid"=> $data["plate"],
       ]);
       $menu->save();
       $this->setAlert("添加成功","menu"); 
    }

    /**
     * 修改菜单
     *
     */
    public function updateMenu($id)
    {
        $data = $this->getPost("menu");
        $info = $this->vd($data,"Menu");
        $menu = Menu::find($id);
        if (!$menu) {
            $this->setAlert("找不到数据","menu");
        }
        $menu["name"] = $data["title"];
        $menu["url"]  = $data["url"];
        $menu["pid"]  = $data["plate"];
        $menu->save();
        $this->setAlert("修改成功","menu");
    }
    
    /**
     * 删除菜单
     *
     */
    public function removeMenu($id)
    {
        $data = Menu::find($id);
        if (!$data) {
            $this->setAlert("找不到内容","menu","alert-danger");
        }
        $data->delete();
        $this->setAlert("删除成功","menu");
    }
    
    /**
     * 页脚设置
     *
     */
    public function footer()
    {
        $this->assign("text",Option::getOpt("front-footer"));
        return $this->fetch();
    }
    
    /**
     * 保存页脚菜单
     *
     */
    public function saveFooter()
    {
        $data = $this->getPost("footer");;
        Option::setOpt("front-footer",$data["text"]);
        $this->setAlert("修改成功","footer");       
    }
    
    
    
    
     
}
