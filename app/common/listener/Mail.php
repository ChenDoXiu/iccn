<?php
declare (strict_types = 1);

namespace app\common\listener;
use app\common\Mail as M;
class Mail
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event)
    {
        M::sendEvent();
    }    
}
