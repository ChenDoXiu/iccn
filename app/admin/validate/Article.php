<?php
declare (strict_types = 1);

namespace app\admin\validate;

use think\Validate;

class Article extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
    protected $rule = [
        "title|标题"        => "require|length:2,50",
        "con|正文"     => "require",
        "permiss|权限"      => "require|number",
        "plate|板块"        => "require|number",
        "pw|密码"         => "regex:[0-9a-zA-Z]{4,15}",
        "absrtact|摘要"     => "length:5,200",
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
    ];
}
