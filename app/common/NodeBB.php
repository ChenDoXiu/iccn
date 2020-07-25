<?php

namespace app\common;
use think\facade\Cache;
/**
 * Class NodeBB
 * @author chenduxiu
 * 获取nodebb的用户
 */
class NodeBB
{
    /**
     * 根据uid获取用户信息
     *
     * @return array;
     */
    public static function getUserInfo($uid)
    {
        if ($arr = self::getSave($uid)) {
            return $arr;
        }
        $arr = self::getinfo($uid);
        if (!$arr) {
            return false;
        }
        self::saveInfo($arr); 

    }

    /**
     * 根据uid获取内容
     *
     */
    protected static function getinfo($uid)
    {        
        $curl = curl_init();

        curl_setopt_array($curl, array( 
            CURLOPT_URL => "https://forum.adodoz.cn/api/user/uid/{$uid}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            return false;
        }else{
            
            $data = json_decode($response,true);
            //处理一些问题
            if ($data["picture"] === null) {
                $data["picture"] = "";
            }
            return $data;
        }

    }
    
    /**
     * 缓存用户信息
     *
     */
    protected static function saveInfo($user)
    {
        $arr = Cache::get("_nodeBBUser",[]);
        $arr[$user["uid"]] = $user;
        Cache::set("_nodeBBUser",$arr);
    }
    /**
     * 根据uid查看用户信息是否被缓存,如果存在，返回用户
     *
     * @return void
     */
    protected static function getSave($uid)
    {
        $arr = Cache::get("_nodeBBUser",[]);
        if (isset($arr[$uid])) {
            return $arr[$uid];
        }
        return false;
    }
    
    
} 
