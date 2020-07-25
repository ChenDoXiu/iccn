<?php

namespace app\common;
use app\common\model\Option as OptionModel;

/**
 * Class Option
 * @author chenduxiu 
 */
class Option
{
    static protected $opt;
    /**
     * 设置选项
     *
     */
    /**
     * 查询所有内容，并存入
     *
     * @return void
     */
    protected static function  select()
    {
        if(!self::$opt){
            $sel = OptionModel::select();
            $arr = [];
            foreach ($sel as $val) {
                $arr[$val["name"]] = $val["value"];
            }
            self::$opt = $arr;
        }
    }

    /**
     * 获取所以选项
     *
     * @return array
     */
    public static function getAll()
    {
        self::select();
        return self::$opt;
    }


    /**
     * 修改选项
     *
     * @return void
     */
    protected static function update($name,$val)
    {

        $data = OptionModel::where("name",$name)->find();
        if ($data) {
            $data->value = $val;
            $data->save(); 
        }else{
            $data = new OptionModel(["name" => $name,"value" => $val]);
            $data->save();
        }
        self::$opt[$name] = $val;
    }

    public static function setOpt($name,$val)
    {
        self::select();
        self::update($name,$val);
    }

    /**
     * 获取选项
     *
     * @return string 
     */
    public static function getOpt($name)
    {
        self::select();
        if (isset(self::$opt[$name])) {
            return self::$opt[$name];
        }else{
            return false;
        }
    }

}
