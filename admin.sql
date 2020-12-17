/*
 Navicat Premium Data Transfer

 Source Server         : localhost server
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : 127.0.0.1:3306
 Source Schema         : npf_project

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 17/12/2020 17:08:38
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin_login
-- ----------------------------
DROP TABLE IF EXISTS `admin_login`;
CREATE TABLE `admin_login`  (
  `admin_login_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_login_adminid` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `admin_login_source` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'login',
  `admin_login_useragent` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `admin_login_browser` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `admin_login_platform` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `admin_login_ip` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `admin_login_created` datetime(0) NOT NULL,
  PRIMARY KEY (`admin_login_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for admin_manager
-- ----------------------------
DROP TABLE IF EXISTS `admin_manager`;
CREATE TABLE `admin_manager`  (
  `admin_manager_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_manager_user` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `admin_manager_pass` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `admin_manager_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `admin_manager_type` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'sub',
  `admin_manager_roleid` bigint(20) NOT NULL DEFAULT 0,
  `admin_manager_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `admin_manager_createby` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `admin_manager_updateby` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `admin_manager_updated` datetime(0) NOT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `admin_manager_created` datetime(0) NOT NULL,
  PRIMARY KEY (`admin_manager_id`) USING BTREE,
  UNIQUE INDEX `idx_user`(`admin_manager_user`) USING BTREE,
  UNIQUE INDEX `idx_nick`(`admin_manager_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_manager
-- ----------------------------
INSERT INTO `admin_manager` VALUES (1, 'admin', 'cd5ea73cd58f827fa78eef7197b8ee606c99b2e6', 'Administrator', 'main', 0, 1, 0, 0, '2020-12-17 16:46:10', '2020-10-30 00:45:51');

-- ----------------------------
-- Table structure for admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu`  (
  `admin_menu_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_menu_parentid` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `admin_menu_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `admin_menu_level` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `admin_menu_index` decimal(6, 1) UNSIGNED NOT NULL DEFAULT 0.0,
  `admin_menu_icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `admin_menu_uri` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `admin_menu_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`admin_menu_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_menu
-- ----------------------------
INSERT INTO `admin_menu` VALUES (1, 0, 'Home', 0, 1.0, 'mdi mdi-home-outline', '/', 1);
INSERT INTO `admin_menu` VALUES (2, 0, 'Admin Section', 0, 3.0, '', '', 1);
INSERT INTO `admin_menu` VALUES (3, 2, 'Admin', 1, 1.0, 'uil-user', '', 1);
INSERT INTO `admin_menu` VALUES (4, 3, 'Admin Manager', 2, 1.0, 'uil-user', '/Admin/Manager/Index', 1);
INSERT INTO `admin_menu` VALUES (5, 3, 'Admin Menus', 2, 2.0, 'uil-user', '/Admin/Menus/Index', 1);
INSERT INTO `admin_menu` VALUES (6, 3, 'Admin Roles', 2, 3.0, 'uil-user', '/Admin/Roles/Index', 1);

-- ----------------------------
-- Table structure for admin_role
-- ----------------------------
DROP TABLE IF EXISTS `admin_role`;
CREATE TABLE `admin_role`  (
  `admin_role_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_role_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `admin_role_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `admin_role_description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_role_menus` json NOT NULL,
  `admin_role_actions` json NOT NULL,
  `admin_role_updateby` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `admin_role_createby` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `admin_role_updated` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_role_created` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`admin_role_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for oauth_connect
-- ----------------------------
DROP TABLE IF EXISTS `oauth_connect`;
CREATE TABLE `oauth_connect`  (
  `oauth_connect_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `oauth_connect_roleid` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `oauth_connect_service` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `oauth_connect_serviceid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `oauth_connect_created` datetime(0) NOT NULL,
  `oauth_connect_createdts` bigint(20) NOT NULL,
  PRIMARY KEY (`oauth_connect_id`) USING BTREE,
  UNIQUE INDEX `idx_oauth_service`(`oauth_connect_service`, `oauth_connect_serviceid`) USING BTREE,
  UNIQUE INDEX `idx_oauth_role`(`oauth_connect_roleid`, `oauth_connect_service`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
