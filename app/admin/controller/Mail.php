<?php

namespace app\admin\controller;
use app\common\Mail as M;
/**
 * Class Mail
 * @author chenduxiu
 * 邮件异步发送，由Ajax调用
 */
class Mail
{
    /**
     * 发送存储的所有邮件
     *
     * @return void
     */
    public function index()
    {
        M::sendEvent();
    }
    
}
