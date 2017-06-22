<?php
namespace app\manager\controller;

use \think\Request;
use think\Db;
//use think\View;

class Publicfun extends BaseController
{
    //开关插件修改数据库数据(状态)
    public function status_change(){
        $id=input('post.id');

        if(input('?post.on')){
            if(input('post.status') == "true"){
                $arr[input('post.field_name')]=input('post.on');
            }else{
                $arr[input('post.field_name')]=input('post.off');
            }
        }else{
            if(input('post.status') == "true"){
                $arr[input('post.field_name')]=1;
            }else{
                $arr[input('post.field_name')]=0;
            }
        }

        if(!empty(input('post.pre_table'))){
            $status_result=Db::name(input('post.pre_table'))->where('id', $id)->update($arr);
        }else{
            $status_result=Db::table(input('post.table_name'))->where('id', $id)->update($arr);
        }
        
        if($status_result){
            $json_arr['status']=true;
        }else{
            $json_arr['status']=false;
            $json_arr['info']="更新数据库失败，".lsql();
        }
        return $json_arr;
    }

    //公共更新数据库数据
    public function data_update($edit_data=null,$pre_table_name=null,$table_name=null){   //参数（post提交过来的数据，编辑的表名，编辑的不带前缀的表名）
        foreach ($edit_data as $key => $value) {    //格式化接收过来的表单数据
            $edit_arr[$value['name']]=$value['value'];
        }
        $id=$edit_arr['id'];   //操作的id
        $arr=[];    //准备更新数据库的数据
        foreach ($edit_arr as $key => $value) {    //过滤掉id
            if($key=='id'){}else{
                $arr[$key]=$value;
            }
        }

        if($pre_table_name){
            $update_result=Db::name($pre_table_name)->where('id', $id)->update($arr);     //更新到数据库中
        }else{
            $update_result=Db::table($table_name)->where('id', $id)->update($arr);     //更新到数据库中
        }

        if($update_result){    //假如更新成功，就返回成功的json回去
            $json_arr=success_info();
        }else{      //假如更新失败，就返回失败的json回去
            $json_arr=error_info('提交失败');
        }
        return $json_arr;
    }

    //公共删除数据库的数据
    public function data_del($del_data=null,$pre_table_name=null,$table_name=null){       //参数（post提交过来的数据，表名，不带前缀的表名）
        if($pre_table_name){   //假如有表前缀的形式，就优先执行带前缀形式的删除
            $del_result=Db::name($pre_table_name)->delete($del_data);   // 根据主键删除
        }else{
            $del_result=Db::table($table_name)->delete($del_data);
        }

        if($del_result){
            $json_arr=success_info();
        }else{
            $json_arr=error_info("删除失败，".lsql());
        }
        return $json_arr;
    }

    //公共新增数据
    // public function data_add(){
    //     $table_name=input('post.table_name');   //表名
    //     $pre_table_name=input('post.pre_table');    //表前缀形式的表名
    //     $data_list=input('post.data_list/a');
    //     foreach ($data_list as $key => $value) {    //接收过来的表单数据，转化一下，方便存数据库
    //         $arr[$value['name']]=$value['value'];
    //     }

    //     if(!empty($pre_table_name)){   //假如有表前缀的形式，就优先执行带前缀形式的数据插入
    //         $result=Db::name($pre_table_name)->insert($arr);
    //     }else{
    //         $result=Db::table($table_name)->insert($arr);
    //     }
        
    //     if($result){
    //         $json_arr['status']=true;
    //     }else{
    //         $json_arr['status']=false;
    //         $json_arr['info']="新增数据失败，最后执行的SQL语句为：".Db::getLastSql();
    //     }
    //     return $json_arr;
    // }

    //公共新增数据
    public function data_add($add_data=null,$pre_table_name=null,$table_name=null){   //参数（post提交过来的数据，添加的表名，添加的不带前缀的表名）
        //$add_data=input('post.field_data/a');    //获取所有post过来的数据
        foreach ($add_data as $key => $value) {    //格式化接收过来的表单数据
            $arr[$value['name']]=$value['value'];
        }

        if($pre_table_name){
            $add_result=Db::name($pre_table_name)->insert($arr);     //插入到数据库中
        }else{
            $add_result=Db::table($table_name)->insert($arr);     //插入到数据库中
        }

        if($add_result){    //假如插入成功，就返回成功的json回去
            $json_arr=success_info();
        }else{      //假如插入失败，就返回失败的json回去
            $json_arr=error_info('提交失败');
        }
        return $json_arr;
    }

    //公共记录管理员日志
    public function record_admin_log($str="进行了一些操作"){
        $arr['adminID']=session('user_id');
        $arr['opType']=1;
        $arr['opTime']=time();
        $arr['opIp']=getIP();
        $msg="管理员(".session('user_name')."):".$str;
        $arr['comment']=$msg;
        $result=Db::table('admoplog')->insert($arr);
        if($result){
            return true;
        }else{
            return false;
        }
    }

    //后台获取在线人数
    public function get_inline(){
        $inline_time=time()-60;    //默认计算1分钟内的在线用户
        $inline_arr=[]; //统计在线的人数数组

        try{    //避免缓存错误的重置
            cache('inline');
        }catch(\Exception $e){
            cache('inline',null);
        }
        
        $inline_total=cache('inline');  //获取缓存中的时间数据
        if($inline_total){
            foreach ($inline_total as $key => $value) {
                if($value['time'] >= $inline_time){
                    $inline_arr[]=$value;
                }
            }
        }else{
            $inline_error=cache('inline_error');
            if($inline_error>=3){
                cache('inline_error',0);
                cache('inline',null);
            }else{
                $inline_error++;
                cache('inline_error',$inline_error);
            }
        }

        //$inline_num=Db::table('user')->where('lasttime>='.$inline_time)->count();
        if($inline_arr){
            $json_arr['status']=true;
            $json_arr['info']=count($inline_arr);
        }else{
            $json_arr['status']=false;
            $json_arr['info']=0;
            $json_arr['error_info']='当前没有在线用户，或者查询出错';
        }
        return $json_arr;
    }

