<?php
namespace app\manager\controller;

use \think\Request;
use think\Db;
use think\View;
//use app\manager\controller\Publicfun; //引入公共类方法

class Index extends BaseController
{
    // public function __construct(){
    //     global $pub;
    //     $pub=new Publicfun; //实例化公共方法类
    // }

    //后台首页
    public function index()
    {
            $view = new View();
            return $view->fetch();
    }

    //设置
    public function setting()
    {
        //提交过来数据更新到数据库中
        if (Request::instance()->isPost()){     // 是否为 POST 请求
            $update_data=input('post.');    //接收过来的数据
            $setting=cache('setting');  //缓存中的数据
            $json_arr['info_arr']=[];   //详细信息数组
            $json_arr['info']='';   //详细信息
            $success_status=0;  //修改成功的标识
            foreach ($update_data as $key => $value) {
                if($value!=$setting[$key]){ //假如有修改，就执行更新数据库
                    $update_result=Db::name('setting')->where('name', $key)->update(['val'=>$value]);
                    if($update_result){
                        $success_status=1;
                        $json_arr['info_arr'][]='更新'.$key.'的值为：'.$value.'成功';
                        $json_arr['info']='更新'.$key.'的值为：'.$value.'成功<br>';
                    }else{
                        $json_arr['info_arr'][]='更新'.$key.'的值为：'.$value.'失败';
                        $json_arr['info']='更新'.$key.'的值为：'.$value.'<b style="red">失败</b><br>';
                    }
                }
            }

            if($success_status){
                $json_arr['status']=true;
                t($json_arr['info']);   //打印到调试trace
                $json_arr['info']=null;

                $result=Db::name('setting')->select();
                if($result){
                    foreach ($result as $key => $value) {       //用键值对形式存入缓存，方便使用
                        $arr[$value['name']]=$value['val'];
                    }
                    cache('setting',$arr);  //将设置数据存入服务器缓存
                }else{
                    t('查询系统设置表失败');
                }
            }else{
                $json_arr['info'].='<br>更新失败，或未做任何更改，'.lsql();
            }            
            return $json_arr;
        }else{  //默认是显示模板的
            $setting_data=Db::name('setting')->select();
            $view = new View();
            $view->setting = $setting_data;
            return $view->fetch();
        }
    }

    //新增设置
    public function setting_add(){
        if (Request::instance()->isPost()){     // 是否为 POST 请求
            $add_data=input('');
            $add_result=Db::name('setting')->insert($add_data);
            if($add_result){
                $result=Db::name('setting')->select();
                if($result){
                    foreach ($result as $key => $value) {       //用键值对形式存入缓存，方便使用
                        $arr[$value['name']]=$value['val'];
                    }
                    cache('setting',$arr);  //将设置数据存入服务器缓存
                }else{
                    t('查询系统设置表失败');
                }

                $json_arr=success_info();
            }else{
                $json_arr=error_info();
            }
            return $json_arr;
        }else{
            $data=Db::query("SELECT COLUMN_NAME,COLUMN_COMMENT,DATA_TYPE,COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'ys_setting' AND TABLE_SCHEMA = '".config('database.database')."'");     //查出字段信息
            $list=Db::name('setting')->limit(5)->select();  //查出示例数据
            $view = new View();
            $view->datas=$data;
            $view->list=$list;
            return $view->fetch();
        }
    }

    //删除设置
    public function setting_del(){
        if (Request::instance()->isPost()){     // 是否为 POST 请求
            $id=input('post.id');   //id值
            $table_name=input('post.table_name');   //完整表名
            $pre_table_name=input('post.pre_table');    //表前缀形式的表名

            if($pre_table_name && $id){   //假如有表前缀的形式，就优先执行带前缀形式的删除
                $del_result=Db::name($pre_table_name)->delete($id);   // 根据主键删除
            }else if($table_name && $id){
                $del_result=Db::table($table_name)->delete($id);
            }

            if($del_result){
                $result=Db::name('setting')->select();
                if($result){
                    foreach ($result as $key => $value) {       //用键值对形式存入缓存，方便使用
                        $arr[$value['name']]=$value['val'];
                    }
                    cache('setting',$arr);  //将设置数据存入服务器缓存
                }else{
                    t('查询系统设置表失败');
                }
                
                $json_arr=success_info();
            }else{
                $txt="删除数据失败，".lsql();
                $json_arr=error_info($txt);
            }
            return $json_arr;
        }else{
            $data=Db::query("SELECT COLUMN_NAME,COLUMN_COMMENT,DATA_TYPE,COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'ys_setting' AND TABLE_SCHEMA = '".config('database.database')."'");     //查出字段信息
            $list=Db::name('setting')->select();  //查出示例数据
            $view = new View();
            $view->datas=$data;
            $view->list=$list;
            return $view->fetch();
        }
    }

