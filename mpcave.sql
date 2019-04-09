/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 5.6.34-log : Database - mpcave
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`mpcave` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `mpcave`;

/*Table structure for table `mp_admin` */

DROP TABLE IF EXISTS `mp_admin`;

CREATE TABLE `mp_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `realname` varchar(30) DEFAULT NULL COMMENT '管理员姓名',
  `username` varchar(30) DEFAULT NULL COMMENT '账号',
  `gender` tinyint(4) DEFAULT NULL COMMENT '1男2女',
  `email` varchar(50) DEFAULT NULL,
  `tel` varchar(15) DEFAULT NULL COMMENT '手机号',
  `password` varchar(32) DEFAULT NULL COMMENT '密码',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '添加时间',
  `last_login_ip` varchar(20) DEFAULT '0.0.0.0',
  `last_login_time` int(10) unsigned DEFAULT NULL COMMENT '最后登录时间',
  `status` tinyint(4) DEFAULT '1' COMMENT '状态0冻结1正常',
  `login_times` int(11) DEFAULT '0' COMMENT '登陆次数',
  `level` tinyint(4) DEFAULT '1' COMMENT '管理员等级',
  `desc` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_admin` */

insert  into `mp_admin`(`id`,`realname`,`username`,`gender`,`email`,`tel`,`password`,`create_time`,`last_login_ip`,`last_login_time`,`status`,`login_times`,`level`,`desc`) values (1,'姜海蕤','jianghairui',1,'1873645345@qq.com','13102163019','32d707d6e29a579e36c4e8031b2018da',1540099895,'218.68.108.73',1554781152,1,58,1,'好人啊\n'),(2,'韩驰','hanchi',1,'','','182190c915daadda6d909f9691819e45',1540102491,'180.212.36.73',1544778022,0,0,1,''),(3,'荣建','rongjian',1,'','','182190c915daadda6d909f9691819e45',1540102510,'221.196.194.14',1540102759,0,0,1,''),(4,'测试用户','test',2,'','','182190c915daadda6d909f9691819e45',1540103141,'60.25.57.208',1543394548,0,0,1,''),(5,'李钟毓','lizhongyu',1,'326693610@qq.com','17631617680','48dc8c0909ae3afb132c13d4256910e1',1553735897,'218.68.108.150',1553738908,0,1,1,'');

/*Table structure for table `mp_auth_group` */

DROP TABLE IF EXISTS `mp_auth_group`;

CREATE TABLE `mp_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `desc` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `mp_auth_group` */

insert  into `mp_auth_group`(`id`,`title`,`desc`,`status`,`rules`) values (1,'（⚰恐怖降临了村庄⚰）','',1,'');

/*Table structure for table `mp_auth_group_access` */

DROP TABLE IF EXISTS `mp_auth_group_access`;

CREATE TABLE `mp_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `mp_auth_group_access` */

insert  into `mp_auth_group_access`(`uid`,`group_id`) values (5,1);

/*Table structure for table `mp_auth_rule` */

DROP TABLE IF EXISTS `mp_auth_rule`;

CREATE TABLE `mp_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '',
  `title` char(30) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `condition` char(100) NOT NULL DEFAULT '',
  `pid` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `mp_auth_rule` */

/*Table structure for table `mp_bidding` */

DROP TABLE IF EXISTS `mp_bidding`;

CREATE TABLE `mp_bidding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `req_id` int(11) DEFAULT NULL,
  `work_id` int(11) DEFAULT NULL,
  `desc` varchar(100) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_bidding` */

insert  into `mp_bidding`(`id`,`uid`,`req_id`,`work_id`,`desc`,`create_time`) values (5,2,7,3,'贼鸡儿棒，我要做1亿个，钱已经打过去了！！','2019-03-27 11:27:57');

/*Table structure for table `mp_cart` */

DROP TABLE IF EXISTS `mp_cart`;

CREATE TABLE `mp_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `num` int(11) DEFAULT '1',
  `use_attr` tinyint(4) DEFAULT '0',
  `attr_id` int(11) DEFAULT NULL,
  `attr` varchar(20) DEFAULT '默认',
  `create_time` int(11) DEFAULT NULL,
  `del` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_cart` */

insert  into `mp_cart`(`id`,`uid`,`goods_id`,`num`,`use_attr`,`attr_id`,`attr`,`create_time`,`del`) values (37,2,1,5,0,NULL,'默认',1554714010,0),(43,2,10,3,0,NULL,'默认',1554791772,0);

/*Table structure for table `mp_collect` */

DROP TABLE IF EXISTS `mp_collect`;

CREATE TABLE `mp_collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `note_id` int(11) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_collect` */

insert  into `mp_collect`(`id`,`uid`,`note_id`,`create_time`) values (2,2,3,'2019-03-14 17:00:50'),(6,1,5,'2019-03-15 10:44:10'),(9,2,6,'2019-03-22 18:05:40'),(10,2,7,'2019-03-25 11:11:45'),(11,2,5,'2019-03-25 11:11:51'),(12,2,4,'2019-03-25 11:12:05'),(13,2,9,'2019-03-25 15:47:47'),(15,3,11,'2019-03-27 09:01:40'),(16,7,7,'2019-03-27 10:59:44'),(17,7,5,'2019-03-27 10:59:52'),(19,3,13,'2019-03-27 18:27:16'),(20,1,13,'2019-03-27 18:27:25'),(26,2,24,'2019-03-28 10:05:41'),(31,14,42,'2019-03-28 17:55:58'),(33,17,44,'2019-03-28 18:01:02'),(34,2,53,'2019-03-28 18:11:20'),(35,2,52,'2019-03-28 18:17:56'),(36,4,54,'2019-03-28 18:23:29'),(40,4,59,'2019-03-28 18:39:22'),(41,2,62,'2019-03-28 19:20:08'),(42,2,42,'2019-03-29 16:24:39'),(43,2,14,'2019-04-02 21:07:52'),(44,3,37,'2019-04-06 14:34:26'),(46,3,66,'2019-04-06 14:44:02'),(47,3,68,'2019-04-07 11:57:54'),(48,3,60,'2019-04-07 12:47:30'),(49,4,68,'2019-04-08 12:00:14'),(50,4,60,'2019-04-08 12:04:20'),(52,2,35,'2019-04-08 17:35:01'),(53,1,65,'2019-04-09 14:00:04'),(54,2,65,'2019-04-09 14:00:04'),(55,2,68,'2019-04-09 14:06:28');

/*Table structure for table `mp_comment` */

DROP TABLE IF EXISTS `mp_comment`;

CREATE TABLE `mp_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note_id` int(11) NOT NULL COMMENT '笔记id',
  `uid` int(11) NOT NULL COMMENT '发表评论的用户id',
  `to_cid` int(11) DEFAULT '0' COMMENT '回复评论的id',
  `to_uid` int(11) DEFAULT '0' COMMENT '回复评论人的id',
  `root_cid` int(11) DEFAULT '0',
  `content` varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT '评论内容',
  `created_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='笔记的评论表';

/*Data for the table `mp_comment` */

insert  into `mp_comment`(`id`,`note_id`,`uid`,`to_cid`,`to_uid`,`root_cid`,`content`,`created_time`) values (1,3,1,0,0,0,'楼下是我！','2019-03-14 17:25:38'),(2,3,1,0,0,0,'内存','2019-03-14 17:25:47'),(3,3,1,2,1,2,'分needed','2019-03-14 17:25:54'),(4,4,1,0,0,0,'这是麦帅！！！','2019-03-14 18:14:50'),(5,4,1,4,1,4,'五星上将！据说只在战时授予！','2019-03-14 18:15:30'),(6,5,1,0,0,0,'一楼是我的','2019-03-15 10:37:41'),(7,4,2,5,1,4,'流弊！','2019-03-18 18:11:23'),(8,7,1,0,0,0,'发表一条评论！！！','2019-03-20 10:38:52'),(9,7,1,0,0,0,'荣建啊。','2019-03-20 10:38:59'),(10,7,1,9,1,9,'? ? ? ? ? ? ? ? ','2019-03-20 10:39:20'),(11,7,3,0,0,0,'神马乱七八糟的','2019-03-24 11:33:09'),(12,18,1,0,0,0,'小老弟儿，挺有才啊！','2019-03-28 09:08:31'),(13,24,8,0,0,0,'彭于晏妻子在线捉奸','2019-03-28 10:04:42'),(14,24,1,0,0,0,'真不要? ','2019-03-28 10:04:54'),(15,24,1,13,8,13,'说的对','2019-03-28 10:05:06'),(16,24,2,14,1,14,'+1','2019-03-28 10:05:11'),(17,47,8,0,0,0,'好棒，真的很好看','2019-03-28 18:01:20'),(18,39,8,0,0,0,'爱上了这个场景??????❤️','2019-03-28 18:01:47'),(19,45,17,0,0,0,'好看哇','2019-03-28 18:01:57'),(20,47,17,0,0,0,'这个挺好看','2019-03-28 18:02:12'),(21,37,8,0,0,0,'太好看了，绘画功底深厚??????','2019-03-28 18:02:54');

/*Table structure for table `mp_design_works` */

DROP TABLE IF EXISTS `mp_design_works`;

CREATE TABLE `mp_design_works` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `desc` varchar(500) DEFAULT NULL COMMENT '描述',
  `pics` varchar(2000) DEFAULT NULL COMMENT '图片多张',
  `uid` int(11) DEFAULT NULL COMMENT '用户id',
  `type` tinyint(4) DEFAULT '1' COMMENT '作品类型1展览2参赛',
  `req_id` int(11) DEFAULT NULL COMMENT '需求ID',
  `win` tinyint(4) DEFAULT NULL COMMENT '是否获奖',
  `status` tinyint(4) DEFAULT NULL COMMENT '状态',
  `bid_num` int(11) DEFAULT '0' COMMENT '竞标数',
  `vote` int(11) DEFAULT '0' COMMENT '票数',
  `create_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_design_works` */

insert  into `mp_design_works`(`id`,`title`,`desc`,`pics`,`uid`,`type`,`req_id`,`win`,`status`,`bid_num`,`vote`,`create_time`) values (1,'对对对','发生的方式发生沙发撒地方','a:2:{i:0;s:56:\"static/uploads/work/2019-03-22/155321768235668700463.png\";i:1;s:56:\"static/uploads/work/2019-03-22/155321768442872300742.png\";}',2,2,2,NULL,NULL,0,3,1553217690),(2,'这次应该是老子了吧','阿的说法都是，阿的说法都是，阿的说法都是，阿的说法都是，阿的说法都是，阿的说法都是，阿的说法都是，阿的说法都是，阿的说法都是，阿的说法都是','a:4:{i:0;s:56:\"static/uploads/work/2019-03-22/155322043888902600841.jpg\";i:1;s:56:\"static/uploads/work/2019-03-22/155322044108425400492.png\";i:2;s:56:\"static/uploads/work/2019-03-22/155322044295750600438.png\";i:3;s:56:\"static/uploads/work/2019-03-22/155322045572914100554.png\";}',2,2,5,NULL,NULL,0,14,1553220462),(3,'这是我的作品','我这作品如果没中奖肯定有黑幕啊！','a:6:{i:0;s:56:\"static/uploads/work/2019-03-22/155324088414955700507.jpg\";i:1;s:56:\"static/uploads/work/2019-03-22/155324089614424200571.jpg\";i:2;s:56:\"static/uploads/work/2019-03-22/155324090496190000968.jpg\";i:3;s:56:\"static/uploads/work/2019-03-22/155324091377523300572.jpg\";i:4;s:56:\"static/uploads/work/2019-03-22/155324092376635200735.jpg\";i:5;s:56:\"static/uploads/work/2019-03-22/155324092947891800285.jpg\";}',1,2,7,NULL,NULL,1,3,1553240957),(4,'自己的作品','展示用，展示用，展示用，展示用，展示用，展示用，展示用，展示用，展示用，展示用，展示用，展示用，展示用，展示用。','a:4:{i:0;s:56:\"static/uploads/work/2019-03-22/155324535238462100920.png\";i:1;s:56:\"static/uploads/work/2019-03-22/155324535446199900683.png\";i:2;s:56:\"static/uploads/work/2019-03-22/155324535754228600205.png\";i:3;s:56:\"static/uploads/work/2019-03-22/155324536023989500878.png\";}',2,1,NULL,NULL,NULL,0,0,1553245372),(5,'标题','啦咯啦咯啦咯啦咯','a:1:{i:0;s:56:\"static/uploads/work/2019-03-22/155324650507112700687.jpg\";}',2,1,NULL,NULL,NULL,0,0,1553246512),(6,'看看','路兔兔','a:1:{i:0;s:56:\"static/uploads/work/2019-03-22/155324662450955600129.jpg\";}',2,1,NULL,NULL,NULL,0,0,1553246635),(7,'23','23123312','a:1:{i:0;s:56:\"static/uploads/work/2019-03-22/155324665293935600528.png\";}',2,1,NULL,NULL,NULL,0,0,1553246655),(8,'奇怪了','发发发发','a:1:{i:0;s:56:\"static/uploads/work/2019-03-22/155324669701727200865.jpg\";}',2,1,NULL,NULL,NULL,0,0,1553246707),(9,'养乐多','平面设计','a:1:{i:0;s:56:\"static/uploads/work/2019-03-27/155369350843157800369.jpg\";}',8,1,NULL,NULL,NULL,0,0,1553693525),(10,'设计','平面设计作品','a:2:{i:0;s:56:\"static/uploads/work/2019-03-27/155369362395849600379.jpg\";i:1;s:56:\"static/uploads/work/2019-03-27/155369365727816000537.jpg\";}',8,1,NULL,NULL,NULL,0,0,1553693668),(11,'创意海报','创意海报设计','a:1:{i:0;s:56:\"static/uploads/work/2019-03-27/155369381988683700442.jpg\";}',8,1,NULL,NULL,NULL,0,0,1553693845),(12,'灯具','设计创意灯具','a:1:{i:0;s:56:\"static/uploads/work/2019-03-27/155369760895898400815.jpg\";}',9,1,NULL,NULL,NULL,0,0,1553697623),(13,'儿童用品','设计系列儿童用品','a:1:{i:0;s:56:\"static/uploads/work/2019-03-27/155369770713796900433.jpg\";}',9,1,NULL,NULL,NULL,0,0,1553697719),(14,'多功能复古书桌','沿袭了重庆巴蜀文化的文化特色','a:1:{i:0;s:56:\"static/uploads/work/2019-03-28/155376678362904800459.jpg\";}',8,2,10,NULL,NULL,0,3,1553766828),(15,'白瓷','温润的色泽，流畅的设计理念','a:1:{i:0;s:56:\"static/uploads/work/2019-03-28/155376718231380900681.jpg\";}',17,1,NULL,NULL,NULL,0,0,1553767206),(16,'瓷','以裂变式的设计元素为主','a:1:{i:0;s:56:\"static/uploads/work/2019-03-28/155376721192114700171.jpg\";}',17,1,NULL,NULL,NULL,0,0,1553767226),(17,'琴','。','a:1:{i:0;s:56:\"static/uploads/work/2019-03-28/155376723238016700986.jpg\";}',17,1,NULL,NULL,NULL,0,0,1553767239);

/*Table structure for table `mp_focus` */

DROP TABLE IF EXISTS `mp_focus`;

CREATE TABLE `mp_focus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `to_uid` int(11) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_focus` */

insert  into `mp_focus`(`id`,`uid`,`to_uid`,`create_time`) values (10,2,3,'2019-03-27 10:41:43'),(13,1,8,'2019-03-28 09:06:45'),(14,1,11,'2019-03-28 09:29:48'),(20,8,9,'2019-03-28 10:05:12'),(21,1,9,'2019-03-28 10:05:19'),(22,9,8,'2019-03-28 10:05:28'),(25,2,17,'2019-03-28 18:04:23'),(26,3,14,'2019-03-28 18:33:13'),(27,3,11,'2019-03-28 18:35:59'),(31,2,8,'2019-03-28 18:49:40'),(32,2,9,'2019-03-28 19:04:27'),(33,3,17,'2019-03-28 19:08:59'),(34,3,4,'2019-04-02 09:46:55'),(35,3,9,'2019-04-07 12:47:32'),(36,46,8,'2019-04-08 21:03:21'),(37,1,4,'2019-04-09 14:00:08');

/*Table structure for table `mp_goods` */

DROP TABLE IF EXISTS `mp_goods`;

