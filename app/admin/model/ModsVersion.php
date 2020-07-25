<?php
declare (strict_types = 1);

namespace app\admin\model;

use think\Model;

/**
 * @mixin think\Model
 */
class ModsVersion extends Model
{
    protected $json = ["version_list"];
}