    //用户
    public function user()
    {
        //提交过来数据更新数据库中的数据
        if (Request::instance()->isPost()){     // 是否为 POST 请求
            $arr['id']=input('post.user_id');
            $arr['username']=input('post.username');
            $password=input('post.password');
            $arr['password']=think_encrypt($password);

            //九宫格密码
            if(!empty(input('post.nine_password'))){
                $arr['nine_password']=think_encrypt(input('post.nine_password'));
            }

            $arr['update_time']=time();
            $arr['update_time_date']=date('Y-m-d H:i:s',$arr['update_time']);
            $update_result=Db::name('admin')->where('id', $arr['id'])->update($arr);
            if($update_result){
                $json_arr['status']=true;
                $json_arr['info']=$arr; //返回最新修改后的数据回去

                if($arr['id'] == session('user_id')){   //假如为当前登录用户，就修改session
                    session('user_data.username',$arr['username']); //更新session里面的用户数据
                    session('user_data.password',$arr['password']);
                }
            }else{
                $json_arr['status']=false;
                $json_arr['info']="更新用户数据失败，".lsql();
            }
            return $json_arr;
        }else{  //默认是显示模板的
            //三表联合查询示例sql语句：select s.Name,C.Cname from student_course as sc left join student as s on s.Sno=sc.Sno left join course as c on c.Cno=sc.Cno
            //$user_data=Db::query('select * from ys_admin as ad left join ys_auth_group_access as ga on ad.id=ga.uid left join ys_auth_group as ag on ga.group_id=ag.id'); //原生写法
            $user_data=Db::name('admin')->alias('ad')->join('ys_auth_group_access ga','ad.id=ga.uid','LEFT')->join('ys_auth_group ag','ga.group_id=ag.id','LEFT')->field('*,ad.status as admin_status,ag.status as group_status')->where('ad.type!=1')->select();    //查询出用户数据(不包括顶级用户)
            $view = new View();
            $view->user_list = $user_data;

            $view->user_group_list = cache('auth_group');  //让前台显示出用户组数据

            return $view->fetch();
        }
    }

    //修改后台用户状态
    public function edit_userstatus(){
        $user_id=input('post.id');
        if(input('post.status') == "true"){
            $arr['status']=1;
        }else{
            $arr['status']=0;
        }
        $arr['update_time']=time();
        $arr['update_time_date']=date("Y-m-d H:i:s",$arr['update_time']);

        $user_status=Db::name('admin')->where('id', $user_id)->update($arr);
        if($user_status){
            $json_arr['status']=true;
            session('user_data.status',$arr['status']); //更新session里面的用户数据
            session('user_data.update_time',$arr['update_time']);
            session('user_data.update_time_date',$arr['update_time_date']);
        }else{
            $json_arr['status']=false;
            $json_arr['info']="更新用户状态失败，".lsql();
        }
        return $json_arr;
    }

    //修改后台用户组
    public function edit_usergroup(){
        $user_id=input('post.user_id');
        $user_group_id=input('post.user_group_id');

        $user_group_result=Db::name('auth_group_access')->where('uid', $user_id)->update(['group_id'=>$user_group_id]);
        if($user_group_result){
            $json_arr['status']=true;

            //重新更新缓存
            $result=Db::name('auth_group_access')->select();
            if($result){
                cache('auth_group_access',$result);
            }else{
                t('获取用户组关联数据失败');
            }
        }else{
            $json_arr['status']=false;
            $json_arr['info']="更新用户组失败，".lsql();
        }
        return $json_arr;
    }

