<?php

namespace app\admin\common;
use think\facade\Db;
use app\admin\model\Plate as PlateModel;
/**
 * Class Plate
 * @author chenduxiu 
 */
class Plate
{
    /**
     * 递归获取板块数
     *
     * @return void
     */
    public static function getPlateTree($table = "plate",$father = 0,&$res = [])
    {
        $arr = Db::name($table)->where("father" , $father)->select(); 

        if (empty($arr)) {
            return array();
        }
        foreach ($arr as $cm) {
            $thisArr=&$res[];
            $list["id"] = $cm["id"];
            $list["name"] = $cm["name"];
            $list["type"] = $cm["type"];
            $list["data"] = $cm;
            $list["child"] = self::getPlateTree($table,$cm["id"],$thisArr);
            $thisArr = $list;
        }
        return $res;
    }

    /**
     * 获取板块列表
     *
     * @return void
     */
    public static function getPlateList($type = -1,$table = "plate")
    {
        return self::getlist(self::getPlateTree($table),$type); 
    }
    

    /**
     * 根据父板块获取列表
     *
     */
    public static function getPlate($father,$table = "plate")
    {
        $arr = Db::name($table)->where("father" , $father)->select();
        return $arr;
    }
    

    /**
     * 判断板块是否存在
     * $id 板块id
     * $type 板块类型
     * @return boolean
     */
    public static function isExist($id,$type)
    {
        $p = PlateModel::where(["id" => $id,"type" => $type])->find();
        if ($p)  {
            return $p; 
        }else{
            return false;
        }
    }
    




    // *
    // 生成横线

    // @return void
    protected static  function line($count)
    {
        $str = "";
        for ($i = 0; $i < $count; $i++) {
            $str .= "--";
        }
        return $str;
    }



    /* 
    *将板块数分析成一维数组
    * @return void
    */
    protected static function getlist($art,$type = -1,&$arr = [],$count = 0)
    {
        foreach ($art as $value) {
            if ($value["type"] == $type || $type === -1) {
                $arr[] = ["name" =>self::line($count).$value["name"],"id" => $value["id"]];
            }

            if (isset($value["child"])) {
                self::getlist($value["child"],$type,$arr, $count + 1); 
            }

        }

        return $arr;
    }



}
