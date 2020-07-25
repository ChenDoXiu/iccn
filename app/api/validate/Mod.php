<?php
declare (strict_types = 1);

namespace app\api\validate;

use think\Validate;

class Mod extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
    protected $rule = [
        "id"       => "number|require",
        "password" => "require",
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $scene = [
        "password" => ["id","password"],
    ];

}
