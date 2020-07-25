<?php

namespace app\admin\common;

/**
 * Class Tool
 * @author chenduxiu
 * 常用工具类
 */
trait RelTool
{

    /**
     * 验证post信息
     *
     * $post 需要验证的数据
     * $validate 验证器
     * $url 验证失败时跳转的地址
     * @return void
     */
    protected function vd($post,$validate = "Article",$url = "create")
    {
        // 验证信息
        $info = $this->validate($post,$validate);
        if (true !== $info) {
            $this->setAlert($info,$url);
        }
    }
    /**
     * 保存图片
     * $file form中的名称
     * $skip 不通过时是否跳转
     * @return string
     */
    protected function saveImg($file,$skip = true,$url = "create")
    {
        $info = Upload::img($file);
        if (false === $info&& $skip) {
            $this->setAlert("封面文件类型错误，仅允许上传png、jpg、gif",$url);
        }
        return $info;

    }
    /**
     * 保存多个图片
     * $file form表单中的name
     * @return array
     */
    protected function saveImgs($file,$url = "create")
    {
        $info = Upload::imgs($file);
        if (false == $info) {
            $this->setAlert("截图文件类型错误，仅允许上传png、jpg、gif",$url);
        }
        return $info; 
    }

    /**
     * 保存mod文件
     * $file form表单中的名称
     * @return string
     */
    protected function saveMod($file,$url = "create")
    {
        $info = Upload::mod($file);
        if (false === $info) {
            $this->setAlert("模组文件类型错误，仅允许上传zip,icmod，如果是其他类型，请打包成zip上传",$url);
        }
        return $info;
    }


    /**
     * 获取板块信息
     * $id 板块id
     * $type 板块类型 0=文章 1=mod
     * $url 不存在板块时跳转路径
     * @return array
     */
    protected function getPlate($id,$type = 0,$url = "create")
    {
        if(!$plate = Plate::isExist($id,$type)){ 
            $this->setAlert("板块不存在",$url);
        }
        return $plate;
    } 
}
