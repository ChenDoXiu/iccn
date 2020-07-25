<?php
declare (strict_types = 1);

namespace app\admin\controller;
use app\admin\common\AdminBase;
use app\admin\model\Mods;
use app\admin\model\Article;
use app\admin\model\Comment;
class Index extends AdminBase
{
    public function index()
    {
        $mod = Mods::where("state",1)->count();
        $artc = Article::where("state",1)->count();
        $comc = Comment::where("state",1)->count();
        $this->assign([
          "modc" => $mod,
          "artc" => $artc,
          "comc" => $comc,
        ]);
        return $this->fetch();
    }
}
