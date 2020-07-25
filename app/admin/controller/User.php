<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\admin\common\AdminBase;;
use app\admin\model\User as UserModel;
use app\admin\common\Auth;
class User extends AdminBase
{
    use \app\admin\common\RelTool;
    protected $userModel;
    /**
     * 初始化
     */
    protected function init()
    {

        if (!$this->user) {
            $this->err("用户不存在");
        }
        
        if ($this->user["usertype"] !== "system") {
            $this->setAlert("ICCN论坛登录者请到论坛修改用户信息","admin/index/index");
        }
        $this->userModel = UserModel::find($this->user["id"]);
        $this->assign("user",$this->user);
    }
    
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index($edit = 0)
    {
        if ($edit != 0) {
            $this->assign("edit",$edit);
            if (!has_full()) {
                set_full(UserModel::find($edit)->toArray());
            }
        }
        $this->getAuthGroup();
        $user = UserModel::select();
        $this->assign("user",$user);
        return $this->fetch();
    }
    /**
     * 用户中心
     *
     */
    public function center()
    {

        return $this->fetch();
    }
    
     /**
     * 修改头像
     *
     */
    public function upphoto()
    {
        $info = $this->saveImg("photo",true,"center");
        
        $this->userModel["photo"] = $info;
        $this->userModel->save();
        $this->setAlert("头像更新成功","center");
    }
    /**
     * 修改个人信息
     *
     */
    public function upinfo()
    {
        $post = $this->getPost();
        $this->vd($post,"User.info","index");
        $this->userModel["name"] = $post["name"];
        $this->userModel["info"] = $post["info"];
        $this->userModel->save();        
        $this->setAlert("信息更新成功","center");
    }
    /**
     * 保存新建的资源
     *
     * @return \think\Response
     */
    public function save()
    {
        $post = $this->getPost();
        set_full($post);
        $this->vd($post,"User.add","index");
        //判断用户名
        if ($this->hasUser($post["user"])) {
            $this->setAlert("用户名已存在","index","alert-danger");
        }
        //判断邮箱
        if ($this->hasMail($post["mail"])) {
            $this->setAlert("邮箱已存在","index","alert-danger");
        }
        //保存
        UserModel::create($post);
        //清除自动填充
        clean_full();
        $this->setAlert("已添加","index");
    }

        /**
     * 修改密码
     *
     */
    public function uppass()
    {
        $post = $this->getPost();
        $this->vd($post,"User.pw","index");
        
        $this->userModel["pw"] = $post["pw"];
        $this->userModel->save();
        $this->setAlert("密码修改成功","index");

    }
    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update($id)
    {
        $url = url("index",["edit" => $id]);
        $post = $this->getPost("index");
        $this->vd($post,"User.update",$url);
        if ($post["pw"] != "") {
           $this->vd($post,"User.pw",$url); 
        }else{
            unset($post["pw"]);
            unset($post["pws"]);
        }
        $user = UserModel::find($id);
        if (!$user) {
            $this->setAlert("用户不存在","index","alert-danger");
        }
        $user->save($post);
        $this->setAlert("修改成功","index");
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
    /**
     * 检测用户名是否存在
     *
     * @return boolean
     */
    protected function hasUser($user)
    {
        $user = UserModel::where("user",$user)->field("id")->find();
        if ($user) {
           return true; 
        }else{
            return false;
        }
    }
    /**
     * 检测邮箱是否存在
     *
     * @return boolean
     */
    protected function hasMail($mail)
    {
        $user = UserModel::where("mail",$mail)->field("id")->find();
        if ($user) {
           return true; 
        }else{
            return false;
        }
        
    }
    
    
}
