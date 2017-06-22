<?php
namespace app\manager\controller;

use \think\Request;
use think\Db;
use think\View;
//use app\manager\controller\Publicfun; //引入公共类方法

class Menu extends BaseController
{
    // public function __construct(){
    //     global $pub;
    //     $pub=new Publicfun; //实例化公共方法类
    // }

    public function index()
    {
            $view = new View();
            return $view->fetch();
    }

    //菜单列表(左侧)
    public function set_menu()
    {
        $where="";
        $where_arr=[];
        $url=input('get.url');
        $title=input('get.title');
        $show=input('get.show');
        if(!empty($url)){
            $where_arr[]="url like '%".$url."%'";
        }
        if(!empty($title)){
            $where_arr[]="title like '%".$title."%'";
        }
        if(!empty($show)){
            $where_arr[]='is_show='.$show;
        }
        if(!empty($where_arr)){
            $where=implode(' and ', $where_arr);
        }

        if(!empty($where)){ //假如有搜索的情况下
            $list_data=Db::name('left_menu')->where($where)->paginate(config('paginate.list_rows'),false,['query'=>input('get.')]);
        }else{
            $list_data=Db::name('left_menu')->paginate(config('paginate.list_rows'),false,['query'=>input('get.')]);
        }
        
        $list_data_content=$list_data->toArray();  //转成数组后的实际数据，不包含分页对象

        $view = new View();
        $view->list_data=$list_data;
        $view->list_data_content = $list_data_content['data'];
        return $view->fetch();
    }

    //新增、编辑菜单(左侧)
    public function add_menu(){
        if (Request::instance()->isPost()){ //异步新增、编辑访问权限
            $post_data=input('post.');    //接收post过来的所有数据
            $arr=[];    //准备提交给数据库的数据
            $parent_id='';      //上级id标识
            foreach ($post_data as $key => $value) {    //过滤下空的数据
                if(!empty($value) || $value=="0"){
                    if($key=='level' && $value=='one'){     //假如是顶级菜单，就标识
                        $arr['one']=1;
                    }else if($key=='level'){    //假如是其它菜单下的子菜单，就上级标识
                        $parent_id=$value;  //上级id标识
                    }else if($key=='id' && !empty($value)){ //假如有id传过来，就不加入到更新数据库的数组里面
                    }else if($key=='type' && $value=='null'){
                        $arr[$key]=null;
                    }else if($key=='weight' && $value==''){
                        $arr[$key]=null;
                    }else{
                        $arr[$key]=$value;
                    }
                }else if($key=='id' && empty($value)){
                }else{
                    $arr[$key]=null;
                }
            }

            if(empty(input('post.id'))){    //假如是新增数据，就执行
                $arr_result=Db::name('left_menu')->insertGetId($arr);
                if($arr_result && !empty($parent_id)){      //假如新增成功，并且有记录上级id，就执行上级id赋值
                    $parent_data=Db::name('left_menu')->where('id',$parent_id)->find();
                    if($parent_data){
                        $two_arr=explode(',', $parent_data['two']);     //上级菜单的子菜单id集合
                        if(in_array($arr_result, $two_arr)){}else{      //假如上级菜单未包含子菜单，就执行关联
                            $two_arr[]=$arr_result;
                            $two_str=trimtwo(implode(',', $two_arr));
                            $parent_result=Db::name('left_menu')->where('id',$parent_id)->update(['two'=>$two_str]);
                            if(!$parent_result){
                                $this->error('关联上级菜单失败');
                            }
                        }
                    }else{
                        $this->error('未找到上级菜单，菜单关联失败');
                    }
                }

                if($arr_result){
                    $json_arr['status']=true;

                    //更新缓存
                    $result=Db::name('left_menu')->order('weight asc')->select();
                    if($result){
                        cache('leftMenu',$result);
                    }else{
                        t('查询后台左边菜单失败');
                    }

                    parent::format_left_menu(); //更新菜单缓存
                }else{
                    $json_arr['status']=false;
                    $json_arr['info']='新增失败，'.lsql();
                }
            }else{  //否则是更新数据
                
                $id=input('post.id');
                $old_parent_id=query_leftMenu_parent($id)['id'];     //查找原来上级的id，以便去除原来上级里的子菜单id记录

                $arr_result=Db::name('left_menu')->where('id',$id)->update($arr);
                if(($arr_result && !empty($parent_id)) || ($old_parent_id != $parent_id)){      //假如更新成功，并且有记录上级id，就执行上级id赋值
                    //首先剔除原上级菜单的关联记录
                    $old_parent_data=Db::name('left_menu')->where('id',$old_parent_id)->find();
                    if($old_parent_data){
                        $two_arr=explode(',', $old_parent_data['two']);     //上级菜单的子菜单id集合
                        foreach ($two_arr as $key => $value) {  //准备剔除原上级菜单里的子id值
                            if($id==$value){
                                unset($two_arr[$key]);
                            }
                        }
                        $two_str=trimtwo(implode(',', $two_arr));
                        $old_parent_result=Db::name('left_menu')->where('id',$old_parent_id)->update(['two'=>$two_str]);
                        if(!$old_parent_result){
                            t('取消关联上级菜单失败');
                        }
                    }else{
                        t('查找原上级菜单的数据失败');
                    }

                    //然后再关联新的上级菜单
                    $parent_data=Db::name('left_menu')->where('id',$parent_id)->find();
                    if($parent_data){
                        $two_arr=explode(',', $parent_data['two']);     //上级菜单的子菜单id集合
                        if(in_array($id, $two_arr)){}else{      //假如上级菜单未包含子菜单，就执行关联
                            $two_arr[]=$id;
                            $two_str=trimtwo(implode(',', $two_arr));
                            $parent_result=Db::name('left_menu')->where('id',$parent_id)->update(['two'=>$two_str]);
                            if(!$parent_result){
                                t('关联上级菜单失败');
                            }
                        }
                    }else{
                        t('未找到上级菜单，菜单关联失败');
                    }
                }

                if($arr_result || ($old_parent_id != $parent_id)){
                    $json_arr['status']=true;

                    //更新缓存
                    $result=Db::name('left_menu')->order('weight asc')->select();
                    if($result){
                        cache('leftMenu',$result);
                    }else{
                        t('查询后台左边菜单失败');
                    }

                    parent::format_left_menu(); //更新菜单缓存
                }else{
                    $json_arr['status']=false;
                    $json_arr['info']='提交失败，'.lsql();
                }
            }
            return $json_arr;
        }else{
            $view = new View();

            $id=input('get.id');
            if($id){
                $data=Db::name('left_menu')->where('id',$id)->find();
                if($data){
                    $view->data=$data;
                }else{
                    $this->error('没有该菜单');
                }
            }
            
            return $view->fetch();
        }
    }

