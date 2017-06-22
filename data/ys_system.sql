/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : ys_system

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2017-06-07 12:03:52
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ys_admin
-- ----------------------------
DROP TABLE IF EXISTS `ys_admin`;
CREATE TABLE `ys_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `nine_password` varchar(255) DEFAULT NULL COMMENT '9宫格密码',
  `reg_time` int(11) DEFAULT NULL COMMENT '注册时间(时间戳)',
  `reg_time_date` datetime DEFAULT NULL COMMENT '注册时间',
  `reg_ip` varchar(255) DEFAULT NULL COMMENT '注册时ip',
  `last_login_time` int(11) DEFAULT NULL COMMENT '最后登录时间(时间戳)',
  `last_login_time_date` datetime DEFAULT NULL COMMENT '最后登录时间',
  `last_login_ip` varchar(255) DEFAULT NULL COMMENT '最后登录ip',
  `update_time` int(11) DEFAULT NULL COMMENT '修改的时间(时间戳)',
  `update_time_date` datetime DEFAULT NULL COMMENT '修改的时间',
  `visit_url` text COMMENT '登录后访问的地址',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '用户状态(1：为正常，0：为停止使用)',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '级别标识',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  FULLTEXT KEY `visit_url` (`visit_url`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='后台管理员表';

-- ----------------------------
-- Records of ys_admin
-- ----------------------------
INSERT INTO `ys_admin` VALUES ('1', 'president', 'MDAwMDAwMDAwMJp_rtHJa5ycwIxqkYp9vGOEoYmWhaB0cQ', 'MDAwMDAwMDAwMIWhtd61fXWos4l-2Yh9qGODi4Gh', null, null, null, '1495295984', '2017-05-20 23:59:44', '127.0.0.1', null, null, 'http://ys.com/manager', '1', '1');
INSERT INTO `ys_admin` VALUES ('2', 'master', 'MDAwMDAwMDAwMJmOqt_KgIaqt3mclYh6oG4', 'MDAwMDAwMDAwMIShqdy0o3Vj', null, null, null, '1496797501', '2017-06-07 09:05:01', '127.0.0.1', null, null, 'http://ys.com/manager', '1', '0');
INSERT INTO `ys_admin` VALUES ('3', 'admin', 'MDAwMDAwMDAwMJCOttnHkGVys4l-2w', null, null, null, null, '1495392337', '2017-05-22 02:45:37', '127.0.0.1', '1494904951', '2017-05-16 11:22:31', 'http://ys.com/manager', '1', '0');

-- ----------------------------
-- Table structure for ys_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `ys_admin_log`;
CREATE TABLE `ys_admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adminID` int(11) NOT NULL COMMENT '后台管理员id',
  `opType` int(11) DEFAULT NULL COMMENT '操作的权限类型',
  `opTime` int(11) NOT NULL COMMENT '操作时的时间戳',
  `opDate` datetime NOT NULL COMMENT '操作时的时间',
  `ip` varchar(255) DEFAULT NULL COMMENT '操作时的ip',
  `content` text COMMENT '操作的内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ys_admin_log
-- ----------------------------
INSERT INTO `ys_admin_log` VALUES ('4', '2', '1', '1493782000', '2017-05-03 11:26:40', '127.0.0.1', '管理员[master]操作了：后台首页');
INSERT INTO `ys_admin_log` VALUES ('5', '2', '1', '1493782341', '2017-05-03 11:32:21', '127.0.0.1', '管理员[master]操作了：后台首页');
INSERT INTO `ys_admin_log` VALUES ('6', '2', '1', '1495392231', '2017-05-22 02:43:51', '127.0.0.1', '管理员[master]操作了：左侧菜单列表');
INSERT INTO `ys_admin_log` VALUES ('7', '2', '1', '1495392235', '2017-05-22 02:43:55', '127.0.0.1', '管理员[master]操作了：查看管理员日志');
INSERT INTO `ys_admin_log` VALUES ('8', '3', '1', '1495392337', '2017-05-22 02:45:37', '127.0.0.1', '管理员[admin]操作了：后台首页');
INSERT INTO `ys_admin_log` VALUES ('9', '3', '1', '1495392345', '2017-05-22 02:45:45', '127.0.0.1', '管理员[admin]操作了：查看管理员日志');
INSERT INTO `ys_admin_log` VALUES ('10', '2', '1', '1495392352', '2017-05-22 02:45:52', '127.0.0.1', '管理员[master]操作了：后台首页');
INSERT INTO `ys_admin_log` VALUES ('11', '2', '1', '1495392355', '2017-05-22 02:45:55', '127.0.0.1', '管理员[master]操作了：后台设置');
INSERT INTO `ys_admin_log` VALUES ('12', '2', '1', '1495392360', '2017-05-22 02:46:00', '127.0.0.1', '管理员[master]操作了：后台设置');

-- ----------------------------
-- Table structure for ys_article
-- ----------------------------
DROP TABLE IF EXISTS `ys_article`;
CREATE TABLE `ys_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '标题-text',
  `simple_intro` text COMMENT '简要介绍-textarea-2',
  `content` text COMMENT '正文内容-edit-2',
  `add_time` int(11) DEFAULT NULL COMMENT '添加的时间戳-hide-2-timestamp',
  `add_date` datetime DEFAULT NULL COMMENT '添加的日期-hide-1-datetime',
  `update_time` int(11) DEFAULT NULL COMMENT '更新文章的时间戳-edit_hide-2-timestamp',
  `update_date` datetime DEFAULT NULL COMMENT '更新文章的日期-edit_hide-1-datetime',
  `recommend` tinyint(1) DEFAULT '0' COMMENT '是否推荐-toggle',
  `weight` int(11) DEFAULT NULL COMMENT '排序(数字越大越靠前)-text',
  `is_show` tinyint(1) DEFAULT '1' COMMENT '是否显示-toggle',
  `owner` tinyint(1) DEFAULT '1' COMMENT '原创-select-1-null-1;2-原创;转载',
  `view_num` int(11) DEFAULT NULL COMMENT '点击量-text',
  `upload` varchar(255) DEFAULT NULL COMMENT '上传附件-upload-2',
  `upload_img` varchar(255) DEFAULT NULL COMMENT '图片上传-upload-2',
  `backup` text COMMENT '备注-edit-2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='后台设置表';