CREATE TABLE `mp_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pcate_id` int(11) DEFAULT NULL COMMENT '分类ID',
  `cate_id` int(11) DEFAULT NULL COMMENT '子分类ID',
  `name` varchar(15) DEFAULT NULL COMMENT '商品名',
  `detail` text COMMENT '商品详情',
  `origin_price` decimal(10,2) DEFAULT '0.00' COMMENT '原价(只显示用)',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `vip_price` decimal(10,2) DEFAULT '0.00' COMMENT '会员价',
  `pics` varchar(2000) DEFAULT NULL COMMENT '缩略图',
  `unit` varchar(30) DEFAULT '件' COMMENT '单位',
  `weight` double(10,2) DEFAULT '0.00' COMMENT '重量',
  `carriage` decimal(10,2) DEFAULT '20.00' COMMENT '运费',
  `reduction` decimal(10,2) DEFAULT '20.00' COMMENT '满减运费',
  `service` varchar(255) DEFAULT NULL COMMENT '7天无理由退货',
  `attr` varchar(20) DEFAULT '默认' COMMENT '规格',
  `use_attr` tinyint(4) DEFAULT '0' COMMENT '是否使用规格',
  `limit` tinyint(4) unsigned DEFAULT '5' COMMENT '限购数量',
  `stock` int(11) DEFAULT '0' COMMENT '库存',
  `sales` int(11) DEFAULT '0' COMMENT '销量',
  `create_time` int(10) unsigned DEFAULT NULL,
  `desc` varchar(50) DEFAULT NULL COMMENT '描述',
  `hot` tinyint(4) DEFAULT '0' COMMENT '1热销',
  `recommend` tinyint(4) DEFAULT '0' COMMENT '1推荐',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) DEFAULT '0' COMMENT '0下架1上架',
  `del` tinyint(4) DEFAULT '0' COMMENT '0删除1正常',
  `width` int(11) DEFAULT '1',
  `height` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_goods` */

insert  into `mp_goods`(`id`,`pcate_id`,`cate_id`,`name`,`detail`,`origin_price`,`price`,`vip_price`,`pics`,`unit`,`weight`,`carriage`,`reduction`,`service`,`attr`,`use_attr`,`limit`,`stock`,`sales`,`create_time`,`desc`,`hot`,`recommend`,`sort`,`status`,`del`,`width`,`height`) values (1,1,8,'文创娃娃','<p>微擎二无群二</p>','1000.00','960.00','0.00','a:2:{i:0;s:56:\"static/upload/goods/2019-04-03/155410354195848200457.jpg\";i:1;s:56:\"static/upload/goods/2019-04-03/155410354435030600820.jpg\";}','个',0.00,'100.00','0.00','0','默认',0,5,94,10,1554296371,'这个地方是显示在商品标题下面的一些简介',0,0,1000,1,0,1024,576),(2,4,15,'宋徽宗瘦金字体书法','<p>宋徽宗真迹,上海拍卖行独一份</p>','25000.00','19800.00','0.00','a:3:{i:0;s:56:\"static/upload/goods/2019-04-03/155410405652581900344.jpg\";i:1;s:56:\"static/upload/goods/2019-04-03/155410406347345100332.jpg\";i:2;s:56:\"static/upload/goods/2019-04-03/155410407104164200553.jpg\";}','幅',0.00,'50.00','0.00','假一赔到公司破产','默认',0,6,96,0,1554260841,'商品列表里的简单描述',1,0,0,1,0,690,545),(3,3,12,'琥珀大班指','<p>描述啊</p>','988.00','600.00','0.00','a:4:{i:0;s:56:\"static/upload/goods/2019-04-03/155417449500795200170.jpg\";i:1;s:56:\"static/upload/goods/2019-04-03/155417449916562600493.jpg\";i:2;s:56:\"static/upload/goods/2019-04-03/155417450348936600606.jpg\";i:3;s:56:\"static/upload/goods/2019-04-03/155417450793772900718.jpg\";}','件',0.00,'35.00','300.00','7天无理由退货','默认',0,2,30,0,1554260797,'商品列表里的简单描述',0,0,0,1,0,360,360),(4,1,8,'文创鸡贼杯','<p>8元一只,你还在等什么???</p>','8.00','8.00','0.00','a:2:{i:0;s:56:\"static/upload/goods/2019-04-03/155417706526139500851.jpg\";i:1;s:56:\"static/upload/goods/2019-04-03/155417707395779400151.jpg\";}','只',0.00,'40.00','200.00','假一赔十','默认',0,3,99,0,1554260832,'商品列表里的简单描述',1,0,0,1,0,240,240),(5,2,10,'山海文创纸巾','<p style=\"text-align: center;\">文创大纸巾</p>','2.00','1.00','0.00','a:2:{i:0;s:56:\"static/upload/goods/2019-04-03/155418668882386000176.jpg\";i:1;s:56:\"static/upload/goods/2019-04-03/155418669177732100630.jpg\";}','包',0.00,'15.00','18.00','7天无理由退货','默认',0,4,100,0,1554260821,'商品列表里的简单描述',0,0,0,1,0,360,360),(7,1,8,'“灯笼红火”小茶杯','<p>红灯笼高高挂起，寓意喜庆。精致的杯身，着多彩釉面，使杯子变得丰富多彩。</p>','140.00','128.00','0.00','a:3:{i:0;s:56:\"static/upload/goods/2019-04-08/155470796967072800722.jpg\";i:1;s:56:\"static/upload/goods/2019-04-08/155470797600014500559.jpg\";i:2;s:56:\"static/upload/goods/2019-04-08/155470797822256700541.jpg\";}','只',0.00,'0.00','0.00','0','默认',0,5,100,0,1554708076,'​红灯笼高高挂起，寓意喜庆。精致的杯身，着多彩釉面，使杯子变得丰富多彩。',0,0,0,1,0,360,360),(8,2,5,'荆州博物馆 拉画笔','                                    ','35.00','10.00','0.00','a:1:{i:0;s:56:\"static/upload/goods/2019-04-08/155470993754398300724.jpg\";}','根',0.00,'6.00','60.00','7天无理由退款','默认',0,5,9000,6845,1554710078,'伸拉式圆珠笔',1,0,0,1,0,360,360),(9,3,17,'荆州博物馆 绣荷包','                                    ','1420.00','550.00','0.00','a:1:{i:0;s:56:\"static/upload/goods/2019-04-08/155471018443228000211.jpg\";}','个',0.00,'0.00','60.00','7天无理由退款','默认',0,5,60,35,1554717313,'荆州博物馆 绣荷包，经典文化，百年传承',0,0,0,1,0,360,360),(10,3,11,'荆州博物馆 绣钱包','','1620.00','650.00','0.00','a:1:{i:0;s:56:\"static/upload/goods/2019-04-08/155471259536713300900.jpg\";}','个',0.00,'0.00','60.00','7天无理由退款','默认',0,5,500,420,1554712729,'精致刺绣钱包',0,0,0,1,0,360,360);

/*Table structure for table `mp_goods_attr` */

DROP TABLE IF EXISTS `mp_goods_attr`;

CREATE TABLE `mp_goods_attr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) DEFAULT NULL,
  `value` varchar(20) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `number` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_goods_attr` */

/*Table structure for table `mp_goods_cate` */

DROP TABLE IF EXISTS `mp_goods_cate`;

CREATE TABLE `mp_goods_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` varchar(255) DEFAULT NULL,
  `cate_name` varchar(25) DEFAULT NULL,
  `pid` int(11) DEFAULT '0',
  `status` tinyint(4) DEFAULT '0' COMMENT '0隐藏1显示',
  `del` tinyint(4) DEFAULT '0' COMMENT '0未删除1已删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_goods_cate` */

insert  into `mp_goods_cate`(`id`,`icon`,`cate_name`,`pid`,`status`,`del`) values (1,'static/upload/2019-03-20/155306475386012400524.jpg','复仿瓷器',0,1,0),(2,'static/upload/2019-03-20/155306476960373000927.jpg','办公用品',0,1,0),(3,'static/upload/2019-03-20/155306479867712000329.jpg','饰品配饰',0,1,0),(4,'static/upload/2019-03-20/155306481629851300660.jpg','复仿书画',0,1,0),(5,'static/upload/2019-03-20/155306610639003500221.jpg','文具',2,1,0),(6,'static/upload/2019-03-20/155306486157324100635.jpg','厅堂摆件',0,1,0),(7,'static/upload/2019-03-20/155306484414836200209.jpg','国风书店',0,1,0),(8,'static/upload/2019-03-20/155306760995394400499.jpg','器型',1,1,0),(9,'static/upload/2019-03-20/155306762469928300727.jpg','窑口',1,1,0),(10,'static/upload/2019-03-20/155306764781932600843.jpg','纸巾',2,1,0),(11,'static/upload/2019-03-20/155306767268258500366.jpg','玛瑙',3,1,0),(12,'static/upload/2019-03-20/155306768226607000934.jpg','琥珀',3,1,0),(13,'static/upload/2019-03-20/155306770809549700559.jpg','花鸟',4,1,0),(14,'static/upload/2019-03-20/155306776343591100816.jpg','山水',4,1,0),(15,'static/upload/2019-03-20/155306902848765500880.jpg','宋徽宗瘦金字体',4,1,0),(16,'static/upload/2019-03-20/155306905964776900341.jpg','颜真卿书法',4,1,0),(17,'static/upload/2019-04-08/155471242021629500875.jpg','布制品',3,1,0);

/*Table structure for table `mp_like` */

DROP TABLE IF EXISTS `mp_like`;