    //修改菜单状态(左侧)
    public function status_menu(){
        $id=input('post.id');

        if(input('post.status') == "true"){
            $arr[input('post.field_name')]=1;
        }else{
            $arr[input('post.field_name')]=0;
        }

        $status_result=Db::name(input('post.pre_table'))->where('id', $id)->update($arr);
        
        if($status_result){
            $json_arr['status']=true;

            //更新缓存
            $result=Db::name('left_menu')->order('weight asc')->select();
            if($result){
                cache('leftMenu',$result);
            }else{
                t('查询后台左边菜单失败');
            }

            parent::format_left_menu(); //更新菜单缓存
        }else{
            $json_arr['status']=false;
            $json_arr['info']="更新数据库失败，".lsql();
        }
        return $json_arr;
    }

    //删除菜单(左侧)
    public function del_menu(){
        if(input('?post.id_arr')){   //批量删除
            $id_arr=input('post.id_arr/a'); //id数组集合
            foreach ($id_arr as $k => $val) {
                //首先删除上级菜单的子菜单id
                $old_parent_id=query_leftMenu_parent($val)['id'];     //查找原来上级的id，以便去除原来上级里的子菜单id记录
                $old_parent_data=Db::name('left_menu')->where('id',$old_parent_id)->find();
                if($old_parent_data){
                    $two_arr=explode(',', $old_parent_data['two']);     //上级菜单的子菜单id集合
                    foreach ($two_arr as $key => $value) {  //准备剔除原上级菜单里的子id值
                        if($val==$value){
                            unset($two_arr[$key]);
                        }
                    }
                    $two_str=trimtwo(implode(',', $two_arr));
                    $old_parent_result=Db::name('left_menu')->where('id',$old_parent_id)->update(['two'=>$two_str]);
                    if(!$old_parent_result){
                        t('取消关联上级菜单失败');
                    }
                }else{
                    t('查找原上级菜单的数据失败');
                }

                //然后再删除该菜单下的所有子菜单
                $data=Db::name('left_menu')->where('id',$val)->find();   //先查出该菜单的下级数据
                if($data){
                    $del_child_result=Db::name('left_menu')->delete([trimtwo($data['two'])]);
                    if($del_child_result){
                        t('删除'.$del_child_result.'条子菜单数据');
                    }else{
                        t('删除子菜单数据失败');
                    }
                }else{
                    $this->error('无法找到当前菜单');
                }
            }

            $del_result=Db::name('left_menu')->delete($id_arr);     //最后删除当前菜单数据
            
            if($del_result){
                $json_arr['status']=true;

                //更新缓存
                $result=Db::name('left_menu')->order('weight asc')->select();
                if($result){
                    cache('leftMenu',$result);
                }else{
                    t('查询后台左边菜单失败');
                }

                parent::format_left_menu(); //更新菜单缓存
            }else{
                $json_arr['status']=false;
                $json_arr['info']="批量删除失败，".lsql();
            }
            return $json_arr;
        }else{  //单个删除
            $id=input('post.id');

            //首先删除上级菜单的子菜单id
            $old_parent_id=query_leftMenu_parent($id)['id'];     //查找原来上级的id，以便去除原来上级里的子菜单id记录
            $old_parent_data=Db::name('left_menu')->where('id',$old_parent_id)->find();
            if($old_parent_data){
                $two_arr=explode(',', $old_parent_data['two']);     //上级菜单的子菜单id集合
                foreach ($two_arr as $key => $value) {  //准备剔除原上级菜单里的子id值
                    if($id==$value){
                        unset($two_arr[$key]);
                    }
                }
                $two_str=trimtwo(implode(',', $two_arr));
                $old_parent_result=Db::name('left_menu')->where('id',$old_parent_id)->update(['two'=>$two_str]);
                if(!$old_parent_result){
                    t('取消关联上级菜单失败');
                }
            }else{
                t('查找原上级菜单的数据失败');
            }

            //然后再删除该菜单下的所有子菜单
            $data=Db::name('left_menu')->where('id',$id)->find();   //先查出该菜单的下级数据
            if($data){
                $del_child_result=Db::name('left_menu')->delete([trimtwo($data['two'])]);
                if($del_child_result){
                    t('删除'.$del_child_result.'条子菜单数据');
                }else{
                    t('删除子菜单数据失败');
                }
            }else{
                $this->error('无法找到当前菜单');
            }

            $del_result=Db::name('left_menu')->delete($id);     //最后删除当前菜单数据
            
            if($del_result){
                $json_arr['status']=true;

                //更新缓存
                $result=Db::name('left_menu')->order('weight asc')->select();
                if($result){
                    cache('leftMenu',$result);
                }else{
                    t('查询后台左边菜单失败');
                }

                parent::format_left_menu(); //更新菜单缓存
            }else{
                $json_arr['status']=false;
                $json_arr['info']="删除失败，".lsql();
            }
            return $json_arr;
        }
    }