    //添加用户
    public function add_user(){
        if (Request::instance()->isPost()){
            $arr['username']=input('post.username');
            $password=input('post.password');
            $arr['password']=think_encrypt($password);  //转换成插入数据

            $user_data=Db::name('admin')->where('username',$arr['username'])->find();   //查询有无重复的用户名
            if($user_data){
                $json_arr['status']=false;
                $json_arr['info']="该用户名已存在";
                return $json_arr;
            }

            //九宫格密码
            if(!empty(input('post.nine_password'))){
                $arr['nine_password']=think_encrypt(input('post.nine_password'));
            }

            $arr['reg_time']=time();
            $arr['reg_time_date']=date('Y-m-d H:i:s',$arr['reg_time']);
            $arr['reg_ip']=getIP();
            $arr['update_time']=time();
            $arr['update_time_date']=date('Y-m-d H:i:s',$arr['update_time']);
            $add_index=Db::name('admin')->insertGetId($arr);    //插入新增用户的数据

            $group_arr['uid']=$add_index;
            $group_arr['group_id']=input('post.usergroup');
            $group_result=Db::name('auth_group_access')->insert($group_arr);    //插入新增用户的组关联
            
            if($add_index && $group_result){    //假如插入成功就返回结果
                $json_arr['status']=true;

                //更新用户组关联的缓存
                $result=Db::name('auth_group_access')->select();
                if($result){
                    cache('auth_group_access',$result);
                }else{
                    t('获取用户组关联数据失败');
                }
            }else{
                $json_arr['status']=false;
                $json_arr['info']="插入用户数据失败，".lsql();
            }
            return $json_arr;
        }else{
            $view = new View();
            $view->user_groups=cache('auth_group');
            return $view->fetch();
        }
    }

    //删除用户
    public function del_user(){
        $user_id=input('post.id');

        $del_admin_result=Db::name('admin')->delete($user_id);
        $del_group_result=Db::name('auth_group_access')->where('uid',$user_id)->delete();

        if($del_admin_result && $del_group_result){    //假如删除成功就返回结果
            $json_arr['status']=true;

            //重新更新缓存
            $result=Db::name('auth_group_access')->select();
            if($result){
                cache('auth_group_access',$result);
            }else{
                t('获取用户组关联数据失败');
            }
        }else{
            $json_arr['status']=false;
            $json_arr['info']="删除用户数据失败，".lsql();
        }
        return $json_arr;
    }

    //用户组
    public function usergroup()
    {
        $usergroup_data=Db::name('auth_group')->select();    //查询出用户组数据
        foreach ($usergroup_data as $key => $value) {   //再查询出具体的规则
            $rules_list=Db::name('auth_rule')->where('id','in',$value['rules'])->order('id asc')->select();
            $usergroup_data[$key]['rules_list']=array();
            foreach ($rules_list as $k => $v) {
                $usergroup_data[$key]['rules_list'][]=$v;
            }
        }

        $view = new View();
        $view->usergroup_list = $usergroup_data;
        $view->admin_id = config('ADMIN_GROUP_ID');

        return $view->fetch();
    }

    //修改后台用户组状态
    public function edit_usergroup_status(){
        $usergroup_id=input('post.id');
        if(input('post.status') == "true"){
            $arr['status']=1;
        }else{
            $arr['status']=0;
        }

        $usergroup_status=Db::name('auth_group')->where('id', $usergroup_id)->update($arr);
        if($usergroup_status){
            $json_arr['status']=true;

            $result=Db::name('auth_group')->select();
            if($result){
                cache('auth_group',$result);
            }else{
                t('获取用户组失败');
            }
        }else{
            $json_arr['status']=false;
            $json_arr['info']="更新用户组状态失败，".lsql();
        }
        return $json_arr;
    }

    //编辑用户组
    public function usergroup_edit()
    {
        $id=input('get.id');
        $usergroup_data=Db::name('auth_group')->where('id',$id)->find();    //查询出对应id用户组数据
        if($usergroup_data){
            $rules_list=Db::name('auth_rule')->where('id','in',$usergroup_data['rules'])->order('id asc')->select();    //再查询出具体的规则
            $usergroup_data['rules_list']=array();
            $usergroup_data['rules_list_id']=array();
            foreach ($rules_list as $k => $v) {
                $usergroup_data['rules_list'][]=$v;
                $usergroup_data['rules_list_id'][]=$v['id'];
            }

            //查询出全部规则
            $all_rules_list=Db::name('auth_rule')->order('id asc')->select();

            $view = new View();
            $view->usergroup_data = $usergroup_data;
            $view->all_rules_list = $all_rules_list;

            return $view->fetch();
        }else{
            $this->error('没有找到对应的用户组');
        }
    }

