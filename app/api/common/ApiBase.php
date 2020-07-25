<?php

namespace app\api\common;
use think\App;
use think\exception\ValidateException;
use think\Validate;
use think\facade\Request;
use app\admin\model\User;
/**
 * Class ApiBase
 * @author chrnduxiu
 * api控制器基类
 */
class ApiBase
{
    protected $request;
    protected $user;
    protected $userModel;
    /**
     * 初始化方法
     *
     */
    protected function initialize()
    {
        $this->app     = new App();
        $this->request = $this->app->request;
    }
    /**
     * 获取用户信息$
     *
     */
    protected function getUser()
    {
        $user = new User();
        $this->userModel = $user;
        $user = $user->isLogin();
        $this->user = $user;
    }
    /**
     * 错误
     *
     */
    protected function error($info)
    {
        return json($this->infoTemp("error",$info,[]));;
    }
    /**
     * 正确
     */
    protected function success($data,$info = [])
    {
        return json($this->infoTemp("success",$info,$data));
    }
    /**
     * 返回值模板
     *
     * @return array
     */
    protected function infoTemp($state,$info,$data)
    {
        $arr = [
            "state" => $state,
            "info"  => $info,
            "data"  => $data,
        ];
        return $arr;
    }
    
    /**
     * 获取post数据
     *
     */
    protected function getPost($vd = false)
    {
        $post = Request::post();
        if (!$post) {
            $this->error = "找不到post参数";
            return false;
        }
        if ($vd) {
            $info = $this->validate($post,$vd);
            if ($info !== true) {
                $this->error = $info;
                return false;
            }
        }
        return $post;
    }
    
        /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $va = "app\\api\\validate\\" . $validate;
            $v = new $va();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }


        try{
        $v->failException(true)->check($data);
        }catch(ValidateException $e){
            return $e->getError(); 
        }
        return true;
    }
    

    
}