-- ----------------------------
-- Records of ys_article
-- ----------------------------
INSERT INTO `ys_article` VALUES ('16', '', '', '<p>fghdfgh</p>', '1495523989', '2017-05-23 15:19:49', null, null, '0', '0', '0', '1', '0', '', '', null);
INSERT INTO `ys_article` VALUES ('17', '阿斯顿发斯蒂芬', '', '<p><img src=\"/ueditor/php/upload/image/20170607/1496807295908357.gif\" title=\"1496807295908357.gif\" alt=\"92e8647aly1fdne2bh93yg20dw07t7wh (1).gif\"/></p>', '1496807280', '2017-06-07 11:48:00', null, null, '0', '0', '127', '1', '0', '', '14,19', null);

-- ----------------------------
-- Table structure for ys_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `ys_auth_group`;
CREATE TABLE `ys_auth_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '用户组名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态(1为正常，0为禁用)',
  `rules` text COMMENT '访问规则(对应ys_auth_rule表的id)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户组表';

-- ----------------------------
-- Records of ys_auth_group
-- ----------------------------
INSERT INTO `ys_auth_group` VALUES ('1', '超级管理员用户组', '1', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,27,28,29,33,34,35,36,37,38,39,42,43,44,45,46,47,48,50,51,52,53,54,85,86,87,88,89,90');
INSERT INTO `ys_auth_group` VALUES ('2', '普通管理员组', '1', '1,46');

-- ----------------------------
-- Table structure for ys_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `ys_auth_group_access`;
CREATE TABLE `ys_auth_group_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL COMMENT '用户id',
  `group_id` mediumint(8) NOT NULL COMMENT '用户组id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户组关联表';

-- ----------------------------
-- Records of ys_auth_group_access
-- ----------------------------
INSERT INTO `ys_auth_group_access` VALUES ('1', '2', '1');
INSERT INTO `ys_auth_group_access` VALUES ('2', '3', '2');

