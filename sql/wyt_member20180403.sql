/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : 127.0.0.1:3306
Source Database       : wyt_tp

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-04-03 10:18:51
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wyt_member
-- ----------------------------
DROP TABLE IF EXISTS `wyt_member`;
CREATE TABLE `wyt_member` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `encrypt` varchar(6) NOT NULL DEFAULT '' COMMENT '加密字段',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态   正常1   禁用2  未激活3',
  `del` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除  1否   2是',
  `sort` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
-- Records of wyt_member
-- ----------------------------
INSERT INTO `wyt_member` VALUES ('1', 'wyt_admin', '18cf9fe323df7c388743919ff58ff5ee', 'abcdef', '0', '1', '1', '0', '0');

-- ----------------------------
-- Table structure for wyt_member_content
-- ----------------------------
DROP TABLE IF EXISTS `wyt_member_content`;
CREATE TABLE `wyt_member_content` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL COMMENT '用户ID',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `nickname` varchar(32) NOT NULL DEFAULT '' COMMENT '昵称',
  `icon` varchar(100) NOT NULL DEFAULT '' COMMENT '头像',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '性别  男1   女2   未知3',
  `birthday` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '生日',
  `content` varchar(100) NOT NULL DEFAULT '' COMMENT '简介',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '电话',
  `wechat` varchar(50) NOT NULL DEFAULT '' COMMENT '微信',
  `tencent` int(15) unsigned NOT NULL DEFAULT '0' COMMENT 'qq',
  `msn` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'MSN',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '所在省',
  `city_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '所在市',
  `district_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '所在区',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '详细地址',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`),
  KEY `email` (`email`) USING BTREE,
  KEY `phone` (`phone`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='用户信息表';

-- ----------------------------
-- Records of wyt_member_content
-- ----------------------------
INSERT INTO `wyt_member_content` VALUES ('1', '1', '', '', '', '1', '0', '', '', '138001380001', '', '0', '0', '0', '0', '0', '', '');

-- ----------------------------
-- Table structure for wyt_member_fail
-- ----------------------------
DROP TABLE IF EXISTS `wyt_member_fail`;
CREATE TABLE `wyt_member_fail` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `login_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '登录时间',
  `login_ip` char(15) NOT NULL DEFAULT '' COMMENT '登录IP',
  `login_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '登录方式 账号1 手机2 邮箱3 第三方4',
  `login_data` text NOT NULL COMMENT '登录json信息',
  `login_code` mediumint(8) NOT NULL DEFAULT '0' COMMENT '登录失败错误码',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='登录失败记录表';

-- ----------------------------
-- Records of wyt_member_fail
-- ----------------------------

-- ----------------------------
-- Table structure for wyt_member_log
-- ----------------------------
DROP TABLE IF EXISTS `wyt_member_log`;
CREATE TABLE `wyt_member_log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL COMMENT '用户ID',
  `login_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '登录时间',
  `login_ip` char(15) NOT NULL DEFAULT '' COMMENT '登录IP',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wyt_member_log
-- ----------------------------

-- ----------------------------
-- Table structure for wyt_member_third_party
-- ----------------------------
DROP TABLE IF EXISTS `wyt_member_third_party`;
CREATE TABLE `wyt_member_third_party` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `third_id` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '第三方平台ID',
  `openid` varchar(32) NOT NULL DEFAULT '' COMMENT '第三方openid',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '第三方平台信息 json格式',
  PRIMARY KEY (`id`),
  KEY `third_id` (`third_id`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='第三方平台信息表';

-- ----------------------------
-- Records of wyt_member_third_party
-- ----------------------------
INSERT INTO `wyt_member_third_party` VALUES ('1', '1', '0', '', '');

-- ----------------------------
-- Table structure for wyt_phpsession
-- ----------------------------
DROP TABLE IF EXISTS `wyt_phpsession`;
CREATE TABLE `wyt_phpsession` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `phpsession` varchar(100) NOT NULL DEFAULT '' COMMENT '客户端session_id',
  `content` text NOT NULL COMMENT '内容，以序列号形式保存',
  `starttime` int(10) NOT NULL COMMENT '开始时间',
  `endtime` int(10) NOT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`),
  KEY `phpsession` (`phpsession`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wyt_phpsession
-- ----------------------------

-- ----------------------------
-- Table structure for wyt_third_party
-- ----------------------------
DROP TABLE IF EXISTS `wyt_third_party`;
CREATE TABLE `wyt_third_party` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '平台名称',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='第三方平台表';

-- ----------------------------
-- Records of wyt_third_party
-- ----------------------------
INSERT INTO `wyt_third_party` VALUES ('1', 'QQ', '0', '1');
INSERT INTO `wyt_third_party` VALUES ('2', '微信', '0', '1');
INSERT INTO `wyt_third_party` VALUES ('3', '新浪微博', '0', '1');
