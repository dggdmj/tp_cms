<?php
namespace app\manager\controller;

use think\Controller;
use think\Db;
use think\View;

class Login extends Controller
{
    public function index()
    {
            //假如已经是登录状态就跳转到访问的页，或后台首页
            if(session('?user_data')){
                if(cookie('?forward_vist')){
                	$this->redirect(cookie('forward_vist'));
                }else{
                	$this->redirect('/manager');
                }
            }

            //cache('setting',null);    //调试，清空设置数据缓存
            $view = new View(); //实例化模板对象
            load_setting($view);     //载入后台设置数据

            return $view->fetch();	//渲染登录模板
    }

    //验证登录
    public function check_login(){
    	$username=input('post.username');      //用户名
              $username=trim($username);        //去两边空格
    	$password=input('post.password');  //密码
              $password=trim($password);
    	$verify=input('post.verify');  //验证码
              $verify=trim($verify);
              $nine_password=input('post.nine_password');  //9宫格密码

              $setting=cache('setting');    //获取设置里的缓存
    	//验证码校验部分
	if($setting['login_allow_verify']){
		$verify_result=captcha_check($verify);
		if(!$verify_result){
			$json_arr['status']=false;
			$json_arr['info']='验证码错误';
			$json_arr['type']=1;
                                           $json_arr['src']=captcha_src();  //重新生成验证码
			return $json_arr;
		}
	}

    	//校验用户名密码
    	$map['username']=$username;
              if($nine_password){
                  $new_nine_password=think_encrypt($nine_password); //9宫格加密密码
                  $map['nine_password']=$new_nine_password;
              }else{    //常规密码
                  $new_password=think_encrypt($password); //加密密码
                  $map['password']=$new_password;
              }
	
	$login_result=Db::name('admin')->where($map)->find();
	
	if($login_result){
                if($login_result['status']==0){ //假如账户被禁用
                    $json_arr['status']=false;
                    $json_arr['info']='该账户被禁用';
                    $json_arr['type']=3;
                    $json_arr['src']=captcha_src();  //重新生成验证码
                }else{  //登录成功！
                    $json_arr['status']=true;
                    session("user_data",$login_result); //记住登录状态
                    session("user_id",$login_result['id']); //记住用户id
                    session("user_name",$login_result['username']); //记住用户名

                    //更新登录信息
                    $last_login_time=time();
                    //$request=request();
                    $arr['visit_url']=cookie('forward_vist');   //登录后访问的地址
                    $arr['last_login_time']=$last_login_time;   //登录的时间戳
                    $arr['last_login_time_date']=date("Y-m-d H:i:s",$last_login_time);  //登录后的时间格式
                    $arr['last_login_ip']=getIP();  //登录时的ip
                    $update_login=Db::name('admin')->where('id', $login_result['id'])->update($arr);
                    if($update_login){   //假如更新用户数据成功，就更新session
                        $login_result['last_login_time']=$arr['last_login_time'];
                        $login_result['last_login_time_date']=$arr['last_login_time_date'];
                        $login_result['last_login_ip']=$arr['last_login_ip'];
                        $login_result['visit_url']=$arr['visit_url'];
                        session('user_data',$login_result);
                    }

                    if(cookie('?forward_vist')){
                        $json_arr['url']=cookie('forward_vist');
                    }else{
                        $json_arr['url']='/manager';
                    }
                }
	}else{ //用户名或密码错误
		$json_arr['status']=false;
    		$json_arr['info']='用户名或密码错误';
    		$json_arr['type']=2;
                        $json_arr['src']=captcha_src();  //重新生成验证码
	}
	return $json_arr;
    }

    //刷新验证码
    public function refresh_verify(){
            return captcha_src();
    }

    //注销登录
    public function login_out(){
            session('user_data', null);
            session("user_id",null);
            session("user_name",null);
            session("user_group_id",null);
            //$this->success('注销成功','/manager/login');
            $this->redirect('/manager');
    }
}