    //自定义调试操作
    public function mytest_action(){
        //d(APP_PATH."index/view/ttt");
        //$this->delDirAndFile(APP_PATH."index/view/Bbgl",true);
        $update_result=Db::table('user')->where('status',1)->setInc('money', 4);
        if($update_result){
            echo $update_result.'<br>';
        }
    }

    /**
     * 删除目录及目录下所有文件或删除指定文件
     * @param str $path   待删除目录路径
     * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
     * @return bool 返回删除状态
     */
     public function delDirAndFile($path, $delDir = FALSE) {
        $handle = opendir($path);
        if ($handle) {
            while (false !== ( $item = readdir($handle) )) {
                if ($item != "." && $item != "..")
                    is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
            }
            closedir($handle);
            if ($delDir)
                return rmdir($path);
        }else {
            if (file_exists($path)) {
                return unlink($path);
            } else {
                return FALSE;
            }
        }
     }

     //上传背景音乐(还需调整为thinkphp5可用)
    public function upload_bg_music(){
        $status=upload_file("/Egg/");
        $this->ajaxReturn($status,"JSON");
    }

    //上传文件
    public function ajax_upload_file($path='/',$sub_name=''){
        $json_arr=[];
        // $upload = new \Upload();  // 执行thinkphp3.2的上传方法，实例化上传类
        // $upload->maxSize = 51457280;    // 设置附件上传大小，默认设置约50MB
        // $upload->exts = array('jpg', 'gif', 'png', 'jpeg','mp3','ogg','wav');   // 设置附件上传类型
        // $upload->rootPath = ROOT_PATH . 'public' . DS . 'uploads';   // 设置附件上传根目录
        // $upload->savePath = $path;  // 设置附件上传（子）目录
        // $upload->subName = $sub_name;   // 子目录名称
        // // 上传文件
        // $info = $upload->upload($post_file_name);

        // if(!$info) {    // 上传错误返回错误信息
        //     $error_info=$upload->getError();
        //     $json_arr['status']=false;
        //     $json_arr['info']=$error_info;
        // }else{  // 上传成功
        //     $json_arr['status']=true;
        //     $json_arr['data']=$info;
        // }
        // return $json_arr;

        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');

        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg
            //echo $info->getExtension()."<br>";
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            //echo $info->getSaveName()."<br>";
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            //echo $info->getFilename()."<br>"; 

            $json_arr['status']=true;
            $json_arr['info']=[];
            $json_arr['info']['filetype']=$info->getExtension();    //文件类型
            $json_arr['info']['url']=DS . 'uploads'. DS .$info->getSaveName();    //文件保存的路径
            $json_arr['info']['savename']=$info->getFilename();    //文件保存的名字

            //准备写入上传文件表中
            $add_arr['title']=$info->getFilename(); //保存后的文件名
            $add_arr['url']=DS . 'uploads'. DS .$info->getSaveName(); //保存后的文件路径
            $add_arr['adminid']=session("user_id"); //管理员id
            $add_arr['admin_name']=session("user_name");    //管理员名称
            $add_arr['add_time']=time();    //上传的时间戳
            $add_arr['add_date']=date('Y-m-d H:i:s',$add_arr['add_time']);  //上传的日期时间格式
            $add_arr['update_time']=time();    //更新的时间戳
            $add_arr['update_date']=date('Y-m-d H:i:s',$add_arr['update_time']);  //更新的日期时间格式

            $upload_result=Db::name('uploadfile')->insertGetId($add_arr);
            if($upload_result){
                $json_arr['info']['file_id']=$upload_result;
            }else{
                $json_arr['info']['file_id']=false;
            }
        }else{
            // 上传失败获取错误信息
            //echo $file->getError();
            $json_arr['status']=false;
            $json_arr['info']=$file->getError();
        }
        return json_encode($json_arr);
    }

    //删除上传文件
    public function ajax_del_upload_file(){
        $id=input('post.id');
        $file_info=Db::name('uploadfile')->where('id',$id)->find(); //上传文件的信息

        if(session('user_group_id')==1){    //假如是超级管理员组的话
            $del_result=Db::name('uploadfile')->delete($id);
        }else{
            $del_result=Db::name('uploadfile')->where('adminid',session('user_id'))->delete($id);
        }

        if($del_result){
            $del_file_result=delDirAndFile(ROOT_PATH . 'public' . $file_info['url']);   //删除实际图片文件
            if($del_file_result){
                $json_arr['status']=true;
            }else{
                $json_arr['status']=false;
                $json_arr['info']='删除图片文件失败，或没有删除文件的系统权限';
            }
        }else{
            $json_arr['status']=false;
            $json_arr['info']='不能删除其它用户的图片，或没有该图片';
        }
        return $json_arr;
    }

    //编辑页面获取上传文件
    public function get_edit_upload(){
        $ids=input('post.ids');
        //$ids=str_replace(';',',',$ids);
        $upload_data=get_upload_data($ids);
        if($upload_data){
            $json_arr['status']=true;
            $json_arr['info']=$upload_data;
        }else{
            $json_arr['status']=false;
            $json_arr['info']='没有找到文件数据';
        }
        return $json_arr;
    }
}