CREATE TABLE `mp_like` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `note_id` int(11) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_like` */

insert  into `mp_like`(`id`,`uid`,`note_id`,`create_time`) values (4,1,4,'2019-03-14 17:48:49'),(5,1,2,'2019-03-15 10:33:37'),(6,1,5,'2019-03-15 10:37:28'),(8,2,3,'2019-03-18 17:54:28'),(9,2,5,'2019-03-18 18:10:50'),(10,1,3,'2019-03-20 09:38:31'),(11,1,7,'2019-03-20 10:38:37'),(12,1,6,'2019-03-20 11:06:43'),(13,1,8,'2019-03-22 09:29:46'),(15,2,6,'2019-03-22 18:05:39'),(16,3,7,'2019-03-24 11:32:54'),(18,2,7,'2019-03-25 11:06:16'),(19,2,4,'2019-03-25 11:06:24'),(20,2,9,'2019-03-25 15:47:45'),(21,3,10,'2019-03-26 18:00:42'),(23,7,7,'2019-03-27 10:59:45'),(24,7,5,'2019-03-27 10:59:51'),(25,3,8,'2019-03-27 11:57:02'),(26,3,6,'2019-03-27 11:57:16'),(27,3,4,'2019-03-27 11:57:25'),(28,3,11,'2019-03-27 15:53:17'),(29,8,7,'2019-03-27 16:18:06'),(30,3,13,'2019-03-27 18:27:10'),(31,1,18,'2019-03-28 09:06:56'),(38,9,24,'2019-03-28 10:04:49'),(39,1,24,'2019-03-28 10:05:26'),(40,4,24,'2019-03-28 10:05:36'),(42,2,24,'2019-03-28 10:05:39'),(43,8,24,'2019-03-28 10:06:19'),(44,11,24,'2019-03-28 10:06:40'),(45,8,25,'2019-03-28 10:11:31'),(46,8,15,'2019-03-28 10:17:12'),(47,8,11,'2019-03-28 10:17:18'),(48,8,14,'2019-03-28 10:17:22'),(49,8,28,'2019-03-28 11:43:00'),(52,1,13,'2019-03-28 14:24:27'),(53,8,32,'2019-03-28 17:27:12'),(54,8,33,'2019-03-28 17:27:14'),(55,3,33,'2019-03-28 17:46:25'),(56,14,42,'2019-03-28 17:55:59'),(58,14,44,'2019-03-28 17:56:05'),(59,14,40,'2019-03-28 17:56:11'),(60,14,43,'2019-03-28 17:56:14'),(61,14,46,'2019-03-28 17:56:17'),(63,5,38,'2019-03-28 18:00:49'),(65,16,48,'2019-03-28 18:00:59'),(66,16,47,'2019-03-28 18:01:04'),(67,5,52,'2019-03-28 18:01:06'),(68,17,44,'2019-03-28 18:01:07'),(69,8,47,'2019-03-28 18:01:11'),(70,17,46,'2019-03-28 18:01:11'),(71,17,48,'2019-03-28 18:01:14'),(72,16,46,'2019-03-28 18:01:19'),(74,16,44,'2019-03-28 18:01:23'),(75,16,43,'2019-03-28 18:01:27'),(76,8,39,'2019-03-28 18:01:30'),(77,16,45,'2019-03-28 18:01:32'),(78,16,41,'2019-03-28 18:01:57'),(79,8,37,'2019-03-28 18:01:57'),(80,16,40,'2019-03-28 18:02:00'),(81,16,39,'2019-03-28 18:02:05'),(82,17,14,'2019-03-28 18:02:18'),(83,17,32,'2019-03-28 18:02:21'),(84,17,38,'2019-03-28 18:02:25'),(85,17,39,'2019-03-28 18:02:29'),(86,17,42,'2019-03-28 18:02:32'),(87,8,35,'2019-03-28 18:03:02'),(88,8,34,'2019-03-28 18:03:20'),(89,8,19,'2019-03-28 18:03:34'),(90,16,38,'2019-03-28 18:03:36'),(91,16,37,'2019-03-28 18:03:42'),(92,16,34,'2019-03-28 18:03:48'),(93,16,35,'2019-03-28 18:03:53'),(94,16,33,'2019-03-28 18:03:57'),(95,16,32,'2019-03-28 18:04:00'),(96,16,15,'2019-03-28 18:04:05'),(98,16,19,'2019-03-28 18:04:12'),(99,16,17,'2019-03-28 18:04:15'),(101,16,14,'2019-03-28 18:04:22'),(102,16,16,'2019-03-28 18:04:31'),(103,5,48,'2019-03-28 18:04:35'),(104,5,45,'2019-03-28 18:04:43'),(105,8,53,'2019-03-28 18:06:59'),(106,8,51,'2019-03-28 18:09:53'),(107,8,50,'2019-03-28 18:09:56'),(108,8,52,'2019-03-28 18:10:04'),(114,2,49,'2019-03-28 18:12:36'),(115,2,48,'2019-03-28 18:16:40'),(116,2,52,'2019-03-28 18:17:55'),(117,16,53,'2019-03-28 18:20:59'),(118,16,54,'2019-03-28 18:21:04'),(119,16,52,'2019-03-28 18:21:11'),(120,16,51,'2019-03-28 18:21:19'),(121,16,50,'2019-03-28 18:21:24'),(122,16,49,'2019-03-28 18:21:28'),(123,11,58,'2019-03-28 18:31:13'),(125,3,63,'2019-03-28 18:35:56'),(126,11,63,'2019-03-28 18:36:05'),(129,2,53,'2019-03-28 18:49:41'),(130,3,58,'2019-03-28 19:04:26'),(131,3,48,'2019-03-28 19:09:02'),(132,3,45,'2019-03-28 19:09:10'),(134,2,63,'2019-03-28 19:20:01'),(136,3,52,'2019-03-28 19:25:34'),(137,16,62,'2019-03-28 22:44:31'),(138,2,14,'2019-04-02 21:07:51'),(139,1,34,'2019-04-03 16:13:56'),(140,3,37,'2019-04-06 14:34:24'),(142,3,66,'2019-04-06 14:44:01'),(143,28,59,'2019-04-07 08:57:02'),(144,3,68,'2019-04-07 11:57:53'),(145,3,60,'2019-04-07 12:47:29'),(148,4,68,'2019-04-08 12:00:10'),(149,4,66,'2019-04-08 12:02:42'),(150,4,64,'2019-04-08 12:02:47'),(151,4,65,'2019-04-08 12:03:01'),(152,4,62,'2019-04-08 12:03:48'),(153,4,54,'2019-04-08 15:18:21'),(154,4,59,'2019-04-08 15:18:25'),(155,4,60,'2019-04-08 15:18:31'),(156,2,64,'2019-04-08 15:57:21'),(157,2,68,'2019-04-08 17:31:24'),(158,2,62,'2019-04-08 17:31:29'),(159,43,68,'2019-04-08 17:43:35'),(160,46,68,'2019-04-08 21:03:11'),(161,1,65,'2019-04-09 14:00:01'),(162,2,65,'2019-04-09 14:00:03'),(163,8,68,'2019-04-09 14:00:43');

/*Table structure for table `mp_note` */

DROP TABLE IF EXISTS `mp_note`;

CREATE TABLE `mp_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL COMMENT '笔记标题',
  `content` text COMMENT '笔记内容',
  `pics` varchar(2000) DEFAULT NULL COMMENT '图片',
  `status` tinyint(4) DEFAULT '1' COMMENT '0审核中1通过审核2未通过',
  `like` int(11) DEFAULT '0' COMMENT '点赞数',
  `recommend` tinyint(4) DEFAULT '0' COMMENT '0不推荐1推荐',
  `collect` int(11) DEFAULT '0' COMMENT '收藏数',
  `reason` varchar(50) DEFAULT NULL COMMENT '拒绝理由',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT NULL,
  `del` tinyint(4) DEFAULT '0' COMMENT '0正常1已删除',
  `width` int(11) DEFAULT '1',
  `height` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_note` */

insert  into `mp_note`(`id`,`uid`,`title`,`content`,`pics`,`status`,`like`,`recommend`,`collect`,`reason`,`create_time`,`update_time`,`del`,`width`,`height`) values (1,2,'啊啊','阿巴阿巴','a:1:{i:0;s:56:\"static/uploads/note/2019-03-14/155255384509947300142.jpg\";}',2,15,0,0,'内容违规','2019-03-14 16:58:16',NULL,1,321,298),(2,2,'优秀！！','发发发','a:1:{i:0;s:56:\"static/uploads/note/2019-03-14/155255390659689700916.jpg\";}',2,54,0,0,'老弟，你这笔记图片太丑了，内容也不行啊','2019-03-14 16:58:35',NULL,1,220,197),(3,2,'这次 图不一样了','有啥好填的','a:1:{i:0;s:56:\"static/uploads/note/2019-03-14/155255393265165400120.jpg\";}',1,30,0,0,'笔记内容涉及政治','2019-03-14 16:59:07',NULL,1,179,198),(4,1,'论抗美援朝战争(三)','道格拉斯.麦克阿瑟\n? ? ? ? ? ? ? ? \n? ? ? ? ? ? \nლ(•̀ _ •́ ლ)答应我','a:2:{i:0;s:56:\"static/uploads/note/2019-03-14/155255679590990900652.jpg\";i:1;s:56:\"static/uploads/note/2019-03-14/155255681284863500615.jpg\";}',1,23,1,1,NULL,'2019-03-14 17:48:30',NULL,1,641,453),(5,1,'这是高圆圆啊','赵又廷，夺妻之仇，不共戴天！? ? ? ','a:4:{i:0;s:56:\"static/uploads/note/2019-03-15/155261736168500800245.jpg\";i:1;s:56:\"static/uploads/note/2019-03-15/155261736509615100416.jpg\";i:2;s:56:\"static/uploads/note/2019-03-15/155261737767430300966.jpg\";i:3;s:56:\"static/uploads/note/2019-03-15/155261738164816100611.jpg\";}',1,51,1,3,NULL,'2019-03-15 10:37:19',NULL,1,640,906),(6,1,'，来了。','，吧。的，的','a:1:{i:0;s:56:\"static/uploads/note/2019-03-19/155297977949375400797.jpg\";}',1,3,1,1,NULL,'2019-03-19 15:16:30',NULL,1,1080,1440),(7,4,'笔记本弄你哦OK明敏','卡的够他按下去头都','a:3:{i:0;s:56:\"static/uploads/note/2019-03-20/155304820677221000268.jpg\";i:1;s:56:\"static/uploads/note/2019-03-20/155304821073868400673.jpg\";i:2;s:56:\"static/uploads/note/2019-03-20/155304821420004300643.jpg\";}',1,21,1,2,NULL,'2019-03-20 10:17:05',NULL,1,640,800),(8,3,'这么多年','jlkj','a:1:{i:0;s:56:\"static/uploads/note/2019-03-21/155314745387390000823.jpg\";}',1,4,1,0,'上不了台面','2019-03-21 13:51:06',NULL,1,1080,1440),(9,2,'两张图的阿斯蒂芬','啥发生的方式发生大法师暗室逢灯','a:3:{i:0;s:56:\"static/uploads/note/2019-03-25/155349942189241800462.png\";i:1;s:56:\"static/uploads/note/2019-03-25/155349942744689500420.png\";i:2;s:56:\"static/uploads/note/2019-03-25/155350003799931300460.png\";}',1,87,0,1,NULL,'2019-03-25 15:37:19',NULL,1,40,40),(10,2,'两张图的1','啥发生的方式发生大法师2','a:4:{i:0;s:56:\"static/uploads/note/2019-03-25/155349942189241800462.png\";i:1;s:56:\"static/uploads/note/2019-03-25/155349942744689500420.png\";i:2;s:56:\"static/uploads/note/2019-03-25/155349982816741400215.png\";i:3;s:56:\"static/uploads/note/2019-03-25/155349983156071000434.png\";}',1,45,0,0,NULL,'2019-03-25 15:43:55',NULL,1,40,40),(11,3,'我','我','a:1:{i:0;s:56:\"static/uploads/note/2019-03-26/155359447727412100936.jpg\";}',1,87,1,1,NULL,'2019-03-26 18:01:24',NULL,1,1080,1440),(12,8,'真棒','当上了设计师，真棒','a:2:{i:0;s:56:\"static/uploads/note/2019-03-27/155367418176587700837.jpg\";i:1;s:56:\"static/uploads/note/2019-03-27/155367420033003600669.jpg\";}',1,13,1,0,NULL,'2019-03-27 16:10:08',NULL,1,500,500),(13,8,'1','1','a:1:{i:0;s:56:\"static/uploads/note/2019-03-27/155367462896619900178.gif\";}',1,8,1,2,NULL,'2019-03-27 16:17:24',NULL,1,60,60),(14,8,'大英博物馆','大英博物馆中的中国文物。','a:6:{i:0;s:56:\"static/uploads/note/2019-03-27/155369390635757500583.jpg\";i:1;s:56:\"static/uploads/note/2019-03-27/155369392147455700396.jpg\";i:2;s:56:\"static/uploads/note/2019-03-27/155369394711554600750.jpg\";i:3;s:56:\"static/uploads/note/2019-03-27/155369397541378800561.jpg\";i:4;s:56:\"static/uploads/note/2019-03-27/155369400075404300674.jpg\";i:5;s:56:\"static/uploads/note/2019-03-27/155369401465836200690.jpg\";}',1,45,1,1,NULL,'2019-03-27 21:40:51',NULL,0,1080,1440),(15,9,'产品','产品','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155373481105598400819.jpg\";}',1,2,1,0,NULL,'2019-03-28 09:00:25',NULL,0,700,466),(16,9,'产品','产品','a:1:{i:0;s:57:\"static/uploads/note/2019-03-28/155373486538846700206.jpeg\";}',1,5,1,0,NULL,'2019-03-28 09:01:13',NULL,0,800,545),(17,9,'产品设计','产品设计','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155373489728449700824.jpg\";}',1,20,1,0,NULL,'2019-03-28 09:01:52',NULL,0,750,437),(18,8,'嘻嘻','感觉有点意思','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155373490771887900517.jpg\";}',1,48,1,0,NULL,'2019-03-28 09:02:03',NULL,1,720,720),(19,9,'产品','产品','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155373493854375500997.jpg\";}',1,35,1,0,NULL,'2019-03-28 09:02:24',NULL,0,640,896),(20,11,'茶之道','喝茶','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155373514449062800210.jpg\";}',1,85,1,0,NULL,'2019-03-28 09:06:05',NULL,1,658,840),(21,11,'美景','美景','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155373549588663200246.jpg\";}',1,14,1,0,NULL,'2019-03-28 09:11:43',NULL,1,781,448),(22,8,'开车了','走起，有人上车吗？','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155373850392360100181.jpg\";}',1,22,0,0,NULL,'2019-03-28 10:01:55',NULL,1,198,191),(23,8,'嘻嘻','摩托车也是车','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155373856393637800549.jpg\";}',1,78,1,0,NULL,'2019-03-28 10:02:52',NULL,1,715,683),(24,9,'结婚照','结婚照','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155373857432335300731.jpg\";}',1,45,1,1,NULL,'2019-03-28 10:03:07',NULL,1,960,639),(25,8,'有这个男生的联系方式吗？','好帅，爱上了','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155373905306206800721.jpg\";}',1,12,1,0,NULL,'2019-03-28 10:11:13',NULL,1,1080,1440),(26,9,'?','??','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155373926910392800845.jpg\";}',1,11,1,0,NULL,'2019-03-28 10:14:46',NULL,1,750,750),(27,15,'老娘就是Fashion','今天去三亚浪去✈️，?','a:4:{i:0;s:56:\"static/uploads/note/2019-03-28/155373924941579000230.jpg\";i:1;s:57:\"static/uploads/note/2019-03-28/155373925746940600936.jpeg\";i:2;s:56:\"static/uploads/note/2019-03-28/155373926151870600468.jpg\";i:3;s:56:\"static/uploads/note/2019-03-28/155373926526987400496.jpg\";}',1,4,1,0,NULL,'2019-03-28 10:15:10',NULL,1,1080,1350),(28,8,'我的挚爱','喜欢','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155373935590751700550.jpg\";}',1,43,1,0,NULL,'2019-03-28 10:16:20',NULL,1,1080,1080),(29,2,'发个试试','他拒绝啦咯','a:6:{i:0;s:56:\"static/uploads/note/2019-03-28/155375589065095500282.jpg\";i:1;s:56:\"static/uploads/note/2019-03-28/155375589088888100562.jpg\";i:2;s:56:\"static/uploads/note/2019-03-28/155375593634531000226.jpg\";i:3;s:56:\"static/uploads/note/2019-03-28/155375616818939500910.jpg\";i:4;s:56:\"static/uploads/note/2019-03-28/155375616847195800269.jpg\";i:5;s:56:\"static/uploads/note/2019-03-28/155375616864700300909.jpg\";}',0,45,1,0,NULL,'2019-03-28 14:51:33',NULL,1,1080,1440),(30,2,'啊啊啊','啦咯啦咯啦咯啦','a:2:{i:0;s:56:\"static/uploads/note/2019-03-28/155375618218303500301.jpg\";i:1;s:56:\"static/uploads/note/2019-03-28/155375618250607900384.jpg\";}',1,51,1,0,NULL,'2019-03-28 14:56:28',NULL,1,1080,1920),(31,9,'我的挚爱','我的挚爱','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376011443105800134.jpg\";}',1,32,1,0,NULL,'2019-03-28 16:02:03',NULL,1,640,641),(32,8,'创意海报设计','给大家分享一些创意海报设计，很有感觉，色彩搭配和谐有趣','a:3:{i:0;s:56:\"static/uploads/note/2019-03-28/155376481921328200476.jpg\";i:1;s:56:\"static/uploads/note/2019-03-28/155376482219921100140.jpg\";i:2;s:56:\"static/uploads/note/2019-03-28/155376482744500100776.jpg\";}',1,9,1,0,NULL,'2019-03-28 17:21:38',NULL,0,564,805),(33,8,'创意海报设计','给大家提供一些有趣的海报设计。','a:3:{i:0;s:56:\"static/uploads/note/2019-03-28/155376494559854000271.jpg\";i:1;s:56:\"static/uploads/note/2019-03-28/155376494965547700155.jpg\";i:2;s:56:\"static/uploads/note/2019-03-28/155376495486734900968.jpg\";}',1,71,1,0,NULL,'2019-03-28 17:24:26',NULL,0,564,798),(34,16,'徐渭人物关系图','画的很开心','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376632178329800763.jpg\";}',1,7,1,0,NULL,'2019-03-28 17:46:20',NULL,0,1080,1080),(35,16,'吉祥物','以小孩子的角度去设计','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376639402901600400.jpg\";}',1,2,1,1,NULL,'2019-03-28 17:46:56',NULL,0,1080,1528),(36,5,'配色:高级灰配色 找到属于你的莫兰迪','高级灰配色 找到属于你的莫兰迪','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376642637766700416.jpg\";}',0,14,1,0,NULL,'2019-03-28 17:47:59',NULL,0,690,502),(37,16,'插画','圣诞节小姐姐','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376646597532700945.jpg\";}',1,97,1,1,NULL,'2019-03-28 17:48:05',NULL,0,1080,1920),(38,16,'永生花','永恒的纪念','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376651031726700151.jpg\";}',1,2,1,0,NULL,'2019-03-28 17:48:42',NULL,0,1080,1440),(39,16,'场景','喜欢指环王','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376654124174500248.jpg\";}',1,145,1,0,NULL,'2019-03-28 17:49:13',NULL,0,1651,1079),(40,14,'博物馆','最帅博物馆','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376659309137600300.jpg\";}',1,121,1,0,NULL,'2019-03-28 17:50:12',NULL,0,474,315),(41,5,'神秘紫配色','静谧而安静的尊贵色','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376654805148700185.jpg\";}',1,151,1,0,NULL,'2019-03-28 17:50:26',NULL,0,690,500),(42,14,'博物馆','精致的作品','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376662976263300402.jpg\";}',1,21,1,2,NULL,'2019-03-28 17:50:54',NULL,0,474,307),(43,14,'博物馆','寂静的','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376666846588400428.jpg\";}',1,114,1,0,NULL,'2019-03-28 17:51:41',NULL,0,499,499),(44,14,'博物馆','寂静的夜晚','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376671366874700504.jpg\";}',1,74,1,1,NULL,'2019-03-28 17:52:16',NULL,0,960,529),(45,5,'春游季','?','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376668451529700520.jpg\";}',1,72,1,0,NULL,'2019-03-28 17:52:16',NULL,0,800,1096),(46,14,'博物馆','设计图','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376676474671100194.jpg\";}',1,30,1,0,NULL,'2019-03-28 17:53:04',NULL,0,474,309),(47,5,'情绪色','?','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376682724152300631.jpg\";}',1,15,1,0,NULL,'2019-03-28 17:53:49',NULL,0,1440,1080),(48,17,'系列餐具','设计了系列简洁的餐具，','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376690967171800810.jpg\";}',1,13,1,0,NULL,'2019-03-28 17:55:49',NULL,0,1280,720),(49,17,'系列设计产品','用一系列文案设计了系列产品','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376695701577600739.jpg\";}',1,2,1,0,NULL,'2019-03-28 17:56:36',NULL,0,854,640),(50,17,'系列文具','设计系列文具用品','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376702120896000500.jpg\";}',1,9,1,0,NULL,'2019-03-28 17:57:26',NULL,0,891,640),(51,17,'年年有鱼','以年年有余的元素设计了系列产品','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376705553805900256.jpg\";}',1,96,1,0,NULL,'2019-03-28 17:57:57',NULL,1,853,640),(52,5,'碎','?','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376706754567600608.jpg\";}',1,37,1,1,NULL,'2019-03-28 17:58:11',NULL,0,900,1034),(53,8,'电影海报','给大家分享一组创意海报，真的很有创意！','a:9:{i:0;s:56:\"static/uploads/note/2019-03-28/155376750142127900985.jpg\";i:1;s:56:\"static/uploads/note/2019-03-28/155376750154945500932.jpg\";i:2;s:56:\"static/uploads/note/2019-03-28/155376750168194100615.jpg\";i:3;s:56:\"static/uploads/note/2019-03-28/155376750187584300847.jpg\";i:4;s:56:\"static/uploads/note/2019-03-28/155376750201994400943.jpg\";i:5;s:56:\"static/uploads/note/2019-03-28/155376750212749900454.jpg\";i:6;s:56:\"static/uploads/note/2019-03-28/155376750223966500724.jpg\";i:7;s:56:\"static/uploads/note/2019-03-28/155376750236723700854.jpg\";i:8;s:56:\"static/uploads/note/2019-03-28/155376750246280300714.jpg\";}',1,7,1,1,NULL,'2019-03-28 18:05:27',NULL,0,564,795),(54,4,'好的东西不用说','这些东西是真的好啊，艺术感十足','a:4:{i:0;s:56:\"static/uploads/note/2019-03-28/155376766488829200282.jpg\";i:1;s:56:\"static/uploads/note/2019-03-28/155376766497562700978.jpg\";i:2;s:56:\"static/uploads/note/2019-03-28/155376766525721800269.jpg\";i:3;s:56:\"static/uploads/note/2019-03-28/155376766538837700218.jpg\";}',1,15,1,1,NULL,'2019-03-28 18:08:28',NULL,0,640,640),(55,9,'按压式垃圾桶','方便简洁的按压式垃圾桶','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376836782771500853.jpg\";}',1,5,1,0,NULL,'2019-03-28 18:19:45',NULL,0,650,459),(56,9,'记录本','斜挂式记录本！','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376840289351900855.jpg\";}',1,41,1,0,NULL,'2019-03-28 18:20:42',NULL,0,960,640),(57,9,'产品包装','简约大气的产品系列包装','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376858055768800850.jpg\";}',1,12,1,0,NULL,'2019-03-28 18:23:33',NULL,0,1024,726),(58,11,'陶瓷院博物馆','陶瓷院博物馆预计2019年4月份正式开馆，作为一座文物展示又具有古风气息的博物馆，它具有情怀，新奇，有趣的风格，欢迎届时大家前来参观','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376882047951300284.jpg\";}',1,24,1,0,NULL,'2019-03-28 18:27:02',NULL,0,1277,765),(59,9,'创意海报','创意立体海报','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376889622796400218.jpg\";}',1,14,1,1,NULL,'2019-03-28 18:28:29',NULL,0,564,753),(60,9,'卫浴用品设计','系列卫浴用品设计','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376893273166500295.jpg\";}',1,58,1,2,NULL,'2019-03-28 18:29:25',NULL,0,1080,753),(61,9,'C4D设计','创意C4D设计海报','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376901193285300501.jpg\";}',1,47,1,0,NULL,'2019-03-28 18:30:57',NULL,0,960,499),(62,9,'创意海报','创意海报设计','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376907084298600283.jpg\";}',1,65,1,1,NULL,'2019-03-28 18:31:25',NULL,0,600,819),(63,11,'旅行商品创新创意设计大赛','以上为旅行商品创新创意设计大赛的中奖作品','a:3:{i:0;s:56:\"static/uploads/note/2019-03-28/155376925945833200885.jpg\";i:1;s:56:\"static/uploads/note/2019-03-28/155376925954938500449.jpg\";i:2;s:56:\"static/uploads/note/2019-03-28/155376925971019100160.jpg\";}',1,4,1,0,NULL,'2019-03-28 18:35:19',NULL,0,1080,1527),(64,4,'好东西要分享','实用小玩意','a:4:{i:0;s:56:\"static/uploads/note/2019-03-28/155377117455747800298.jpg\";i:1;s:56:\"static/uploads/note/2019-03-28/155377117465482800391.jpg\";i:2;s:56:\"static/uploads/note/2019-03-28/155377117476705200681.jpg\";i:3;s:56:\"static/uploads/note/2019-03-28/155377117491587200812.jpg\";}',1,2,1,0,NULL,'2019-03-28 19:06:44',NULL,0,640,640),(65,4,'茶叶罐','茶叶保鲜看我家的','a:5:{i:0;s:56:\"static/uploads/note/2019-03-28/155377140898239200882.jpg\";i:1;s:56:\"static/uploads/note/2019-03-28/155377140908914600143.jpg\";i:2;s:56:\"static/uploads/note/2019-03-28/155377140925554700382.jpg\";i:3;s:56:\"static/uploads/note/2019-03-28/155377140938453200996.jpg\";i:4;s:56:\"static/uploads/note/2019-03-28/155377140950263000496.jpg\";}',1,3,1,2,NULL,'2019-03-28 19:11:10',NULL,0,640,640),(66,4,'文创小礼品','很有意思的小东西','a:3:{i:0;s:56:\"static/uploads/note/2019-03-28/155377191465241600594.jpg\";i:1;s:56:\"static/uploads/note/2019-03-28/155377191476464900865.jpg\";i:2;s:56:\"static/uploads/note/2019-03-28/155377191489112600822.jpg\";}',1,2,1,1,NULL,'2019-03-28 19:19:07',NULL,0,640,640),(67,8,'1225','涂卡','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155377254568958000360.jpg\";}',1,0,0,0,NULL,'2019-03-28 19:29:20',NULL,1,564,805),(68,8,'插花艺术','晚上起来给大家分享一组插花艺术','a:6:{i:0;s:56:\"static/uploads/note/2019-04-01/155407477080973300614.jpg\";i:1;s:56:\"static/uploads/note/2019-04-01/155407477088993200579.jpg\";i:2;s:56:\"static/uploads/note/2019-04-01/155407477096775900467.jpg\";i:3;s:56:\"static/uploads/note/2019-04-01/155407477136910400406.jpg\";i:4;s:56:\"static/uploads/note/2019-04-01/155407477144133700758.jpg\";i:5;s:56:\"static/uploads/note/2019-04-01/155407482160218500601.jpg\";}',1,6,1,3,NULL,'2019-04-01 07:27:03',NULL,0,500,705),(69,2,'发发','的点点滴滴','a:3:{i:0;s:56:\"static/uploads/note/2019-04-09/155477593452680800817.jpg\";i:1;s:56:\"static/uploads/note/2019-04-09/155477593473691400275.jpg\";i:2;s:56:\"static/uploads/note/2019-04-09/155477593493437200227.jpg\";}',1,0,0,0,NULL,'2019-04-09 10:12:23',NULL,0,204,115);

/*Table structure for table `mp_notify_log` */

DROP TABLE IF EXISTS `mp_notify_log`;

CREATE TABLE `mp_notify_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(50) DEFAULT NULL,
  `detail` text,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` tinyint(4) DEFAULT NULL COMMENT '1支付回调2充值回调3运费回调',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_notify_log` */

/*Table structure for table `mp_order` */

DROP TABLE IF EXISTS `mp_order`;

CREATE TABLE `mp_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `pay_order_sn` varchar(25) DEFAULT NULL COMMENT '订单号',
  `pay_price` decimal(10,2) DEFAULT NULL COMMENT '实际支付价格',
  `total_price` decimal(10,2) DEFAULT NULL COMMENT '总价',
  `carriage` decimal(10,2) DEFAULT NULL COMMENT '运费',
  `trans_id` varchar(30) DEFAULT NULL COMMENT '微信单号',
  `receiver` varchar(20) DEFAULT NULL COMMENT '收货人',
  `tel` varchar(15) DEFAULT NULL COMMENT '收货人手机号',
  `address` varchar(255) DEFAULT NULL COMMENT '收货地址',
  `refund_apply` tinyint(4) DEFAULT '0' COMMENT '1申请退款2已退款3拒绝退款',
  `reason` varchar(50) DEFAULT NULL COMMENT '退款理由',
  `status` tinyint(4) DEFAULT '0' COMMENT '0待付款1待发货2待收货3已完成',
  `send` tinyint(4) DEFAULT '0' COMMENT '0未发货1已发货',
  `create_time` int(11) DEFAULT NULL,
  `pay_time` int(11) DEFAULT NULL COMMENT '支付时间',
  `send_time` int(11) DEFAULT NULL COMMENT '发货时间',
  `finish_time` int(11) DEFAULT NULL COMMENT '完成(收货)时间',
  `refund_time` int(11) DEFAULT NULL COMMENT '退款时间',
  `tracking_name` varchar(10) DEFAULT NULL COMMENT '快递公司',
  `tracking_num` varchar(30) DEFAULT NULL COMMENT '快递单号',
  `del` tinyint(4) DEFAULT '0' COMMENT '1已删除',
  PRIMARY KEY (`id`),
  KEY `ORDER_UID` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_order` */

