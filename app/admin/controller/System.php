<?php

namespace app\admin\controller;
use app\admin\common\AdminBase;
use app\common\Option;
use app\common\Mail;
/**
 * Class System
 * @author chenduxiu
 */
class System extends AdminBase
{
     
    /**
     * 首页
     *
     */
    public function index()
    {

         if ($this->request->has("submit")) {
            $data = $this->getPost();
            Option::setOpt("site-title",$data["title"]);
            Option::setOpt("site-subtitle",$data["subtitle"]);
            if (isset($data["regist"])) {
                Option::setOpt("regist",1);
            }else{
                Option::setOpt("regist",0);
            }
            Option::setOpt("regist-per",$data["per"]);
            $this->setAlert("修改成功","index");

        }
        $this->getAuthGroup();
        $this->assign([
            "title" => Option::getOpt("site-title"),
            "subtitle" => Option::getOpt("site-subtitle"),
            "regist" => Option::getOpt("regist"),
            "per" => Option::getOpt("regist-per"),
        ]);
        return $this->fetch();
    }
    /**
     * 修改meta标签
     *
     */
    public function meta()
    {
        if ($this->request->has("submit")) {
            $data = $this->getPost();
            Option::setOpt("site-desc",$data["desc"]);
            Option::setOpt("site-keyword",$data["key"]);
            $this->setAlert("修改成功","meta");

        }        
        $this->assign([
            "desc" => Option::getOpt("site-desc"),
            "key" => Option::getOpt("site-keyword"),
        ]);
        return $this->fetch();
    }
    /**
     * 邮件设置
     *
     */
    public function mail()
    {
        if ($this->request->has("submit")) {
            $data = $this->getPost();
            Option::setOpt("mail-smtp",$data["smtp"]);
            Option::setOpt("mail-user",$data["user"]);
            Option::setOpt("mail-pass",$data["pass"]);            
            Option::setOpt("mail-mail",$data["mail"]);
            Option::setOpt("mail-name",$data["name"]);
            Option::setOpt("mail-port",$data["port"]);
            $this->setAlert("修改成功","mail");
        }        
        $this->assign([
            "smtp"  => Option::getOpt("mail-smtp"),
            "user"  => Option::getOpt("mail-user"),
            "pass"  => Option::getOpt("mail-pass"),
            "mail"  => Option::getOpt("mail-mail"),
            "name"  => Option::getOpt("mail-name"),
            "port"  => Option::getOpt("mail-port"),
        ]);
        return $this->fetch();
    }
    
    
    
} 
