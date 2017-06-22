<?php
namespace app\manager\controller;

use think\Controller;
use think\Db;

class BaseController extends Controller
{
    public function __construct()
    {
        load_setting();  //载入后台设置数据

        $this->check_login();	//首先检查用户是否登录

        $this->check_auto_clear();  //检查是否有设置自动清理数据库和缓存的操作

        //检查是否为顶级管理员
        if(session("user_data.type")==1){
            session('user_group_id',1);     //赋予最高管理员组权限
        }else{  //假如不是顶级管理员，就检查权限
            $this->check_user();    //检查用户是否有权限访问
        }
        $this->check_other_user();  //检查是否有多人登录账户

        $this->load_top_menu(); //取出顶部菜单(并存缓存里)
        $this->format_left_menu(); //格式化左边菜单数据
    }

    

    //首先检查用户是否登录
    private function check_login(){
        if(session('?user_data')==false){   //假如是未登录状态（即session记录的用户数据不存在），就跳转到登录页
            $request=request(); //请求信息的对象
            if($request->path()!='manager/login'){  //假如页面地址等于登录页的判断
                cookie('forward_vist',$request->url(true));  //记录之前访问的页面完整url，以便登录之后跳转
            }else{
                cookie('forward_vist','/manager');
            }
            $this->redirect('/manager/login');  //跳转到登录页
        }
    }

    //检查是否有设置自动清理数据库和缓存的操作
    private function check_auto_clear(){
        //session('first_clear',null);    //调试
        if(session('?first_clear')){}else{      //登录时清理
            $setting=cache('setting');    //获取设置里的缓存
            //判断是否需要自动清理管理员日志
            if($setting['auto_clear_adminlog']){
                $delete_result=Db::name('admin_log')->where('opTime','<=',strtotime("-3 month"))->delete();
                if(!$delete_result){
                    t('管理员日志删除数据失败，执行的sql为：'.Db::getLastSql().'，数据剩余：'.Db::name('admin_log')->count().'条');
                }else{
                    t('管理员日志删除数据成功！，删除了：'.$delete_result.'条，数据剩余：'.Db::name('admin_log')->count().'条');
                }
            }

            //判断是否需要自动清理runtime缓存目录
            if($setting['auto_clear_runtime']){
                if(cache('clear_runtime_wait_days')){   //假如有记录等待天数的缓存，就执行判断
                    $clear_wait_days=(int)$setting['auto_clear_runtime'];   //删除缓存目录需要等待的天数
                    $clear_wait_days_stamp=$clear_wait_days*24*3600;    //转成时间戳（秒）

                    $clear_runtime_time=cache('clear_runtime_wait_days');   //开始计算的时间
                    $now_time=time();   //当前的时间
                    if($now_time - $clear_runtime_time >= $clear_wait_days_stamp){      //时间到了，该清除runtime缓存目录了
                        $del_result=delDirAndFile(RUNTIME_PATH,true);   //删除runtime缓存目录下的所有文件
                        if(!$del_result){
                            t('runtime缓存目录删除失败，执行删除的时间：'.date("Y-m-d H:i:s"));
                        }else{
                            t('runtime缓存目录删除成功！执行删除的时间：'.date("Y-m-d H:i:s"));
                        }
                    }
                }else{  //假如没有记录等待天数的缓存，就记录，以便时间到了，清理runtime缓存目录
                    cache('clear_runtime_wait_days',time());
                }
            }

            session('first_clear',1);
        }
    }

    //检查用户是否有权限访问
    private function check_user(){
        $request=request(); //获取URL信息的对象，以便获取路由的地址判断权限
        $url=$request->path();

        $Auth = new \auth\Auth();   //实例化权限验证类
        $result=$Auth->check($url,session('user_data.id')); //验证

        if($result['status']){      //验证成功
            //d($result['info']);
            $setting=cache('setting');
            if($setting['record_admin_log']){  //假如开启记录日志，就记录
                admin_log($result['info']); //记录管理员日志
            }
        }
    }

    //检查是否有多人登录账户
    private function check_other_user(){
        if(session('?first_visit')){    //第一次登录时不判断
            $setting_data=cache('setting');   //取出系统设置数据
            $user_data=session('user_data');    //取出当前登录用户的session数据
            if($setting_data['single_login']){
                $login_result=Db::name('admin')->where('id',$user_data['id'])->find();  //取出用户在数据库中的最新数据
                if($login_result['last_login_ip'] != $user_data['last_login_ip']){  //假如有别人在其它地方登录，就注销，弹出提示
                    session('user_data', null);
                    session("user_id",null);
                    session("user_name",null);

                    session("user_group_id",null);

                    session('first_visit', null);
                    $notice_txt='该账号在ip：'.$login_result['last_login_ip'].'处登录';
                    $this->error($notice_txt,'/manager','',60);
                }
            }
        }else{
            session('first_visit',1);   //第一次登录的标识，以便后续判断多用户登录账号
        }
    }

    //取出顶部菜单(并存缓存里)
    private function load_top_menu(){
        //cache('topMenu',null);   //调试
        if(cache('topMenu')){
            return cache('topMenu');
        }else{
            $result=Db::name('manager_topmenu')->order('weight asc')->select();
            if($result){
                cache('topMenu',$result);
                return cache('topMenu');
            }else{
                t('查询后台顶部菜单失败');
            }
        }
    }