insert  into `mp_order`(`id`,`uid`,`pay_order_sn`,`pay_price`,`total_price`,`carriage`,`trans_id`,`receiver`,`tel`,`address`,`refund_apply`,`reason`,`status`,`send`,`create_time`,`pay_time`,`send_time`,`finish_time`,`refund_time`,`tracking_name`,`tracking_num`,`del`) values (1,1,'155453634607064600298','44600.00','44600.00','5000.00','4200000298201904084806774681','姜海蕤','13102163019','天津市西青区张家窝镇灵泉北里8号楼2501',2,'退款理由',1,0,1554536346,1554713065,NULL,NULL,1554792480,'1',NULL,0),(2,1,'155453643615065900625','960.00','960.00','0.00','4200000303201904084087667922','李钟毓','18526860284','天津市西青区中北镇华鼎智地2号楼613山海文化有限公司',2,'这是爱的奉献。',1,0,1554536436,1554712963,NULL,NULL,1554792472,'1',NULL,0),(3,1,'155453763465417300347','960.00','960.00','0.00','4200000289201904083500682524','李钟毓','18526860284','天津市西青区中北镇华鼎智地2号楼613山海文化有限公司',2,'Monaco',1,0,1554537634,1554712956,NULL,NULL,1554792468,'1',NULL,0),(4,1,'155453769186228200454','1220.00','1220.00','20.00','4200000296201904083532660028','王林林','18526860284','天津市西青区天津工业大学新校区东苑6号楼',2,'琥珀大扳指',1,0,1554537691,1554712942,NULL,NULL,1554792464,'1',NULL,0),(8,1,'155454434908579900170','2440.00','2440.00','40.00','4200000294201904080324980188','甘荣建','18529756789','天津市河西区土城地铁站附近',2,'退款理由',1,0,1554544349,1554712923,NULL,NULL,1554792456,'1',NULL,0),(9,1,'155454484383758500170','29600.00','29600.00','5000.00','4200000302201904085654209569','甘荣建','18529756789','天津市河西区土城地铁站附近',2,'退款理由',1,0,1554544843,1554712912,NULL,NULL,1554792448,'1',NULL,0),(10,1,'155454497154308800873','1220.00','1220.00','20.00','4200000303201904089909769584','王林林','18526860284','天津市西青区天津工业大学新校区东苑6号楼',2,'啦啦啦啦啦',1,0,1554544971,1554712932,NULL,NULL,1554792434,'1',NULL,0),(11,1,'155454504018816900353','960.00','960.00','0.00','4200000305201904084901734219','王林林','18526860284','天津市西青区天津工业大学新校区软件学院C-501',2,'monk看KKK',1,0,1554545040,1554712899,NULL,NULL,1554791667,'1',NULL,0),(12,1,'155459990148918100135','960.00','960.00','0.00','4200000304201904085857816658','姜海蕤','18526860284','天津市天津市西青区张家窝灵泉北里8号楼2门501',2,'噢噢噢哦哦',1,0,1554599901,1554712881,NULL,NULL,1554791658,'1',NULL,0),(13,2,'155460343679289100772','3319.00','3319.00','175.00','4200000296201904080282920088','张三','13114857103','广东省广州市海珠区新港中路397号',2,'噢噢噢哦哦',1,0,1554603436,1554715993,NULL,NULL,1554791602,'1',NULL,0),(14,2,'155460361890056000953','1291.00','1291.00','75.00','4200000300201904086988796991','张三','13114857103','广东省广州市海珠区新港中路397号',2,'太难吃了',1,0,1554603618,1554697108,NULL,NULL,1554714911,'1',NULL,0),(15,2,'155460375107482600130','1291.00','1291.00','75.00','4200000291201904072064973362','张三','13114857103','广东省广州市海珠区新港中路397号',0,NULL,3,0,1554603751,1554603765,1554714911,1554714911,NULL,'EMS','805054209040024373',0),(16,2,'155460380790991600482','19850.00','19850.00','50.00','4200000301201904076286163223','张三','13114857103','广东省广州市海珠区新港中路397号',0,NULL,3,0,1554603807,1554603818,1554714911,1554714911,NULL,'圆通','805054209040024373',0),(17,1,'155468624924073200204','48.00','48.00','40.00','4200000306201904081840762644','姜海蕤','13102163019','天津市天津市西青区张家窝灵泉北里8号楼2门501',2,'哦么days',1,0,1554686249,1554686254,NULL,NULL,1554714911,NULL,NULL,0),(18,1,'155468815011926000202','699.00','699.00','90.00','4200000297201904085816094968','姜海蕤','18526860284','天津市天津市西青区张家窝灵泉北里8号楼2门501',2,'退款理由',1,0,1554688150,1554688156,NULL,NULL,1554714911,NULL,NULL,0),(19,2,'155470103691409700396','20485.00','20485.00','85.00',NULL,'张三','13114857103','广东省广州市海珠区新港中路397号',0,NULL,0,0,1554701036,NULL,NULL,NULL,NULL,NULL,NULL,1),(20,1,'155471218807873700496','1949.00','1949.00','21.00','4200000308201904081256037112','姜海蕤','18526860284','天津市天津市西青区张家窝灵泉北里8号楼2门501',0,NULL,3,0,1554712188,1554712193,1554714911,1554714911,NULL,'顺丰','805054209040024373',0),(21,1,'155471248209172100948','1228.00','1228.00','0.00','4200000303201904080892819287','姜海蕤','18526860284','天津市天津市西青区张家窝灵泉北里8号楼2门501',2,'绣荷包',1,0,1554712482,1554712487,NULL,NULL,1554714911,NULL,NULL,0),(22,1,'155471252080140500798','1356.00','1356.00','0.00','4200000298201904081832703320','姜海蕤','13102163019','天津市天津市宝坻区霍各庄镇北王庄村',2,'噢噢噢哦哦',1,0,1554712520,1554712526,NULL,NULL,1554714911,NULL,NULL,0),(23,2,'155471288443281200641','1060.00','1060.00','100.00','4200000306201904087338068453','哈哈','13114857103','江苏省南通市海门市考虑考虑',0,NULL,3,0,1554712884,1554712889,1554714911,1554714911,NULL,'天天','805054209040024373',0),(24,2,'155471314660335300353','2314.00','2314.00','150.00','4200000289201904086262507465','哈哈','13114857103','江苏省南通市海门市考虑考虑',2,'辣鸡',2,0,1554713146,1554713156,1554714911,NULL,1554714911,'EMS','805054209040024373',0),(25,8,'155471467562673700281','19850.00','19850.00','50.00','4200000292201904084804433297','李钟毓','17631617680','山海文化',0,NULL,3,0,1554714675,1554714683,1554714911,1554714911,NULL,'顺丰','805054209040024373',0),(26,1,'155471470139319600208','21870.00','21870.00','150.00','4200000290201904087230632583','姜海蕤','18526860284','天津市天津市西青区张家窝灵泉北里8号楼2门501',0,NULL,3,0,1554714701,1554714706,1554714911,1554714911,NULL,'顺丰','805054209040024373',0),(27,8,'155471490603783600923','20613.00','20613.00','85.00','4200000298201904083519912355','李钟毓','17631617680','天津市天津市河东区山海里',2,'不买了',2,0,1554714906,1554714911,1554714911,NULL,1554714911,'圆通','805054209040024373',0);

/*Table structure for table `mp_order_detail` */

DROP TABLE IF EXISTS `mp_order_detail`;

CREATE TABLE `mp_order_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL COMMENT '商品id',
  `goods_name` varchar(50) DEFAULT NULL COMMENT '商品名',
  `total_price` decimal(10,2) DEFAULT '0.00' COMMENT '实际支付',
  `num` int(11) DEFAULT '1' COMMENT '数量',
  `unit_price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `carriage` decimal(10,2) DEFAULT '0.00' COMMENT '运费',
  `weight` double(10,2) DEFAULT '0.00' COMMENT '重量',
  `use_attr` tinyint(4) DEFAULT '0' COMMENT '0不使用1使用',
  `attr` varchar(255) DEFAULT '默认',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '下单时间',
  `evaluate` tinyint(4) DEFAULT '0' COMMENT '0待评论1已评论',
  `comment` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `DETAIL_ORDER_ID` (`order_id`),
  KEY `DETAIL_GOODS_ID` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_order_detail` */

insert  into `mp_order_detail`(`id`,`order_id`,`goods_id`,`goods_name`,`total_price`,`num`,`unit_price`,`carriage`,`weight`,`use_attr`,`attr`,`create_time`,`evaluate`,`comment`) values (1,1,2,'宋徽宗瘦金字体书法','44600.00',2,'19800.00','5000.00',0.00,0,'默认',1554537634,0,NULL),(2,2,1,'文创娃娃','960.00',1,'960.00','0.00',0.00,0,'默认',1554537634,0,NULL),(3,3,1,'文创娃娃','960.00',1,'960.00','0.00',0.00,0,'默认',1554537634,0,NULL),(4,4,3,'琥珀大班指','1220.00',2,'600.00','20.00',0.00,0,'默认',1554537691,0,NULL),(11,8,3,'琥珀大班指','1220.00',2,'600.00','20.00',0.00,0,'默认',1554544349,0,NULL),(12,8,3,'琥珀大班指','1220.00',2,'600.00','20.00',0.00,0,'默认',1554544349,0,NULL),(13,9,1,'文创娃娃','4800.00',5,'960.00','0.00',0.00,0,'默认',1554544843,0,NULL),(14,9,2,'宋徽宗瘦金字体书法','24800.00',1,'19800.00','5000.00',0.00,0,'默认',1554544843,0,NULL),(15,10,3,'琥珀大班指','1220.00',2,'600.00','20.00',0.00,0,'默认',1554544971,0,NULL),(16,11,1,'文创娃娃','960.00',1,'960.00','0.00',0.00,0,'默认',1554545040,0,NULL),(17,12,1,'文创娃娃','960.00',1,'960.00','0.00',0.00,0,'默认',1554599901,0,NULL),(18,13,1,'文创娃娃','2020.00',2,'960.00','100.00',0.00,0,'默认',1554603436,0,NULL),(19,13,4,'文创鸡贼杯','64.00',3,'8.00','40.00',0.00,0,'默认',1554603436,0,NULL),(20,13,3,'琥珀大班指','1235.00',2,'600.00','35.00',0.00,0,'默认',1554603436,0,NULL),(21,14,3,'琥珀大班指','1235.00',2,'600.00','35.00',0.00,0,'默认',1554603618,0,NULL),(22,14,4,'文创鸡贼杯','56.00',2,'8.00','40.00',0.00,0,'默认',1554603618,0,NULL),(23,15,3,'琥珀大班指','1235.00',2,'600.00','35.00',0.00,0,'默认',1554603751,0,NULL),(24,15,4,'文创鸡贼杯','56.00',2,'8.00','40.00',0.00,0,'默认',1554603751,0,NULL),(25,16,2,'宋徽宗瘦金字体书法','19850.00',1,'19800.00','50.00',0.00,0,'默认',1554603807,0,NULL),(26,17,4,'文创鸡贼杯','48.00',1,'8.00','40.00',0.00,0,'默认',1554686249,0,NULL),(27,18,3,'琥珀大班指','635.00',1,'600.00','35.00',0.00,0,'默认',1554688150,0,NULL),(28,18,4,'文创鸡贼杯','48.00',1,'8.00','40.00',0.00,0,'默认',1554688150,0,NULL),(29,18,5,'山海文创纸巾','16.00',1,'1.00','15.00',0.00,0,'默认',1554688150,0,NULL),(30,19,3,'琥珀大班指','635.00',1,'600.00','35.00',0.00,0,'默认',1554701036,0,NULL),(31,19,2,'宋徽宗瘦金字体书法','19850.00',1,'19800.00','50.00',0.00,0,'默认',1554701036,0,NULL),(32,20,9,'荆州博物馆 绣荷包','1650.00',3,'550.00','0.00',0.00,0,'默认',1554712188,0,NULL),(33,20,8,'荆州博物馆 拉画笔','26.00',2,'10.00','6.00',0.00,0,'默认',1554712188,0,NULL),(34,20,7,'“灯笼红火”小茶杯','256.00',2,'128.00','0.00',0.00,0,'默认',1554712188,0,NULL),(35,20,5,'山海文创纸巾','17.00',2,'1.00','15.00',0.00,0,'默认',1554712188,0,NULL),(36,21,7,'“灯笼红火”小茶杯','128.00',1,'128.00','0.00',0.00,0,'默认',1554712482,0,NULL),(37,21,9,'荆州博物馆 绣荷包','1100.00',2,'550.00','0.00',0.00,0,'默认',1554712482,0,NULL),(38,22,9,'荆州博物馆 绣荷包','1100.00',2,'550.00','0.00',0.00,0,'默认',1554712520,0,NULL),(39,22,7,'“灯笼红火”小茶杯','256.00',2,'128.00','0.00',0.00,0,'默认',1554712520,0,NULL),(40,23,1,'文创娃娃','1060.00',1,'960.00','100.00',0.00,0,'默认',1554712884,0,NULL),(41,24,5,'山海文创纸巾','19.00',4,'1.00','15.00',0.00,0,'默认',1554713146,0,NULL),(42,24,3,'琥珀大班指','1235.00',2,'600.00','35.00',0.00,0,'默认',1554713146,0,NULL),(43,24,1,'文创娃娃','1060.00',1,'960.00','100.00',0.00,0,'默认',1554713146,0,NULL),(44,25,2,'宋徽宗瘦金字体书法','19850.00',1,'19800.00','50.00',0.00,0,'默认',1554714675,0,NULL),(45,26,1,'文创娃娃','2020.00',2,'960.00','100.00',0.00,0,'默认',1554714701,0,NULL),(46,26,2,'宋徽宗瘦金字体书法','19850.00',1,'19800.00','50.00',0.00,0,'默认',1554714701,0,NULL),(47,27,2,'宋徽宗瘦金字体书法','19850.00',1,'19800.00','50.00',0.00,0,'默认',1554714906,0,NULL),(48,27,3,'琥珀大班指','635.00',1,'600.00','35.00',0.00,0,'默认',1554714906,0,NULL),(49,27,7,'“灯笼红火”小茶杯','128.00',1,'128.00','0.00',0.00,0,'默认',1554714906,0,NULL);

/*Table structure for table `mp_paylog` */

DROP TABLE IF EXISTS `mp_paylog`;

CREATE TABLE `mp_paylog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pay_order_sn` char(25) DEFAULT NULL COMMENT '支付订单号',
  `total_price` decimal(10,2) DEFAULT '0.00' COMMENT '支付金额',
  `order_sn` text COMMENT '订单流水号',
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '发起订单时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_paylog` */

/*Table structure for table `mp_req` */

DROP TABLE IF EXISTS `mp_req`;

CREATE TABLE `mp_req` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL COMMENT '活动图片',
  `cover` varchar(255) DEFAULT NULL,
  `title` varchar(30) DEFAULT NULL COMMENT '赛事名称',
  `org` varchar(20) DEFAULT NULL COMMENT '举办方单位',
  `explain` varchar(1000) DEFAULT NULL COMMENT '赛事说明',
  `theme` varchar(30) DEFAULT '不限' COMMENT '征集主题',
  `part_obj` varchar(30) NOT NULL DEFAULT '不限' COMMENT '参赛对象',
  `part_way` varchar(255) DEFAULT NULL COMMENT '参赛方式',
  `linkman` varchar(15) DEFAULT NULL COMMENT '联系人',
  `tel` varchar(15) DEFAULT NULL COMMENT '手机号',
  `weixin` varchar(50) DEFAULT NULL COMMENT '微信号',
  `recommend` tinyint(4) DEFAULT '0' COMMENT '0不推荐1推荐',
  `show` tinyint(4) DEFAULT '1' COMMENT '1显示0隐藏',
  `status` tinyint(4) DEFAULT '0' COMMENT '0审核中1通过2未通过',
  `part_num` int(11) DEFAULT '0',
  `reason` varchar(50) DEFAULT NULL COMMENT '拒绝理由',
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '活动起始时间',
  `deadline` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '截止投稿时间',
  `vote_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '截止投票时间',
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '活动结束时间',
  `del` tinyint(4) DEFAULT '0' COMMENT '0正常1已删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_req` */