    //菜单列表(顶部)
    public function set_menu_top()
    {
        $list_data=Db::name('manager_topmenu')->select();

        $view = new View();
        $view->list_data=$list_data;
        return $view->fetch();
    }

    //新增、编辑菜单(顶部)
    public function add_menu_top(){
        if (Request::instance()->isPost()){ //异步新增、编辑访问权限
            $post_data=input('post.');    //接收post过来的所有数据
            $arr=[];    //准备更新服务器的数组
            foreach ($post_data as $key => $value) {    //转成需要的数组
                if($key=='id'){}else{
                    $arr[$key]=$value;
                }
            }

            if(empty(input('post.id'))){    //假如是新增数据，就执行
                $arr_result=Db::name('manager_topmenu')->insert($arr);

                if($arr_result){
                    $json_arr['status']=true;

                    //更新缓存
                    $result=Db::name('manager_topmenu')->order('weight asc')->select();
                    if($result){
                        cache('topMenu',$result);
                    }else{
                        t('查询后台顶部菜单失败');
                    }
                }else{
                    $json_arr['status']=false;
                    $json_arr['info']='新增失败，'.lsql();
                }
            }else{  //否则是更新数据
                $id=input('post.id');

                $arr_result=Db::name('manager_topmenu')->where('id',$id)->update($arr);
            }

            if($arr_result){
                $json_arr['status']=true;

                //更新缓存
                $result=Db::name('manager_topmenu')->order('weight asc')->select();
                if($result){
                    cache('topMenu',$result);
                }else{
                    t('查询后台顶部菜单失败');
                }
            }else{
                $json_arr['status']=false;
                $json_arr['info']='提交失败，'.lsql();
            }
            return $json_arr;
        }else{
            $view = new View();

            $id=input('get.id');
            if($id){
                $data=Db::name('manager_topmenu')->where('id',$id)->find();
                if($data){
                    $view->data=$data;
                }else{
                    $this->error('没有该菜单');
                }
            }
            
            return $view->fetch();
        }
    }

    //删除菜单(顶部)
    public function del_menu_top(){
        $id=input('post.id');

        $del_result=Db::name('manager_topmenu')->delete($id);
        
        if($del_result){
            $json_arr['status']=true;

            //更新缓存
            $result=Db::name('manager_topmenu')->order('weight asc')->select();
            if($result){
                cache('topMenu',$result);
            }else{
                t('查询后台顶部菜单失败');
            }
        }else{
            $json_arr['status']=false;
            $json_arr['info']="删除失败，".lsql();
        }
        return $json_arr;
    }
}