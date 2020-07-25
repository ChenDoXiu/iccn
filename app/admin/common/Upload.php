<?php

namespace app\admin\common;
use think\facade\Filesystem;
use think\facade\Request;
use think\exception\ValidateException;
use think\facade\Config;
/**
 * Class Upload
 * @author chenduxiu
 */
class Upload
{
    /**
     * 存储单个图片
     *
     * @return string
     */
    public static function img($name)
    {
        try{
            $img = Request::file($name); 
        }catch(\Exception $e){
            return false;
        }
        if (!$img) {
            return false;
        }
        $info = self::valiImg($img);
        if (!$info) {
            return false;
        }
        return self::save($img);
    }
    /**
     * 存储多个图片
     *
     * @return array
     */
    public static function imgs($name)
    {
        $imgs = Request::file($name);
        $arr = [];
        foreach ($imgs as $img) {
            $info = self::valiImg($img);
            if (!$info) {
                return false;
            }
            $arr[] = self::save($img);
        }
        return $arr;
    }
    
    /**
     * 存储模组文件
     *
     * @return void
     */
    public static function mod($name)
    {
        $mod = Request::file($name);
        $info = self::valiMod($mod);
        if (!$info) {
            return false;
        }
        return ["path" => self::save($mod,"mods"),"size" => $mod->getSize(),"md5" => $mod->hash("md5")];
    }
    
    /**
     * 存储文件
     *
     */
    protected static function save($file,$path = "image")
    {

        $u = Config::get("filesystem.disks.public.url");
        $path = @Filesystem::putFile($path,$file,"md5");
        $path = $u . "/" .$path;
        return $path;
    }


    /**
     * 验证文件是否是图片
     *
     * @return boolean
     */
    protected static function valiImg($file)
    {
        try{
            $validate = validate(["file" => "file|fileExt:jpg,jpeg,png,gif"]);
            $validate->check(["file" => $file]);
        }catch(ValidateException $e){
            return false;
        }
        return true;
    }
    
    /**
     * 验证文件是否是zip格式
     *
     * @return boolean
     */
    public static function valiMod($file)
    {        
        try{
            $validate = validate(["file" => "file|fileExt:zip,icmod"]);
            $validate->check(["file" => $file]);
        }catch(ValidateException $e){
            return false;
        }
        return true;
    }
    
}
