<?php

namespace app\api\controller;
use app\api\common\ApiBase;
use app\common\Mod as Modlist;
use app\index\common\ConPW;
/**
 * Class Mod
 * @author chenduxiu
 * 模组相关api
 */
class Mod extends ApiBase
{
    /**
     * 获取所有的模组列表
     *
     */
    public function list()
    { 
        $arr = Modlist::getModList(1,1,0,false)->toArray(); ;
        $list = [];
        foreach ($arr as $val) {
            $val = $this->clean($val);
            $list[] = $val;
        }
       
       return $this->success($list);
    }
    
    /**
     * 根据id获取模组信息
     *
     */
    public function id($id = 0)
    {
        $arr =  Modlist::getById($id)->toArray();
        if ($arr && $arr["state"] == 1) {
            $arr = $this->clean($arr);
            return $this->success($arr); 
        }else{
            return $this->error("找不到模组信息");
        }
    }
    /**
     * 根据iden获取某个模组的所有版本 
     *
     */
    public function iden($iden = 0)
    {
        $arr = Modlist::getModAllVersion($iden)->toArray();
        if ($arr) {
            $list = [];
            foreach ($arr as $val) {
                $val = $this->clean($val);
                $list[] = $val;
            }
            return $this->success($list);
        }else{
            return $this->error("找不到模组信息");
        }
    }
    /**
     * 下载模块
     *
     */
    public function download($id)
    {       
        $mod = Modlist::getById($id);
        if (!$mod) {
            return $this->error("找不到内容");
        }else{
            //判断是否含有密码
            if ($mod['pass'] != '') {
                if(!ConPW::hasLock($id,1)){
                    return $this->error("密码保护");
                }
            }
            $url = $mod["file"];
            //下载量增加
            $mod->download++;
            $mod->save();
            $info = [
                "name" => $mod["name"]."v".$mod["version"],
                "url"  => $mod["file"],
            ];
            return $this->success($info);
        }
 
    }
    
    /**
     * 输入密码
     *
     */
    public function password()
    {
        $post = $this->getPost("Mod.password");
        if (!$post) {
             return $this->error($this->error);
        }
        $info = ConPW::mod($post["id"],$post["password"]);
        if ($info) {
            return $this->success("验证成功");
        }else{
            return $this->error("密码错误");
        }


    }
    

    
    /**
     * 
     *
     * @return void
     */
    public function test()
    {
        return <<<a
<form action="/api/login/nodebb" method="post">
<input type="text" name="user">
<input name="pw">
<input name="com">
<input name="type" value="1">
<input type="submit">
</form>

a;
    }
    
    
    /**
     * 清除数据中的敏感信息。
     *
     */
    protected function clean($arr)
    {
        $tag = [ 
            "file",
            "pass",
            "userinfo",
            "plateinfo",
        ];
        foreach ($tag as $val) {
           unset($arr[$val]); 
        }
        return $arr;
    }
    
    
}
