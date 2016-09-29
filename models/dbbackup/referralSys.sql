/*
SQLyog Enterprise - MySQL GUI v7.02 
MySQL - 5.6.12-log : Database - snackr_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`snackr_db` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `snackr_db`;

/*Table structure for table `tbl_input_code` */

DROP TABLE IF EXISTS `tbl_input_code`;

CREATE TABLE `tbl_input_code` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(256) NOT NULL COMMENT 'Customer Email',
  `code` varchar(10) NOT NULL COMMENT 'Referral Code',
  `created_time` datetime DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_input_code` */

insert  into `tbl_input_code`(`id`,`email`,`code`,`created_time`,`modified_time`) values (1,'emelyan@gmail.com','xP0E4r','2015-09-25 16:27:30','2015-09-25 16:27:30'),(2,'michel@gmail.com','CZp28c','2015-09-25 16:29:45','2015-09-25 16:29:45'),(3,'emelyan@gmail.com','G1PxUV','2015-09-25 16:33:35','2015-09-25 16:33:35'),(4,'michel@gmail.com','ehTIBY','2015-09-25 16:35:17','2015-09-25 16:35:17'),(5,'matko@asdf.com','9jlX1P','2015-09-25 16:41:01','2015-09-25 16:41:01');

/*Table structure for table `tbl_referral_code` */

DROP TABLE IF EXISTS `tbl_referral_code`;

CREATE TABLE `tbl_referral_code` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(256) NOT NULL COMMENT 'Code Owner''s Email',
  `code` varchar(10) NOT NULL COMMENT 'ReferralCode',
  `created_time` datetime DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_referral_code` */

insert  into `tbl_referral_code`(`id`,`email`,`code`,`created_time`,`modified_time`) values (1,'matko@asdf.com','xP0E4r','2015-09-25 15:56:18','2015-09-25 15:56:18'),(2,'matko@asdf.com','GlPxUV','2015-09-25 16:14:05','2015-09-25 16:14:05'),(3,'matko@asdf.com','CZp28c','2015-09-25 16:28:43','2015-09-25 16:28:43'),(4,'michel@gmail.com','45KxbG','2015-09-25 16:29:56','2015-09-25 16:29:56'),(5,'emelyan@gmail.com','6ZKZCw','2015-09-25 16:30:40','2015-09-25 16:30:40'),(6,'emelyan@gmail.com','9jlX1P','2015-09-25 16:33:43','2015-09-25 16:33:43'),(7,'matko@asdf.com','ehTIBY','2015-09-25 16:34:32','2015-09-25 16:34:32'),(8,'matko@asdf.com','4o6qy4','2015-09-25 16:36:55','2015-09-25 16:36:55'),(9,'matko@asdf.com','QgJ4C5','2015-09-25 16:39:12','2015-09-25 16:39:12'),(10,'emelyan@gmail.com','aWGkd5','2015-09-25 16:41:36','2015-09-25 16:41:36'),(11,'emelyan@gmail.com','W4xcOr','2015-09-25 16:41:39','2015-09-25 16:41:39'),(12,'emelyan@gmail.com','DA3tWd','2015-09-25 16:41:44','2015-09-25 16:41:44');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
