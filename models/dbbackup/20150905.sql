/*
SQLyog Enterprise - MySQL GUI v7.02 
MySQL - 5.5.25a : Database - snackr_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`snackr_db` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `snackr_db`;

/*Table structure for table `tbl_customer` */

DROP TABLE IF EXISTS `tbl_customer`;

CREATE TABLE `tbl_customer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `class` char(1) DEFAULT 'N' COMMENT 'CustomerClass(''N'':Normal,''R'':Restaurant)',
  `created_time` datetime NOT NULL,
  `modified_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_customer` */

insert  into `tbl_customer`(`id`,`email`,`pwd`,`class`,`created_time`,`modified_time`) values (18,'matko@asdf.com','123','N','2015-09-05 09:14:07','2015-09-05 09:14:07');

/*Table structure for table `tbl_log` */

DROP TABLE IF EXISTS `tbl_log`;

CREATE TABLE `tbl_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `remote_addr` varchar(255) NOT NULL,
  `action_time` datetime NOT NULL,
  `action_type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_log` */

insert  into `tbl_log`(`id`,`email`,`remote_addr`,`action_time`,`action_type`) values (5,'matko@asdf.com','127.0.0.1','2015-09-05 09:14:07','customer created');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
