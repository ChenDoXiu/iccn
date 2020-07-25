<?php
declare (strict_types = 1);

namespace app\admin\controller;
use app\admin\common\AdminBase;
use app\common\Xss;
use app\admin\common\Plate;
use app\admin\model\Article as ArticleModel;
class Article extends AdminBase
{
    use \app\admin\common\RelTool;
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index($state = 1)
    {
        $this->assign("state",$state);        
        $res = ArticleModel::with(["plateinfo","userinfo"])->where("state",$state);
        //判断权限
        if (!$this->isAdmin()) {
            $res = $res->where("user",session("user")["id"]);
        }
        $res = $res->paginate(10);
        $this->assign("res",$res);
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $this->getAuthGroup();
        $this->assign("plate_list",Plate::getPlateList(0));
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
        //过滤表单内容
        $post = $this->getArtInfo($post);
        //存入数据库
        $art = new ArticleModel();
        $art->save($post);
        //看起来貌似没有错误了呢～
        //所以清除掉自动填充的内容
        clean_full();
        //保存成功，返回
        $this->setAlert("已发布，请等待审核","create","alert-success");
    }
    /**
     * 处理文章表单
     *
     */
    protected function getArtInfo($post,$cover = true,$url = "create")
    {
        //富文本去头尾空格
        $post["con"] = trim($post["con"]);
        //验证信息
        $this->vd($post,"Article",$url);
        //富文本消毒
        $post["con"] = $this->cleanXss($post["con"]); 
        //处理上传的文件 
        $cover = $this->saveImg("cover",$cover,$url);
        if ($cover) {
           $post["cover"] = $cover;
        }
        //判断板块是否存在
        $plate = $this->getPlate($post["plate"],0,$url);
        //获取发布者
        $post["user"] = session("user")["id"];
        //判断是否需要审核
        if ($plate["audit"] == 1)  {
            $post["state"] = 4; 
        }else{
            $post["state"] = 1;
        }
        return $post;
    }
    

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     */
    public function edit($id)
    {
        $this->getAuthGroup();        
        $this->assign("plate_list",Plate::getPlateList(0));
        if(!has_full()) {
            if(!$res = $this->isPer($id)){
                $this->setAlert("无操作权限","index","alert-danger");
            }
            set_full($res->toArray()); 
        }
        return $this->fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function update($id)
    {
        $post = $this->getPost("index");
        set_full($post);
        $res = $this->isPer($id);
        if (!$res) {
            $this->setAlert("无操作权限","index");
        }
        //过滤post信息
        $post = $this->getArtInfo($post,false,url("edit",["id" => $id]));
        //保存
        $res->save($post);
        clean_full();
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
        if ($art = $this->isPer($id)) {
            $art->together(["comment"])->delete();
            $this->setAlert("删除成功","index");
        }else{
            $this->setAlert("无操作权限","index","alert-danger");
        }
    }

    /**
     * 判断是否有权限
     *
     * @return void
     */
    protected function isPer($id)
    {
        
        $res = ArticleModel::with("comment")->find($id);
        if (!$res) {
            return false;
        }
        if ($res["user"] == session("user")["id"] || $this->isAdmin()) {
            return $res;
        }
        return false;
    }
    
    
}
