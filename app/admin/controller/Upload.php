<?php

namespace app\admin\controller;
use app\admin\common\Upload as Up;

/**
 * Class Upload
 * @author chenduxiu
 * 上传
 */
class Upload
{
    /**
     * 图片上传传
     *
     */
    public function img()
    {
        $url =  Up::img("img");
        if ($url) {
            $arr = array();
            $arr["url"] = $url;
            return json($arr);
        } 
    }
    
}
