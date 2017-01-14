/*
 Navicat MySQL Data Transfer

 Source Server         : conn
 Source Server Version : 50538
 Source Host           : localhost
 Source Database       : question

 Target Server Version : 50538
 File Encoding         : utf-8

 Date: 01/14/2017 19:40:05 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `huo15_QuestConfig`
-- ----------------------------
DROP TABLE IF EXISTS `huo15_QuestConfig`;
CREATE TABLE `huo15_QuestConfig` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `conf_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `conf_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `conf_content` text COLLATE utf8_unicode_ci,
  `conf_order` int(10) unsigned DEFAULT NULL,
  `conf_tips` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Records of `huo15_QuestConfig`
-- ----------------------------
BEGIN;
INSERT INTO `huo15_QuestConfig` VALUES ('1', null, 'isCloseSystem', null, null, null, null, '0'), ('2', null, 'examTime', null, null, null, null, '60');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