    //修改用户组权限
    public function usergroup_update(){
        $update_arr['id']=input('post.id');
        $update_arr['title']=input('post.usergroup_name');
        $rules=input('post.url_rules/a');
        if($rules){
            $update_arr['rules']=implode(',',$rules);
        }else{
            $update_arr['rules']='';
        }
        
        $update_result=Db::name('auth_group')->where('id', $update_arr['id'])->update($update_arr);
        if($update_result){
            $json_arr['status']=true;
            
            $result=Db::name('auth_group')->select();
            if($result){
                cache('auth_group',$result);
            }else{
                t('获取用户组失败');
            }
        }else{
            $json_arr['status']=false;
            $json_arr['info']="修改失败，".lsql();
        }
        return $json_arr;
    }

    //新增用户组
    public function usergroup_add(){
        if (Request::instance()->isPost()){ //异步新增用户组
            $add_arr['title']=input('post.usergroup_name');
            $rules=input('post.url_rules/a');
            if($rules){
                $add_arr['rules']=implode(',',$rules);
            }else{
                $add_arr['rules']='';
            }
            
            $add_result=Db::name('auth_group')->insert($add_arr);
            if($add_result){
                $json_arr['status']=true;

                $result=Db::name('auth_group')->select();
                if($result){
                    cache('auth_group',$result);
                }else{
                    t('获取用户组失败');
                }
            }else{
                $json_arr['status']=false;
                $json_arr['info']="新增失败，".lsql();
            }
            return $json_arr;
        }else{  //默认显示新增页面
            //查询出全部规则
            $all_rules_list=Db::name('auth_rule')->order('id asc')->select();
            $view = new View();
            $view->all_rules_list = $all_rules_list;

            return $view->fetch();
        }
    }

    //删除用户组
    public function usergroup_del(){
        $id=input('post.id');

        $del_group_result=Db::name('auth_group')->delete($id);

        $access_result=Db::name('auth_group_access')->where('group_id',$id)->select();  //检查有没有用户关联组id
        if($access_result){
            $del_access_result=Db::name('auth_group_access')->where('group_id',$id)->delete();
        }else{
            $del_access_result=true;
        }
        
        if($del_group_result && $del_access_result){
            $json_arr['status']=true;

            $result=Db::name('auth_group')->select();
            if($result){
                cache('auth_group',$result);
            }else{
                t('获取用户组失败');
            }

            $result=Db::name('auth_group_access')->select();
            if($result){
                cache('auth_group_access',$result);
            }else{
                t('获取用户组关联数据失败');
            }
        }else{
            $json_arr['status']=false;
            $json_arr['info']="删除失败，".lsql();
        }
        return $json_arr;
    }

    //访问权限列表
    public function rules_list()
    {
        $url=input('get.name');     //搜索权限的url
        $title=input('get.title');      //搜索权限的名称
        $type=input('get.type');    //搜索权限的类型组

        $map=[];    //搜索的条件组合
        if($title){
            $map[]='a.title like "%'.$title.'%"';
        }
        if($type!='all' && !empty($type)){
            $map[]='a.type='.$type;
        }
        if($url){
            $map[]='a.name="'.$url.'"';
        }
        if(!empty($map)){
            $map_str=implode(' and ', $map);
        }

        if(!empty($map)){   //假如是搜索的话
            $list_data=Db::name('auth_rule')->alias('a')->join(config('database.database').'.ys_auth_rule_group arg','a.type = arg.id','LEFT')->where($map_str)->order('a.id desc')->field('a.*,a.title as rule_title,arg.title as group_title')->paginate(config('paginate.list_rows'),false,['query'=>input('get.')]);    //查询出所有的访问规则数据，并且分页
        }else{  //默认不搜索
            $list_data=Db::name('auth_rule')->alias('a')->join(config('database.database').'.ys_auth_rule_group arg','a.type = arg.id','LEFT')->order('a.id desc')->field('a.*,a.title as rule_title,arg.title as group_title')->paginate(config('paginate.list_rows'),false,['query'=>input('get.')]);    //查询出所有的访问规则数据，并且分页
        }
        $list_data_content=$list_data->toArray();  //转成数组后的实际数据

        $view = new View();
        $view->list_data = $list_data;
        $view->list_data_content = $list_data_content['data'];
        return $view->fetch();
    }

