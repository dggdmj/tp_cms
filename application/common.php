<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\Db;

//用于字符串的调试输出，同时打印sql
function t($error_msg){	//参数（需要trace的信息）
	trace('调试信息：'.$error_msg.'，最后执行的sql为：'.Db::getLastSql());
}
//自定义的调试方法
function d($obj){
	header("Content-type:text/html;charset=utf-8");
	dump("最后执行的SQL语句是：".Db::getLastSql());
	dump($obj);
	die;
}

//使用cache方法
function get_cache($str=''){
	return cache($str);
}

//载入后台设置数据
function load_setting($view=null){	//参数（模板视图对象）
	//cache('setting',null);	//调试，清除缓存
	if(cache('setting')){
		if($view){	//假如有视图对象，就赋设置数据到模板
			$view->setting=cache('setting');
			return cache('setting');
		}
	}else{
		$result=Db::name('setting')->select();
		if($result){
			foreach ($result as $key => $value) {       //用键值对形式存入缓存，方便使用
				$arr[$value['name']]=$value['val'];
			}

			cache('setting',$arr);	//将设置数据存入服务器缓存
			if($view){	//假如有视图对象，就赋设置数据到模板
				$view->setting=cache('setting');
			}
			return cache('setting');
		}else{
			t('查询系统设置表失败');
		}
	}
}

/**
 * 系统加密方法
 *
 * @param string $data
 *        	要加密的字符串
 * @param string $key
 *        	加密密钥
 * @param int $expire
 *        	过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
	$key = md5 ( empty ( $key ) ? config ( 'DATA_AUTH_KEY' ) : $key );
	$data = base64_encode ( $data );
	$x = 0;
	$len = strlen ( $data );
	$l = strlen ( $key );
	$char = '';
	
	for($i = 0; $i < $len; $i ++) {
		if ($x == $l)
			$x = 0;
		$char .= substr ( $key, $x, 1 );
		$x ++;
	}
	
	$str = sprintf ( '%010d', $expire ? $expire + time () : 0 );
	
	for($i = 0; $i < $len; $i ++) {
		$str .= chr ( ord ( substr ( $data, $i, 1 ) ) + (ord ( substr ( $char, $i, 1 ) )) % 256 );
	}
	return str_replace ( array (
			'+',
			'/',
			'=' 
	), array (
			'-',
			'_',
			'' 
	), base64_encode ( $str ) );
}

/**
 * 系统解密方法
 *
 * @param string $data
 *        	要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key
 *        	加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = '') {
	$key = md5 ( empty ( $key ) ? config ( 'DATA_AUTH_KEY' ) : $key );
	$data = str_replace ( array (
			'-',
			'_' 
	), array (
			'+',
			'/' 
	), $data );
	$mod4 = strlen ( $data ) % 4;
	if ($mod4) {
		$data .= substr ( '====', $mod4 );
	}
	$data = base64_decode ( $data );
	$expire = substr ( $data, 0, 10 );
	$data = substr ( $data, 10 );
	
	if ($expire > 0 && $expire < time ()) {
		return '';
	}
	$x = 0;
	$len = strlen ( $data );
	$l = strlen ( $key );
	$char = $str = '';
	
	for($i = 0; $i < $len; $i ++) {
		if ($x == $l)
			$x = 0;
		$char .= substr ( $key, $x, 1 );
		$x ++;
	}
	
	for($i = 0; $i < $len; $i ++) {
		if (ord ( substr ( $data, $i, 1 ) ) < ord ( substr ( $char, $i, 1 ) )) {
			$str .= chr ( (ord ( substr ( $data, $i, 1 ) ) + 256) - ord ( substr ( $char, $i, 1 ) ) );
		} else {
			$str .= chr ( ord ( substr ( $data, $i, 1 ) ) - ord ( substr ( $char, $i, 1 ) ) );
		}
	}
	return base64_decode ( $str );
}

// 获取客户端IP地址
function getIP(){
	return request()->ip();
}

//记录管理员操作日志
function admin_log($txt=''){	//参数（管理员操作的内容）
	$arr['adminID']=session('user_data.id');
	$arr['opType']=session('opType');	//操作类型
	$arr['opTime']=time();
	$arr['opDate']=date("Y-m-d H:i:s",$arr['opTime']);
	$arr['ip']=getIP();
	$arr['content']='管理员['.session("user_name").']操作了：'.$txt;
	$result=Db::name('admin_log')->insert($arr);
	if(!$result){
		t('记录管理员日志失败');
	}
}

//自定义sql分页方法
function _page_sql($sql,$view=null,$param=array(),$num = 15){ //参数（原生sql语句，视图对象，参数数组，分页条数）
        $_list = Db::query($sql);   //再执行原生查询方法
        $_count = $_list ? count($_list) : 0;   //统计总行数
        $page = new \Page($_count, $num, $param);   //执行thinkphp3.2的分页方法
        $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );   //设置分页输出显示的选项
        $page->setConfig ( 'prev', '上一页' );
        $page->setConfig ( 'next', '下一页' );
        $sql = $sql . " limit {$page->firstRow},{$page->listRows}"; //给sql加上分页语句
        $pageData = Db::query($sql);    //执行最终查询数据
        
        //$view->mypage_count=$_count;   //总记录，给模板
        $view->page=$page->show();   //分页，给模板
        $view->datas=$_list;   //查询的数据，给模板
        return $pageData;   //并返回分页查询后的数据
}

//字符串转为数组（加强版，空字符也能转）
function explodekong($s,$str){
	$newarr=array();
	if($s==""){
		preg_match_all("/./u", $str, $arr);
		$newarr=$arr[0];
		// for($i=0;$i<strlen($str);$i++){
		// 	$newarr[]=$str[$i];
		// }
	}else{
		$newarr=explode($s,$str);
	}
	return $newarr;
}
//删除字符串两边多余的逗号
function trimtwo($str){
	return trim($str,",");
}

/** 
 * 删除目录及目录下所有文件或删除指定文件 
 * @param str $path   待删除目录路径 
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录） 
 * @return bool 返回删除状态 
 */  
