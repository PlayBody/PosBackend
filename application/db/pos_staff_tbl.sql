/*
 Navicat Premium Data Transfer

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 100417
 Source Host           : localhost:3306
 Source Schema         : pos_db

 Target Server Type    : MySQL
 Target Server Version : 100417
 File Encoding         : 65001

 Date: 26/07/2021 21:35:07
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for pos_staff_tbl
-- ----------------------------
DROP TABLE IF EXISTS `pos_staff_tbl`;
CREATE TABLE `pos_staff_tbl`  (
  `staff_id` int(6) NOT NULL AUTO_INCREMENT COMMENT ' ',
  `mail_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `attendance_status` int(1) NULL DEFAULT NULL,
  `visit_time` datetime(0) NULL DEFAULT NULL,
  `del_flag` int(1) NULL DEFAULT 0,
  `create_date` datetime(0) NULL DEFAULT NULL,
  `update_date` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`staff_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pos_staff_tbl
-- ----------------------------
INSERT INTO `pos_staff_tbl` VALUES (2, 'staff1@gmail.com', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', 'スタッフ', 'avatar01.jpg', 1, NULL, 0, NULL, '2021-07-14 17:21:43');
INSERT INTO `pos_staff_tbl` VALUES (5, 'staff2@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '222', NULL, 2, NULL, 0, NULL, '2021-07-14 15:14:23');
INSERT INTO `pos_staff_tbl` VALUES (6, 'staff4@gmail.com', '356a192b7913b04c54574d18c28d46e6395428ab', 'テスト５つ', NULL, NULL, NULL, 0, '2021-07-14 15:16:27', '2021-07-14 15:16:43');
INSERT INTO `pos_staff_tbl` VALUES (7, NULL, 'da39a3ee5e6b4b0d3255bfef95601890afd80709', NULL, NULL, 2, NULL, 1, '2021-07-19 08:45:38', '2021-07-19 08:45:38');

SET FOREIGN_KEY_CHECKS = 1;
