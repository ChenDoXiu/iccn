<?php

namespace app\index\controller;
use app\index\common\IndexBase;
use app\admin\model\Mods;
use app\index\common\ConPW;
/**
 * Class Download
 * @author chenduxiu
 * 下载模块
 */
class Download  extends IndexBase
{
    /** 
     * 下载模块
     */
    public function index($id = 0)
    {
         $mod = Mods::find($id);
        if (!$mod) {
            $this->error("找不到内容","index/index/home");
        }else{
            //判断是否含有密码
            if ($mod['pass'] != '') {
                if(!ConPW::hasLock($id,1)){
                    $this->error("你没有权限下载文件","index/indrx/home");
                }
            }
            $url = $mod["file"];
            //下载量增加
            $mod->download++;
            $mod->save();
            return download("../public".$url,$mod["name"].$mod["version"]);
        }
    }
    
} 