insert  into `mp_req`(`id`,`uid`,`pic`,`cover`,`title`,`org`,`explain`,`theme`,`part_obj`,`part_way`,`linkman`,`tel`,`weixin`,`recommend`,`show`,`status`,`part_num`,`reason`,`start_time`,`deadline`,`vote_time`,`end_time`,`del`) values (1,2,'static/uploads/note/2019-03-15/155263910118162100607.png','static/uploads/note/2019-03-15/155263910118162100607.png','1','1','1','1','1','1','1','13114857103','1',0,1,1,0,NULL,'2019-04-03 16:09:35','2019-03-17 23:59:59','2019-03-16 23:59:59','2019-03-18 23:59:59',1),(2,2,'static/uploads/note/2019-03-15/155263920388232600735.jpg','static/uploads/note/2019-03-15/155263920388232600735.jpg','赛事','主板','塔','看看','看看','啊','看看','13114857103','Hgfgh',0,1,1,1,NULL,'2019-04-03 16:09:39','2019-11-28 23:59:59','2021-03-15 23:59:59','2025-01-01 23:59:59',1),(3,2,'static/uploads/note/2019-03-26/155358568065370500646.png','static/uploads/note/2019-03-26/155358568065370500646.png','再不对直播吃屎','了','居然涉及政治敏感11','了','看看','了','咯','13114857103','Ggbb ',0,1,0,0,'内容违规,涉及政治敏感信息','2019-04-03 16:09:41','2019-07-16 23:59:59','2020-03-15 23:59:59','2022-12-08 23:59:59',1),(4,2,'static/uploads/note/2019-03-15/155264002296966100526.jpg','static/uploads/note/2019-03-15/155264002296966100526.jpg','看看','里','他','8兔兔','看看','OK','兔兔','13114857103','掏空了快乐',0,1,1,0,'内容违规','2019-04-03 16:09:44','2019-01-13 23:59:59','2020-03-15 23:59:59','2024-03-22 23:59:59',1),(5,1,'static/uploads/note/2019-03-20/155305435504726500820.jpg','static/uploads/note/2019-03-20/155305435504726500820.jpg','傅里叶级数','法兰西学院','这是一个很严肃的比赛，楷模锁了咯啊洗涤剂flow咯咯咙\n\n? ☺ ? ? ? ? ? ? \n您摸烟雨楼台分析医院。！！\n哦咯咯OK呀咯惠敏\n2019年3月20日。卡路里吧啦','任意主题','社会精英、在校学生。','小程序内提交参赛作品及描述等待审核即可','姜海蕤','13102163019','Jianghairui',0,1,1,1,NULL,'2019-04-03 16:09:47','2019-03-25 23:59:59','2019-04-05 23:59:59','2019-04-20 23:59:59',1),(6,1,'static/uploads/note/2019-03-22/155323653585028500611.jpg','static/uploads/note/2019-03-22/155323653585028500611.jpg','大运河文创设计大赛河南设计工作坊-招募启动','河南博物馆、中国文字博物馆','本次河南工作坊是大运河文创设计大赛的重要组成部分。在参与本次工作坊的文博单位参赛素材中，将甄选出10件珍贵文物数字资源作为素材进行文化创意设计。此次工作坊邀请了文博、文创领域专家学者进行专业授课和经验分享。最终将设计作品中遴选出50%成系列的、有针对性的、可量化生产的设计产品方案，直接纳入“大运河文化带文化遗产创新创意设计大赛”的复赛，并向有关博物馆推荐。','大运河文化','工业设计、产品设计、艺术设计专业优先','活动开始后设计师即可在活动详情页内参赛','李钟毓','18526860284','jianghairui',0,1,1,0,NULL,'2019-04-03 16:09:50','2019-03-27 23:59:59','2019-04-06 23:59:59','2019-04-22 23:59:59',0),(7,4,'static/uploads/note/2019-03-22/155324042404655500304.jpg','static/uploads/note/2019-03-22/155324042404655500304.jpg','广西文创大赛','广西博物馆','赛事说明','春天的气息','学生狗','小程序主页活动模块参加','甘荣健','18526860284','grj',0,1,1,1,NULL,'2019-04-03 16:09:53','2019-03-24 23:59:59','2019-03-25 23:59:59','2019-03-31 23:59:59',1),(8,11,'static/uploads/req/2019-03-28/155376534517594700469.jpg','static/uploads/req/2019-03-28/155376534517594700469.jpg','献礼建国70周年，第二届“工博风尚”文创产品大赛','重庆工业博物馆','今年正值新中国建国70周年，也是重庆工业博物馆的开馆元年，“工博风尚”文创产品设计大赛将再次升级启动。第二届“工博风尚”文创产品设计大赛的主办单位为重庆工业博物馆，指导单位为重庆市经信委、大渡口区政府和重庆渝富集团。大赛主题将结合建国70周年作为献礼工程文化活动，征集方向以产品用途为标准设定为三类，包括适用于文旅渠道推售，能有效结合城市文化、工业文化、工业博物馆藏品元素和建国70周年纪念意义的文旅纪念品类文创产品；适用于工业行业、企业、机构，与工业博物馆联名设计的企业联名开发类文创礼品；以及结合藏品元素与青少年精工研学，开发适用于青少年进行动手制作、体验的社教类文创产品。\n\n此外本次文创产品设计大赛的奖品福利也更丰厚，在传播力度、联动平台、发展模式等维度都会进行一次全面的提档升级，期待全国乃至世界各地具有工业情怀和创造能力的个人、团队及机构来此一展才智风采。\n\n献礼新中国建国70周年，重庆工业博物馆2019年的开馆蓄势之举，用创意让百年工业历史重新焕发生机，用设计让城市工业文化拥抱当代生活','文创产品征集活动','全国文创设计师','公众号私信','重庆工业博物馆','15122079522','15122079521',0,1,0,0,NULL,'2019-04-03 16:09:57','2019-05-15 23:59:59','2019-03-15 23:59:59','2019-05-15 23:59:59',1),(9,11,'static/uploads/req/2019-03-28/155376571581335000234.jpg','static/uploads/req/2019-03-28/155376571581335000234.jpg','第三届山海文创设计大赛','山海文化有限公司','走进人们生活，是推动博物馆资源“活化”的重要方式，也是博物馆发挥社会教育职 为认真贯彻和落实习近平新时代中国特色社会主义思想和十九大精神，让收藏在博物馆里的文物、书写在古籍中的文字“活”起来，增强文化自信。让文物“走出”博物馆，随创意能的有效举措。\n本届大赛力求整合创新创造要素，以天津陶瓷院为核心，辐射带动天津博物馆文创的发展。通过比赛和品牌活动，发掘博物馆文创领域具有成长性的人才和项目，鼓励创新，努力营造范围更广、影响更大的博物馆文创发展氛围。','全国文创设计大赛','全国设计类大学生，设计爱好者以及大学设计类团队，组织，机构','山洞公众号私信投稿','山洞','15122079523','15122079523',1,1,1,0,NULL,'2019-03-28 17:50:03','2019-05-03 23:59:59','2019-05-06 23:59:59','2019-05-12 23:59:59',0),(10,11,'static/uploads/req/2019-03-28/155376616476487100522.jpg','static/uploads/req/2019-03-28/155376616476487100522.jpg','献礼建国70周年，第二届“工博风尚”文创产品大赛','重庆工业博物馆','今年正值新中国建国70周年，也是重庆工业博物馆的开馆元年，“工博风尚”文创产品设计大赛将再次升级启动。第二届“工博风尚”文创产品设计大赛的主办单位为重庆工业博物馆，指导单位为重庆市经信委、大渡口区政府和重庆渝富集团。大赛主题将结合建国70周年作为献礼工程文化活动，征集方向以产品用途为标准设定为三类，包括适用于文旅渠道推售，能有效结合城市文化、工业文化、工业博物馆藏品元素和建国70周年纪念意义的文旅纪念品类文创产品；适用于工业行业、企业、机构，与工业博物馆联名设计的企业联名开发类文创礼品；以及结合藏品元素与青少年精工研学，开发适用于青少年进行动手制作、体验的社教类文创产品。\n\n此外本次文创产品设计大赛的奖品福利也更丰厚，在传播力度、联动平台、发展模式等维度都会进行一次全面的提档升级，期待全国乃至世界各地具有工业情怀和创造能力的个人、团队及机构来此一展才智风采。\n\n献礼新中国建国70周年，重庆工业博物馆2019年的开馆蓄势之举，用创意让百年工业历史重新焕发生机，用设计让城市工业文化拥抱当代生活','全国文创产品设计','全国文创设计师，爱好者','公众号私信投稿','重庆工业博物馆','15122079522','15122079522',1,1,1,1,NULL,'2019-03-28 17:58:04','2019-04-10 23:59:59','2019-04-15 23:59:59','2019-05-15 23:59:59',0),(11,11,'static/uploads/req/2019-03-28/155376654662256300695.jpg','static/uploads/req/2019-03-28/155376654662256300695.jpg','2019第八届太湖奖金设计大赛','第十五届中国国际设计博览会','为深入贯彻国家创新驱动发展战略，服务高水平对外开放，实现经济高质量发展要求，进一步加快创意设计产业发展和传统产业转型升级，提升我国外观设计及商标保护环境，唤醒大众保护原创尊重设计的意识，特举办2019第八届太湖奖设计大赛。','全国文创产品设计','全国设计师','微信公众号投稿','中国国际设计博览会','15122079524','15122079524',1,1,1,0,NULL,'2019-03-28 18:09:12','2019-04-26 23:59:59','2019-04-26 23:59:59','2019-05-15 23:59:59',0),(12,11,'static/uploads/req/2019-03-28/155376748698268800468.jpg','static/uploads/req/2019-03-28/155376748698268800468.jpg','“紫金奖”文化创意大赛','省部门联合举办','本届大赛以贯彻落实中央《关于实施中华优秀传统文化传承发展工程的意见》和《“互联网+中华文明”三年行动计划》为契机，致力于聚集国内外优秀设计师资源，挖掘当下新生代原创力量，引导文化创意设计紧贴大众生活，将传统文化与现代科技、艺术价值与实用功能、创意设计与市场需求相结合，为百姓生活提供全方位的艺术化产品，构建以大赛为核心的文创产业链。','将文化元素与现代科技，艺术与市场需求相结合','全国文创艺术设计师，团队','网站投稿','“紫金奖”举办方','13672086889','13672086889',1,1,1,0,NULL,'2019-03-28 18:09:12','2019-04-30 23:59:59','2019-05-26 23:59:59','2019-06-26 23:59:59',0),(13,11,NULL,'static/uploads/req/2019-03-28/155376811053623500297.jpg','2019年中国文字博物馆文化创意产品设计大赛','中国文字博物馆','选取中国文字博物馆5件馆藏文物或以汉字的不同书体形式（甲骨文为主）为元素，进行设计创作，力求使汉字与生活、艺术与创意相融合，让汉字以更加贴近大众的方式进行推广，诠释中国故事。','优秀汉字文化创意设计作品','从事文化创意产品设计单位，个人，企业','登陆中国文字博物馆官方网站进行电子设计稿','中国文字博物馆','15222246226','2266051',0,1,1,0,NULL,'2019-03-28 18:19:15','2019-04-30 23:59:59','2019-05-24 23:59:59','2019-05-31 23:59:59',0);

/*Table structure for table `mp_role` */

DROP TABLE IF EXISTS `mp_role`;

