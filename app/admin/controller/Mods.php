<?php
declare (strict_types = 1);

namespace app\admin\controller;
use app\admin\common\AdminBase;
use app\admin\common\Plate;
use app\admin\model\Mods as ModsModel;
use app\admin\model\ModsVersion;
use app\common\Mod;
use app\admin\model\Comment;
class Mods extends AdminBase
{
    use \app\admin\common\RelTool;
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index($state = 1)
    {
        //判断权限
        $user = $this->isAdmin()?0:session("user")["id"];
        $res = Mod::getModList(10,$state,$user);
        $this->assign([
            "state" => $state,
            "res" => $res,
        ]);
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create($iden = "")
    {
        $this->getAuthGroup();
        if ($iden !== "") {
            set_full(["iden" => $iden]);
        }
        $this->assign("plate_list",Plate::getPlateList(1));
         return $this->fetch();
    } 

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {
        $post = $this->getPost("create");
        //设置自动填充表单内容
        set_full($post);
        //处理post表单
        $post = $this->getModInfo($post);
        //存入数据库
        $art = new ModsModel();
        $art->save($post);

        //看起来貌似没有错误了呢～
        //所以清除掉自动填充的内容
        clean_full();
        //保存成功，返回
        $this->setAlert("提交成功，请等待审核通过","create","alert-success");
   
    }
    /**
     * 处理post提交的模组信息
     *
     * @return array;
     */
    protected function getModInfo($post)
    {
        //富文本去头尾空格
        $post["desc"] = trim($post["desc"]);
        //验证信息
        $this->vd($post,"Mods");
        //富文本消毒
        $post["desc"] = $this->cleanXss($post["desc"]); 
        //处理上传的图标
        $post["icon"] = $this->saveImg("icon");
        //处理上传的截图
        $post["pic"] = $this->saveImgs("pic");
        //获取发布者
        $post["user"] = session("user")["id"];
        //处理上传的文件
        $file = $this->saveMod("mod");
        $post["file"] = $file["path"];
        $post["size"] = $file["size"];
        $post["md5"]  = $file["md5"];
        //判断板块是否存在
        $plate = $this->getPlate($post["plate"],1);
        //判断是否需要审核
        if ($plate['audit'] == 1)  {
            $post["state"] = 4; 
        }else{
            $post["state"] = 1;
        }
        return $post;
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if($this->isPer($id)){
            Mod::delMod($id);
            $this->setAlert("删除成功","index","alert-success");
        }else{
            $this->setAlert("你没有操作此模组的权限","index","alert-danger");
        };
        
    }
    /**
     * 删除某一版本
     *
     * @return void
     */
    public function delVer($id)
    {
        $mod = ModsModel::find($id);
        if ($this->isPer($mod["iden"])) {
            Mod::delVer($id);
            $this->setAlert("删除成功",url("version",["id" => $mod["iden"]]));
        }else{
            $this->setAlert("权限不足","index","alert-danger");

        }
    }
    


    /**
     * 查看历史版本
     *
     */
    public function version($id = 0)
    {
        if(!$modlist = Mod::getModAllVersion($id)){
            $this->error("找不到数据","index");
        }
        $this->assign("res",$modlist); 
        return $this->fetch();

    }
    


    /**
     * 判断用户对某个模组是否有操作权限
     * $iden 模组标识
     * @return boolean
     */
    protected function isPer($iden)
    {
        //管理员返回true
        if ($this->isAdmin()) {
            return true;
        }
        $user = session("user")["id"];
        //查找是否存在
        $id = ModsModel::where("iden",$iden)->field("user")->find();
        //如果不存在，返回true
        if (!$id) {
            return true;
        }
        //如果存在，判断用户是否符合
        if ($id["user"] == $user) {
            return true;
        }
        //如果都没通过，反回false
        return false;
    }
    
}
