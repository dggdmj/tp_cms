<?php
namespace app\index\controller;

use think\Db;
use think\Controller;

class BaseController extends Controller
{
    public function __construct()
    {
		// echo '网站修复中..';die;
        $this->get_web_site();   //获取系统设置
    }

        //获取系统设置
    private function get_web_site(){
        if(!session('?web_site')){
            $web_site=Db::name('config')->select();   //查询系统设置表
            $list = array();
            foreach ($web_site as $key => $value) {
            	$list[$value['name']] = $value;
            }
            session('web_site',$list);
        }
    }
}
