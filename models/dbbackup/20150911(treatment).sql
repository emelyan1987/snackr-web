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

/*Table structure for table `tbl_treatment` */

DROP TABLE IF EXISTS `tbl_treatment`;

CREATE TABLE `tbl_treatment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `dish_id` bigint(20) NOT NULL,
  `action` char(1) NOT NULL COMMENT '(''L'':like,''D'':dislike,''N'':never see)',
  `created_time` datetime DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_treatment` */

insert  into `tbl_treatment`(`id`,`email`,`dish_id`,`action`,`created_time`,`modified_time`) values (30,'matko@asdf.com',10,'L','2015-09-11 10:02:52','2015-09-11 11:44:18'),(31,'matko@asdf.com',14,'L','2015-09-11 10:03:21','2015-09-11 11:44:16'),(32,'matko@asdf.com',15,'L','2015-09-11 10:04:12','2015-09-11 11:44:15'),(33,'matko@asdf.com',16,'L','2015-09-11 10:04:25','2015-09-11 11:44:14'),(34,'emelyan@gmail.com',7,'N','2015-09-11 10:04:51','2015-09-11 11:39:29'),(35,'emelyan@gmail.com',6,'N','2015-09-11 10:05:57','2015-09-11 11:40:41'),(36,'emelyan@gmail.com',5,'N','2015-09-11 10:06:07','2015-09-11 11:39:30'),(37,'emelyan@gmail.com',4,'N','2015-09-11 10:06:14','2015-09-11 11:41:13'),(38,'emelyan@gmail.com',3,'N','2015-09-11 10:06:15','2015-09-11 11:39:31'),(39,'emelyan@gmail.com',2,'N','2015-09-11 10:06:23','2015-09-11 11:40:41'),(40,'emelyan@gmail.com',1,'N','2015-09-11 10:06:39','2015-09-11 11:39:33'),(41,'lajbaher@asdf.com',23,'L','2015-09-11 10:07:26','2015-09-11 10:07:26'),(42,'lajbaher@asdf.com',22,'L','2015-09-11 10:07:27','2015-09-11 10:07:27'),(43,'matko@asdf.com',21,'L','2015-09-11 10:07:28','2015-09-11 11:44:13'),(44,'lajbaher@asdf.com',20,'L','2015-09-11 10:07:29','2015-09-11 10:07:29'),(45,'lajbaher@asdf.com',19,'L','2015-09-11 10:07:29','2015-09-11 10:07:29'),(46,'lajbaher@asdf.com',18,'L','2015-09-11 10:07:30','2015-09-11 10:07:30'),(47,'lajbaher@asdf.com',17,'L','2015-09-11 10:07:31','2015-09-11 10:07:31'),(48,'lajbaher@asdf.com',16,'L','2015-09-11 10:07:31','2015-09-11 10:07:31'),(49,'lajbaher@asdf.com',15,'L','2015-09-11 10:07:32','2015-09-11 10:07:32'),(50,'lajbaher@asdf.com',14,'L','2015-09-11 10:07:33','2015-09-11 10:07:33'),(51,'lajbaher@asdf.com',13,'L','2015-09-11 10:07:40','2015-09-11 10:07:40'),(52,'lajbaher@asdf.com',12,'L','2015-09-11 10:07:42','2015-09-11 10:07:42'),(53,'lajbaher@asdf.com',11,'L','2015-09-11 10:07:43','2015-09-11 10:07:43'),(54,'lajbaher@asdf.com',10,'L','2015-09-11 10:07:52','2015-09-11 10:07:52'),(55,'lajbaher@asdf.com',9,'L','2015-09-11 10:07:54','2015-09-11 10:07:54'),(56,'lajbaher@asdf.com',8,'L','2015-09-11 10:07:55','2015-09-11 10:07:55'),(57,'lajbaher@asdf.com',7,'L','2015-09-11 10:09:26','2015-09-11 10:09:26'),(58,'lajbaher@asdf.com',6,'L','2015-09-11 10:09:27','2015-09-11 10:09:27'),(59,'emelyan@gmail.com',23,'N','2015-09-11 10:58:34','2015-09-11 11:40:30'),(60,'emelyan@gmail.com',22,'N','2015-09-11 10:58:35','2015-09-11 11:40:31'),(61,'emelyan@gmail.com',21,'L','2015-09-11 10:58:38','2015-09-11 11:41:47'),(62,'emelyan@gmail.com',20,'L','2015-09-11 10:58:39','2015-09-11 11:41:48'),(63,'emelyan@gmail.com',19,'L','2015-09-11 10:58:42','2015-09-11 11:41:50'),(64,'emelyan@gmail.com',18,'L','2015-09-11 10:58:43','2015-09-11 11:41:51'),(65,'emelyan@gmail.com',17,'L','2015-09-11 10:58:46','2015-09-11 11:41:52'),(66,'emelyan@gmail.com',16,'L','2015-09-11 10:58:47','2015-09-11 11:41:54'),(67,'emelyan@gmail.com',15,'L','2015-09-11 10:58:51','2015-09-11 11:41:55'),(68,'emelyan@gmail.com',14,'L','2015-09-11 10:58:54','2015-09-11 11:41:56'),(69,'emelyan@gmail.com',13,'N','2015-09-11 10:58:57','2015-09-11 11:39:23'),(70,'emelyan@gmail.com',12,'L','2015-09-11 10:58:58','2015-09-11 11:41:57'),(71,'emelyan@gmail.com',11,'L','2015-09-11 10:59:00','2015-09-11 11:41:58'),(72,'emelyan@gmail.com',10,'L','2015-09-11 10:59:01','2015-09-11 11:42:00'),(73,'emelyan@gmail.com',9,'N','2015-09-11 10:59:09','2015-09-11 11:40:40'),(74,'emelyan@gmail.com',8,'N','2015-09-11 10:59:10','2015-09-11 11:39:28'),(75,'emelyan@gmail.com',0,'L','2015-09-11 11:17:12','2015-09-11 11:38:22');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