CREATE TABLE `mp_role` (
  `uid` int(11) DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `role` tinyint(4) DEFAULT '0',
  `org` varchar(30) DEFAULT NULL COMMENT '组织机构名',
  `name` varchar(30) DEFAULT NULL COMMENT '姓名',
  `identity` varchar(20) DEFAULT NULL COMMENT '身份证号',
  `id_front` varchar(255) DEFAULT NULL COMMENT '身份证正面',
  `id_back` varchar(255) DEFAULT NULL COMMENT '身份证反面',
  `tel` varchar(15) DEFAULT NULL COMMENT '手机号',
  `weixin` varchar(50) DEFAULT NULL COMMENT '微信号',
  `works` varchar(2048) DEFAULT NULL COMMENT '作品6张',
  `license` varchar(255) DEFAULT NULL COMMENT '营业执照',
  `reason` varchar(50) DEFAULT '亲人两行泪' COMMENT '驳回原因',
  `desc` varchar(100) DEFAULT '暂无备注' COMMENT '备注',
  `fake` tinyint(4) DEFAULT '0',
  UNIQUE KEY `UID_UNIQUE` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_role` */

insert  into `mp_role`(`uid`,`cover`,`role`,`org`,`name`,`identity`,`id_front`,`id_back`,`tel`,`weixin`,`works`,`license`,`reason`,`desc`,`fake`) values (1,'static/uploads/role/2019-03-14/155255810514879100387.jpg',3,'山海书画展览馆','姜海蕤','120224199201080730','static/uploads/role/2019-03-14/155255810514879100387.jpg','static/uploads/role/2019-03-14/155255811060223700789.jpg','13102163019','Jianghairui1992','a:0:{}','static/uploads/role/2019-03-14/155255813683573300449.jpg','内容违规','主营产品： 碳带 不干胶标签 热敏标签纸 条码纸 标签纸 条码打印机 商标 标贴 纸制品 胶粘品',0),(4,'static/uploads/role/2019-03-22/155324024817051200740.jpg',2,'广西华源文创有限公司','甘荣健','120224199201080730','static/uploads/role/2019-03-22/155324024817051200739.jpg','static/uploads/role/2019-03-22/155324025789713900767.jpg','18526860284','gri','a:0:{}','static/uploads/role/2019-03-22/155324027683156500165.jpg','亲人两行泪','主营产品： 毛绒玩具 家居日用品',0),(8,'static/uploads/role/2019-03-27/155367322892264000941.jpg',3,'','李钟毓','130602199605150315','static/uploads/role/2019-03-27/155367324052129300953.jpg','static/uploads/role/2019-03-27/155367324686090700256.jpg','17631617680','','a:1:{i:0;s:56:\"static/uploads/note/2019-03-27/155367326314001100699.jpg\";}',NULL,'亲人两行泪','我是钟毓',0),(9,'static/uploads/role/2019-03-27/155367488447101200825.jpg',3,'','王林林','342201199602106485','static/uploads/role/2019-03-27/155367489791328000320.jpg','static/uploads/role/2019-03-27/155367490272196400549.jpg','18722369522','wll6485','a:1:{i:0;s:56:\"static/uploads/note/2019-03-27/155367492741237700223.jpg\";}',NULL,'亲人两行泪','',0),(11,'static/uploads/role/2019-03-28/155375992802433300134.jpg',1,'山海文化有限公司','杨金达','120111199702152512','static/uploads/role/2019-03-28/155373524723444700543.jpg','static/uploads/role/2019-03-28/155373525099819000305.jpg','15822245018','','a:0:{}','static/uploads/role/2019-03-28/155375993265417800951.jpg','亲人两行泪','',0),(14,'static/uploads/role/2019-03-28/155376600792455300422.jpg',1,'山海文化博物馆','刘连山','13028220010109452X','static/uploads/role/2019-03-28/155376604671268700785.jpg','static/uploads/role/2019-03-28/155376605459970300356.jpg','15175627933','ABVD','a:0:{}','static/uploads/role/2019-03-28/155376608169300700151.jpg','亲人两行泪','',0),(5,'static/uploads/role/2019-03-28/155376599805408300177.jpg',3,'','宋','130682199504151361','static/uploads/role/2019-03-28/155376604123344300464.jpg','static/uploads/role/2019-03-28/155376604617217900424.jpg','15620571517','','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376609222272900809.jpg\";}',NULL,'亲人两行泪','',0),(16,'static/uploads/role/2019-03-28/155376616056403700939.jpg',3,'','王宇','130206199112042344','static/uploads/role/2019-03-28/155376617841098100572.jpg','static/uploads/role/2019-03-28/155376618184173900523.jpg','18222663400','Wy18222663400','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376620132669800752.jpg\";}',NULL,'亲人两行泪','',0),(17,'static/uploads/role/2019-03-28/155376649301633600362.jpg',3,'','史惠敏','342201199602106485','static/uploads/role/2019-03-28/155376650055962300692.jpg','static/uploads/role/2019-03-28/155376650514595800815.jpg','13522224445','13522224445','a:1:{i:0;s:56:\"static/uploads/note/2019-03-28/155376655660641900286.jpg\";}',NULL,'亲人两行泪','',0),(3,'static/uploads/role/2019-03-28/155377200637641500882.jpg',2,'山海文化有限公司','刘连山','130626198609027636','static/uploads/role/2019-03-28/155377201697636100402.jpg','static/uploads/role/2019-03-28/155377202206115300693.jpg','18622388853','','a:0:{}','static/uploads/role/2019-03-28/155377203381110900404.jpg','亲人两行泪','',0),(2,'static/uploads/role/2019-03-29/155385128244459700797.jpg',4,'啥机构啊','啥法人啊','120103199208113817','static/uploads/role/2019-03-29/155385128759478500269.jpg','static/uploads/role/2019-03-29/155385129116252400253.jpg','13114857103','rfhjj','a:0:{}','static/uploads/role/2019-03-29/155385131548394600886.jpg','亲人两行泪','KKK哦哦哦121',0);

/*Table structure for table `mp_slideshow` */

DROP TABLE IF EXISTS `mp_slideshow`;

CREATE TABLE `mp_slideshow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `sort` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_slideshow` */

insert  into `mp_slideshow`(`id`,`title`,`pic`,`url`,`status`,`sort`) values (5,'文创会员大礼包','static/upload/2019-04-08/155471303860102200936.jpg','pages/recharge/recharge',1,3),(6,'山海文化山洞文创生态平台','static/upload/2019-04-08/155471299851547000460.jpg','http://tjtcy.cn/index.html',1,1),(7,'爱上山洞文创','static/upload/2019-04-08/155471302168325100501.jpg','pages/active/active',1,2);

/*Table structure for table `mp_syslog` */

DROP TABLE IF EXISTS `mp_syslog`;

CREATE TABLE `mp_syslog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `detail` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `ip` varchar(30) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL COMMENT '0登录1增2删3改',
  PRIMARY KEY (`id`),
  KEY `sys_admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_syslog` */

insert  into `mp_syslog`(`id`,`admin_id`,`detail`,`create_time`,`ip`,`type`) values (1,1,'登录账号',1552382115,'218.68.107.82',0),(2,1,'登录账号',1552441846,'218.68.107.82',0),(3,1,'登录账号',1552454243,'218.68.107.120',0),(4,1,'登录账号',1552454270,'218.68.107.120',0),(5,1,'登录账号',1552545349,'218.68.107.120',0),(6,1,'登录账号',1552620651,'218.68.107.120',0),(7,1,'登录账号',1552639942,'218.68.107.120',0),(8,1,'登录账号',1552702695,'221.196.194.249',0),(9,1,'登录账号',1552871358,'218.68.107.120',0),(10,1,'登录账号',1552884584,'218.68.107.120',0),(11,1,'登录账号',1552893614,'218.68.107.120',0),(12,1,'登录账号',1552893759,'218.68.107.120',0),(13,1,'登录账号',1552897025,'218.68.107.120',0),(14,1,'登录账号',1552901262,'218.68.107.120',0),(15,1,'登录账号',1552968062,'218.68.107.120',0),(16,1,'登录账号',1552972386,'218.68.107.120',0),(17,1,'登录账号',1552982203,'218.68.107.120',0),(18,1,'登录账号',1553051010,'218.68.107.120',0),(19,1,'登录账号',1553130315,'221.198.235.59',0),(20,1,'登录账号',1553218380,'221.198.235.59',0),(21,1,'登录账号',1553476024,'218.68.108.150',0),(22,1,'登录账号',1553562643,'218.68.108.150',0),(23,1,'登录账号',1553579204,'218.68.108.150',0),(24,1,'登录账号',1553655203,'218.68.108.150',0),(25,1,'登录账号',1553674125,'218.68.108.150',0),(26,1,'登录账号',1553683793,'218.68.108.150',0),(27,1,'登录账号',1553694221,'117.12.133.31',0),(28,1,'登录账号',1553734420,'218.68.108.150',0),(29,1,'登录账号',1553735398,'218.68.108.150',0),(30,5,'登录账号',1553738908,'218.68.108.150',0),(31,1,'登录账号',1553739008,'218.68.108.150',0),(32,1,'登录账号',1553760054,'218.68.108.150',0),(33,1,'登录账号',1553764091,'218.68.108.150',0),(34,1,'登录账号',1553764651,'218.68.108.150',0),(35,1,'登录账号',1553820824,'218.68.108.150',0),(36,1,'登录账号',1554038913,'60.25.12.177',0),(37,1,'登录账号',1554083502,'221.197.66.101',0),(38,1,'登录账号',1554090662,'221.197.66.101',0),(39,1,'登录账号',1554099997,'221.197.66.101',0),(40,1,'登录账号',1554109500,'221.197.66.101',0),(41,1,'登录账号',1554173465,'61.181.218.211',0),(42,1,'登录账号',1554184660,'218.68.105.30',0),(43,1,'登录账号',1554195016,'218.68.105.30',0),(44,1,'登录账号',1554254071,'218.68.105.30',0),(45,1,'登录账号',1554260640,'218.68.105.30',0),(46,1,'登录账号',1554276882,'218.68.105.30',0),(47,1,'登录账号',1554292449,'60.25.63.79',0),(48,1,'登录账号',1554340035,'218.68.105.30',0),(49,1,'登录账号',1554350169,'218.68.105.30',0),(50,1,'登录账号',1554359816,'218.68.105.30',0),(51,1,'登录账号',1554607551,'218.68.108.73',0),(52,1,'登录账号',1554617607,'218.68.108.73',0),(53,1,'登录账号',1554632898,'218.68.108.73',0),(54,1,'登录账号',1554701892,'218.68.108.73',0),(55,1,'登录账号',1554709778,'218.68.108.73',0),(56,1,'登录账号',1554714762,'218.68.108.73',0),(57,1,'登录账号',1554771720,'218.68.108.73',0),(58,1,'登录账号',1554772230,'218.68.108.73',0),(59,1,'登录账号',1554781152,'218.68.108.73',0);

/*Table structure for table `mp_token` */

DROP TABLE IF EXISTS `mp_token`;

CREATE TABLE `mp_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `end_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UID_INDEX` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_token` */

insert  into `mp_token`(`id`,`uid`,`token`,`value`,`end_time`) values (1,1,'6QjVls7vt7eEVQq60mWzYtV12zQ6EgEenhriQyviR2i0u99PWE4FBIVaSqoScuD3rhcvbL41ucwo38VkzdJXG4KaJJOCWyISWkMBLtjqTnAJiLz7TiHPLBPP8M6Q8VD98ckttejWCnzA8ZhqZphUaRzPcV8pQdXjSr9STZqj','a:2:{s:6:\"openid\";s:28:\"o2UbX5SVvuPYwfC47Ej-SedRDr-g\";s:11:\"session_key\";s:24:\"1msCTApj6JwvOvdObxSqog==\";}',1555393263),(3,2,'GYJdgCDgPYjw3a0LZewERysXryWvd4IKNc1sXosMqiPuBvAS9EAVxmwAGJGYZ6t2Yvl68RF7pCmAQiDqjaIPpc65VSUxiQsbrw4ASAfMDOuYezalZXGF6UbZpKK3V2MnG42TTzGxdnf5Y5SCcuTX8jQOvhdnrTELwfVfuURe','a:2:{s:6:\"openid\";s:28:\"o2UbX5d2Zz7wRFoRXAiEErs7cM4g\";s:11:\"session_key\";s:24:\"us1/U6iOT6Dsa+EmBewVUg==\";}',1555397710),(4,3,'zGTRdMQFHfviFTKZUbb4csKP3YiYBgZh2yvPxsik9aYkeGFXJ5LFFAyjZy3psh5KiA4nOSxoXTlAYKhIBSiwWBbBB3CZLMahCsujqLNqTxjhaffiQ7phRFJwRpdZl4uLJpBxGzk3xtbFpELJuEMO3tQKa4k9Bd1gpXLNPebu','a:2:{s:6:\"openid\";s:28:\"o2UbX5WgU1IIlEeLSLBlGAcThyDI\";s:11:\"session_key\";s:24:\"TurPMA9KvfpKZwF7Hd3bUg==\";}',1555394393),(5,4,'zM1AAnVLCUE5tznDKGI08lhMKJZAsHHUCWNGb1fnqShXl3aOWkmERgGiLuSCP3B2OoXA880LCKZR9DtQK3psQ66xgJwZkaqSEKyP5wtOAqM4aYzh0vELcvTFwMC4AxmzgYN5tRaLCbYBdSuOta17gPow8u1Oa3GZtJQSLaFn','a:2:{s:6:\"openid\";s:28:\"o2UbX5cWM1WMiD21KZaUlgwr5JEU\";s:11:\"session_key\";s:24:\"HEdYmDuSDYzC9NoseHvFwA==\";}',1555376579),(6,5,'ChKRexlwTy5g60WUBIgfIgx8D48mPxegBXsmYgHxaZZ9l6r9MWYZuZVRNHK6VtC2fqBzx6TkkJmDGthvNSqvPcuNlqVIFZxhNDx1i1dy3HuL2BJIXctir08GKi9qhzYHTCkybOxcWDuAJKZXo7nh1YAmVqeW4JIC5hAqBtRW','a:2:{s:6:\"openid\";s:28:\"o2UbX5fdm7fkMKt44Jo7Qc35xwNY\";s:11:\"session_key\";s:24:\"qAWhoMEU6cYzXcZBVZwRQg==\";}',1555393520),(7,6,'yo1bcnITcf2zfXyf5qQB7bzjhzn4ZEZ5xP1rjYUrONqbG5eloAchvbH3RMWNruKU3Rt72ciIEYMFgGJUzOPfpQVe9wGoisPqGG29So1hl4AbJrkCDmRgPtxLdioCrSWHtDXYNb5DB8XZzhkAad7Kq9TTcs3jA3nW7Ryz6Aud','a:2:{s:6:\"openid\";s:28:\"o2UbX5YGSpQmQWuPE676UfeTPAnI\";s:11:\"session_key\";s:24:\"5UWkdjrl0V2gog560SO6Fw==\";}',1555220872),(8,7,'0jpJtZTQiUfbTebcNAiKhz80ICyEDhnRt18B1yo15qTIo5m7AhDyu4Awe8z273QcdWvdQCNgfuqMMncyxlgCdAl4CcWC9ONAMxEqh2t7RWzf7GFfHo9tum0INBLgo2Je7Km5ScLoPIVkX0hO691Tnaa8TXCQUKfNpCBgrdnI','a:2:{s:6:\"openid\";s:28:\"o2UbX5U_QT88xIORf9G973GJz9QE\";s:11:\"session_key\";s:24:\"5dFNWeWUwsVq+z7AB6Qb4w==\";}',1554259902),(9,8,'3Zm7tnp4Q6LPJjHxg0gYffDAd5Y386GOf6CPp7jsOFvh3YnHhNNV6MvoGapsrVyMX4NofaiWCLYyg3sF6DnGqkGPkIqojyjcY1QCyFSaSp0HiSxYQQAMe324WDoRN1p358IQNEKmRgkE24ZedOk0YsOiygiD6KvGwOlbfVYT','a:2:{s:6:\"openid\";s:28:\"o2UbX5Ro7JQ7QcEkcFZay2y0yGMY\";s:11:\"session_key\";s:24:\"as2J5AeEMuQ0q/FJhZK1Jg==\";}',1555394432),(10,9,'LKJmRBlW2mKe3avwQItdB8md5Gj7KtA7tgdl47wC8WU264k3ITbbNxFl1U7gbCH79Z0Z0rhAhoPPccK2FZVjC9TJxx42r9ZeHncnQng6S5tE1h7bOoOmLdpFV6LBRHwsyajOkKiGov7A9jmHr3kA9NCYWCHAXjFMiUmz7rql','a:2:{s:6:\"openid\";s:28:\"o2UbX5ehrmWebyzGADeg7jkLWyGQ\";s:11:\"session_key\";s:24:\"v5bByMO8/XRLO3K1G2CY5w==\";}',1554715116),(11,10,'XATcgmK1RkjtMCWXlirUTDRbsovnunEw3EM9wpyU0SoxJKe6tcsyfBBAtwqOvInIIDExPkVLNB2njhqfA8z62VAzR3844xTL4ItXctbjmnlihUD3bIaklQY9C1C54WWkUpyPmAhgXvGuEmg7Vopc8XY8Baatw2zn41METqwu','a:2:{s:6:\"openid\";s:28:\"o2UbX5b-mfq3K4IxA_2nLfRWKdqU\";s:11:\"session_key\";s:24:\"hI9CCSyR9iT2cvbj32f2nw==\";}',1554375668),(12,11,'n3pThrO8LoEu463QVB2rspJLPh7PJ9UglMnZfHYYDPurwQSOY9pp9ujRcy71MgOism3FnYVeedud4G3t4r5txOpEKvKFOSWLpPbzVD9mUPUdtHtnasCaPfmHGhRJdIUqEABAsztxlHgmKVxpCcKjMbCfEL9RydmQhdMrEr9I','a:2:{s:6:\"openid\";s:28:\"o2UbX5eb9fWTYntxHMkMF5_txBIY\";s:11:\"session_key\";s:24:\"J05UooK5V+TIZir8iGA/UQ==\";}',1554377270),(13,15,'QyMYsEWKpViToFg0XwDmoAolNglzqoBrlV2A7ZTa94HAFma0mxKJwMoR6ow89tpobCOJkU577V8HbKWnTgH7vWODNgGdESft84KguSkk3BkLMuVyeieudYqM5bNGr46hOO3xxnBdPhVMAvSCEwKldLWX4m1hhdqC0lWnWXJR','a:2:{s:6:\"openid\";s:28:\"o2UbX5U_QT88xIORf9G973GJz9QE\";s:11:\"session_key\";s:24:\"QCQg9NcDYZ4pFQXNjuvnRg==\";}',1554371033),(14,14,'Jw9WGVdRcZlQ2seBCc6MamLUThev5WslP8SvuClS2eTe17zuLke6VF0rOGdAgWdTSwpfw66XyAcvsHQUzVAnGTJNP3Jk0RmqHSgh5iYYWvaU2wzQbrtkezu2tCffrmJdGVwAZtqnFzlAvTSkkEMqyoHDU0s7sYdSglRyxFsM','a:2:{s:6:\"openid\";s:28:\"o2UbX5Xdrfj9rye52qnpD78DRnOY\";s:11:\"session_key\";s:24:\"DJWqTqZAF4lj7pJv082znA==\";}',1555381471),(15,16,'fyoMXAo8qHwvfJO6Hdbb70uj6isePX0EQGjmnZu56jG0KIK9YrbMVFwt40VE9FUkakAAsmGu9VKSNMqe7crNKmGDq7qisDauXzjO9wC8QUld9KosKaflh1P5WFDQjTuq7tkp6ewsxxdMUwIdXJhm8qmpnpiwL9reXI3iwFZk','a:2:{s:6:\"openid\";s:28:\"o2UbX5bNQDS7LA5S9Q18_uSvCzSY\";s:11:\"session_key\";s:24:\"1mTSBxHM3PuQdsjarlLVVQ==\";}',1554989046),(16,17,'n4zexx6YGrVhtuWc6ucDj1Ud1sdNoIFrepARoRG7K8FnXy11umQSWiRBRXGE1S4VokJKQBMLI1AGM4EDmCJBmkLNMx8D8yNsDh6Avb1bvXqLX6rQlelEX5sIULv9ef5cs0IXsKC6j8GqX18ICjaWC0HPiG0mdWIQUXzzSb4H','a:2:{s:6:\"openid\";s:28:\"o2UbX5ex9vwbq0qxpJ8UkwWKkxNo\";s:11:\"session_key\";s:24:\"dAWTO9Mb2rUtT2rdS6mbWg==\";}',1555157625),(17,18,'N9zkJcNMLwDvHZDvw7sRfBGVBHaKtg2GkB2NaJGsi5PSmIZl7gNj6zy9EDAsTVfhJtZPDgDmbKe4NsRHZKtgonZQkVpBTEEXfBThjnXb6zA89vDMufNhHeyTd7s1WFGFgWsSTibRSElHFs8bOt7aCFcUc9XFnscTO9aWsgGz','a:2:{s:6:\"openid\";s:28:\"o2UbX5cW6bmJgjC_Mcg8fzJGhh5c\";s:11:\"session_key\";s:24:\"S0zhv5/PsPpQxZJUWmIpXA==\";}',1554377053),(18,19,'TGBiav0XMvRRosVPXYTVOWnvkGvj8m2pqF3fB5MnOJMFaesvRdHTVQigFZmJ1EJNhmw6cw5IZJ3RGtwcsqoH3LwItla8rrvongO0EHNnlXEectRHLUHtnYRECnp4oAPWP6uZDCtEyBEug0QEgiWam9vkfBTEfsI1KaT4Ebwg','a:2:{s:6:\"openid\";s:28:\"o2UbX5df4mIZMDLswkC3LLK3keTo\";s:11:\"session_key\";s:24:\"pRD1e+NiQgMPkdiPZ7CY5g==\";}',1554446258),(19,20,'NuAF5rqOoEZETkHavcgp5OmncAaRR9ys4rucn491mTdceZdklqcPSguITNohOWtIw7jT0zP3NgpmSr0cxvbBNdSvFAgqL3mpASt3EtQsa7Gw9hMEWoABVPF3pgwUdTSUOt4bsJpPF3xFjTG00wQcIrYvnRsDqeLVyaCucIX0','a:2:{s:6:\"openid\";s:28:\"o2UbX5XwZzaQ2wou2XIUkDCOkwJ8\";s:11:\"session_key\";s:24:\"87iolD4jhuarEbsU4q0hjQ==\";}',1554449063),(20,21,'COR4CpCqzXRdhpoK6oSYci6di5tnI41TEyoypBXtDrz4cEmcHlhOHYmdg4VvafwChktG46U7HYErG67ALBORRXkLlXaMFT5GAzaaBGk41rp90QrA5xg4WfCXKWiWHwZfRDg1LWLRxN9HcasSJjGw7plYLEVdqR9w8DW2JBjs','a:2:{s:6:\"openid\";s:28:\"o2UbX5XAWmhMHXg7pcUKh8_OynP8\";s:11:\"session_key\";s:24:\"IqqdGuq1ACA+HGXazbno+A==\";}',1554449583),(21,22,'mmq6crvPPlCV1xfcNvzRfZUY6LFaemWTDtQNDEYt5N3XEGG4YLtzoSmEvfCp0zd112AWBL5nKDTGmTfBgmNRTbTm2X6x5MKpjcPDdICjOnWdhRbyHZA8Z3ODPEcMEKdcwAHTZ7lu3Sn2cJbZ3nK0TWZJbsTQickjom5wOH9q','a:2:{s:6:\"openid\";s:28:\"o2UbX5R_eFBlzcvgU_Jfj-hjq2RA\";s:11:\"session_key\";s:24:\"XSWUnzqEo1WiOC6l9OT7ug==\";}',1555397537),(22,23,'PvmfqUM469c1IhVA0sYjrv3onmImqCgR6niWnNXBehvZqUu94uUIWQ8wXMwkvP5oLyy8pfpOFyxiqYRVUy14vkudzlbsdaTzwaJhBOOCcLRgp2hWQluxKt3Hk6QhltXemHXhTALa0VyaYEpekd5KGUIOO9E8jtGfT9CUuhON','a:2:{s:6:\"openid\";s:28:\"o2UbX5egV1O6Vf_0uduDCWpqv3xM\";s:11:\"session_key\";s:24:\"STf0BKp8zRnGQT5/9bJ0Sg==\";}',1554528111),(23,24,'5leKyrQ2XCgj99YmlbDxnGSKoimbUrfHE9UDR3IU2n6klWIctlzp356lLYL3BQ2bPKqmw9hsQlaaYucWZUaexhsM9IhOomJ1gtudTXasDuri1M9EJdldBML6qicr6NTmpelfhCiyERO9ANJnwrkzAus43YimIE6VrsvCpSha','a:2:{s:6:\"openid\";s:28:\"o2UbX5eICyLli0HZvisphhEzerQQ\";s:11:\"session_key\";s:24:\"vSqoFgPfoTKdQlG7eTZ8MA==\";}',1554978600),(24,25,'UewWmHDCymL9N6vb80gCOE1Wy0mCROMSO8mG78Bbkm6gX6sbtXGoIeC2UailLbwE9Slw74E1B0TtuRuHXNEBfEv7fKudRjFh4sk3LP5aFwdKc2eifOcs9ZrYPg1S9jcYfr2Y6oTNXVhYXHvcvGmfZeozDUmNga9yi1ICQ3Uv','a:2:{s:6:\"openid\";s:28:\"o2UbX5YS0xagB3rqT6l77JgNDF0Q\";s:11:\"session_key\";s:24:\"1Iy9X81xgOVWwbS8g2lwmg==\";}',1555156677),(25,26,'sujKABP1GrXniUWUHLef2SA2BpaAjOcd8PfTCX6ApIgsrgW6pl6g4nmvvLm3rE6Zi2H5gZVXAHJDQbSPUc3qTEtNeCzqcvjkRmVd9YjCzDgeHoOKkM4Bq1yDurXUP54o2MEeAKItXC3BABuL4yA4BhEGrjkIkMDTCyz7ooB3','a:2:{s:6:\"openid\";s:28:\"o2UbX5RJbSDGRnEVRCcIvZ8yi7Lo\";s:11:\"session_key\";s:24:\"wp9Df70SjQymNyATVgxcsA==\";}',1555169142),(26,27,'J2je3w1H1OAEaRlfycMlP2V2MYDGaEnW05Gmt1PkiIfEymPkzSygMmAYcLRQjra2uSQdvwNxFzoq5bpN3vQgvAgL8gpOtjVJojBD19Xhg2SZwoEgwwDwTyrunv3iPgbCW8EbX5JmJUb7fEPjznBB3V71YIFJCwIerZ9pG1A0','a:2:{s:6:\"openid\";s:28:\"o2UbX5WEecjtFHZYyPa4tEQA6MP0\";s:11:\"session_key\";s:24:\"O3JEtioPVOetYG7hpG2yrA==\";}',1555202355),(27,28,'985ghQjT1w4uuflaoP4qDLhDb9MNWBfHhEMxVwkrepZ9YnOgGdV7FOMCsLQDM6PFOb5xTkR6BrHHTOk2C2vzu8QBKuu08vB4Pzi2sgRMRvI86rwAL0LiDPWooAzXuWxJijpq9CcjQSfJeR5sDgGSp52qtNKZCPr3fUPS9THa','a:2:{s:6:\"openid\";s:28:\"o2UbX5R5jXONA9QVeL8T8BgQR-1k\";s:11:\"session_key\";s:24:\"puqFFKg3ItbvQssKjiYqzA==\";}',1555203331),(28,29,'5tqW0Nxw6vCjxQO8BcAAveQg3xAKZMFrtgyfk1g0ieqe15sLbUGcs8U4BVzufwF0INbKAUJvy1IUQfRTxSUnRPGt05PJszQVlBrclOKl9Wz2PN3iPLtJbqJepJzWvp0ncJoZVF79uHLDrZJPmE8J0ufmXD9XhyVjoWImPdd5','a:2:{s:6:\"openid\";s:28:\"o2UbX5dHChTybnmJQm2ykvzaI2Z0\";s:11:\"session_key\";s:24:\"Szf156P3BGu3k2AJ3oudYA==\";}',1555205848),(29,30,'Qt2WgDZLTf8xB5USgNWvHIHl9uf7FyOPaSBLdUTXoUTIScNlFOF07EV9k0MnBcz3llwKqdZB9bN53GwYBTcujZl8woP9XYuvp7TRcibVOkc3xXD43nO8RoitgiP0PHILJ3ibDRjEdbUBKDVVPr0V0QBFHBjlDeGvexAoqhav','a:2:{s:6:\"openid\";s:28:\"o2UbX5e1I0YDu7tK2cJ5mHKoTpS8\";s:11:\"session_key\";s:24:\"fP6eVO2Qzyd2Gc+mxrncWQ==\";}',1555209876),(30,31,'RTvvkozPwAm7DXZOKrXSN7utaEg5scrJC2QCnLJbjH3g7gKObEEnjtQQzVhKCLRdx5kOejRBM3rJIYzTmGTb7Rkov9qfXlWoJNvXzA894MKqukosyWCrNR5Xy23AEMTkjMZkb4Q4yicMISymcGZUjRlLVZAWAHRb75fV3J3a','a:2:{s:6:\"openid\";s:28:\"o2UbX5eeh6FhUukAH1d1x3Dpj5RU\";s:11:\"session_key\";s:24:\"6jimM9hbWDgpq60X56dZNA==\";}',1555213007),(31,32,'SI1SO4o3QAJRZkUvb7cNMG6FpppZVCvLb1gVyySvA3HPOSSEl6taA8Kgtpnbp3elzhuwAXINlVBEielsM44SgjhbcEhIQc8JLGJMyqbYEe5fUlmOmTO5YF0TcduYSWgX02uuzg3cWxUgMcTzLR05ovXMglQCP1Efn8LSKeBU','a:2:{s:6:\"openid\";s:28:\"o2UbX5a7DXSBX6Rd0Y30oboN05_c\";s:11:\"session_key\";s:24:\"LnI5e7nBGJQGoAFmzLQfvA==\";}',1555213452),(32,33,'xsACZJ4XKA3HNfw4b4QgbpllVFpCB8ABQYzxqxCpt4K86CW1TPUhzThQKKyvAbR7wEAtl8UwBotUt43B3amnWJgR2OJfsbdsRnvOCOkQw3xH1VomwLZGYWImHBluDLyypfJrfqtuXxZAUf6jWpfdZl5IkBLGUXxOQZGFzjQN','a:2:{s:6:\"openid\";s:28:\"o2UbX5cA7iQhiRem3jUfC7BbXeko\";s:11:\"session_key\";s:24:\"PgfMf+SXPwnNXuwMezt+vQ==\";}',1555220462),(33,34,'ArdK9079rS2Xou4DxZDvAQXxsWXSEjRxgcm1cfhatlUsk2dpKlVm7xxs00nyk3dRzK5CHgdAlIKAQaOat5BnGBsBgyKvd8WyOL6XavQOKlKIG4uZJX37SJaZETIe7vWYYYNvrEDcbOaznZcNrIoO1vlLxks17XBCWtTISBWc','a:2:{s:6:\"openid\";s:28:\"o2UbX5QzMjStqx38YhVlhSXqbwbU\";s:11:\"session_key\";s:24:\"HXhwg7WidTKfpQBgMK/h5w==\";}',1555224177),(34,35,'dCX3zkGIdQRJiaK8rCq7kt1nlntiUhksnrfKjG3A6j6FYXC1uxzXDWByRY1mCV1fJPjBlQCUjgPJiZaOv04bkel2aOI1YIoa3F8z9gAB2G2OqXpldhrdECRDvRVFOxg0kTF0gvlSglHCXK8LFyBfVVuEpNvWhxV038KeBrAd','a:2:{s:6:\"openid\";s:28:\"o2UbX5a9MU76h4SogOPDI8XP9P4w\";s:11:\"session_key\";s:24:\"eEGqEMLes5ea44oyaoUxFQ==\";}',1555238973),(35,36,'7pKqJ9Qpt439v9Myc9BdYsw2QY3T0YbYLUdeOirAJEwKnSKZlVO63ehFRuABWHTYSOyicxnFUZCTzYJs2xyoREstLJ0I9fLGRD0hIfXyKgkHhAsynSDohL602YKc88z9QHqanb9lHflZyaosRHmDLoUNfruQzMfsttWyBfom','a:2:{s:6:\"openid\";s:28:\"o2UbX5QyB74dTFJOd8rQkQF5AP2Y\";s:11:\"session_key\";s:24:\"rXFIp8V+wZjXFnFzH1XXOg==\";}',1555252792),(36,37,'rcXMNYYRnQiTEHdMu4Xt6GpyTw80Y0pRAMU7evvVBzSrhbl3spP3ctWZ5ftsRqJifz5nWpBRFeJdqDx3C2xAzOa8CP5Yy70H2WKVCPIYtkrzl3bPCSpA7rf26eZK5HKXsqMVYkfDOKCkTxXpcnUD0R9hqTbUZKA8wzE5BkMv','a:2:{s:6:\"openid\";s:28:\"o2UbX5RGwG4F2WLZYn9u2Wv_82kM\";s:11:\"session_key\";s:24:\"K/3z1e0A9rJtoWvsbqN9Pg==\";}',1555282424),(37,38,'YnVqrnLhW1hJ79Mekrx3yqgaEmpbsIdx6ruBMNkBZSXnMOkvtVwEVXf5BQ7JgMekMFax1tEySXJQwtlMxvGG8HNlXRqbEC8ANRvNQaLUQwvcFZiHkSlnBMT5iwjxDTkXj0q6eReQZTQ7Klx3iuMohcXhBwtP6pydQQvstXWE','a:2:{s:6:\"openid\";s:28:\"o2UbX5U3QIiWSViuHRdMEgF7Q73w\";s:11:\"session_key\";s:24:\"rEJ6AhGt1ZN/ioKwJxBeNg==\";}',1555294433),(38,39,'lRPvZUUEo9mXManTBaffnwYFHu9egwrXlrnbSn2NWauL3xQdZuvrc34kjphIUU2Wb2z2DurN2hT2oCeLqgCMk7SXUMs34bMeGNfISwxbfsvYkBNYpBvO41PWhIZFzY3o3TLLvLaGQUVFsaTf7elyUpMbEpSdFMKqB7bDTz6a','a:2:{s:6:\"openid\";s:28:\"o2UbX5S0X5UoouSdWjTG5i4niYrE\";s:11:\"session_key\";s:24:\"3sImby/4hMdezRSk9WVkjg==\";}',1555308250),(39,40,'2IxxirJRISaGatbcYj92dLYBay5dL5vrW6BeI8ijG67GzFyU8ZYRjce8Gz2U63MG0k7KAKqap8XfQQy1TrbpGWllG0rnZwfflNSI2w83zC5HLlifE2olKncbLHllGfc3JjBDZ2wyFvyNba5jGkpHAQHl9rJQ66jr3sE1Qfyq','a:2:{s:6:\"openid\";s:28:\"o2UbX5e-KGCHEXIbMTF1JIKAj6I8\";s:11:\"session_key\";s:24:\"rtmiIaYDfNah35PKDeluaw==\";}',1555320218),(40,41,'QNla4gU89w1nt2LUQvreoxm9XGG35RR7DFUmTLusnW8KUl7SUlHBAzlJDBs5jtyOOYQyvl8WEPXcnizG4XoXYj8H9Jsi12lCpTsentauHrn6iu17IkdLo3VsKvZGzC33RvizcK1rZlFdwf0FqqfA2p8EUy6imkkfiarJhdZM','a:2:{s:6:\"openid\";s:28:\"o2UbX5QnJh3imFItFFaoiUJJmQbI\";s:11:\"session_key\";s:24:\"ANhJd2vCo4r0RFZ3qJRiSQ==\";}',1555320512),(41,42,'3NjUHjZWajyJD10rhPC2C9kZkvAGtVdt3XOkvbFANmZJVYdzfUlELgQapb7EraSUWJfOjLg8Cy2Ah9usFeLE60dRQJuqnW6uNvFJY2h430LBmfobMI6TsCR1OGBi2L2Cpbcnu3zBhhkIoKI76dRoPpQjLcOFPZc7KsvIA1gW','a:2:{s:6:\"openid\";s:28:\"o2UbX5cIFWicBPcPuVB69BYDFa54\";s:11:\"session_key\";s:24:\"OUzVaL8f5Iu8ddGoCVxSBg==\";}',1555320906),(42,43,'QHZUZYaFYUf2BE7yMqVeSF0griecgDKUvuE28wIcJYGykoCCvCB7sBhyhh9ckhMdEnTDVwUe1yZnpszCzOmxA7h99y9NEBoWEXFbwjIFSTTQeFXekC1Q5vtDZgtjl5vfIH1iwYXVClEnBHOrkTTxDfURNIrlYMOjghZb1b8z','a:2:{s:6:\"openid\";s:28:\"o2UbX5aUl8_jfeTwre020Td7sV5M\";s:11:\"session_key\";s:24:\"RM+EibeeF1AR2vfY1Gy3Gg==\";}',1555321391),(43,44,'J10uFUXaXE3iubtMBf0cnm4890m0bq6ceBPxhGbICpwBwbh3PWZcnSVrcnc3BYJr3t5GxSrC0QWAegr4SibtJdTTRFA0jQCr3kCJxpqXUZbZDf3UFlEdvGHG9kW7Go9FtlykLHchtHgPpniKXYdoz821RIJrxvnbx3bWsmY9','a:2:{s:6:\"openid\";s:28:\"o2UbX5Ypy2gZQDOWbUUuVDMYxFBo\";s:11:\"session_key\";s:24:\"HSySW8/ej2OquienAfrSKw==\";}',1555322111),(44,45,'dOK1ZqvWvlUW9h4o2usQDMKTntiPfB1bpk0Slthsuh2GTcMc2qnvVZrtZEZiy0H8UyNVB2cDLjMYgeHpOO2fUIlcqHu62MZ56U5ublVfCVZvw2ZqkMDgIkGvemfzycS5kUqA3weDwVWzOvQxSbvpcaoZD3kVX4D96R7GOxqB','a:2:{s:6:\"openid\";s:28:\"o2UbX5Zg4DKaVq39hv56ajrl3arc\";s:11:\"session_key\";s:24:\"Vd0pM2y6Fy3KadHmx96NMg==\";}',1555333295),(45,46,'iAkR9BhXoQi3zvp5AdCDfsqfIQBoilmQbUQyV8nEbvBqPapm7eoUNoo9tBLX4nxhveSsZgEZiIPGLU0L2dwnwEVmdHUwew5tYUfVhNXAH0DjvtWeCnjSePl37MDZ7MUPQKVLsaQIgOOEG6dIPEzENJJJHIuuQYzHxhi33KXv','a:2:{s:6:\"openid\";s:28:\"o2UbX5VNfcj6W8_UU_oRRcuhmmvc\";s:11:\"session_key\";s:24:\"HspyIZRQPR/RRBb/i5UM7w==\";}',1555333355);

/*Table structure for table `mp_tracking` */

DROP TABLE IF EXISTS `mp_tracking`;

CREATE TABLE `mp_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) DEFAULT NULL,
  `del` tinyint(4) DEFAULT '0' COMMENT '1已删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_tracking` */

insert  into `mp_tracking`(`id`,`name`,`del`) values (1,'顺丰',0),(2,'申通',0),(3,'中通',0),(4,'圆通',0),(5,'EMS',0),(6,'天天',0),(7,'韵达',0),(8,'百世',0);

/*Table structure for table `mp_user` */

DROP TABLE IF EXISTS `mp_user`;

CREATE TABLE `mp_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realname` varchar(30) DEFAULT NULL COMMENT '实名',
  `balance` decimal(10,2) DEFAULT '0.00' COMMENT '账户余额',
  `sex` tinyint(4) DEFAULT '3' COMMENT '0保密1男2女',
  `age` tinyint(4) DEFAULT '0' COMMENT '年龄',
  `avatar` varchar(255) DEFAULT NULL COMMENT '微信头像',
  `tel` varchar(11) DEFAULT NULL COMMENT '手机号',
  `vip` tinyint(4) DEFAULT '0' COMMENT '1是0不是',
  `vip_time` int(11) DEFAULT NULL COMMENT '会员到期时间',
  `integral` int(11) DEFAULT '0' COMMENT '积分数量',
  `focus` int(11) DEFAULT '0' COMMENT '关注数',
  `status` tinyint(4) DEFAULT '1' COMMENT '1正常2拉黑',
  `auth` tinyint(4) DEFAULT '0' COMMENT '0未认证1审核中2已认证3审核未通过',
  `user_auth` tinyint(4) DEFAULT '0' COMMENT '0未授权1授权',
  `role` tinyint(4) DEFAULT '0' COMMENT '0普通用户1博物馆2文创机构3设计师4工厂',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '注册时间',
  `last_login_time` int(11) unsigned DEFAULT NULL COMMENT '最后登录时间',
  `openid` varchar(35) DEFAULT NULL COMMENT 'openID',
  `unionid` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'unionID',
  `org` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '组织机构名称',
  `desc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '暂无描述',
  `fake` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `openid` (`openid`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_user` */

insert  into `mp_user`(`id`,`nickname`,`realname`,`balance`,`sex`,`age`,`avatar`,`tel`,`vip`,`vip_time`,`integral`,`focus`,`status`,`auth`,`user_auth`,`role`,`create_time`,`last_login_time`,`openid`,`unionid`,`org`,`desc`,`fake`) values (1,'姜海蕤','姜海蕤','0.00',1,28,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKgdkxwSV2qTVwPQBHYsGfpB6gIStKSc5vaoiaNMcn1AymA57M5w6t1a7IuqEZ38KM21q7kIne46MA/132','13102163019',1,1565591632,0,0,1,2,1,1,1552286754,1554788463,'o2UbX5SVvuPYwfC47Ej-SedRDr-g','oNW9svwcXkD4z1BkVAbA8t6EPlHs','山海书画展览馆','我是个好人啊',0),(2,'环形山','www','0.00',1,52,'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83eqSIJibzjcxlUuahhmKCQJMB0icG66ia922MR7Bf4YkD4AU0H6zEK8FIsbdrJfGCAhv5g1CL70R5t06g/132','13114857103',1,1656487567,0,0,1,2,1,4,1552287625,1554792910,'o2UbX5d2Zz7wRFoRXAiEErs7cM4g','oNW9sv1717NPt-gp_i75pDdToq4k','啥机构啊','KKK哦哦哦121',0),(3,'刘连山',NULL,'0.00',1,0,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJyb4yl7JJCDCHWuNQcCCBLb1e9V8icq0scUdiaJohgPLY7HLz2R5fJc5N90VzoHy2Qef9UzMWev7SQ/132',NULL,1,1595587725,0,1,1,2,1,2,1552979743,1554789593,'o2UbX5WgU1IIlEeLSLBlGAcThyDI','oNW9sv98zp_oWqbBMfNKalwnQMrI','山海文化有限公司','',0),(4,'Kevin',NULL,'0.00',1,0,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTIxSsoFRspPAxia19AQqEauZuWJsnuhWdl7Q0kRSqSPRPR9AM0y31A3LZLsBBDsTic5WxmGHMoAFZiaw/132',NULL,0,NULL,0,2,1,2,1,2,1553048080,1554771779,'o2UbX5cWM1WMiD21KZaUlgwr5JEU','oNW9sv0viPXH4RJLikwU8riv59so','广西荣建文创有限公司','暂无描述',0),(5,'S?',NULL,'0.00',2,0,'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83eqXA5icpIAicicaqWfxBdibe3y2Fo5dmWl7HUeD8JFz81oPraUaDvz6RwPtYTWdPdTy5icALjzTWmeVREw/132',NULL,0,NULL,0,0,1,2,1,3,1553048853,1554788720,'o2UbX5fdm7fkMKt44Jo7Qc35xwNY','oNW9sv_zFUDFZB0W8dkf8qHdEKmc','','',0),(6,'施敏敏',NULL,'0.00',2,0,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKhVicETeQ2PLbuvvWvqicoqLegwKU4AMMNe8ogzyPYFhaqPIUCqicSDiaPU7sarbUce50fguV71ZytSg/132',NULL,0,NULL,0,0,1,0,1,0,1553233672,1554616072,'o2UbX5YGSpQmQWuPE676UfeTPAnI','oNW9sv3QIpyPpD1uGT4Ah06BjQDw',NULL,'暂无描述',0),(8,'想你想你想我',NULL,'0.00',1,0,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLwm6wEWOXcuNyic1wqDQ6zCwqhEujGriccYibu9BPpogTCopUP1k1LUziaibtgl5ozklQujQvZLpMR96w/132',NULL,0,NULL,0,4,1,2,1,3,1553590450,1554789632,'o2UbX5Ro7JQ7QcEkcFZay2y0yGMY','oNW9sv4A4o_WTbMZpQryhEN0IeAc','','我是钟毓',0),(9,'林林',NULL,'0.00',2,0,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKzFcichsmbeI4jqse6pUsG4UnTibfo5oF563jjmiaPH1nsHj1hhlzqgaHfjT6XicjOpia8AGXEa9Uicyicw/132',NULL,0,NULL,0,4,1,2,1,3,1553674669,1554110316,'o2UbX5ehrmWebyzGADeg7jkLWyGQ','oNW9sv_tqpPyq7uFi0iIFVvcLZ58','','暂无描述',0),(10,'亦朵',NULL,'0.00',0,0,'https://wx.qlogo.cn/mmopen/vi_32/7tIzxxRxMys6RjVAlMjKq7agpqnn7NFdNM3jMvtM3fmia0zLBSYvI8SjjbODH9SogBRK4WibkPZpAKv2Ej0kP1gQ/132',NULL,0,NULL,0,0,1,0,1,0,1553675927,1553770868,'o2UbX5b-mfq3K4IxA_2nLfRWKdqU','oNW9svwmcdDOaqe2F9r88oDnebCg',NULL,'暂无描述',0),(11,'?  Aloof',NULL,'0.00',1,0,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJI03LXToYPFQEt3IYiaDqyEEBKg5Luoxsvib5iaIwf4hRsiadBAfQ7UqfprTDnbWjlL6ziaWfic3gMHuicg/132',NULL,0,NULL,0,2,1,2,1,1,1553735041,1553772470,'o2UbX5eb9fWTYntxHMkMF5_txBIY','oNW9sv_6s6mnuIwqgG5D2N7G9g5E','山海文化有限公司','暂无描述',0),(14,'天空',NULL,'0.00',0,0,'https://wx.qlogo.cn/mmopen/vi_32/dJVeuF2AhabUicktj6L2g2jTa7macfMUEmFL1u9icAVG0HVMSzC9HAnSGw2r6Ab9rZibKAtZWH3JmVrOQ2Trko8kQ/132',NULL,0,NULL,0,1,1,2,1,1,1553737106,1554776671,'o2UbX5Xdrfj9rye52qnpD78DRnOY','oNW9svwvfD3HX77O1pMr6uH0xp0o','山海文化博物馆','',0),(15,'清流?',NULL,'0.00',1,0,'https://wx.qlogo.cn/mmopen/vi_32/iak5WicAM80nOYPicgDFDl1NqPc4E7qyuYyehNVSiaibMmnRBHq82Wsicg3thJywUdY4A1aqSQZQPW0ibibEIW3nkic4Xog/132',NULL,0,NULL,0,0,1,0,1,0,1553739059,1553766233,'o2UbX5U_QT88xIORf9G973GJz9QE','oNW9sv-HNtilC4ZeJ0dUaA7Dm_rU',NULL,'暂无描述',0),(16,'王宇Vicky',NULL,'0.00',2,0,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJjnVoyAicg2VZ0tjfibHGXF4ug5ibUHgiabJgJNo5ILqphJXhOmh1MViatZnGT2sRlQCQEMc6vTTwic7zw/132',NULL,0,NULL,0,0,1,2,1,3,1553766129,1554384246,'o2UbX5bNQDS7LA5S9Q18_uSvCzSY','oNW9sv3Qb4eycXGP4fl3SURBoakQ','','',0),(17,'?大白猫',NULL,'0.00',2,0,'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83epicnc7nb4o7ibNUKIo4AicNuI9SBiczZt63OThxp0MHDgloAQdhCDH7LnG6ibdehRdBLp7Eu7PlHMnwFQ/132',NULL,0,NULL,0,2,1,2,1,3,1553766387,1554552825,'o2UbX5ex9vwbq0qxpJ8UkwWKkxNo','oNW9sv55sQb79ywd1vWpxOa9_eOY','','',0),(18,'陆剑客','陆剑客','0.00',1,30,'static/uploads/role/2019-03-28/155377231475127700177.jpg','18178064862',0,NULL,0,0,1,0,1,0,1553772165,1553772253,'o2UbX5cW6bmJgjC_Mcg8fzJGhh5c','oNW9sv-yvV--XnuBDe9RW3h-64eY',NULL,'你好啊',0),(19,'吴雯芸',NULL,'0.00',0,0,'https://wx.qlogo.cn/mmhead/2kjSVsQiaxMz82wDu4tHJAAoP8HyB2H8Rn3Gom6BONyk/132',NULL,0,NULL,0,0,1,0,1,0,1553841447,1553841458,'o2UbX5df4mIZMDLswkC3LLK3keTo','oNW9sv37QEvtnyKRX69sQ0igJmQQ',NULL,'暂无描述',0),(20,'黄家弘',NULL,'0.00',0,0,'https://wx.qlogo.cn/mmhead/o7q1AsoC9SjOh2Ficeohsbkkl9sAicBXJercSFFW4d3ic0/132',NULL,0,NULL,0,0,1,0,1,0,1553844232,1553844263,'o2UbX5XwZzaQ2wou2XIUkDCOkwJ8','oNW9sv_8OYZkW03scBk667Wu76C0',NULL,'暂无描述',0),(21,'谢颖梅',NULL,'0.00',0,0,'https://wx.qlogo.cn/mmhead/MiavM5hIB4BagaXG1Oe56iasSrpZxgCFruag0IZ2546KI/132',NULL,0,NULL,0,0,1,0,1,0,1553844328,1553844783,'o2UbX5XAWmhMHXg7pcUKh8_OynP8','oNW9svyyD1Q1NXEdupHOm1Wg121g',NULL,'暂无描述',0),(22,'我都错了',NULL,'0.00',1,0,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTIkcKX6GI0wDayHTLnWbrNh4tkfQmcYgaRAicGb4p5c7iaKt8tyz6uXgT3wouicrTKN0terGGc8Xic1uw/132',NULL,0,NULL,0,0,1,0,1,0,1553855087,1554792737,'o2UbX5R_eFBlzcvgU_Jfj-hjq2RA','oNW9svzhQIzwTDUBhTBc0FdDJej4',NULL,'暂无描述',0),(23,'倪武盛',NULL,'0.00',0,0,'https://wx.qlogo.cn/mmhead/TkrVzEBBoTCWvJPEDLic3QiaVTet6ibN9Bvicw75UubrNew/132',NULL,0,NULL,0,0,1,0,1,0,1553923311,1553923311,'o2UbX5egV1O6Vf_0uduDCWpqv3xM','oNW9sv5yif0VRCkkpxm6hHVSBPM8',NULL,'暂无描述',0),(24,NULL,NULL,'0.00',3,0,NULL,NULL,0,NULL,0,0,1,0,0,0,1554169305,1554373800,'o2UbX5eICyLli0HZvisphhEzerQQ',NULL,NULL,'暂无描述',0),(25,NULL,NULL,'0.00',3,0,NULL,NULL,0,NULL,0,0,1,0,0,0,1554551877,1554551877,'o2UbX5YS0xagB3rqT6l77JgNDF0Q',NULL,NULL,'暂无描述',0),(26,'Redundant',NULL,'0.00',2,0,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJgib82ux83bK9GVcQ96NVrSBoXPZB9mlq7OOiasEiciaJU4ARaHpJjicjsSScLjQS2UYiceb4WfglgWfYA/132',NULL,0,NULL,0,0,1,0,1,0,1554564342,1554564342,'o2UbX5RJbSDGRnEVRCcIvZ8yi7Lo','oNW9sv7wKVjnxaULnNCy7b9VWYvA',NULL,'暂无描述',0),(27,'喜樂常安',NULL,'0.00',1,0,'https://wx.qlogo.cn/mmopen/vi_32/M7LbWicN18lCJtqIYqt3ACwH1HkAuoGhIAibsjGQgtAcXnibk1xyKwJDVulIkGaIV56hNcVmWXEg1bQEiadIP26P4A/132',NULL,0,NULL,0,0,1,0,1,0,1554597555,1554597555,'o2UbX5WEecjtFHZYyPa4tEQA6MP0','oNW9sv90NNiLurAg66tjvFqDG88g',NULL,'暂无描述',0),(28,'风絮',NULL,'0.00',1,0,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTI8S8c66ANMjbXIdO8FdP54l5BJndug0VRLWzwIibziaeoXibmjB9fSicwHnbUPdH5glFS0FMiakMZt5pg/132',NULL,0,NULL,0,0,1,0,1,0,1554598531,1554598531,'o2UbX5R5jXONA9QVeL8T8BgQR-1k','oNW9sv74OXaEGeWODxipz-1e_oi4',NULL,'暂无描述',0),(29,'超~*',NULL,'0.00',0,0,'https://wx.qlogo.cn/mmopen/vi_32/YbjIHvuZ0zY86XUrmXiagwpFqZlhwKtaX4D30tGH7SDiccqdTsdjP2kzeYI5NSwPZbhAuwicoorIrnvXAxwX8cLXQ/132',NULL,0,NULL,0,0,1,0,1,0,1554601048,1554601048,'o2UbX5dHChTybnmJQm2ykvzaI2Z0','oNW9svwAAf0M1W2jjF6a5AS-a7J8',NULL,'暂无描述',0),(30,'关路阳',NULL,'0.00',2,0,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKibEUVoa39YOQW3PTh5p5qa99u96JzF5U4wkOXfvKk5YrcWl4IZG1vy57mppSyAnSEFcSkricljBSw/132',NULL,0,NULL,0,0,1,0,1,0,1554605076,1554605076,'o2UbX5e1I0YDu7tK2cJ5mHKoTpS8','oNW9sv2-UcLQtSOZVERAwmSlb-pQ',NULL,'暂无描述',0),(31,'郭明瑀|居理新房14722010800',NULL,'0.00',1,0,'https://wx.qlogo.cn/mmopen/vi_32/umBbHGdic4jULReWWR5rJbEXCct5HriccL2ibvoaMCnYORUhAiavzacib47Y87dMGwrNO2KgQpOTNzNk2UqlUGF3M9A/132',NULL,0,NULL,0,0,1,0,1,0,1554608207,1554608207,'o2UbX5eeh6FhUukAH1d1x3Dpj5RU','oNW9sv6XmIYvBxVSLgScBhyTCCVM',NULL,'暂无描述',0),(32,NULL,NULL,'0.00',3,0,NULL,NULL,0,NULL,0,0,1,0,0,0,1554608651,1554608652,'o2UbX5a7DXSBX6Rd0Y30oboN05_c',NULL,NULL,'暂无描述',0),(33,NULL,NULL,'0.00',3,0,NULL,NULL,0,NULL,0,0,1,0,0,0,1554615661,1554615662,'o2UbX5cA7iQhiRem3jUfC7BbXeko',NULL,NULL,'暂无描述',0),(34,'叶婷玮',NULL,'0.00',0,0,'https://wx.qlogo.cn/mmhead/sfQ8CwibLSt2n2uN4M5icYEQfxx2T6cBia7P81jYzCcicYg/132',NULL,0,NULL,0,0,1,0,1,0,1554619375,1554619377,'o2UbX5QzMjStqx38YhVlhSXqbwbU','oNW9sv1IHfQk0Wt1VHBSDekx39mE',NULL,'暂无描述',0),(35,'A Lifetime',NULL,'0.00',1,0,'https://wx.qlogo.cn/mmopen/vi_32/8Bdw1UH4CkutTySfB9vQnic2QHicPhPdZxO2p5Ms5WH8wG2HKpC1bpSYUJyib0mumRukzYuKMcgUgnSiaPXR7rJYfg/132',NULL,0,NULL,0,0,1,0,1,0,1554634149,1554634173,'o2UbX5a9MU76h4SogOPDI8XP9P4w','oNW9sv13T0Dk1e5bKM4j4QvvO0jQ',NULL,'暂无描述',0),(36,'?',NULL,'0.00',2,0,'https://wx.qlogo.cn/mmopen/vi_32/qYqBvuCLwCniaNfg7NTk1VibkkiczVpdtQVia7XIpuiapAicX5Xaj4fs1x0rjLoySFFxrqn3LJ21J7ZV0BS3RejtwJBQ/132',NULL,0,NULL,0,0,1,0,1,0,1554647992,1554647992,'o2UbX5QyB74dTFJOd8rQkQF5AP2Y','oNW9sv1WL0nMcyn-jBU9zxvmkKs4',NULL,'暂无描述',0),(37,'该吃饭了！',NULL,'0.00',2,0,'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83eoUEZtvgjx3LkyqznYFDuRypU96S3obT8L1ibhVibnkwPwEicaUfynmcYaKGhsQnIM25vx3iautkUe4Zg/132',NULL,0,NULL,0,0,1,0,1,0,1554677624,1554677624,'o2UbX5RGwG4F2WLZYn9u2Wv_82kM','oNW9sv0TSaPOOnjc1T48Mfa6Mx28',NULL,'暂无描述',0),(38,'阿伯赐的2佛鸽',NULL,'0.00',1,0,'https://wx.qlogo.cn/mmopen/vi_32/CDMBAZuHJgD01ckHLichyxypreaBq3XcXRCE4nHH91R7jKicyK3muwUoUJ4czaibZd5WZj6NPUsE6G5iaAYpX4tJog/132',NULL,0,NULL,0,0,1,0,1,0,1554689633,1554689633,'o2UbX5U3QIiWSViuHRdMEgF7Q73w','oNW9sv8MMBCXNBNIl3K_XswqfS3k',NULL,'暂无描述',0),(39,NULL,NULL,'0.00',3,0,NULL,NULL,0,NULL,0,0,1,0,0,0,1554703450,1554703450,'o2UbX5S0X5UoouSdWjTG5i4niYrE',NULL,NULL,'暂无描述',0),(40,NULL,NULL,'0.00',3,0,NULL,NULL,0,NULL,0,0,1,0,0,0,1554715418,1554715418,'o2UbX5e-KGCHEXIbMTF1JIKAj6I8',NULL,NULL,'暂无描述',0),(41,'作死小能手',NULL,'0.00',1,0,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKslpcuibjVArjsffx983hMlsElFXxGdSicM70QJamceem9SrO0dHB0mWicy8KMOP9qm1yk1TyTibAqvg/132',NULL,0,NULL,0,0,1,0,1,0,1554715705,1554715712,'o2UbX5QnJh3imFItFFaoiUJJmQbI','oNW9sv3C3n2rb3LL7mgCa3NSgY6U',NULL,'暂无描述',0),(42,NULL,NULL,'0.00',3,0,NULL,NULL,0,NULL,0,0,1,0,0,0,1554716104,1554716106,'o2UbX5cIFWicBPcPuVB69BYDFa54',NULL,NULL,'暂无描述',0),(43,'宇',NULL,'0.00',1,0,'https://wx.qlogo.cn/mmopen/vi_32/kpQw1lHLxnwHA61PkShic5Z5ojHKvPicsiaHWOLViccbiaxM27tBfZHPJicxBbWpn0CwOWxEpgUH4ZtR45GSTaPMtQLQ/132',NULL,0,NULL,0,0,1,0,1,0,1554716588,1554716591,'o2UbX5aUl8_jfeTwre020Td7sV5M','oNW9sv7fnG7jYUyLC_Q3XJPiCiZk',NULL,'暂无描述',0),(44,'许湖娥',NULL,'0.00',0,0,'https://wx.qlogo.cn/mmhead/PIab3jtVuoWNia06QiaLqjE63F4xjibDanYvzmRGIRwibcM/132',NULL,0,NULL,0,0,1,0,1,0,1554716944,1554717311,'o2UbX5Ypy2gZQDOWbUUuVDMYxFBo','oNW9sv7Mb1sVcJR7-r4TW48_j8fk',NULL,'暂无描述',0),(45,'fantastic',NULL,'0.00',2,0,'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83erKrgV9BLfIEpbsfXNyknDGfW023icRH8AVmseWm4XoEZeCOszHyeb3N3mmjnO2RNUHQnBKxmS4ibqQ/132',NULL,0,NULL,0,0,1,0,1,0,1554728495,1554728495,'o2UbX5Zg4DKaVq39hv56ajrl3arc','oNW9sv1N3GumBf05ra5TDoh3GgnY',NULL,'暂无描述',0),(46,'名字不重要№',NULL,'0.00',1,0,'https://wx.qlogo.cn/mmopen/vi_32/v6Xb7GXuldM3qJ6uG8qc8PrOkhs0I7TunXWaXQW6HluQuXTOuKBZzYGUvgDoZ0qwUd4CkcmT2k8wCY9LVZgZEQ/132',NULL,0,NULL,0,0,1,0,1,0,1554728552,1554728555,'o2UbX5VNfcj6W8_UU_oRRcuhmmvc','oNW9sv3BmO_0HIMWdHFYpVxpJpo8',NULL,'暂无描述',0);

/*Table structure for table `mp_verify` */

DROP TABLE IF EXISTS `mp_verify`;

CREATE TABLE `mp_verify` (
  `tel` varchar(15) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_verify` */

insert  into `mp_verify`(`tel`,`code`,`create_time`) values ('13102163019','666666',1553360788),('13114857103','666666',1554843338),('18526860284','985125',1553580637),('17631617680','266853',1553673266),('18722369522','688632',1553674931),('15822245018','487518',1553759934),('15175627933','813401',1553766086),('15620571517','469690',1553766119),('18222663400','758061',1553766203),('13522224445','197196',1553766557),('18622388853','728876',1553772036),('13445879658','780561',1553851320);

/*Table structure for table `mp_vip` */

DROP TABLE IF EXISTS `mp_vip`;

CREATE TABLE `mp_vip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `detail` varchar(255) DEFAULT NULL COMMENT '描述',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `pic` varchar(255) DEFAULT NULL COMMENT '图片',
  `create_time` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '0不显示1显示',
  `days` int(11) DEFAULT NULL,
  `type` tinyint(4) DEFAULT '1' COMMENT '1邮寄礼品2无礼品',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_vip` */

insert  into `mp_vip`(`id`,`title`,`detail`,`price`,`pic`,`create_time`,`status`,`days`,`type`) values (1,'30天会员','获得价值88元山海文创日历一套','39.00','static/upload/2019-03-25/155349525793420600848.JPG',1539760256,1,30,1),(2,'年度会员','获得价值88元山海文创日历一套+7.5折购买文创孵化产品','199.00','static/upload/2019-03-25/155349515697611500936.JPG',1539760256,1,365,1),(3,'终身会员','获得价值88元山海文创日历一套+价值500元文创大礼包一套+6折购买文创孵化产品','399.00','static/upload/2019-03-25/155349519685841600874.JPG',1539760256,1,200000,1);

/*Table structure for table `mp_vip_order` */

DROP TABLE IF EXISTS `mp_vip_order`;

CREATE TABLE `mp_vip_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `order_sn` varchar(25) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `trans_id` varchar(30) DEFAULT NULL,
  `vip_id` int(11) DEFAULT NULL,
  `days` int(11) DEFAULT '0',
  `name` varchar(20) DEFAULT NULL,
  `tel` varchar(15) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL COMMENT '收货地址',
  `create_time` int(11) DEFAULT NULL,
  `pay_time` int(11) DEFAULT NULL,
  `tracking_no` varchar(20) DEFAULT NULL COMMENT '物流单号',
  `tracking_type` tinyint(4) DEFAULT '1' COMMENT '1顺丰2申通3中通4圆通5EMS6天天7韵达8百世9京东',
  `status` tinyint(4) DEFAULT '0' COMMENT '-1支付失败0待支付1已支付2已完成',
  `send` tinyint(4) DEFAULT '0' COMMENT '0未发货1已发货',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_vip_order` */

insert  into `mp_vip_order`(`id`,`uid`,`order_sn`,`price`,`trans_id`,`vip_id`,`days`,`name`,`tel`,`address`,`create_time`,`pay_time`,`tracking_no`,`tracking_type`,`status`,`send`) values (1,1,'v155262803772104700867','28.00',NULL,1,30,'姜海蕤','13102163019','天津市西青区张家窝镇灵泉北里8号楼2门501',1552628037,NULL,NULL,1,0,0),(2,1,'v155263048972240400335','28.00',NULL,1,30,'Jianghirui','13102163019','天津市西青区张家窝镇灵泉北里',1552630489,NULL,NULL,1,0,0),(3,1,'v155263059718551600600','28.00',NULL,1,30,'Jianghirui','13102163019','天津市西青区张家窝镇灵泉北里',1552630597,NULL,NULL,1,0,0),(4,1,'v155263087634749300722','28.00','4200000239201903154293486229',1,30,'Jianghirui','13102163019','天津市西青区张家窝镇灵泉北里',1552630876,1552630891,NULL,1,1,0),(5,1,'v155263161418027200890','28.00','4200000239201903155163455628',1,30,'Jianghirui','13102163019','天津市西青区张家窝镇灵泉北里',1552631614,1552631632,NULL,1,1,0),(6,1,'v155263167341883000488','64.00','4200000245201903159111914092',2,90,'甘荣建','18526860284','天津市西青区张家窝镇灵泉北里8号楼2门501',1552631673,1552631687,NULL,1,1,0),(7,2,'v155289376775258900730','28.00',NULL,1,30,'1','13114857103','广东省广州市海珠区新港中路397号',1552893767,NULL,NULL,1,0,0),(8,2,'v155289383652230000587','28.00',NULL,1,30,'憨吃','13114857103','广东省广州市海珠区新港中路397号',1552893836,NULL,NULL,1,0,0),(9,2,'v155289395592179900317','280.00',NULL,3,365,'憨吃','13114857103','1231232',1552893955,NULL,NULL,1,0,0),(10,2,'v155289395650544400436','280.00','4200000255201903183390464339',3,365,'憨吃','13114857103','1231232',1552893956,1552893967,NULL,1,1,0),(11,2,'v155289532180850900698','64.00','4200000245201903181528455290',2,312,'这是啥','13114857103','江苏省南通市海门市考虑考虑',1552895321,1552895326,NULL,1,1,0),(12,2,'v155289540735865400431','64.00','4200000254201903184282507792',2,312,'发发','13114857103','江苏省南通市海门市考虑考虑',1552895407,1552895411,NULL,1,1,0),(13,2,'v155289560876356100871','280.00',NULL,3,365,'傻鸟','13002255417','江苏省南通市海门市考虑考虑',1552895608,NULL,NULL,1,0,0),(14,2,'v155289564873520800181','280.00',NULL,3,365,'12','13114857103','发士大夫士大夫撒地方的',1552895648,NULL,NULL,1,0,0),(15,2,'v155289572087958500148','280.00',NULL,3,365,'1','13114857103','广东省广州市海珠区新港中路397号',1552895720,NULL,NULL,1,0,0),(16,2,'v155289589099209600157','64.00',NULL,2,90,'好','13114857103','江苏省南通市海门市考虑考虑',1552895890,NULL,NULL,1,0,0),(17,2,'v155289589425640700107','64.00','4200000249201903188344996733',2,90,'好','13114857103','江苏省南通市海门市考虑考虑',1552895894,1552895898,NULL,1,1,0),(18,1,'v155323308187033100408','28.00','4200000294201903224809481200',1,30,'姜海蕤','13102163019','天津市天津市宝坻区霍各庄镇北王庄村',1553233081,1553233088,NULL,1,1,0),(19,7,'v155365547013912300235','280.00','4200000291201903278945859657',3,365,'清流','18526860284','123456',1553655470,1553655476,NULL,1,1,0),(20,3,'v155368371937975900639','28.00','4200000290201903273570044429',1,30,'刘连山','18622388853','天津市天津市西青区中北镇中北大道万科假日风景花园62—2—203',1553683719,1553683725,NULL,1,1,0),(21,3,'v155368375583945100155','64.00','4200000289201903275366604704',2,90,'刘连山','18622388853','天津市天津市西青区中北镇中北大道万科假日风景花园62—2—203',1553683755,1553683761,NULL,1,1,0),(22,3,'v155368378755389600165','280.00','4200000290201903271438684046',3,365,'刘连山','18622388853','天津市天津市西青区中北镇中北大道万科假日风景花园62—2—203',1553683787,1553683791,NULL,1,1,0),(23,11,'v155373651341086200993','280.00',NULL,3,365,'杨金达','15822245018','天津市天津市西青区李七庄街育水佳苑D区2号楼',1553736513,NULL,NULL,1,0,0),(24,2,'v155375244263929200470','28.00','4200000293201903280543489714',1,30,'王八','13114857103','江苏省南通市海门市考虑考虑',1553752442,1553752447,NULL,1,1,0),(25,2,'v155421038662681700406','39.00','4200000307201904029158107678',1,30,'王八','13114857103','江苏省南通市海门市考虑考虑',1554210386,1554210391,NULL,1,1,0),(26,2,'v155421040923716900401','39.00','4200000290201904023458449412',1,30,'王八','13114857103','江苏省南通市海门市考虑考虑',1554210409,1554210413,NULL,1,1,0),(27,2,'v155421041542132200887','39.00',NULL,1,30,'王八','13114857103','江苏省南通市海门市考虑考虑',1554210415,NULL,NULL,1,0,0),(28,2,'v155421041577355700703','39.00','4200000305201904021754540286',1,30,'王八','13114857103','江苏省南通市海门市考虑考虑',1554210415,1554210420,NULL,1,1,0),(29,1,'v155427044190031700168','199.00',NULL,2,365,'甘荣建','18526860284','天津市西青区张家窝镇灵泉北里8号楼2门501',1554270441,NULL,NULL,1,0,0);

/*Table structure for table `mp_vote` */

DROP TABLE IF EXISTS `mp_vote`;

CREATE TABLE `mp_vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `vip` int(11) DEFAULT '0' COMMENT '会员票',
  `work_id` int(11) DEFAULT NULL,
  `req_id` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

/*Data for the table `mp_vote` */

insert  into `mp_vote`(`id`,`uid`,`vip`,`work_id`,`req_id`,`create_time`) values (1,1,1,3,7,1553243932),(2,2,1,1,2,1553250437),(3,2,1,2,5,1553250444),(4,3,0,3,7,1553398497),(5,3,0,1,2,1553398509),(6,3,0,2,5,1553398516),(7,2,1,3,7,1553480618),(8,3,1,14,10,1553767725),(9,46,0,14,10,1554728710),(10,1,1,14,10,1554779715);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