-- ----------------------------
-- Table structure for ys_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `ys_auth_rule`;
CREATE TABLE `ys_auth_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '访问的url',
  `title` varchar(255) NOT NULL COMMENT '权限名称',
  `type` mediumint(9) DEFAULT '2' COMMENT '权限组标识',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '权限状态(1为正常，0为禁止)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`title`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8 COMMENT='权限规则表';

-- ----------------------------
-- Records of ys_auth_rule
-- ----------------------------
INSERT INTO `ys_auth_rule` VALUES ('1', 'manager', '后台首页', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('2', 'manager/Index/setting', '后台设置', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('3', 'manager/Index/setting_add', '后台新增设置', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('4', 'manager/Index/setting_del', '后台删除设置', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('5', 'manager/Index/user', '用户管理', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('6', 'manager/Index/edit_userstatus', '修改用户状态', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('7', 'manager/PublicFun/status_change', '状态修改', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('8', 'manager/Index/edit_usergroup', '修改用户组', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('9', 'manager/Index/add_user', '新增用户', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('10', 'manager/Index/del_user', '删除用户', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('11', 'manager/Index/usergroup', '用户组', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('12', 'manager/Index/usergroup_add', '新增用户组', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('13', 'manager/Index/usergroup_del', '删除用户组', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('14', 'manager/Index/usergroup_edit', '编辑用户组', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('15', 'manager/Index/edit_usergroup_status', '修改用户组状态', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('16', 'manager/Index/usergroup_update', '修改用户组权限', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('17', 'manager/Index/rules_list', '权限列表', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('18', 'manager/Index/rules_edit', '编辑访问权限', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('19', 'manager/Index/rule_add', '新增访问权限', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('20', 'manager/Index/rule_del', '删除访问规则', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('21', 'manager/Index/edit_rules_status', '修改访问权限状态', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('27', 'manager/Index/rule_group_list', '权限类型管理', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('28', 'manager/Index/rule_group_add_edit', '权限类型的添加或编辑', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('29', 'manager/Index/del_rule_group', '权限类型的删除', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('33', 'manager/Menu/set_menu', '左侧菜单列表', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('34', 'manager/Menu/add_menu', '新增左侧菜单', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('35', 'manager/Menu/del_menu', '删除左侧菜单', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('36', 'manager/Menu/status_menu', '修改左侧菜单状态', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('37', 'manager/Menu/set_menu_top', '顶部菜单列表', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('38', 'manager/Menu/add_menu_top', '新增顶部菜单', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('39', 'manager/Menu/del_menu_top', '删除顶部菜单', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('42', 'manager/Trash/trash_run', '开始数据清理', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('43', 'manager/Trash/clear_runtime', '清理runtime缓存目录', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('44', 'manager/Trash', '数据清理', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('45', 'manager/Trash/clear_session', '清理session', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('46', 'manager/Index/adminlog', '查看管理员日志', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('47', 'manager/Page', '页面自动生成', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('48', 'manager/Page/start_page', '开始生成页面', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('50', 'manager/Article/data_add', '添加文章', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('51', 'manager/PublicFun/ajax_upload_file', '上传文件', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('52', 'manager/Article/data_edit', '文章编辑', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('53', 'manager/Article', '文章管理', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('54', 'manager/Article/data_del', '删除文章', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('85', 'manager/uploadfile', '上传文件库列表页', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('86', 'manager/uploadfile/data_add', '上传文件库添加', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('87', 'manager/uploadfile/data_edit', '上传文件库编辑', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('88', 'manager/uploadfile/data_del', '上传文件库删除', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('89', 'manager/PublicFun/ajax_del_upload_file', '删除上传文件', '1', '1');
INSERT INTO `ys_auth_rule` VALUES ('90', 'manager/PublicFun/get_edit_upload', '编辑上传文件', '1', '1');

-- ----------------------------
-- Table structure for ys_auth_rule_group
-- ----------------------------
DROP TABLE IF EXISTS `ys_auth_rule_group`;
CREATE TABLE `ys_auth_rule_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '权限组名',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='权限组表';

