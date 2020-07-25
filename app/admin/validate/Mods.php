<?php
declare (strict_types = 1);

namespace app\admin\validate;

use think\Validate;

class Mods extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
    protected $rule = [
        "name|标题"         => "require|length:2,50",
        "version|版本号"    => "require|regex:[A-Za-z0-9.-_]{1,20}",
        "iden|模组标识"     => "require|regex:[0-9a-zA-Z]{4,255}",
        "desc|描述"         => "require",
        "permiss|权限"      => "require|number",
        "plate|板块"        => "require|number",
        "pass|密码"         => "regex:[0-9a-zA-Z]{4,20}",
        "absrtact|摘要"     => "length:5,200",
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [];
}
