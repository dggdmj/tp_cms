<?php
namespace app\manager\controller;

use \think\Request;
use think\Db;
use think\View;

class Trash extends BaseController
{
    // 数据清理
    public function index() {
        $view = new View();
        return $view->fetch();
    }

    //开始数据清理
    public function trash_run(){
        $data_type=input('data_type');	//接收过来要清理的数据类型
        $datas=input('datas/a');	//接收过来的表单数据数组
        if($datas){}else{
            $json_arr['status']=false;
            $json_arr['info']='没有选中任何数据清理';
            return $json_arr;
        }

        switch ($data_type) {
        	case 'cache':	//清理缓存
    		foreach ($datas as $key => $value) {    //接收过来的表单数据，转化为标准数组
			cache($value['value'],null);	//清空缓存
		}
		$json_arr['status']=true;
        		break;
        	case 'month3':	//清理三个月之前的数据库数据
                            $arr['stamp']=[];	//时间戳格式处理数组
                            $arr['date']=[];	//日期格式处理数组
                            foreach ($datas as $key => $value) {    //接收过来的表单数据，转化为标准数组
                                $data_arr=explode('-',$value['value']);     //分割表名和字段名
                                if($value['name']=='time_stamp'){	//假如字段是时间戳类型的话，就压入时间戳处理数组
                                    $arr['stamp'][]=$data_arr;
                                }
                                if($value['name']=='time_date'){	//假如字段是时间类型的话，就压入时间处理数组
                                    $arr['date'][]=$data_arr;
                                }
                            }

                            //先处理时间戳数据
                            $json_arr['info']=[];   //出错的提示信息数组
                            $has_error=false;   //是否有删除出错
                            foreach ($arr['stamp'] as $key => $value) {
                                $delete_result=Db::table($value[0])->where($value[1],'<=',strtotime("-3 month"))->delete();
                                //$count_result=Db::table($value[0])->count();
                                if(!$delete_result){
                                    $has_error=true;
                                    $json_arr['status']=false;
                                    $json_arr['info'][]='表：'.$value[0].'删除数据失败，执行的sql为：'.Db::getLastSql().'，数据剩余：'.Db::table($value[0])->count().'条';
                                }else{
                                    $json_arr['info'][]='表：'.$value[0].'删除数据成功！，数据剩余：'.Db::table($value[0])->count().'条';
                                }
                            }

                            //处理正常日期格式数据
                            foreach ($arr['date'] as $key => $value) {
                                $delete_result=Db::execute('delete from '.$value[0].' where unix_timestamp('.$value[1].') <= '.strtotime("-3 month"));
                                //$count_result=Db::table($value[0])->count();
                                if(!$delete_result){
                                    $has_error=true;
                                    $json_arr['status']=false;
                                    $json_arr['info'][]='表：'.$value[0].'删除数据失败，执行的sql为：'.Db::getLastSql().'，数据剩余：'.Db::table($value[0])->count().'条';
                                }else{
                                    $json_arr['info'][]='表：'.$value[0].'删除数据成功！，数据剩余：'.Db::table($value[0])->count().'条';
                                }
                            }

                            if(!$has_error){
                                $json_arr['status']=true;
                            }
        		break;
              case 'month3_zhudan': //清理三个月之前的注单数据库数据
                            $arr['date']=[];    //日期格式处理数组
                            foreach ($datas as $key => $value) {    //接收过来的表单数据，转化为标准数组
                                $data_arr=explode('-',$value['value']);     //分割表名和字段名
                                $arr['date'][]=$data_arr;
                            }

                            //处理正常日期格式数据
                            $json_arr['info']=[];   //出错的提示信息数组
                            $has_error=false;   //是否有删除出错
                            foreach ($arr['date'] as $key => $value) {
                                $delete_result=Db::execute('delete from '.$value[0].' where unix_timestamp('.$value[1].') <= '.strtotime("-3 month"));
                                if(!$delete_result){
                                    $has_error=true;
                                    $json_arr['status']=false;
                                    $json_arr['info'][]='表：'.$value[0].'删除数据失败，执行的sql为：'.Db::getLastSql().'，数据剩余：'.Db::table($value[0])->count().'条';
                                }else{
                                    $json_arr['info'][]='表：'.$value[0].'删除数据成功！，数据剩余：'.Db::table($value[0])->count().'条';
                                }
                            }

                            if(!$has_error){
                                $json_arr['status']=true;
                            }
                            break;
              case 'important': //清理重要数据库数据
                            $json_arr['info']=[];   //出错的提示信息数组
                            $has_error=false;   //是否有删除出错
                            foreach ($datas as $key => $value) {    //接收过来的表单数据，转化为标准数组
                                if($value['name']=='user'){ //清理一年未登录的用户
                                    switch ($value['value']) {
                                        case 'one_year':    //清理一年
                                            $delete_result=Db::table('user')->where('lasttime','<=',strtotime("-1 year"))->delete();
                                            //$count_result=Db::table('user')->count();
                                            if(!$delete_result){
                                                $has_error=true;
                                                $json_arr['status']=false;
                                                $json_arr['info'][]='表：user删除数据失败，执行的sql为：'.Db::getLastSql().'，数据剩余：'.Db::table('user')->count().'条';
                                            }else{
                                                $json_arr['info'][]='表：user删除数据成功！删除了：'.$delete_result.'条记录，数据剩余：'.Db::table('user')->count().'条';
                                            }
                                            break;
                                        default:
                                            # code...
                                            break;
                                    }
                                }

                                if($value['name']=='msg_user'){ //提现提醒信息用户表保留3000条数据(msg_user)
                                    $data3000=Db::table('msg_user')->order('id desc')->limit('3000,1')->select();
                                    if($data3000){
                                        $delete_result=Db::table('msg_user')->where('id','<=',$data3000[0]['id'])->delete();
                                        if(!$delete_result){
                                            $has_error=true;
                                            $json_arr['status']=false;
                                            $json_arr['info'][]='表：msg_user删除数据失败，执行的sql为：'.Db::getLastSql().'，数据剩余：'.Db::table('msg_user')->count().'条';
                                        }else{
                                            $json_arr['info'][]='表：msg_user删除数据成功！删除了：'.$delete_result.'条记录，数据剩余：'.Db::table('msg_user')->count().'条';
                                        }
                                    }else{
                                        $has_error=true;
                                        $json_arr['status']=false;
                                        $json_arr['info'][]='表：msg_user数据不足3000条';
                                    }
                                }
                            }

                            if(!$has_error){
                                $json_arr['status']=true;
                                $json_arr['info_show']=true;    //清理成功也显示出清理的信息
                            }
                            break;
        	default:
        		# code...
        		break;
        }
        return $json_arr;
    }
}