-- ----------------------------
-- Records of ys_auth_rule_group
-- ----------------------------
INSERT INTO `ys_auth_rule_group` VALUES ('1', '基本');
INSERT INTO `ys_auth_rule_group` VALUES ('2', '默认');

-- ----------------------------
-- Table structure for ys_left_menu
-- ----------------------------
DROP TABLE IF EXISTS `ys_left_menu`;
CREATE TABLE `ys_left_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '无' COMMENT '菜单名称',
  `url` varchar(255) DEFAULT 'javascript:;' COMMENT '菜单链接',
  `type` int(11) DEFAULT NULL COMMENT '权限标识',
  `is_show` tinyint(1) DEFAULT '1' COMMENT '是否显示',
  `one` tinyint(1) DEFAULT NULL COMMENT '一级菜单标识',
  `two` varchar(255) DEFAULT NULL COMMENT '二级菜单id',
  `blank` tinyint(1) DEFAULT '0' COMMENT '是否新窗口打开',
  `quick` tinyint(1) DEFAULT '0' COMMENT '是否为常用菜单栏',
  `weight` int(11) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ys_left_menu
-- ----------------------------
INSERT INTO `ys_left_menu` VALUES ('21', '微信管理', 'javascript:;', null, '1', '1', '22,23,24,28,29', '0', '0', '1');
INSERT INTO `ys_left_menu` VALUES ('22', '公众号管理', 'javascript:;', null, '1', null, null, '0', '0', '1');
INSERT INTO `ys_left_menu` VALUES ('23', '微信会员', 'javascript:;', null, '1', null, null, '0', '0', '5');
INSERT INTO `ys_left_menu` VALUES ('24', '回复管理', 'javascript:;', null, '1', null, '25,26,27', '0', '0', '8');
INSERT INTO `ys_left_menu` VALUES ('25', '欢迎消息', 'javascript:;', null, '1', null, null, '0', '0', '1');
INSERT INTO `ys_left_menu` VALUES ('26', '文字消息回复', 'javascript:;', null, '1', null, null, '0', '0', '5');
INSERT INTO `ys_left_menu` VALUES ('27', '图片消息回复', 'javascript:;', null, '1', null, null, '0', '0', '10');
INSERT INTO `ys_left_menu` VALUES ('28', '自定义菜单', 'javascript:;', null, '1', null, null, '0', '0', '15');
INSERT INTO `ys_left_menu` VALUES ('29', '微信设置', 'javascript:;', null, '1', null, null, '0', '0', '20');
INSERT INTO `ys_left_menu` VALUES ('30', '文章管理', '/manager/Article', null, '0', '1', null, '0', '0', '5');
INSERT INTO `ys_left_menu` VALUES ('31', '上传文件库', '/manager/uploadfile', null, '0', '1', null, '0', '0', '99');

-- ----------------------------
-- Table structure for ys_manager_topmenu
-- ----------------------------
DROP TABLE IF EXISTS `ys_manager_topmenu`;
CREATE TABLE `ys_manager_topmenu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '无' COMMENT '顶部按钮名称',
  `url` varchar(255) DEFAULT 'javascript:;' COMMENT '点击访问的url',
  `is_show` varchar(255) DEFAULT NULL COMMENT '对应用户组显示出来(多个组id之间用逗号分隔)',
  `blank` tinyint(1) DEFAULT '0' COMMENT '是否新窗口打开',
  `weight` int(11) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ys_manager_topmenu
-- ----------------------------
INSERT INTO `ys_manager_topmenu` VALUES ('1', '自定义1', 'javascript:;', null, '0', null);

