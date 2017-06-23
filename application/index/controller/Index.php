<?php
namespace app\index\controller;

use think\Db;
use think\View;
use app\index\controller\PublicFun; //引入公共类方法
class Index extends BaseController
{
    public function index()
    {
    	$view = new View();
    	return $view->fetch();
    }
}
