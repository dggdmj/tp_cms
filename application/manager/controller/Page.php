<?php
namespace app\manager\controller;

use \think\Request;
use think\Db;
use think\View;

class Page extends BaseController
{
    //页面自动生成
    public function index()
    {
            $view = new View();
            return $view->fetch();
    }

    //开始生成
    public function start_page(){
        //$this->error('该功能正在开发中...');

        $create_name=input('post.create_name');   //接收post过来的数据
        $get_rule_name=input('post.rule_name');     //默认权限名称
        if(!$create_name && !$get_rule_name){
            $this->error('名称不能为空');
        }

        $result_info='';    //处理之后的结果信息

        //一、复制文件
        $temp_copy_path_url=APP_PATH."manager".DS."tpl".DS;  //临时存放模板的位置

        $control_tpl_path=APP_PATH."manager".DS."tpl".DS."control".DS.'Mycustom'.EXT;  //控制器模板位置
        $create_control=ucfirst($create_name);  //控制器首字母大写
        $control_tpl_temp_path=$temp_copy_path_url.$create_control.EXT;  //临时存放控制器文件
        $temp_copy=copy($control_tpl_path,$control_tpl_temp_path); //先复制控制器过去，以便稍后进行改名操作
        
        $html_path=APP_PATH."manager".DS."tpl".DS."view".DS;    //html的模板位置
        $html_tpl_path=$html_path.'index.html';
        $html_tpl_path2=$html_path.'data_add.html';
        $html_tpl_path3=$html_path.'data_edit.html';
        $html_tpl_path4=$html_path.'search.html';

        $temp_path_name=$temp_copy_path_url.'index.html';   //列表页
        $temp_path_name2=$temp_copy_path_url.'data_add.html';   //新增页
        $temp_path_name3=$temp_copy_path_url.'data_edit.html';  //编辑页
        $temp_path_name4=$temp_copy_path_url.'search.html'; //搜索页

        $temp_copy2=copy($html_tpl_path,$temp_path_name);    //复制模板文件过去
        $temp_copy3=copy($html_tpl_path2,$temp_path_name2);
        $temp_copy4=copy($html_tpl_path3,$temp_path_name3);
        $temp_copy5=copy($html_tpl_path4,$temp_path_name4);

        if($temp_copy && $temp_copy2 && $temp_copy3 && $temp_copy4 && $temp_copy5){ //假如全数复制成功，就执行接下来的改名
            $result_info.='1.文件复制成功<br>';

            //二、修改名字
            $edit_control_name=$this->replace_file_name($control_tpl_temp_path,$create_name);
            if($edit_control_name){
                $result_info.='2.模板控制器里的名称修改成功<br>';
            }else{
                $this->error('修改模板控制器文件里的名称失败');
            }

            //html模板
            $edit_html_name=$this->replace_file_name($temp_path_name,$create_name);
            $edit_html_name2=$this->replace_file_name($temp_path_name2,$create_name);
            $edit_html_name3=$this->replace_file_name($temp_path_name3,$create_name);
            $edit_html_name4=$this->replace_file_name($temp_path_name4,$create_name);
            if($edit_html_name && $edit_html_name2 && $edit_html_name3 && $edit_html_name4){ //假如全数替换成功，就执行接下来的文件移动
                $result_info.='3.模板html里的名称修改成功<br>';

                //先移动控制器模板
                $manager_path=APP_PATH."manager".DS."controller".DS;    //后台控制器路径
                $control_move_result=rename($control_tpl_temp_path,$manager_path.$create_control.EXT);
                if($control_move_result){
                    $result_info.='4.控制器模板文件移动成功<br>';
                }else{
                    $this->error('控制器模板文件移动失败');
                }

                //再创建模板文件夹，准备移动模板文件
                $floder_result_arr=$this->create_floder_fun($create_name);
                if($floder_result_arr['status']){
                    $result_info.='5.'.$floder_result_arr['info']."<br>";

                    //移动模板html文件
                    $manager_view_path=APP_PATH."manager".DS."view".DS.$create_name.DS;    //后台视图路径
                    $view_move_result=rename($temp_path_name,$manager_view_path.'index.html');
                    $view_move_result2=rename($temp_path_name2,$manager_view_path.'data_add.html');
                    $view_move_result3=rename($temp_path_name3,$manager_view_path.'data_edit.html');
                    $view_move_result4=rename($temp_path_name4,$manager_view_path.'search.html');
                    if($view_move_result && $view_move_result2 && $view_move_result3 && $view_move_result4){    //假如模板html文件全数移动成功，就准备添加权限
                        $result_info.='6.模板html文件移动成功<br>';

                        //给新生成的文件添加默认权限
                        $add_rule_result=$this->add_default_rule($get_rule_name,$create_name);
                        if($add_rule_result){
                            $result_info.='7.添加到默认权限里成功<br><h2 style="color:green">生成成功！<h2>';

                            //返回最终结果
                            $json_arr['status']=true;
                            $json_arr['info']=$result_info;
                            return $json_arr;
                        }else{
                            $this->error('添加默认权限失败');
                        }
                    }else{
                        $this->error('模板html文件移动失败');
                    }
                }else{
                    $this->error($floder_result_arr['info']);
                }
            }else{
                $this->error('修改模板html文件里的名称失败');
            }
        }else{
            $this->error('复制文件失败');
        }
    }