-- ----------------------------
-- Table structure for ys_setting
-- ----------------------------
DROP TABLE IF EXISTS `ys_setting`;
CREATE TABLE `ys_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '设置的名称',
  `name` varchar(255) NOT NULL COMMENT '设置的控制标识',
  `val` varchar(255) DEFAULT NULL COMMENT '设置的值',
  `type` int(11) DEFAULT '2' COMMENT '类型标示',
  `type_style` int(11) DEFAULT '1' COMMENT '后台展示的形式(1:输入框，2:开关，3:下拉框，4:文本编辑器，5:上传，6:复选框，7:文本域，8:单选)',
  `type_txt` varchar(255) DEFAULT NULL COMMENT '后台生成表单的文字选项(用;号分隔，第一个元素必须是开启的意思)',
  `help` varchar(255) DEFAULT NULL COMMENT '帮助说明的文字',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='后台设置表';

-- ----------------------------
-- Records of ys_setting
-- ----------------------------
INSERT INTO `ys_setting` VALUES ('2', '后台登录是否启用验证码', 'login_allow_verify', '0', '1', '2', '启用;不启用', null);
INSERT INTO `ys_setting` VALUES ('3', '是否限制登录失败的次数', 'login_error_times', '0', '1', '2', '是;否', null);
INSERT INTO `ys_setting` VALUES ('4', '登录失败的次数值(单位：次)', 'error_times_num', '6', '1', '1', null, '开启“是否限制登录失败的次数”才生效');
INSERT INTO `ys_setting` VALUES ('5', '禁止登录的时间间隔(单位：秒)', 'error_wait_time', '600', '1', '1', null, '开启“是否限制登录失败的次数”才生效');
INSERT INTO `ys_setting` VALUES ('6', '是否只允许单人登录', 'single_login', '0', '1', '2', '是;否', null);
INSERT INTO `ys_setting` VALUES ('7', '是否启用手机版九宫格登录', 'nine_login', '1', '1', '2', '启用;不启用', null);
INSERT INTO `ys_setting` VALUES ('8', '是否自动填入登录用户名', 'record_login_username', '1', '1', '2', '是;否', null);
INSERT INTO `ys_setting` VALUES ('9', '后台操作是否记录管理员日志', 'record_admin_log', '0', '1', '2', '是;否', null);
INSERT INTO `ys_setting` VALUES ('10', '登录后是否自动清理3个月前的管理员日志', 'auto_clear_adminlog', '0', '1', '2', '是;否', null);
INSERT INTO `ys_setting` VALUES ('11', '一定时间后登录时自动清理runtime缓存目录(单位:天)', 'auto_clear_runtime', '30', '1', '1', null, '关闭的话设为0');
INSERT INTO `ys_setting` VALUES ('12', '测试测试', 'testtest', '', '2', '1', '', '仅作');

-- ----------------------------
-- Table structure for ys_uploadfile
-- ----------------------------
DROP TABLE IF EXISTS `ys_uploadfile`;
CREATE TABLE `ys_uploadfile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '名称-text',
  `url` text COMMENT 'url-textarea',
  `adminid` int(11) DEFAULT NULL COMMENT '管理员id-text-2',
  `admin_name` varchar(255) DEFAULT NULL COMMENT '管理员名称-text',
  `add_time` int(11) DEFAULT NULL COMMENT '上传的时间戳-hide-2-timestamp',
  `add_date` datetime DEFAULT NULL COMMENT '上传的日期-hide-1-datetime',
  `update_time` int(11) DEFAULT NULL COMMENT '更新文件的时间戳-edit_hide-2-timestamp',
  `update_date` datetime DEFAULT NULL COMMENT '更新文件的日期-edit_hide-1-datetime',
  `weight` int(11) DEFAULT NULL COMMENT '排序(数字越大越靠前)-text',
  `param` text COMMENT '参数-textarea-2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ys_uploadfile
-- ----------------------------
INSERT INTO `ys_uploadfile` VALUES ('14', '736e4f4f872fe382768a148bae4a8166.gif', '\\uploads\\20170607\\736e4f4f872fe382768a148bae4a8166.gif', '2', 'master', '1496801755', '2017-06-07 10:15:55', '1496801755', '2017-06-07 10:15:55', null, null);
INSERT INTO `ys_uploadfile` VALUES ('19', '49d6d9e95194ec256f21eb71efef29bb.pdf', '\\uploads\\20170607\\49d6d9e95194ec256f21eb71efef29bb.pdf', '2', 'master', '1496801936', '2017-06-07 10:18:56', '1496801936', '2017-06-07 10:18:56', null, null);
