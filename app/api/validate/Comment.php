<?php
declare (strict_types = 1);

namespace app\api\validate;

use think\Validate;

class Comment extends Validate
{
    protected $rule = [
        "comment"   => "require",
        "id"        => "number|require",
        "com"       => "number",
        "type"      => "in:1,2|require",
    ];
}