    //编辑访问权限
    public function rules_edit()
    {
        if (Request::instance()->isPost()){ //假如为post请求，就异步修改访问权限
            $update_arr['id']=input('post.id');
            $update_arr['name']=input('post.url_name');
            $update_arr['title']=input('post.rule_title');
            if(input('post.type')){
                $update_arr['type']=input('post.type');
            }
        
            $update_result=Db::name('auth_rule')->where('id', $update_arr['id'])->update($update_arr);
            if($update_result){
                $json_arr['status']=true;

                $result=Db::name('auth_rule')->select();
                if($result){
                    cache('auth_rule',$result);
                }else{
                    t('获取权限url失败');
                }
            }else{
                $json_arr['status']=false;
                $json_arr['info']="修改失败，".lsql();
            }
            return $json_arr;
        }else{  //非post请求，就显示出页面
            $id=input('get.id');
            $rule_data=Db::name('auth_rule')->where('id',$id)->find();    //查询出对应id访问权限数据
            if($rule_data){
                $view = new View();
                $view->rule_data = $rule_data;
                return $view->fetch();
            }else{
                $this->error('没有找到对应的访问权限');
            }
        }
    }

    //修改后台访问权限状态
    public function edit_rules_status(){
        $rule_id=input('post.id');
        if(input('post.status') == "true"){
            $arr['status']=1;
        }else{
            $arr['status']=0;
        }

        $rule_status=Db::name('auth_rule')->where('id', $rule_id)->update($arr);
        if($rule_status){
            $json_arr['status']=true;

            $result=Db::name('auth_rule')->select();
            if($result){
                cache('auth_rule',$result);
            }else{
                t('获取权限url失败');
            }
        }else{
            $json_arr['status']=false;
            $json_arr['info']="更新访问权限状态失败，".lsql();
        }
        return $json_arr;
    }

    //删除访问规则
    public function rule_del(){
        if(input('?post.id_arr')){   //批量删除
            $id_arr=input('post.id_arr/a'); //id数组集合
            $del_rules_result=Db::name('auth_rule')->delete($id_arr);   //批量删除

            $all_usergroup=Db::name('auth_group')->select();  //然后查出所有用户组
            foreach ($all_usergroup as $key => $value) {    //将规则id的字符串集合转成数组
                $rules_arr=explode(',',$value['rules']);
                $new_rules_arr=array_diff($rules_arr,$id_arr);   //返回被删减后的id集合数组
                if($rules_arr != $new_rules_arr){    //假如与原本数组不同，就执行更新操作
                    Db::name('auth_group')->where('id',$value['id'])->update(['rules' =>implode(',',$new_rules_arr)]);  //删除完成后转成字符串，更新数据库
                }
            }
            
            if($del_rules_result){
                $json_arr['status']=true;

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
                $json_arr['status']=false;
                $json_arr['info']="批量删除失败，".lsql();
            }
            return $json_arr;
        }else{  //单个删除
            $id=input('post.id');
            $del_rule_result=Db::name('auth_rule')->delete($id);    //首先先删除这条访问权限记录

            $all_usergroup=Db::name('auth_group')->select();  //然后查出所有用户组
            foreach ($all_usergroup as $key => $value) {    //将规则id的字符串集合转成数组
                $rules_arr=explode(',',$value['rules']);
                $search_key=array_search($id,$rules_arr);   //查找id集合数组中有无要删除的id，返回找到的键值
                if($search_key !== false){    //假如说有要删除的值，就执行删除
                    array_splice($rules_arr,$search_key,1); //删除该值和键
                    Db::name('auth_group')->where('id',$value['id'])->update(['rules' =>implode(',',$rules_arr)]);  //删除完成后转成字符串，更新数据库
                }
            }

            if($del_rule_result){
                $json_arr['status']=true;

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
                $json_arr['status']=false;
                $json_arr['info']="删除失败，".lsql();
            }
            return $json_arr;
        }
    }

    //新增访问权限
    public function rule_add(){
        if (Request::instance()->isPost()){ //异步新增访问权限
            $add_arr['name']=input('post.url_name');
            $add_arr['title']=input('post.rule_title');
            $add_arr['type']=input('post.type');
        
            $add_result=Db::name('auth_rule')->insertGetId($add_arr);

            $master_rules=Db::name('auth_group')->where('id',config('ADMIN_GROUP_ID'))->find(); //查找最高管理权限组的数据
            $rules_arr=explode(',',$master_rules['rules']);
            $rules_arr[]=$add_result;
            $update_master=Db::name('auth_group')->where('id',config('ADMIN_GROUP_ID'))->update(['rules'=>implode(',',$rules_arr)]); //把新添加的访问权限，默认分配给最高管理用户组

            if($add_result && $update_master){
                $json_arr['status']=true;
                
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
                $json_arr['status']=false;
                $json_arr['info']="新增失败，".lsql();
            }
            return $json_arr;
        }else{  //默认显示新增页面
            $view = new View();
            return $view->fetch();
        }
    }

