<?php
namespace app\index\controller;

use think\Db;
use think\View;
class Index
{
    public function index()
    {
    	$view = new View();
    	return $view->fetch();
    }
}
