<?php
declare (strict_types = 1);

namespace app\admin\validate;

use think\Validate;

class Menu extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	     "title|标题"     => "require|length:1,50",
	     "url|链接"       => "url",
	     "plate|板块"     => "require|number",
	];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [];
}
