<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\admin\common\AdminBase;;
use app\admin\model\Comment as Com;
use app\index\common\Comment as C;
class Comment extends AdminBase
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if ($this->isAdmin()) {
            $com = C::getComList();
        }else{
            $com = C::getComList(false,false,$this->user["id"]);
        }
        // dump($com);
        $this->assign("comments",$com);
        return $this->fetch();
    }

    

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $com = Com::find($id);
        if (!$com) {
            $this->setAlert("找不到资源","index","alert-danger");
        }
        if (!$this->isAdmin()&&$com["user"] != session("user")["id"]) {
           $this->setAlert("权限不足","index","alert-danger");
        }
        $com->state = 2;
        $com->save();
        $this->setAlert("删除成功！","index");
    }
}
