DROP TABLE IF EXISTS `wyt_order`;
CREATE TABLE `wyt_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `out_trade_no` varchar(32) NOT NULL DEFAULT '' COMMENT '订单号',
  `out_pay_no` varchar(32) NOT NULL DEFAULT '' COMMENT '第三方订单号',
  `price` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '原价单位分',
  `real_pay` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '支付金额，单位分',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结算时间',
  `pay_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '支付类型',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1未结算，2已支付',
  PRIMARY KEY (`id`),
  UNIQUE KEY `out_trade_no` (`out_trade_no`),
) ENGINE=MyISAM AUTO_INCREMENT=259 DEFAULT CHARSET=utf8 COMMENT='订单表';


DROP TABLE IF EXISTS `wyt_pay_type`;
CREATE TABLE `wyt_pay_type` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '名称',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态1正常，2禁用',
  `del` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态1正常，2删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='支付类型表';

/*短信验证*/
DROP TABLE IF EXISTS `wyt_comm_check`;
CREATE TABLE `wyt_comm_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT '' COMMENT '验证码',
  `verify_time` int(12) unsigned DEFAULT '0' COMMENT '过期时间',
  `key` varchar(40) DEFAULT '' COMMENT '唯一字符串',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='通信验证';


-- ----------------------------
-- Table structure for wyt_log
-- ----------------------------
DROP TABLE IF EXISTS `wyt_log`;
CREATE TABLE `wyt_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `ip` char(15) NOT NULL DEFAULT '' COMMENT '操作人IP',
  `msg` varchar(1024) NOT NULL DEFAULT '' COMMENT '内容',
  `url` varchar(250) NOT NULL DEFAULT '' COMMENT '事件路径',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '日志类型',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='日志表';

DROP TABLE IF EXISTS `wyt_pay_type`;
CREATE TABLE `wyt_pay_type` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL COMMENT '名称',
  `sort` mediumint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `createtime` int(10) NOT NULL COMMENT '创建时间',
  `updatetime` int(10) NOT NULL COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1启用，2禁用',
  `del` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否被删除,0被删除，1否',
  `icon` varchar(100) NOT NULL COMMENT '图片',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='支付方式类型表';

-- ----------------------------
-- Records of wyt_pay_type
-- ----------------------------
INSERT INTO `wyt_pay_type` VALUES ('1', '支付宝', '0', '1499045277', '1499045277', '1', '1', '');
INSERT INTO `wyt_pay_type` VALUES ('2', '微信', '0', '1499045277', '1499045277', '1', '1', '');