    //添加到默认权限里面去
    private function add_default_rule($add_rule_name,$rule_url){  //参数（填写的默认权限名称，权限url）
        $view_name[]='';    //列表页
        $view_name[]='data_add';    //添加
        $view_name[]='data_edit';   //编辑
        $view_name[]='data_del';    //删除
        $view_title[]='列表页';
        $view_title[]='添加';
        $view_title[]='编辑';
        $view_title[]='删除';

        foreach ($view_name as $key => $value) {
            if(!empty($value)){
                $add_arr['name']='manager/'.$rule_url.'/'.$value;  //权限url
            }else{
                $add_arr['name']='manager/'.$rule_url;
            }
            
            $add_arr['title']=$add_rule_name.$view_title[$key]; //权限名称
            $add_arr['type']=config('DEFAULT_RULE_GROUP_ID');   //给默认权限类型

            $add_result=Db::name('auth_rule')->insertGetId($add_arr);

            $master_rules=Db::name('auth_group')->where('id',config('ADMIN_GROUP_ID'))->find(); //查找最高管理权限组的数据
            $rules_arr=explode(',',$master_rules['rules']);
            $rules_arr[]=$add_result;
            $update_master=Db::name('auth_group')->where('id',config('ADMIN_GROUP_ID'))->update(['rules'=>implode(',',$rules_arr)]); //把新添加的访问权限，默认分配给最高管理用户组

            if($add_result && $update_master){                
                $result=Db::name('auth_rule')->select();
                if($result){
                    cache('auth_rule',$result);
                }else{
                    t('获取权限url失败');
                }

                $result=Db::name('auth_group')->select();
                if($result){
                    cache('auth_group',$result);
                }else{
                    t('获取用户组失败');
                }
            }else{
                $json_arr=error_info('创建权限失败');
                return $json_arr;
            }
        }

        return true;
    }









    //替换文件里面的名称
    private function replace_file_name($file_name,$str){    //参数（文件，要替换的字符串）
        $file_str=file_get_contents($file_name);     //打开文件，读入字符串
        $file_str=str_ireplace("mycustom", $str, $file_str); //替换字符串
        file_put_contents($file_name, $file_str);    //写入文件
        return true;
    }




    //创建文件夹
    private function create_floder_fun($floder_name){  //参数（要创建的文件夹名称）
        $manager_view_path='manager'.DS.'view'.DS;   //默认到后台的视图文件夹中
        $create_path=APP_PATH.$manager_view_path;   //最终创建的路径

        //要创建的多级目录
        $path=$create_path.$floder_name;
        //判断目录存在否，存在给出提示，不存在则创建目录
        if(is_dir($path)){  
            $json_arr['status']=false;
            $json_arr['info']="该目录：".$path." 已经存在";
            return $json_arr;
        }else{
            //第三个参数是“true”表示能创建多级目录，iconv防止中文目录乱码
            $res=mkdir(iconv("GBK", "UTF-8", $path),0777,true); 
            if($res){
                $json_arr['status']=true;
                $json_arr['info']="目录：".$path." 创建成功";
            }else{
                $json_arr['status']=false;
                $json_arr['info']="目录：".$path." 创建失败";
            }
            return $json_arr;
        }
    }
}
