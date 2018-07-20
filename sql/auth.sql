/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : 127.0.0.1:3306
Source Database       : wyt_tp

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-03-28 10:34:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wyt_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `wyt_auth_group`;
CREATE TABLE `wyt_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `no_edit` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可编辑：1可编辑，2不可编辑（包括修改，删除）',
  `mtype` tinyint(1) NOT NULL DEFAULT '1' COMMENT '角色类型：1管理员，2普通用户',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '角色名称',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `rules` text NOT NULL COMMENT '角色拥有的规则id',
  `sort` mediumint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1启用，2禁用',
  `shows` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1为显示，0为隐藏',
  `del` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否删除：1正常，2删除',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='角色表';

-- ----------------------------
-- Records of wyt_auth_group
-- ----------------------------
INSERT INTO `wyt_auth_group` VALUES ('1', '2', '1', '超级管理员', '', '', '0', '2018-03-28 10:33:31', '1', '1', '1', '0');

-- ----------------------------
-- Table structure for wyt_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `wyt_auth_group_access`;
CREATE TABLE `wyt_auth_group_access` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `group_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户-角色关联细表';

-- ----------------------------
-- Records of wyt_auth_group_access
-- ----------------------------
INSERT INTO `wyt_auth_group_access` VALUES ('1', '1', '1');

-- ----------------------------
-- Table structure for wyt_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `wyt_auth_rule`;
CREATE TABLE `wyt_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一标识',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '权限名称',
  `pid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
  `auth_path` varchar(50) NOT NULL DEFAULT '' COMMENT '全路径',
  `level` tinyint(3) NOT NULL DEFAULT '0' COMMENT '基数',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `desc` varchar(300) NOT NULL DEFAULT '' COMMENT '权限描述',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1后台系统权限',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1启用，2禁用',
  `condition` char(100) NOT NULL DEFAULT '' COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证',
  `shows` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1为显示，0为隐藏',
  `sort` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `del` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否删除：1正常，2删除',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='权限表';

-- ----------------------------
-- Records of wyt_auth_rule
-- ----------------------------
