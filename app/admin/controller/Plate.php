<?php
declare (strict_types = 1);

namespace app\admin\controller;
use app\admin\common\AdminBase;
use think\Request;
use app\admin\model\Plate as PlateModel;
use app\admin\model\Article;
use app\admin\model\Mods;
class Plate extends AdminBase
{
    use \app\admin\common\RelTool;

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
                set_full(PlateModel::find($edit)->toArray());
            }
        }
        $this->getPlateGroup();
        return $this->fetch();
    }


    /**
     * 保存新建的资源
     *
     ;* @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $post = $this->getPost("index");
        set_full($post);
        $this->vd($post,"Plate","index");
        PlateModel::create($post);
        clean_full();
        $this->setAlert("添加成功","index");
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
        $post = $this->getPost("index");
        set_full($post);
        $this->vd($post,"Plate",url("index",["edit" => $id]));
        $pla = PlateModel::find($id);
        $pla->save($post);
        clean_full();
        $this->setAlert("添加成功","index");
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $pla = PlateModel::find($id);
        //判断是否含有子板块
        $chi = PlateModel::where("father",$pla["id"])->find();

        if ($chi) {
            $this->setAlert("该板块下含有子版块，无法删除。","index","alert-danger");
        }
        //判断是否含有文章或模组
        if ($pla["type"] == 0) {
            //文章板块
            $id = Article::where("plate",$pla["id"])->field("id")->find();
            if ($id) {
                $this->setAlert("该板块下含有文章，无法删除。","index","alert-danger");
            }
        }
        if ($pla["type"] == 1) {
            //模组板块
            $id = Mods::where("plate",$pla["id"])->field("id")->find();
            if ($id) {
                $this->setAlert("该板块下含有模组，无法删除。","index","alert-danger");
            }
        }
        $pla->delete(); 
        $this->setAlert("删除成功","index");
    }
}
