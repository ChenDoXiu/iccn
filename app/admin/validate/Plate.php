<?php
declare (strict_types = 1);

namespace app\admin\validate;

use think\Validate;

class Plate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
    protected $rule = [
        "name|板块名称"   => "require|length:1,20",
        "desc|板块描述"   => "require|length:4,60",
        "father|父板块"   => "require|number",
        "audit|审核"      => "require|in:0,1",
        "type|类型"       => "require|in:0,1",
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [];
}