    //权限类型管理
    public function rule_group_list(){
        $rule_group_data=Db::name('auth_rule_group')->select();    //查询出用户数据(不包括顶级用户)
        $view = new View();
        $view->rule_group_list = $rule_group_data;
        return $view->fetch();
    }
    //权限类型的添加或编辑
    public function rule_group_add_edit(){
        if (Request::instance()->isPost()){ //异步新增访问权限
            $arr['title']=input('post.title');

            if(input('post.id')){   //假如是编辑状态，那就更新表
                $arr_result=Db::name('auth_rule_group')->where('id',input('post.id'))->update($arr);
            }else{  //假如是新增状态，那就添加数据
                $arr_result=Db::name('auth_rule_group')->insert($arr);
            }

            if($arr_result){
                $json_arr['status']=true;

                $result=Db::name('auth_rule_group')->select();
                if($result){
                    cache('auth_rule_group',$result);
                }else{
                    t('获取权限url组失败');
                }
            }else{
                $json_arr['status']=false;
                $json_arr['info']="提交失败，".lsql();
            }
            return $json_arr;
        }else{  //默认显示新增页面
            $view = new View();

            $id=input('get.id');
            if($id){
                $data=Db::name('auth_rule_group')->where('id',$id)->find();
                if($data){
                    $view->rule_data=$data;
                }else{
                    $this->error('没有该组名');
                }
            }

            return $view->fetch();
        }
    }
    //权限类型的删除
    public function del_rule_group(){
        $id=input('post.id');

        $del_group_result=Db::name('auth_rule_group')->delete($id);
        
        if($del_group_result){
            $json_arr['status']=true;

            $result=Db::name('auth_rule_group')->select();
            if($result){
                cache('auth_rule_group',$result);
            }else{
                t('获取权限url组失败');
            }
        }else{
            $json_arr['status']=false;
            $json_arr['info']="删除失败，".lsql();
        }
        return $json_arr;
    }


    //管理员日志
    public function adminlog(){
        $username=input('get.username');     //搜索用户名
        $ip=input('get.ip');      //搜索ip
        $start_time=input('get.opDate_start');      //操作的开始时间
        $start_time_stamp=strtotime($start_time);
        $end_time=input('get.opDate_end');      //操作的结束时间
        $end_time_stamp=strtotime($end_time);
        $type=input('get.opType');    //搜索权限的类型组
        $content=input('get.content');  //搜索的备注内容

        $map=[];    //搜索的条件组合
        if($username){
            $map[]='a.username like "%'.$username.'%"';
        }
        if($username){
            $map[]='alog.ip="'.$ip.'"';
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
        if($content){
            $map[]='alog.content like "%'.$content.'%"';
        }

        if(!empty($map)){
            $map_str=implode(' and ', $map);
        }

        if(!empty($map)){   //假如是搜索的话
            $list_data=Db::name('admin_log')->alias('alog')->join(config('database.database').'.ys_admin a','alog.adminID = a.id','LEFT')->join(config('database.database').'.ys_auth_rule_group arg','alog.opType = arg.id','LEFT')->where($map_str)->order('alog.opTime desc')->paginate(config('paginate.list_rows'),false,['query'=>input('get.')]);    //查询出所有的管理员日志记录，并且分页
        }else{  //默认不搜索
            $list_data=Db::name('admin_log')->alias('alog')->join(config('database.database').'.ys_admin a','alog.adminID = a.id','LEFT')->join(config('database.database').'.ys_auth_rule_group arg','alog.opType = arg.id','LEFT')->order('alog.opTime desc')->paginate(config('paginate.list_rows'),false,['query'=>input('get.')]);    //查询出所有的管理员日志记录，并且分页
        }
        $list_data_content=$list_data->toArray();  //转成数组后的实际数据

        $view = new View();
        $view->list_data = $list_data;
        $view->list_data_content = $list_data_content['data'];
        return $view->fetch();
    }
}