    //格式化左边菜单数据
    protected function format_left_menu(){
        $left_menu=$this->load_left_menu(); //取出左边菜单(并存缓存里)
        if($left_menu){
            //整理出快捷的常用功能菜单，在左边的顶部
            $has_quick_menu=false;
            foreach ($left_menu as $key => $value) {
                if($value['quick']){     //假如显示，且为快捷菜单
                    if(empty($value['type']) or in_array($value['type'],session('user_in_group'))){     //假如有设置权限显示
                        $has_quick_menu=true;
                        $left_quick_menu[]=$value;
                        cache('left_quick_menu',$left_quick_menu);
                    }
                }
            }
            if(!$has_quick_menu){
                cache('left_quick_menu',null);
            }

            //首先将id作为数组的键，以便后续取值操作
            foreach ($left_menu as $key => $value) {
                $left_menu_id[$value['id']]=$value;
            }

            foreach ($left_menu as $key => $value) {    //先取出一级菜单
                if($value['is_show'] && $value['one']){ //假如状态为显示，且标识为一级菜单，就执行
                    if(empty($value['type']) or in_array($value['type'],session('user_in_group'))){ //假如没有设定权限，或者菜单为当前用户所属用户组的话，就执行
                        $left_menu_list[$value['id']]=[];   //一级菜单数组
                        $left_menu_list[$value['id']]['title']=$value['title'];     //一级菜单名称
                        $left_menu_list[$value['id']]['blank']=$value['blank'];     //一级菜单的打开方式
                        $left_menu_list[$value['id']]['url_str']=$value['url'];     //url
                        
                        $left_menu_list[$value['id']]['two']=[];   //初始化二级菜单

                        if(empty($value['two'])){     //假如底下没有子菜单的话，就取默认url
                            $left_menu_list[$value['id']]['url']=$value['url'];     //一级菜单的url
                        }else{  //有子菜单的话，就强制把url制空
                            $left_menu_list[$value['id']]['url']='javascript:;';     //一级菜单的空url
                        }

                        //二级菜单
                        if(!empty($value['two'])){
                            $left_two=explode(',',trimtwo($value['two']));  //二级菜单的id集合
                            foreach ($left_two as $key2 => $value2) {
                                if(!empty($left_menu_id[$value2]) && $left_menu_id[$value2]['is_show']){  //假如有该菜单数据，且允许显示，就执行赋值
                                    if(empty($left_menu_id[$value2]['type']) or in_array($left_menu_id[$value2]['type'],session('user_in_group'))){ //假如没有设定权限，或者菜单为当前用户所属用户组的话，就执行
                                        $left_menu_list[$value['id']]['two'][$value2]=[];   //二级菜单数组
                                        $left_menu_list[$value['id']]['two'][$value2]['title']=$left_menu_id[$value2]['title'];  //二级菜单的名称
                                        $left_menu_list[$value['id']]['two'][$value2]['blank']=$left_menu_id[$value2]['blank'];  //二级菜单的打开方式
                                        $left_menu_list[$value['id']]['two'][$value2]['url_str']=$left_menu_id[$value2]['url'];  //二级菜单url

                                        $left_menu_list[$value['id']]['two'][$value2]['three']=[];    //初始化二级下的子菜单

                                        if(empty($left_menu_id[$value2]['two'])){     //假如底下没有子菜单的话，就取默认url
                                            $left_menu_list[$value['id']]['two'][$value2]['url']=$left_menu_id[$value2]['url'];     //二级菜单的url
                                        }else{  //有子菜单的话，就强制把url制空
                                            $left_menu_list[$value['id']]['two'][$value2]['url']='javascript:;';     //二级菜单的空url
                                        }

                                        //假如在二级菜单下有三级菜单，就执行赋值
                                        if(!empty($left_menu_id[$value2]['two'])){
                                            $left_two_three=explode(',',trimtwo($left_menu_id[$value2]['two']));  //二级菜单的子菜单id集合
                                            foreach ($left_two_three as $key3 => $value3) {
                                                if(!empty($left_menu_id[$value3]) && $left_menu_id[$value3]['is_show']){  //假如有该菜单数据，就执行赋值
                                                    if(empty($left_menu_id[$value3]['type']) or in_array($left_menu_id[$value3]['type'],session('user_in_group'))){ //假如没有设定权限，或者菜单为当前用户所属用户组的话，就执行
                                                        $left_menu_list[$value['id']]['two'][$value2]['three'][$value3]=[];
                                                        $left_menu_list[$value['id']]['two'][$value2]['three'][$value3]['title']=$left_menu_id[$value3]['title'];
                                                        $left_menu_list[$value['id']]['two'][$value2]['three'][$value3]['blank']=$left_menu_id[$value3]['blank'];
                                                        $left_menu_list[$value['id']]['two'][$value2]['three'][$value3]['url']=$left_menu_id[$value3]['url'];
                                                        $left_menu_list[$value['id']]['two'][$value2]['three'][$value3]['url_str']=$left_menu_id[$value3]['url'];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // header("Content-type:text/html;charset=utf-8");
            // cache('left_menu_data',$left_menu_list);
            // print_r(cache('left_menu_data'));
            // die;
            return cache('left_menu_data',$left_menu_list);
        }
    }
    //取出左边菜单(并存缓存里)
    private function load_left_menu(){
        //cache('leftMenu',null);   //调试
        if(cache('leftMenu')){
            return cache('leftMenu');
        }else{
            $result=Db::name('left_menu')->order('weight asc')->select();
            if($result){
                cache('leftMenu',$result);
                return cache('leftMenu');
            }else{
                t('查询后台左边菜单失败');
                return false;
            }
        }
    }
}