function delDirAndFile($path, $delDir = FALSE) {  
    if (is_array($path)) {  
        foreach ($path as $subPath)  
            delDirAndFile($subPath, $delDir);  
    }  
    if (is_dir($path)) {  
        $handle = opendir($path);  
        if ($handle) {  
            while (false !== ( $item = readdir($handle) )) {  
                if ($item != "." && $item != "..")  
                    is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");  
            }  
            closedir($handle);  
            if ($delDir)  
                return rmdir($path);  
        }  
    } else {  
        if (file_exists($path)) {  
            return unlink($path);  
        } else {  
            return FALSE;  
        }  
    }  
    clearstatcache();  
}

//返回成功的json状态
function success_info($txt=''){	//参数（成功的提示信息）
	$json_arr['status']=true;
	$json_arr['info']=$txt;
	return $json_arr;
}
//返回失败的json状态
function error_info($txt=''){	//参数（失败的提示信息）
	$json_arr['status']=false;
	$json_arr['info']=$txt;
	return $json_arr;
}

//返回最后执行的sql语句
function lsql(){
	return '最后执行的SQL语句为：'.Db::getLastSql();
}

//查询上级菜单(左侧)
function query_leftMenu_parent($id=0){	//参数（需要查询上级的id）
	if($id){
		$left_menu_data=cache('leftMenu');	//获取左侧菜单数据
		foreach ($left_menu_data as $key => $value) {
			if(!empty($value['two'])){	//假如下级菜单id集合不为空，就执行查询
				$two_arr=explode(',', $value['two']);
				if(in_array($id, $two_arr)){	//假如查询到包含id，就返回上级菜单的名称
					return $value;	//返回上级菜单
				}
			}
		}
	}
}
//过滤掉所有超过3层的菜单数据(左侧)
function filter_leftMenu(){
	$left_menu_data=cache('leftMenu');	//获取左侧菜单数据
	if($left_menu_data){
		$filter_data=[];	//过滤后的菜单数据
		foreach ($left_menu_data as $key => $value) {	//枚举菜单来做检测
			$filter_ok=false;		//检测是否超出了3级
			foreach ($left_menu_data as $key2 => $value2) {
				$filter_two_ok=false;
				if(!empty($value2['two'])){	//假如下级菜单id集合不为空，就执行查询
					$two_arr=explode(',', $value2['two']);
					if(in_array($value['id'], $two_arr)){	//假如查询到包含id，就继续查询上级

						$filter_two_ok=true;

						foreach ($left_menu_data as $key3 => $value3) {
							if(!empty($value3['two'])){	//假如下级菜单id集合不为空，就执行查询
								$two_arr=explode(',', $value3['two']);
								if(in_array($value2['id'], $two_arr)){	//假如查询到包含id，就过滤掉
									
									$filter_ok=true;
									break;

								}
							}
						}

					}
				}
				if($filter_two_ok){	//假如有超出3级，就跳出此层循环
					break;
				}
			}
			if(!$filter_ok){	//假如该菜单没有超出3级，就压入过滤后的数组
				$filter_data[]=$value;
			}
		}
		return $filter_data;
	}else{
		t('无法获取到菜单的缓存数据');
		return false;
	}
}

//上传文件
// function upload_file($path='/',$sub_name=''){
// 	//把文件上传到服务器
// 	$upload = new \Think\Upload();	// 实例化上传类
// 	$upload->maxSize = 51457280;	// 设置附件上传大小，默认设置约50MB
// 	$upload->exts = array('jpg', 'gif', 'png', 'jpeg','mp3','ogg','wav');	// 设置附件上传类型
// 	$upload->rootPath = C('UPLOADS');	// 设置附件上传根目录
// 	$upload->savePath = $path;	// 设置附件上传（子）目录
// 	$upload->subName = $sub_name;	// 子目录名称
// 	// 上传文件
// 	$info = $upload->upload();

// 	if(!$info) {	// 上传错误返回错误信息
// 		$error_info=$upload->getError();
// 		$arr['status']=false;
// 		$arr['info']=$error_info;
// 	}else{	// 上传成功
// 		$arr['status']=true;
// 		$arr['data']=$info;
// 	}
// 	return $arr;
// }

//上传文件
function upload(){
    // 获取表单上传文件 例如上传了001.jpg
    $file = request()->file('file');
    // 移动到框架应用根目录/public/uploads/ 目录下
    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
    if($info){
        // 成功上传后 获取上传信息
        // 输出 jpg
        echo $info->getExtension();
        // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
        echo $info->getSaveName();
        // 输出 42a79759f284b767dfcb2a0197904287.jpg
        echo $info->getFilename(); 
    }else{
        // 上传失败获取错误信息
        echo $file->getError();
    }
}

//获取上传文件的数据
function get_upload_data($ids){	//参数（id字符串）
	$map['id']=array('in',$ids);
	$result=Db::name('uploadfile')->where($map)->select();
	if($result){
		return $result;
	}else{
		return false;
	}
}