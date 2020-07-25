<?php

namespace app\admin\model;
use think\facade\Config;
use think\facade\Session;
use think\Model;
/**
 * Class User
 * @author chenduxiu
 */
class User extends Model
{
	public function getNameAttr($name,$user){
		if ($name === "") {
			return $user['user'];
		}else{
			return $name;
		}
	}
   /**
    * 登录
    *
    * @return boolean
    */
   public function login($user,$pw)
   {

       if ($pw === false) {
           return false;
       }
       $where = [
           "user" => $user,
           "active"=> 1,
       ];
       $user = $this->where($where)->find();
       if (!$user) {
           return false;
       }
       if(password_verify($pw,$user["pw"])){
           $user["usertype"] = "system";
           Session::set("user",$user->toArray()); 
           return true;
       }else{
           return false;
       }
   }
   /**
    * nodebb登录对接
    */
   public function nodeBBLogin($user,$pw)
   {
      
       $userinfo = $this->getNBUser($user);
       if (!$userinfo) {
           return false;
       }
       $userinfo["auth"] = 2;
       $userinfo["id"] = $userinfo["uid"];
       $userinfo["name"] = $userinfo["username"];
       $userinfo["usertype"] = "nodebb";
       $uid = $userinfo["uid"];
       $token = $this->getNBToken($uid,$pw);
       if (!$token) {
           return false;
       }else{
           Session::set("user",$userinfo);
           Session::set("nodebb_token",$token);
           return true;
       }
   }
   
   /**
    * 获取node BB用户信息,失败返回false
    */
   protected function getNBUser($user)
   {
       $curl = curl_init();

       curl_setopt_array($curl, array( 
           CURLOPT_URL => "https://forum.adodoz.cn/api/user/{$user}",
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
           return json_decode($response,true);
       }
   }
   
   /**
    * 根据uid获取nodebb的token
    */
   protected function getNBToken($uid,$pw)
   {
       $curl = curl_init();

       curl_setopt_array($curl, array(
           CURLOPT_URL => "https://forum.adodoz.cn/api/v1/users/{$uid}/tokens",
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => "",
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => "POST",
           CURLOPT_POSTFIELDS => "password={$pw}",
           CURLOPT_HTTPHEADER => array(
               "Content-Type: application/x-www-form-urlencoded"
           ),
       ));

       $response = curl_exec($curl);
       $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
       if ($httpCode !== 200) {
           return false;
       }else{
           $token = json_decode($response,true)["payload"]["token"];
           return $token;
       }
   }
   
   /**
    * 判断nodebb用户是否登录
    */
   protected function isNBLogin($token)
   {
       $curl = curl_init();

       curl_setopt_array($curl, array(
           CURLOPT_URL => "https://forum.adodoz.cn/api/login",
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => "",
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => "GET",
           CURLOPT_HTTPHEADER => array(
               "Authorization: Bearer {$token}"
           ),
       ));

       $response = curl_exec($curl);
       $str = substr($response,2,4);
       if ($str === "user") {
          return true;
       }else{
           return false;
       }
   }
   
   /**
    * 判断是否登录
    * 如果登录返回用户信息
    *
    * @return boolean|Model
    */
   public function isLogin()
   {
       $user = Session::get("user"); 
       if (!$user) {
           return false;
       }
       //系统用户登录
       if ($user["usertype"] == "system") {
           $where = [
               "user" => $user["user"],
               "pw"   => $user["pw"],
               "active"=> 1,
           ];
           $user = $this->where($where)->find();

           if ($user) {
               $user["usertype"] = "system";
               Session::set("user",$user);
               return $user;    
           }else{
               return false;
           }
       }
       //nodebb用户登录
       if ($user["usertype"] == "nodebb") {
          $token = Session::get("nodebb_token"); 
          $login = $this->isNBLogin($token);
          if ($login) {
              return $user;
          }else{
              return false;
          }
       }

   }

   /**
    * 登出
    *
    * @return void
    */
   public function loginOut()
   {
       Session::clear();
   }
   /**
    * 设置密码
    *
    */
   public function setPwAttr($pw)
   {
       return password_hash($pw, PASSWORD_DEFAULT);
   }
   

}
