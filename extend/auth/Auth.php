<?php
namespace auth;

use think\Controller;
use think\Db;

class Auth extends Controller{

    public function __construct() {

    }

    /**
      * 检查权限
      * @param name string  需要验证的url
      * @param uid  int           认证用户的id
     */
    public function check($name, $uid) {
        if(!cache('auth_group')){
            $this->get_user_group();    //获取用户组，并存缓存里面
        }
        if(!cache('auth_group_access')){
            $this->get_user_group_access();    //获取用户组关联，并存缓存里面
        }
        if(!cache('auth_rule')){
            $this->get_auth_rule();    //获取权限url，并存缓存里面
        }
        if(!cache('auth_rule_group')){
            $this->get_auth_rule_group();    //获取权限url组，并存缓存里面
        }

        $auth_group=cache('auth_group');    //用户组数据
        $auth_group_access=cache('auth_group_access');    //用户组关联
        $auth_rule=cache('auth_rule');    //权限url规则

        //先检查用户所在几个用户组中
        $in_group=[];   //用户所在用户组的组id
        foreach ($auth_group_access as $key => $value) {
            if($value['uid']==$uid){
                $in_group[]=$value['group_id'];
                if($value['group_id']==1){  //假如是超级管理员用户组的话，就记录session
                    session('user_group_id',1);
                }
            }
        }

        //获取用户组所对应的规则
        if(empty($in_group)){
            $this->error('用户没有所属用户组');
        }else{
            $in_rules='';
            foreach ($auth_group as $key => $value) {
                foreach ($in_group as $key2 => $value2) {
                    if($value['id']==$value2 && $value['status']){
                        $in_rules.=$value['rules'].',';
                    }
                }
            }

            if(empty($in_rules)){
                $this->error('用户组被禁用或者用户组没有指定任何权限');
            }

            //获取用户组权限
            $rules_data=[];     //最终用户所拥有的权限
            $rules_arr=explode(',',$in_rules);  //规则id数组
            foreach ($auth_rule as $key => $value) {
                if(in_array($value['id'], $rules_arr) && $value['status']){
                    $rules_data[]=$value;
                }
            }

            if(empty($in_rules)){
                $this->error('该用户没有任何权限或者该权限被禁用了');
            }

            //验证用户权限
            $url_result=false;  //权限验证的结果
            foreach ($rules_data as $key => $value) {
                $safe_name=trim(strip_tags($name)); //过滤访问的url规则
                $name_length=strlen($safe_name);    //访问的url字符串长度
                $rule_length=strlen($value['name']);    //规则的字符串长度

                $safe_name=strtolower($safe_name);  //全部转成小写
                $value['name']=strtolower($value['name']);
                if($safe_name===$value['name'] && $value['status']){  //假如全等，权限验证就通过
                    // dump($safe_name);
                    // d($value['name']);
                    $url_result=true;
                    $json_arr['info']=$value['title'];
                    session('opType',$value['type']);   //记录权限操作类型到session，方便记录日志
                    break;
                }else if(strpos($safe_name,$value['name'])==0){   //假如设定的url是从访问的url的索引开头开始的，就放行
                    if($name_length>$rule_length){  //判断url末尾一个字符串是不是特殊字符
                        $end_str=substr($safe_name,$rule_length,1);   //获取访问的url末尾的后一个字符
                        //preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$end_str);
                        if($value['status']){
                            if($end_str=='?' || $end_str=='#'){
                                $url_result=true;
                                $json_arr['info']=$value['title'];
                                session('opType',$value['type']);   //记录权限操作类型到session，方便记录日志
                                break;
                            }
                        }
                    }
                }

            }

            if(!$url_result){
                $this->error('没有访问权限或者该权限被禁用了');
            }else{
                session('user_in_group',$in_group);     //把用户所在的用户组id数组存进session，方便使用
                $json_arr['status']=true;
                return $json_arr;
            }
        }
    }



    //获取用户组
    private function get_user_group(){
        $result=Db::name('auth_group')->select();
        if($result){
            cache('auth_group',$result);
            return cache('auth_group');
        }else{
            $this->error('获取用户组失败');
        }
    }
    //获取用户组关联
    private function get_user_group_access(){
        $result=Db::name('auth_group_access')->select();
        if($result){
            cache('auth_group_access',$result);
            return cache('auth_group_access');
        }else{
            $this->error('获取用户组关联数据失败');
        }
    }
    //获取权限url
    private function get_auth_rule(){
        $result=Db::name('auth_rule')->select();
        if($result){
            cache('auth_rule',$result);
            return cache('auth_rule');
        }else{
            $this->error('获取权限url失败');
        }
    }
    //获取权限url组
    private function get_auth_rule_group(){
        $result=Db::name('auth_rule_group')->select();
        if($result){
            cache('auth_rule_group',$result);
            return cache('auth_rule_group');
        }else{
            $this->error('获取权限url组失败');
        }
    }

}
