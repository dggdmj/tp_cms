<?php
namespace app\manager\controller;

use \think\Request;
use think\Db;
use think\View;
use app\manager\controller\Publicfun; //引入公共类方法

class Mycustom extends BaseController
{
    public function __construct(){
        global $pub;
        $pub=new Publicfun; //实例化公共方法类
    }

    // 默认列表页
    public function index() {
        $username=input('get.username');     //搜索用户名

        $start_time=input('get.opDate_start');      //操作的开始时间
        $start_time_stamp=strtotime($start_time);   //操作的开始时间戳

        $end_time=input('get.opDate_end');      //操作的结束时间
        $end_time_stamp=strtotime($end_time);   //操作的结束时间戳

        $type=input('get.opType');    //搜索权限的类型组

        $map=[];    //搜索的条件组合
        if($username){
            $map[]='a.username like "%'.$username.'%"';
        }
        if($start_time){
            $map[]='alog.opTime>='.$start_time_stamp;
        }
        if($end_time){
            $map[]='alog.opTime<='.$end_time_stamp;
        }
        if($type!='all' && !empty($type)){
            $map[]='alog.opType='.$type;
        }

        if(!empty($map)){
            $map_str=implode(' and ', $map);
        }

        // //表关联查询示例
        // if(!empty($map)){   //假如是搜索的话
        //     $list_data=Db::name('admin_log')->alias('alog')->join(config('database.database').'.ys_admin a','alog.adminID = a.id','LEFT')->join(config('database.database').'.ys_auth_rule_group arg','alog.opType = arg.id','LEFT')->where($map_str)->order('alog.opTime desc')->paginate(config('paginate.list_rows'),false,['query'=>input('get.')]);    //查询出所有的管理员日志记录，并且分页
        // }else{  //默认不搜索
        //     $list_data=Db::name('admin_log')->alias('alog')->join(config('database.database').'.ys_admin a','alog.adminID = a.id','LEFT')->join(config('database.database').'.ys_auth_rule_group arg','alog.opType = arg.id','LEFT')->order('alog.opTime desc')->paginate(config('paginate.list_rows'),false,['query'=>input('get.')]);    //查询出所有的管理员日志记录，并且分页
        // }

        if(!empty($map)){   //假如是搜索的话
            $list_data=Db::name('mycustom')->where($map_str)->order('id desc')->paginate(config('paginate.list_rows'),false,['query'=>input('get.')]);
        }else{  //默认不搜索
            $list_data=Db::name('mycustom')->order('id desc')->paginate(config('paginate.list_rows'),false,['query'=>input('get.')]);
        }
        $list_data_content=$list_data->toArray();  //转成数组后的实际数据

        $title_data=Db::query("SELECT COLUMN_NAME,COLUMN_COMMENT,DATA_TYPE,COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'ys_mycustom' AND TABLE_SCHEMA = '".config('database.database')."'");     //查出字段信息

        $view = new View();
        $view->list_data = $list_data;
        $view->list_data_content = $list_data_content['data'];
        $view->title_data=$title_data;
        return $view->fetch();
    }

    


    //添加
    public function data_add(){
        global $pub;
        if (Request::instance()->isPost()){     // 是否为 POST 请求，用于添加处理数据
            $json_arr=$pub->data_add(input('post.field_data/a'),input('post.pre_table'));   //获取所有post过来的数据，并插入到数据库中
            return $json_arr;


        }else{      //用于显示添加数据的模板



            $view = new View();

            $data=Db::query("SELECT COLUMN_NAME,COLUMN_COMMENT,DATA_TYPE,COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'ys_mycustom' AND TABLE_SCHEMA = '".config('database.database')."'");     //查出字段信息
            //$list=Db::name('setting')->limit(5)->select();  //查出示例数据
            
            $view->datas=$data;
            //$view->list=$list;
            return $view->fetch();
        }
    }



    //编辑
    public function data_edit(){
        global $pub;
        if (Request::instance()->isPost()){ //假如为post请求，就异步修改访问权限
            $json_arr=$pub->data_update(input('post.field_data/a'),input('post.pre_table'));   //获取所有post过来的数据，并插入到数据库中
            return $json_arr;



        }else{  //非post请求，就显示出编辑页面



            $id=input('get.id');
            $edit_data=Db::name('mycustom')->where('id',$id)->find();    //查询出对应id访问权限数据

            $data=Db::query("SELECT COLUMN_NAME,COLUMN_COMMENT,DATA_TYPE,COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'ys_mycustom' AND TABLE_SCHEMA = '".config('database.database')."'");     //查出字段信息

            if($edit_data && $data){
                $view = new View();
                $view->edit_data = $edit_data;
                $view->datas=$data;
                return $view->fetch();
            }else{
                $this->error('没有找到该记录');
            }
        }
    }







    //删除
    public function data_del(){
        global $pub;
        if(input('?post.id_arr')){   //批量删除
            $json_arr=$pub->data_del(input('post.id_arr/a'),input('post.pre_table'));   //获取所有post过来的数据，并插入到数据库中



        }else{  //单个删除



            $json_arr=$pub->data_del(input('post.id'),input('post.pre_table'));   //获取所有post过来的数据，并插入到数据库中
        }
        return $json_arr;
    }
}