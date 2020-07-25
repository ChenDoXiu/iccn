<?php

namespace app\index\validate;
use think\Validate;;

/**
 * Class Comment
 * @author chenduxiu
 */
class Comment extends Validate
{
    protected $rule = [
        "comment"   => "require|token",
        "id"        => "number",
        "type"      => "in:1,2",
    ];
    protected $message = [
        "comment"   => "评论不能为空",
        "id"        => "系统错误。",
        "type"      => "系统错误",
    ];


}
