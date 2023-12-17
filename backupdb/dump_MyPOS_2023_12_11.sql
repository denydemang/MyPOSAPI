/*
 Navicat Premium Data Transfer

 Source Server         : mariadblocal
 Source Server Type    : MariaDB
 Source Server Version : 110300 (11.3.0-MariaDB-log)
 Source Host           : localhost:3306
 Source Schema         : mypos

 Target Server Type    : MariaDB
 Target Server Version : 110300 (11.3.0-MariaDB-log)
 File Encoding         : 65001

 Date: 11/12/2023 01:06:44
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for access
-- ----------------------------
DROP TABLE IF EXISTS `access`;
CREATE TABLE `access`  (
  `branchcode` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_role` bigint(20) UNSIGNED NOT NULL,
  `id_module` bigint(20) UNSIGNED NOT NULL,
  `xUpdate` tinyint(1) NULL DEFAULT 0,
  `xDelete` tinyint(1) NULL DEFAULT 0,
  `xApprove` tinyint(1) NULL DEFAULT 0,
  `xCreate` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_module_fk`(`id_module`) USING BTREE,
  UNIQUE INDEX `id_role_id_module`(`id_role`, `id_module`) USING BTREE,
  CONSTRAINT `id_module_fk` FOREIGN KEY (`id_module`) REFERENCES `modules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_role_fk` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of access
-- ----------------------------
INSERT INTO `access` VALUES ('int', 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `access` VALUES ('int', 2, 1, 2, 1, 1, 1, 1);
INSERT INTO `access` VALUES ('int', 3, 1, 3, 1, 1, 1, 1);
INSERT INTO `access` VALUES ('int', 5, 1, 4, 0, 0, 0, 0);
INSERT INTO `access` VALUES ('int', 6, 1, 5, 0, 0, 0, 0);

-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories`  (
  `branchcode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name_branchcode`(`name`, `branchcode`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 31 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of categories
-- ----------------------------
INSERT INTO `categories` VALUES ('int', 1, 'Makanan', 1, '2023-10-09 19:31:37', '2023-11-30 11:54:11');
INSERT INTO `categories` VALUES ('int', 25, 'Snacks', 1, '2023-11-30 11:54:26', '2023-11-30 11:54:26');
INSERT INTO `categories` VALUES ('int', 27, 'dewi', 1, '2023-12-10 22:42:07', '2023-12-10 22:42:07');
INSERT INTO `categories` VALUES ('int', 28, 'asxaada', 1, '2023-12-10 22:42:14', '2023-12-10 22:42:14');

-- ----------------------------
-- Table structure for company_profiles
-- ----------------------------
DROP TABLE IF EXISTS `company_profiles`;
CREATE TABLE `company_profiles`  (
  `branchcode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `profile_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `npwp` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `moto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`branchcode`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_profiles
-- ----------------------------
INSERT INTO `company_profiles` VALUES ('int', 'psdsds', 'sadad', 'dasdada', 'denyk819@gmail.com', 'asdsadasd', 'asdadadad', '2023-10-21 06:57:09', '2023-10-21 06:57:09');

-- ----------------------------
-- Table structure for customers
-- ----------------------------
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers`  (
  `branchcode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cust_no` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `phone` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `cust_no_branch_code`(`cust_no`, `branchcode`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of customers
-- ----------------------------
INSERT INTO `customers` VALUES ('DENY', 2, 'Cust_001', 'Deny Demang', NULL, '123123', 1, '2023-10-22 19:39:28', '2023-10-22 19:39:28');
INSERT INTO `customers` VALUES ('int', 3, 'Cust_002', 'Deny Demang', NULL, '123123', 1, '2023-10-22 19:41:48', '2023-10-22 19:41:48');
INSERT INTO `customers` VALUES ('int', 4, '1', 'cust_deny', 'sarwogadung', '9129313', 1, '2023-11-22 11:57:55', '2023-11-22 11:57:55');

-- ----------------------------
-- Table structure for detail_grns
-- ----------------------------
DROP TABLE IF EXISTS `detail_grns`;
CREATE TABLE `detail_grns`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_grns` bigint(20) UNSIGNED NOT NULL,
  `id_product` bigint(20) UNSIGNED NOT NULL,
  `id_unit` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qty` int(11) NOT NULL,
  `bonusqty` int(11) NOT NULL,
  `unitbonusqty` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `price` decimal(20, 2) NOT NULL,
  `total` decimal(20, 2) NULL DEFAULT NULL,
  `discount` decimal(20, 2) NULL DEFAULT 0.00,
  `sub_total` decimal(20, 2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `grns_fk_id_product`(`id_product`) USING BTREE,
  INDEX `grns_fk_id_unit`(`id_unit`) USING BTREE,
  INDEX `grns_fk_id_grns`(`id_grns`) USING BTREE,
  INDEX `grns_fk_id_uit_bonus`(`unitbonusqty`) USING BTREE,
  CONSTRAINT `grns_fk_id_grns` FOREIGN KEY (`id_grns`) REFERENCES `grns` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `grns_fk_id_product` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `grns_fk_id_uit_bonus` FOREIGN KEY (`unitbonusqty`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `grns_fk_id_unit` FOREIGN KEY (`id_unit`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 67 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of detail_grns
-- ----------------------------
INSERT INTO `detail_grns` VALUES (63, 29, 7, 'rim', 200, 3, 'rim', 10000.00, 2000000.00, 10000.00, 1990000.00, '2023-11-06 11:22:25', '2023-11-06 11:22:25');
INSERT INTO `detail_grns` VALUES (64, 29, 11, 'rim', 400, 3, 'rim', 10000.00, 4000000.00, 10000.00, 3990000.00, '2023-11-06 11:22:25', '2023-11-06 11:22:25');
INSERT INTO `detail_grns` VALUES (65, 30, 2, 'pcs', 200, 3, 'rim', 100000.00, 20000.00, 0.00, 20000.00, '2023-11-27 10:23:40', '2023-11-27 10:23:40');
INSERT INTO `detail_grns` VALUES (66, 30, 5, 'pcs', 200, 3, 'rim', 120102.00, 121910.00, 0.00, 200000.00, '2023-11-27 10:24:37', '2023-11-27 10:24:37');

-- ----------------------------
-- Table structure for detail_purchase_return
-- ----------------------------
DROP TABLE IF EXISTS `detail_purchase_return`;
CREATE TABLE `detail_purchase_return`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_purchase_return` bigint(20) UNSIGNED NOT NULL,
  `id_product` bigint(20) UNSIGNED NOT NULL,
  `id_unit` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qty` int(11) NOT NULL,
  `cogs` decimal(20, 6) NOT NULL,
  `sub_total` decimal(20, 6) NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `dtp_id_product_fk`(`id_product`) USING BTREE,
  INDEX `dtp_id_unit_fk`(`id_unit`) USING BTREE,
  INDEX `dtp_id_purchase_return`(`id_purchase_return`) USING BTREE,
  CONSTRAINT `dtp_id_product_fk` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `dtp_id_purchase_return` FOREIGN KEY (`id_purchase_return`) REFERENCES `purchase_return` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `dtp_id_unit_fk` FOREIGN KEY (`id_unit`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of detail_purchase_return
-- ----------------------------
INSERT INTO `detail_purchase_return` VALUES (1, 1, 2, 'pcs', 25, 2324.000000, 2000.000000, '2023-11-13 15:30:40', '2023-11-13 15:30:40');
INSERT INTO `detail_purchase_return` VALUES (12, 5, 7, 'pcs', 50000, 19.614162, 980708.100000, '2023-11-15 14:00:43', '2023-11-15 14:00:43');
INSERT INTO `detail_purchase_return` VALUES (13, 5, 11, 'pcs', 25000, 19.809740, 495243.500000, '2023-11-15 14:00:43', '2023-11-15 14:00:43');

-- ----------------------------
-- Table structure for detail_purchases
-- ----------------------------
DROP TABLE IF EXISTS `detail_purchases`;
CREATE TABLE `detail_purchases`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_purchases` bigint(20) UNSIGNED NOT NULL,
  `id_product` bigint(20) UNSIGNED NOT NULL,
  `id_unit` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(20, 2) NOT NULL,
  `total` decimal(20, 2) NOT NULL,
  `discount` decimal(20, 2) NULL DEFAULT 0.00,
  `sub_total` decimal(20, 2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_product_fk`(`id_product`) USING BTREE,
  INDEX `id_purchases_fk`(`id_purchases`) USING BTREE,
  INDEX `id_unit_fk`(`id_unit`) USING BTREE,
  CONSTRAINT `id_product_fk` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `id_purchases_fk` FOREIGN KEY (`id_purchases`) REFERENCES `purchases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_unit_fk` FOREIGN KEY (`id_unit`) REFERENCES `units` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 46 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of detail_purchases
-- ----------------------------
INSERT INTO `detail_purchases` VALUES (41, 14, 7, 'pcs', 10, 9999.00, 0.00, 0.00, 9999.00, '2023-11-03 07:06:39', '2023-11-03 07:06:39');
INSERT INTO `detail_purchases` VALUES (42, 15, 7, 'pcs', 10, 9999.00, 0.00, 0.00, 9999.00, '2023-11-03 07:06:42', '2023-11-03 07:06:42');

-- ----------------------------
-- Table structure for detail_sales
-- ----------------------------
DROP TABLE IF EXISTS `detail_sales`;
CREATE TABLE `detail_sales`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_sales` bigint(20) UNSIGNED NOT NULL,
  `id_product` bigint(20) UNSIGNED NOT NULL,
  `id_unit` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(20, 2) NOT NULL,
  `total` decimal(20, 2) NOT NULL,
  `discount` decimal(20, 2) NULL DEFAULT 0.00,
  `sub_total` decimal(20, 2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_product_fk_1`(`id_product`) USING BTREE,
  INDEX `id_unit_fk_dts`(`id_unit`) USING BTREE,
  INDEX `id_sales_fk`(`id_sales`) USING BTREE,
  CONSTRAINT `id_product_fk_1` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `id_sales_fk` FOREIGN KEY (`id_sales`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_unit_fk_dts` FOREIGN KEY (`id_unit`) REFERENCES `units` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 92 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of detail_sales
-- ----------------------------

-- ----------------------------
-- Table structure for grns
-- ----------------------------
DROP TABLE IF EXISTS `grns`;
CREATE TABLE `grns`  (
  `branchcode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `trans_no` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `received_date` date NOT NULL,
  `id_purchase` bigint(20) UNSIGNED NOT NULL,
  `received_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `grand_total` decimal(20, 2) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `is_approve` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `trans_no_branchcode_fk`(`trans_no`, `branchcode`) USING BTREE,
  UNIQUE INDEX `grns_id_purchase_unique`(`branchcode`, `id_purchase`) USING BTREE,
  INDEX `grns_id_purchase_fk`(`id_purchase`) USING BTREE,
  CONSTRAINT `grns_id_purchase_fk` FOREIGN KEY (`id_purchase`) REFERENCES `purchases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 31 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of grns
-- ----------------------------
INSERT INTO `grns` VALUES ('int', 29, 'GRN-2023-11-06-001', '2023-11-03', 15, 'demang', 88392.00, 'lengkap lur', 0, '2023-11-06 11:18:15', '2023-11-06 11:22:25');
INSERT INTO `grns` VALUES ('int', 30, 'GRN-2023-11-27-001', '2023-11-27', 14, 'denkur', 121312.00, 'Lengkap Lur', 0, '2023-11-27 10:22:46', '2023-11-27 10:22:46');

-- ----------------------------
-- Table structure for log_inv_out
-- ----------------------------
DROP TABLE IF EXISTS `log_inv_out`;
CREATE TABLE `log_inv_out`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `branchcode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ref_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date` date NOT NULL,
  `id_product` bigint(20) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(20, 6) NOT NULL,
  `id_stock` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_stock_fk`(`id_stock`) USING BTREE,
  CONSTRAINT `id_stock_fk` FOREIGN KEY (`id_stock`) REFERENCES `stocks` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 52 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of log_inv_out
-- ----------------------------
INSERT INTO `log_inv_out` VALUES (50, 'int', 'PRTN-2023-11-15-001', '2023-11-14', 7, 50000, 19.614162, 36, '2023-11-15 14:00:43', '2023-11-15 14:00:43');
INSERT INTO `log_inv_out` VALUES (51, 'int', 'PRTN-2023-11-15-001', '2023-11-14', 11, 25000, 19.809740, 37, '2023-11-15 14:00:43', '2023-11-15 14:00:43');

-- ----------------------------
-- Table structure for modules
-- ----------------------------
DROP TABLE IF EXISTS `modules`;
CREATE TABLE `modules`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `sub_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `description` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `is_active` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of modules
-- ----------------------------
INSERT INTO `modules` VALUES (1, 'dasboard', 'dashboard', 'Dashboard', 1);
INSERT INTO `modules` VALUES (2, 'product_management', 'master_category', 'master_category', 1);
INSERT INTO `modules` VALUES (3, 'product_management', 'master_item', 'master item', 1);
INSERT INTO `modules` VALUES (4, 'user_management', 'master_user', 'master_user', 1);
INSERT INTO `modules` VALUES (5, 'user_management', 'role_user', 'role_user', 1);

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products`  (
  `branchcode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `barcode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `brands` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_category` bigint(20) UNSIGNED NOT NULL,
  `id_unit` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10, 2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `maxstock` int(11) NOT NULL,
  `minstock` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `branchcode_barcode_unique`(`branchcode`, `barcode`) USING BTREE,
  INDEX `id_category_fk_products`(`id_category`) USING BTREE,
  INDEX `products_FK`(`id_unit`) USING BTREE,
  CONSTRAINT `id_category_fk_products` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `products_FK` FOREIGN KEY (`id_unit`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 45 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of products
-- ----------------------------
INSERT INTO `products` VALUES ('int', 2, '323113', 'Crispy Kenctucky', 'Kentucky', 25, 'pcs', 5000.00, 1, 10000, 1, '2023-10-11 06:49:15', '2023-11-30 11:55:59');
INSERT INTO `products` VALUES ('int', 5, '211wsdqkjkdsfs', 'VItal asdasd', 'Vital', 1, 'pcs', 23213123.00, 1, 1, 100, '2023-10-14 21:11:51', '2023-10-14 21:11:51');
INSERT INTO `products` VALUES ('int', 7, '423few324', 'Hape', 'Vital', 1, 'pcs', 23213123.00, 1, 1, 100, '2023-10-15 10:16:58', '2023-10-15 10:16:58');
INSERT INTO `products` VALUES ('dmk', 8, '423few324', 'Hape', 'Vital', 1, 'pcs', 23213123.00, 1, 1, 100, '2023-10-20 09:43:55', '2023-10-20 09:43:55');
INSERT INTO `products` VALUES ('int', 11, '12187913', 'Sabun Lifebuoy', 'Lifebuoy', 1, 'pair', 20000.00, 1, 1, 1, '2023-10-22 04:14:52', '2023-11-30 11:41:53');
INSERT INTO `products` VALUES ('dmk', 12, '12187913', 'Sabun Sssssd', 'Lifebuoy', 1, 'pcs', 20000.00, 1, 1, 1, '2023-10-22 04:23:43', '2023-10-22 04:23:43');

-- ----------------------------
-- Table structure for purchase_return
-- ----------------------------
DROP TABLE IF EXISTS `purchase_return`;
CREATE TABLE `purchase_return`  (
  `branchcode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `trans_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_grn` bigint(20) UNSIGNED NOT NULL,
  `trans_date` date NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `total` decimal(20, 2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  `is_approve` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `trans_no`(`trans_no`) USING BTREE,
  INDEX `pr_id_grn_fk`(`id_grn`) USING BTREE,
  CONSTRAINT `pr_id_grn_fk` FOREIGN KEY (`id_grn`) REFERENCES `grns` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of purchase_return
-- ----------------------------
INSERT INTO `purchase_return` VALUES ('int', 1, 'prtn_001', 29, '2023-11-13', 'dada', 25000.00, '2023-11-13 15:22:55', '2023-11-13 15:22:55', 0);
INSERT INTO `purchase_return` VALUES ('int', 5, 'PRTN-2023-11-15-001', 29, '2023-11-14', 'Barang Cacat', 1475952.00, '2023-11-15 14:00:43', '2023-11-15 14:16:56', 1);

-- ----------------------------
-- Table structure for purchases
-- ----------------------------
DROP TABLE IF EXISTS `purchases`;
CREATE TABLE `purchases`  (
  `branchcode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `trans_no` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `trans_date` date NOT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `id_supplier` bigint(20) UNSIGNED NOT NULL,
  `total` decimal(20, 2) NOT NULL,
  `other_fee` decimal(20, 2) NULL DEFAULT 0.00,
  `ppn` decimal(20, 2) NULL DEFAULT 0.00,
  `payment_term` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `is_approve` tinyint(1) NOT NULL DEFAULT 0,
  `grand_total` decimal(20, 2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  `is_credit` tinyint(1) NOT NULL DEFAULT 1,
  `is_received` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `trans_no_branch_code_index`(`trans_no`, `branchcode`) USING BTREE,
  INDEX `id_supplier_fk`(`id_supplier`) USING BTREE,
  INDEX `id_user_fk`(`id_user`) USING BTREE,
  CONSTRAINT `id_supplier_fk` FOREIGN KEY (`id_supplier`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `id_user_fk` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of purchases
-- ----------------------------
INSERT INTO `purchases` VALUES ('int', 14, 'PRC-2023-11-03-001', '2023-10-28', 6, 1, 1000.00, 0.00, 0.00, NULL, 1, 25000.00, '2023-11-03 07:06:39', '2023-11-03 13:35:08', 1, 1);
INSERT INTO `purchases` VALUES ('int', 15, 'PRC-2023-11-03-002', '2023-10-28', 6, 1, 1000.00, 2500.00, 0.00, NULL, 0, 25000.00, '2023-11-03 07:06:42', '2023-11-06 10:28:21', 1, 1);

-- ----------------------------
-- Table structure for remember_token
-- ----------------------------
DROP TABLE IF EXISTS `remember_token`;
CREATE TABLE `remember_token`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `token_expired` datetime NOT NULL,
  `id_user` bigint(20) UNSIGNED NULL DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sax`(`id_user`) USING BTREE,
  CONSTRAINT `sax` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of remember_token
-- ----------------------------
INSERT INTO `remember_token` VALUES (37, 'bb34ebcd-ce28-4cb0-ade2-8d9ec2ea26df', '2023-12-11 08:43:00', 15, '2023-12-11 00:43:00', '2023-12-11 00:43:00');

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  `branchcode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`, `branchcode`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (1, 'superadmin', 1, '2023-10-11 06:30:51', '2023-10-11 06:30:51', 'int');

-- ----------------------------
-- Table structure for sales
-- ----------------------------
DROP TABLE IF EXISTS `sales`;
CREATE TABLE `sales`  (
  `branchcode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `trans_no` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `trans_date` date NOT NULL,
  `id_cust` bigint(20) UNSIGNED NULL DEFAULT NULL,
  `id_user` bigint(20) UNSIGNED NULL DEFAULT NULL,
  `total` decimal(20, 2) NOT NULL,
  `ppn` decimal(20, 2) NOT NULL,
  `other_fee` decimal(20, 2) NULL DEFAULT 0.00,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `grand_total` decimal(20, 2) NOT NULL,
  `paid` decimal(20, 2) NOT NULL,
  `change_amount` decimal(20, 2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  `is_credit` tinyint(1) NOT NULL DEFAULT 0,
  `is_approve` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `trans_no_branchcode_index`(`trans_no`, `branchcode`) USING BTREE,
  INDEX `id_user_fk_sales`(`id_user`) USING BTREE,
  INDEX `id_cust_fk_sales`(`id_cust`) USING BTREE,
  CONSTRAINT `id_cust_fk_sales` FOREIGN KEY (`id_cust`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `id_user_fk_sales` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE = InnoDB AUTO_INCREMENT = 41 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sales
-- ----------------------------

-- ----------------------------
-- Table structure for stocks
-- ----------------------------
DROP TABLE IF EXISTS `stocks`;
CREATE TABLE `stocks`  (
  `branchcode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `date` date NOT NULL,
  `id_product` bigint(20) UNSIGNED NOT NULL,
  `actual_stock` int(11) NOT NULL DEFAULT 0,
  `used_stock` int(11) NOT NULL DEFAULT 0,
  `cogs` decimal(20, 6) NOT NULL DEFAULT 0.000000,
  `id_unit` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  `is_approve` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_id_product_stocks`(`id_product`) USING BTREE,
  INDEX `fk_id_unit_stocks`(`id_unit`) USING BTREE,
  CONSTRAINT `fk_id_product_stocks` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_id_unit_stocks` FOREIGN KEY (`id_unit`) REFERENCES `units` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 49 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stocks
-- ----------------------------
INSERT INTO `stocks` VALUES ('int', 14, 'GRN-2023-11-03-001', '2023-11-03', 7, 20, 0, 9999.000000, 'pcs', '2023-11-03 13:46:27', '2023-11-03 15:31:14', 1);
INSERT INTO `stocks` VALUES ('int', 16, 'PRTN-2023-11-03-001', '2023-11-03', 7, 5, 0, 10000.000000, 'pcs', '2023-11-03 14:26:46', '2023-11-03 14:55:41', 0);
INSERT INTO `stocks` VALUES ('int', 36, 'GRN-2023-11-06-001', '2023-11-03', 7, 101500, 50000, 19.614162, 'pcs', '2023-11-06 11:18:15', '2023-11-15 14:00:43', 0);
INSERT INTO `stocks` VALUES ('int', 37, 'GRN-2023-11-06-001', '2023-11-03', 11, 201500, 25000, 19.809740, 'pcs', '2023-11-06 11:18:15', '2023-11-15 14:00:43', 0);

-- ----------------------------
-- Table structure for suppliers
-- ----------------------------
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers`  (
  `branchcode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contact` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `branchcode`(`branchcode`, `name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of suppliers
-- ----------------------------
INSERT INTO `suppliers` VALUES ('int', 1, 'PT BERKAH JAYA', 'umbulharjo', '0328923', 1, '2023-10-12 04:50:00', '2023-10-12 04:50:00');

-- ----------------------------
-- Table structure for units
-- ----------------------------
DROP TABLE IF EXISTS `units`;
CREATE TABLE `units`  (
  `id` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `convert_value` decimal(20, 2) NOT NULL,
  `status` tinyint(1) NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of units
-- ----------------------------
INSERT INTO `units` VALUES ('gross', 'gross', 144.00, 1, '2023-10-12 19:45:57', '2023-10-12 19:58:12');
INSERT INTO `units` VALUES ('kg', 'kilogram', 1.00, 1, '2023-10-20 10:43:03', '2023-10-20 10:43:03');
INSERT INTO `units` VALUES ('kodi', 'kodi', 20.00, 1, '2023-10-12 19:45:24', '2023-10-12 19:58:12');
INSERT INTO `units` VALUES ('lbr', 'lembar', 1.00, 1, '2023-10-10 11:06:43', '2023-10-12 19:58:12');
INSERT INTO `units` VALUES ('liter', 'liter', 1.00, 1, '2023-10-20 15:51:28', '2023-10-20 15:51:28');
INSERT INTO `units` VALUES ('lusin', 'lusin', 12.00, 1, '2023-10-12 19:45:40', '2023-10-12 19:58:12');
INSERT INTO `units` VALUES ('pair', 'pair', 1.00, 1, '2023-10-20 10:44:33', '2023-10-20 10:44:33');
INSERT INTO `units` VALUES ('pcs', 'pieces', 1.00, 1, '2023-10-10 11:04:22', '2023-10-12 19:58:12');
INSERT INTO `units` VALUES ('rim', 'rim', 500.00, 1, '2023-10-10 11:05:10', '2023-10-12 19:58:12');

-- ----------------------------
-- Table structure for units_group
-- ----------------------------
DROP TABLE IF EXISTS `units_group`;
CREATE TABLE `units_group`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `id_unit` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `units_group_FK`(`id_unit`) USING BTREE,
  CONSTRAINT `units_group_FK` FOREIGN KEY (`id_unit`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of units_group
-- ----------------------------
INSERT INTO `units_group` VALUES (1, 'quantity', 'pcs');
INSERT INTO `units_group` VALUES (2, 'quantity', 'lusin');
INSERT INTO `units_group` VALUES (3, 'quantity', 'kodi');
INSERT INTO `units_group` VALUES (4, 'quantity', 'gross');
INSERT INTO `units_group` VALUES (5, 'quantity', 'pair');
INSERT INTO `units_group` VALUES (6, 'paper', 'lbr');
INSERT INTO `units_group` VALUES (7, 'paper', 'rim');
INSERT INTO `units_group` VALUES (8, 'weight', 'kg');
INSERT INTO `units_group` VALUES (9, 'volume', 'liter');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `branchcode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_role` bigint(11) UNSIGNED NULL DEFAULT NULL,
  `active` tinyint(1) NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username_branchcode_index`(`username`, `branchcode`) USING BTREE,
  INDEX `users_id_role_fk`(`id_role`) USING BTREE,
  CONSTRAINT `users_id_role_fk` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (6, 'int', 'dewichayono', 'Dewi Cahyono', '$2y$10$ESLzGBtJKLDpDW0J.cAly.K43PxOJNrtbRUsPqo1HjB5nvxMMKbx6', 1, 0, '2023-10-13 21:58:39', '2023-12-10 23:19:22');
INSERT INTO `users` VALUES (15, 'int', 'admin', 'ADMIN', '$2y$10$zod.Q8SX/GiVuOOsj868OuDps1ZCdynAtih.ciQXBvU7Frdm3f9VS', 1, 1, '2023-12-10 23:48:08', '2023-12-10 23:48:08');

-- ----------------------------
-- View structure for access_view
-- ----------------------------
DROP VIEW IF EXISTS `access_view`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `access_view` AS SELECT a.branchcode, b.id as id_role, b.`name` as role_name , c.id as id_module ,c.`name` as module_name, c.sub_name as module_sub_name, d.id as id_user, d.username , e.token ,
a.xApprove, a.xCreate, a.xDelete, a.xUpdate,
d.active as isactiveuser, b.active as isactiverole

from access a left JOIN roles b ON a.id_role = b.id and a.branchcode =b.branchcode

left JOIN modules c ON a.id_module = c.id 
INNER JOIN users d On b.id= d.id_role
LEFT JOIN remember_token e on d.id =e.id_user
where d.active =1 AND b.active= 1 ;

-- ----------------------------
-- View structure for grns_view
-- ----------------------------
DROP VIEW IF EXISTS `grns_view`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `grns_view` AS select `g`.`branchcode` AS `branchcode`,`g`.`id` AS `id`,`g`.`trans_no` AS `trans_no`,`g`.`received_date` AS `received_date`,`p`.`id` AS `id_purchase`,`p`.`trans_no` AS `purchase_trans_no`,`s`.`id` AS `id_supplier`,`s`.`name` AS `supplier_name`,`g`.`received_by` AS `received_by`,`g`.`description` AS `description`,`p`.`total` AS `total_purchase`,`p`.`other_fee` AS `other_fee`,`p`.`ppn` AS `ppn`,`p`.`grand_total` AS `grand_total`,`g`.`is_approve` AS `is_approve`,`d`.`id` AS `id_detail_grns`,`c`.`id` AS `id_product`,`c`.`barcode` AS `barcode`,`c`.`name` AS `name`,`c`.`brands` AS `brands`,`d`.`id_unit` AS `id_unit`,`d`.`qty` AS `qty`,`d`.`bonusqty` AS `bonusqty`,`d`.`unitbonusqty`,`d`.`price` AS `price`,`d`.`total` AS `total`,`d`.`discount` AS `discount`,`d`.`sub_total` AS `sub_total` from ((((`grns` `g` left join `detail_grns` `d` on(`g`.`id` = `d`.`id_grns`)) left join `purchases` `p` on(`g`.`id_purchase` = `p`.`id` and `g`.`branchcode` = `p`.`branchcode`)) left join `products` `c` on(`d`.`id_product` = `c`.`id` and `g`.`branchcode` = `c`.`branchcode`)) left join `suppliers` `s` on(`p`.`id_supplier` = `s`.`id` and `p`.`branchcode` = `s`.`branchcode`)) ;

-- ----------------------------
-- View structure for products_view
-- ----------------------------
DROP VIEW IF EXISTS `products_view`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `products_view` AS select  `a`.`branchcode` AS `branchcode`,`a`.`id` AS `id`,`a`.`barcode` AS `barcode`,`a`.`name` AS `name`,`a`.`brands` AS `brands`,`b`.`id` AS `id_category`,`b`.`name` AS `category`,`a`.`id_unit` AS `unit`,`a`.`price` AS `price`,`a`.`status` AS `status`,`a`.`maxstock` AS `maxstock`,`a`.`minstock` AS `minstock`,sum(ifnull(`c`.`actual_stock`,0) - ifnull(`c`.`used_stock`,0)) AS `remaining_stock` from ((`products` `a` left join `categories` `b` on(`a`.`id_category` = `b`.`id` and `a`.`branchcode` = `b`.`branchcode`)) left join (select `stocks`.`branchcode` AS `branchcode`,`stocks`.`id` AS `id`,`stocks`.`ref` AS `ref`,`stocks`.`date` AS `date`,`stocks`.`id_product` AS `id_product`,`stocks`.`actual_stock` AS `actual_stock`,`stocks`.`used_stock` AS `used_stock`,`stocks`.`cogs` AS `cogs`,`stocks`.`id_unit` AS `id_unit`,`stocks`.`created_at` AS `created_at`,`stocks`.`updated_at` AS `updated_at`,`stocks`.`is_approve` AS `is_approve` from `stocks` where `stocks`.`is_approve` = 1) `c` on(`a`.`id` = `c`.`id_product` and `a`.`branchcode` = `c`.`branchcode`)) group by `a`.`id`,`a`.`branchcode`,`a`.`barcode` ;

-- ----------------------------
-- View structure for purchases_view
-- ----------------------------
DROP VIEW IF EXISTS `purchases_view`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `purchases_view` AS select `a`.`branchcode` AS `branchcode`,`a`.`id` AS `id`,`a`.`trans_no` AS `trans_no`,`a`.`trans_date` AS `trans_date`,`c`.`id` AS `id_user`,`c`.`name` AS `pic_name`,`d`.`id` AS `id_supplier`,`d`.`name` AS `supplier_name`,`a`.`total` AS `total_purchase`,`a`.`other_fee` AS `other_fee`,`a`.`ppn` AS `ppn`,`a`.`payment_term` AS `payment_term`,`a`.`is_approve` AS `is_approve`,`a`.`is_credit` AS `is_credit`,`b`.`id` AS `id_detail_purchases`,`e`.`id` AS `id_product`,`e`.`barcode` AS `barcode`,`e`.`name` AS `product_name`,`b`.`id_unit` AS `unit`,`b`.`qty` AS `qty`,`b`.`price` AS `price`,`b`.`total` AS `total`,`b`.`discount` AS `discount`,`b`.`sub_total` AS `sub_total`,`a`.`grand_total` AS `grand_total` from ((((`purchases` `a` left join `detail_purchases` `b` on(`a`.`id` = `b`.`id_purchases`)) left join `users` `c` on(`a`.`id_user` = `c`.`id` and `a`.`branchcode` = `c`.`branchcode`)) left join `suppliers` `d` on(`a`.`id_supplier` = `d`.`id` and `a`.`branchcode` = `d`.`branchcode`)) left join `products` `e` on(`b`.`id_product` = `e`.`id` and `a`.`branchcode` = `e`.`branchcode`)) ;

-- ----------------------------
-- View structure for purchase_return_view
-- ----------------------------
DROP VIEW IF EXISTS `purchase_return_view`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `purchase_return_view` AS SELECT
	pr.branchcode,
	pr.id,
	pr.trans_no,
	pr.trans_date,
	g.id AS id_grn,
	g.trans_no AS grn_trans_no,
	p.id AS id_purchase,
	p.trans_no AS purchase_trans_no,
	s.id as id_supplier,
	s.`name` as supplier_name,
	pr.reason,
	dpr.id AS id_detail_purchase_return,
	pd.id AS id_product,
	pd.barcode,
	pd.NAME AS product_name,
	dpr.id_unit,
	dpr.qty,
	dpr.cogs,
	dpr.sub_total,
	pr.total,
	pr.is_approve 
FROM
	purchase_return pr
	LEFT JOIN detail_purchase_return dpr ON pr.id = dpr.id_purchase_return
	LEFT JOIN grns g ON pr.id_grn = g.id 
	AND pr.branchcode = g.branchcode
	LEFT JOIN purchases p ON g.id_purchase = p.id 
	AND g.branchcode = p.branchcode
	LEFT JOIN products pd ON dpr.id_product = pd.id
	AND pr.branchcode = pd.branchcode
	LEFT JOIN suppliers s ON p.id_supplier = s.id
	AND s.branchcode =pr.branchcode ;

-- ----------------------------
-- View structure for sales_view
-- ----------------------------
DROP VIEW IF EXISTS `sales_view`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `sales_view` AS select `a`.`branchcode` AS `branchcode`,`a`.`id` AS `id`,`a`.`trans_no` AS `trans_no`,`a`.`trans_date` AS `trans_date`,`a`.`total` AS `total_sales`,`a`.`ppn` AS `sales_ppn`,`a`.`other_fee` AS `other_fee`,`a`.`notes` AS `sales_notes`,`a`.`is_credit` AS `is_sales_credit`,`c`.`id` AS `id_cust`,`c`.`cust_no` AS `cust_no`,`c`.`name` AS `cust_name`,`d`.`id` AS `id_user`,`d`.`username` AS `username`,`d`.`name` AS `pic_name`,`e`.`id` AS `id_role`,`e`.`name` AS `role_name`,`b`.`id` AS `id_detail_sales`,`f`.`id` AS `id_product`,`f`.`barcode` AS `barcode`,`f`.`name` AS `product_name`,`b`.`id_unit` AS `unit`,`b`.`qty` AS `qty`,`b`.`price` AS `price`,`b`.`total` AS `total`,`b`.`discount` AS `discount`,`b`.`sub_total` AS `sub_total`,`a`.`grand_total` AS `grand_total`,`a`.`paid` AS `paid`,`a`.`change_amount` AS `change_amount` from (((((`sales` `a` left join `detail_sales` `b` on(`a`.`id` = `b`.`id_sales`)) left join `customers` `c` on(`a`.`id_cust` = `c`.`id` and `a`.`branchcode` = `c`.`branchcode`)) left join `users` `d` on(`a`.`id_user` = `d`.`id` and `a`.`branchcode` = `d`.`branchcode`)) left join `roles` `e` on(`d`.`id_role` = `e`.`id` and `d`.`branchcode` = `e`.`branchcode`)) left join `products` `f` on(`b`.`id_product` = `f`.`id` and `a`.`branchcode` = `f`.`branchcode`)) ;

-- ----------------------------
-- View structure for units_view
-- ----------------------------
DROP VIEW IF EXISTS `units_view`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `units_view` AS select `u`.`id` AS `id_unit`,`u`.`name` AS `name`,`u`.`convert_value` AS `convert_value`,`u`.`status` AS `status`,`ug`.`group_name` AS `group_name` from (`units` `u` left join `units_group` `ug` on(`u`.`id` = `ug`.`id_unit`)) order by `ug`.`group_name` ;

-- ----------------------------
-- View structure for users_view
-- ----------------------------
DROP VIEW IF EXISTS `users_view`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `users_view` AS select `a`.`branchcode` AS `branchcode`,`a`.`id` AS `id`,`a`.`username` AS `username`,`a`.`name` AS `name`,`a`.`password` AS `password`,`a`.`active` AS `active`,`b`.`id` AS `id_role`,`b`.`name` AS `role` from (`users` `a` left join `roles` `b` on(`a`.`id_role` = `b`.`id` and `a`.`branchcode` = `b`.`branchcode`)) ;

SET FOREIGN_KEY_CHECKS = 1;
