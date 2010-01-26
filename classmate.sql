-- phpMyAdmin SQL Dump
-- version 3.2.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 26, 2010 at 09:39 AM
-- Server version: 5.1.32
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `classmate`
--

-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_account_attributes`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_account_attributes` (
  `accountId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oit_tbl_account_attributes`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_api_log`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_api_log` (
  `apiLogId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` varchar(16) NOT NULL DEFAULT '',
  `function` varchar(64) NOT NULL DEFAULT '',
  `args` text NOT NULL,
  `message` varchar(255) NOT NULL DEFAULT '',
  `priority` varchar(16) NOT NULL DEFAULT '',
  `priorityName` varchar(64) NOT NULL DEFAULT '',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`apiLogId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_api_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_evaluation`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_evaluation` (
  `evaluationId` int(11) NOT NULL AUTO_INCREMENT,
  `eventId` int(11) NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`evaluationId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_evaluation`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_evaluation_user`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_evaluation_user` (
  `eventId` int(11) NOT NULL DEFAULT '0',
  `accountId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`eventId`,`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oit_tbl_evaluation_user`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_event`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_event` (
  `eventId` int(11) NOT NULL AUTO_INCREMENT,
  `workshopId` int(11) NOT NULL DEFAULT '0',
  `locationId` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `startTime` time NOT NULL DEFAULT '00:00:00',
  `endTime` time NOT NULL DEFAULT '00:00:00',
  `maxSize` int(11) NOT NULL DEFAULT '0',
  `minSize` int(11) NOT NULL DEFAULT '0',
  `roleSize` int(10) unsigned NOT NULL DEFAULT '0',
  `waitlistSize` int(11) NOT NULL DEFAULT '0',
  `waitlistTotal` int(11) NOT NULL DEFAULT '0',
  `totalAttendance` int(11) NOT NULL DEFAULT '0',
  `status` enum('open','closed','canceled') NOT NULL DEFAULT 'open',
  PRIMARY KEY (`eventId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_event`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_event_attendee`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_event_attendee` (
  `eventId` int(11) NOT NULL DEFAULT '0',
  `accountId` int(10) unsigned NOT NULL DEFAULT '0',
  `status` enum('attending','waitlist','canceled') NOT NULL DEFAULT 'attending',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `attended` binary(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eventId`,`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oit_tbl_event_attendee`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_event_instructor`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_event_instructor` (
  `eventId` int(11) NOT NULL DEFAULT '0',
  `accountId` int(11) unsigned NOT NULL DEFAULT '0',
  KEY `eventId` (`eventId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oit_tbl_event_instructor`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_event_restriction`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_event_restriction` (
  `eventId` int(11) NOT NULL DEFAULT '0',
  `realm` varchar(64) NOT NULL DEFAULT '',
  `users` longtext NOT NULL,
  PRIMARY KEY (`eventId`,`realm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oit_tbl_event_restriction`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_location`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_location` (
  `locationId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `description` mediumtext NOT NULL,
  `address` varchar(255) NOT NULL DEFAULT '',
  `capacity` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`locationId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_location`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_account`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_account` (
  `accountId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `realm` varchar(64) NOT NULL DEFAULT '',
  `password` varchar(128) NOT NULL DEFAULT '',
  `apiCode` varchar(255) NOT NULL DEFAULT '',
  `role` int(10) unsigned NOT NULL DEFAULT '0',
  `emailAddress` varchar(255) NOT NULL DEFAULT '',
  `firstName` varchar(64) NOT NULL DEFAULT '',
  `lastName` varchar(64) NOT NULL DEFAULT '',
  `timezone` varchar(32) NOT NULL DEFAULT '',
  `lastLogin` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`accountId`),
  UNIQUE KEY `username` (`username`,`realm`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `oit_tbl_ot_account`
--

INSERT INTO `oit_tbl_ot_account` (`accountId`, `username`, `realm`, `password`, `apiCode`, `role`, `emailAddress`, `firstName`, `lastName`, `timezone`, `lastLogin`) VALUES
(1, 'admin', 'local', '21232f297a57a5a743894a0e4a801fc3', '', 2, 'admin@admin.com', 'Admin', 'Mcadmin', 'America/New_York', 1264514562);

-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_auth_adapter`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_auth_adapter` (
  `adapterKey` varchar(24) NOT NULL,
  `class` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `displayOrder` int(11) NOT NULL,
  PRIMARY KEY (`adapterKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oit_tbl_ot_auth_adapter`
--

INSERT INTO `oit_tbl_ot_auth_adapter` (`adapterKey`, `class`, `name`, `description`, `enabled`, `displayOrder`) VALUES
('local', 'Ot_Auth_Adapter_Local', 'ClassMate Account', 'Authentication using an account created through ClassMate', 1, 1),
('wrap', 'Ot_Auth_Adapter_Wrap', 'NCSU Wrap', 'Authentication using your Unity ID and Password', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_bug`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_bug` (
  `bugId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL DEFAULT '',
  `submitDt` int(10) unsigned NOT NULL DEFAULT '0',
  `reproducibility` enum('always','sometimes','never') NOT NULL DEFAULT 'always',
  `severity` enum('minor','major','crash') NOT NULL DEFAULT 'minor',
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'low',
  `status` enum('new','ignore','escalated','fixed') NOT NULL DEFAULT 'new',
  PRIMARY KEY (`bugId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_ot_bug`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_bug_text`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_bug_text` (
  `bugTextId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bugId` int(10) unsigned NOT NULL DEFAULT '0',
  `accountId` int(10) unsigned NOT NULL DEFAULT '0',
  `postDt` int(10) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  PRIMARY KEY (`bugTextId`),
  KEY `bugId` (`bugId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_ot_bug_text`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_cron_status`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_cron_status` (
  `name` varchar(255) NOT NULL DEFAULT '',
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `lastRunDt` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oit_tbl_ot_cron_status`
--

INSERT INTO `oit_tbl_ot_cron_status` (`name`, `status`, `lastRunDt`) VALUES
('email-queue', 'enabled', 0),
('workshop-evaluation-reminder', 'enabled', 1256216722),
('workshop-signup-low-attendance', 'enabled', 0),
('workshop-signup-reminder', 'enabled', 1254492732);

-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_custom_attribute`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_custom_attribute` (
  `attributeId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `objectId` varchar(64) NOT NULL DEFAULT '',
  `label` text NOT NULL,
  `type` enum('text','textarea','radio','checkbox','select','ranking') CHARACTER SET utf8 NOT NULL DEFAULT 'text',
  `options` text NOT NULL,
  `required` binary(1) NOT NULL DEFAULT '\0',
  `direction` enum('vertical','horizontal') NOT NULL DEFAULT 'vertical',
  `order` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`attributeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_ot_custom_attribute`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_custom_attribute_value`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_custom_attribute_value` (
  `objectId` varchar(64) NOT NULL DEFAULT '',
  `parentId` varchar(255) NOT NULL DEFAULT '',
  `attributeId` int(11) NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  PRIMARY KEY (`objectId`,`parentId`,`attributeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oit_tbl_ot_custom_attribute_value`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_email_queue`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_email_queue` (
  `queueId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attributeName` varchar(128) NOT NULL DEFAULT '',
  `attributeId` int(10) unsigned NOT NULL DEFAULT '0',
  `zendMailObject` blob NOT NULL,
  `queueDt` int(10) unsigned NOT NULL DEFAULT '0',
  `sentDt` int(10) unsigned NOT NULL DEFAULT '0',
  `status` enum('waiting','sent','error') NOT NULL DEFAULT 'waiting',
  PRIMARY KEY (`queueId`),
  KEY `attributeName` (`attributeName`),
  KEY `attributeId` (`attributeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_ot_email_queue`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_log`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_log` (
  `logId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountId` int(10) unsigned NOT NULL DEFAULT '0',
  `role` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `request` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `sid` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `message` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `priority` int(10) unsigned NOT NULL DEFAULT '0',
  `priorityName` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `attributeName` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `attributeId` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`logId`),
  KEY `userId` (`accountId`),
  KEY `attributeName` (`attributeName`),
  KEY `attributeId` (`attributeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_ot_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_nav`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_nav` (
  `id` int(11) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `display` varchar(255) NOT NULL DEFAULT '',
  `module` varchar(255) NOT NULL DEFAULT '',
  `controller` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(255) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `target` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oit_tbl_ot_nav`
--

INSERT INTO `oit_tbl_ot_nav` (`id`, `parent`, `display`, `module`, `controller`, `action`, `link`, `target`) VALUES
(1, 0, 'Home', 'default', 'index', 'index', 'index/', '_self'),
(2, 0, 'My Events', 'default', 'index', 'history', 'index/history/', '_self'),
(3, 0, 'Schedule', 'workshop', 'schedule', 'index', 'workshop/schedule', '_self'),
(4, 0, 'Workshops', 'workshop', 'index', 'index', 'workshop/', '_self'),
(5, 4, 'Add Workshop', 'workshop', 'index', 'add', 'workshop/index/add/', '_self'),
(6, 4, 'Teaching Labs', 'workshop', 'location', 'index', 'workshop/location/', '_self'),
(7, 0, 'Admin', 'ot', 'index', 'index', '', ''),
(8, 7, 'Access', 'ot', 'index', 'index', '', '_blank'),
(9, 8, 'Registered Applications', 'ot', 'oauth', 'all-consumers', 'ot/oauth/all-consumers', '_self'),
(10, 8, 'Users', 'ot', 'account', 'all', 'ot/account/all', '_self'),
(11, 8, 'User Access Roles', 'ot', 'acl', 'index', 'ot/acl/index', '_self'),
(12, 7, 'Configuration', 'ot', 'index', 'index', '', ''),
(13, 12, 'App Config', 'ot', 'config', 'index', 'ot/config/index', '_self'),
(14, 12, 'App Triggers', 'ot', 'trigger', 'index', 'ot/trigger/index', '_self'),
(15, 12, 'Authentication Types', 'ot', 'auth', 'index', 'ot/auth', '_self'),
(16, 12, 'Custom Fields', 'ot', 'custom', 'index', 'ot/custom/index', '_self'),
(17, 12, 'Debug Mode', 'ot', 'debug', 'index', 'ot/debug', '_self'),
(18, 12, 'Maintenance Mode', 'ot', 'maintenance', 'index', 'ot/maintenance', '_self'),
(19, 12, 'Navigation Editor', 'ot', 'nav', 'index', 'ot/nav/index', '_self'),
(20, 12, 'Themes', 'ot', 'theme', 'index', 'ot/theme', '_self'),
(21, 7, 'Bug Reports', 'ot', 'bug', '', 'ot/bug', '_self'),
(22, 7, 'Caching', 'ot', 'cache', '', 'ot/cache', '_self'),
(23, 7, 'Cron Jobs', 'ot', 'cron', 'index', 'ot/cron/index', '_self'),
(24, 7, 'Database Backup', 'ot', 'backup', '', 'ot/backup', '_self'),
(25, 7, 'Email Queue', 'ot', 'emailqueue', 'index', 'ot/emailqueue/index', '_self'),
(26, 7, 'Logs', 'ot', 'log', 'index', 'ot/log/index', '_self'),
(27, 7, 'Reporting', 'reporting', 'index', 'index', 'reporting', '_self'),
(28, 7, 'Search Re-Indexing', 'search', 'index', 'reindex', 'search/index/reindex/', '_self'),
(29, 7, 'Version Information', 'ot', 'index', 'index', 'ot/index/index', '_self');

-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_oauth_client_token`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_oauth_client_token` (
  `consumerId` varchar(32) NOT NULL DEFAULT '',
  `accountId` int(10) unsigned NOT NULL DEFAULT '0',
  `token` varchar(255) NOT NULL DEFAULT '',
  `tokenSecret` varchar(255) NOT NULL DEFAULT '',
  `tokenType` enum('request','access') NOT NULL DEFAULT 'request',
  PRIMARY KEY (`consumerId`,`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oit_tbl_ot_oauth_client_token`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_oauth_server_consumer`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_oauth_server_consumer` (
  `consumerId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `imageId` int(10) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `website` varchar(255) NOT NULL DEFAULT '',
  `registeredAccountId` int(10) unsigned NOT NULL DEFAULT '0',
  `callbackUrl` varchar(255) NOT NULL DEFAULT '',
  `consumerKey` varchar(255) NOT NULL DEFAULT '',
  `consumerSecret` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`consumerId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_ot_oauth_server_consumer`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_oauth_server_nonce`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_oauth_server_nonce` (
  `nonceId` int(11) NOT NULL AUTO_INCREMENT,
  `consumerId` int(11) NOT NULL DEFAULT '0',
  `token` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `timestamp` bigint(20) NOT NULL DEFAULT '0',
  `nonce` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`nonceId`),
  UNIQUE KEY `osn_consumer_key` (`consumerId`,`token`,`timestamp`,`nonce`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_ot_oauth_server_nonce`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_oauth_server_token`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_oauth_server_token` (
  `tokenId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `consumerId` int(10) unsigned NOT NULL DEFAULT '0',
  `accountId` int(10) unsigned NOT NULL DEFAULT '0',
  `token` varchar(128) NOT NULL DEFAULT '',
  `tokenSecret` varchar(128) NOT NULL DEFAULT '',
  `tokenType` enum('request','access') NOT NULL DEFAULT 'request',
  `requestDt` int(10) unsigned NOT NULL DEFAULT '0',
  `authorized` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tokenId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_ot_oauth_server_token`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_role`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_role` (
  `roleId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `inheritRoleId` int(10) unsigned NOT NULL DEFAULT '0',
  `editable` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`roleId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `oit_tbl_ot_role`
--

INSERT INTO `oit_tbl_ot_role` (`roleId`, `name`, `inheritRoleId`, `editable`) VALUES
(1, 'guest', 0, 1),
(2, 'administrator', 0, 0),
(3, 'oit_ot_staff', 0, 0),
(15, 'authUser', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_role_rule`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_role_rule` (
  `ruleId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `roleId` int(11) NOT NULL DEFAULT '0',
  `type` enum('allow','deny') NOT NULL DEFAULT 'allow',
  `resource` varchar(64) NOT NULL DEFAULT '',
  `privilege` varchar(64) NOT NULL DEFAULT '',
  `scope` enum('application','remote') NOT NULL DEFAULT 'application',
  PRIMARY KEY (`ruleId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=176 ;

--
-- Dumping data for table `oit_tbl_ot_role_rule`
--

INSERT INTO `oit_tbl_ot_role_rule` (`ruleId`, `roleId`, `type`, `resource`, `privilege`, `scope`) VALUES
(8, 2, 'allow', '*', '*', 'application'),
(9, 3, 'allow', '*', '*', 'application'),
(46, 14, 'allow', 'admin_acl', 'index', 'application'),
(47, 14, 'allow', 'admin_bug', 'index', 'application'),
(48, 14, 'allow', 'admin_nav', 'index', 'application'),
(49, 14, 'allow', 'admin_translate', '*', 'application'),
(50, 14, 'allow', 'default_index', '*', 'application'),
(51, 14, 'allow', 'error_error', '*', 'application'),
(52, 14, 'allow', 'login_index', '*', 'application'),
(93, 1, 'allow', 'getVersions', '*', 'remote'),
(94, 1, 'deny', 'getConfigOption', '*', 'remote'),
(95, 1, 'deny', 'getMyAccount', '*', 'remote'),
(96, 1, 'deny', 'updateMyAccount', '*', 'remote'),
(97, 1, 'deny', 'getAccount', '*', 'remote'),
(98, 1, 'deny', 'updateAccount', '*', 'remote'),
(99, 1, 'deny', 'getCronJobs', '*', 'remote'),
(100, 1, 'deny', 'setCronJobStatus', '*', 'remote'),
(101, 1, 'deny', 'getBugReports', '*', 'remote'),
(102, 1, 'deny', 'describe', '*', 'remote'),
(118, 15, 'allow', 'account_index', 'add', 'application'),
(119, 15, 'allow', 'account_index', 'change-password', 'application'),
(120, 15, 'allow', 'account_index', 'edit', 'application'),
(121, 15, 'allow', 'account_index', 'index', 'application'),
(122, 15, 'allow', 'default_index', '*', 'application'),
(123, 15, 'allow', 'workshop_evaluate', '*', 'application'),
(124, 15, 'allow', 'workshop_index', 'add-document', 'application'),
(125, 15, 'allow', 'workshop_index', 'add-link', 'application'),
(126, 15, 'allow', 'workshop_index', 'delete-document', 'application'),
(127, 15, 'allow', 'workshop_index', 'delete-link', 'application'),
(128, 15, 'allow', 'workshop_index', 'edit', 'application'),
(129, 15, 'allow', 'workshop_index', 'edit-document', 'application'),
(130, 15, 'allow', 'workshop_index', 'edit-link', 'application'),
(131, 15, 'allow', 'workshop_instructor', 'add-attendee', 'application'),
(132, 15, 'allow', 'workshop_instructor', 'contact', 'application'),
(133, 15, 'allow', 'workshop_instructor', 'evaluation-results', 'application'),
(134, 15, 'allow', 'workshop_instructor', 'index', 'application'),
(135, 15, 'allow', 'workshop_instructor', 'print-signup-sheet', 'application'),
(136, 15, 'allow', 'workshop_instructor', 'promote-attendee', 'application'),
(137, 15, 'allow', 'workshop_instructor', 'remove-attendee', 'application'),
(138, 15, 'allow', 'workshop_instructor', 'take-roll', 'application'),
(139, 15, 'allow', 'workshop_schedule', 'edit-event', 'application'),
(140, 15, 'allow', 'workshop_signup', 'cancel', 'application'),
(141, 15, 'allow', 'workshop_signup', 'index', 'application'),
(142, 15, 'allow', 'workshop_signup', 'reserve', 'application'),
(159, 1, 'allow', 'api_index', '*', 'application'),
(160, 1, 'allow', 'cron_index', '*', 'application'),
(161, 1, 'allow', 'default_index', 'index', 'application'),
(162, 1, 'allow', 'default_migration', '*', 'application'),
(163, 1, 'allow', 'error_error', '*', 'application'),
(164, 1, 'allow', 'login_index', '*', 'application'),
(165, 1, 'allow', 'search_index', 'tag', 'application'),
(166, 1, 'allow', 'workshop_index', 'details', 'application'),
(167, 1, 'allow', 'workshop_index', 'download-document', 'application'),
(168, 1, 'allow', 'workshop_index', 'download-handouts', 'application'),
(169, 1, 'allow', 'workshop_index', 'index', 'application'),
(170, 1, 'allow', 'workshop_index', 'workshop-list', 'application'),
(171, 1, 'allow', 'workshop_location', 'details', 'application'),
(172, 1, 'allow', 'workshop_location', 'index', 'application'),
(173, 1, 'allow', 'workshop_schedule', 'event-details', 'application'),
(174, 1, 'allow', 'workshop_schedule', 'get-events', 'application'),
(175, 1, 'allow', 'workshop_schedule', 'index', 'application');

-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_trigger_action`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_trigger_action` (
  `triggerActionId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `triggerId` varchar(64) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT '',
  `helper` varchar(64) NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`triggerActionId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=106 ;

--
-- Dumping data for table `oit_tbl_ot_trigger_action`
--

INSERT INTO `oit_tbl_ot_trigger_action` (`triggerActionId`, `triggerId`, `name`, `helper`, `enabled`) VALUES
(14, 'Login_Index_Signup', 'Signup for an account', 'Ot_Trigger_Plugin_Email', 1),
(15, 'Login_Index_Forgot', 'User forgot password', 'Ot_Trigger_Plugin_Email', 1),
(16, 'Admin_Account_Create_Password', 'Admin created account', 'Ot_Trigger_Plugin_Email', 1),
(17, 'Admin_Account_Create_NoPassword', 'When a WRAP account gets created', 'Ot_Trigger_Plugin_Email', 1),
(18, 'User_Automatically_Moved_From_Waitlist_To_Attending', 'Student Email', 'Ot_Trigger_Plugin_EmailQueue', 1),
(19, 'User_Automatically_Moved_From_Waitlist_To_Attending', 'Instructor Email', 'Ot_Trigger_Plugin_EmailQueue', 1),
(20, 'Instructor_Cancels_Users_Reservation', 'Instructor canceled student registration', 'Ot_Trigger_Plugin_EmailQueue', 1),
(21, 'Instructor_Registered_User', 'Email to Student', 'Ot_Trigger_Plugin_EmailQueue', 1),
(22, 'Instructor_Registered_User_For_Waitlist', 'Email to Student', 'Ot_Trigger_Plugin_EmailQueue', 1),
(23, 'Instructor_Promote_User_Waitlist_To_Attending', 'Email to Student', 'Ot_Trigger_Plugin_EmailQueue', 1),
(87, 'Event_Signup', 'Sign up for an Event', 'Ot_Trigger_Plugin_EmailQueue', 1),
(88, 'Event_Signup_Waitlist', 'You are on the wait list', 'Ot_Trigger_Plugin_EmailQueue', 1),
(89, 'Event_Signup', 'Instructor notification', 'Ot_Trigger_Plugin_EmailQueue', 1),
(90, 'Event_Cancel_Reservation', 'Student Cancel', 'Ot_Trigger_Plugin_EmailQueue', 1),
(91, 'Event_Cancel_Reservation', 'Instructor notification', 'Ot_Trigger_Plugin_EmailQueue', 1),
(92, 'Event_Waitlist_To_Attending', 'Student Email', 'Ot_Trigger_Plugin_EmailQueue', 1),
(93, 'Event_Waitlist_To_Attending', 'Instructor notification', 'Ot_Trigger_Plugin_EmailQueue', 1),
(94, 'Instructor_Cancels_Users_Reservation', 'instructor canceled student registration', 'Ot_Trigger_Plugin_EmailQueue', 1),
(95, 'Instructor_Registered_User', 'student email', 'Ot_Trigger_Plugin_EmailQueue', 1),
(96, 'Instructor_Registered_User_For_Waitlist', 'student email', 'Ot_Trigger_Plugin_EmailQueue', 1),
(97, 'Instructor_Promote_User_Waitlist_To_Attending', 'Student email', 'Ot_Trigger_Plugin_EmailQueue', 1),
(98, 'Event_Signup_Waitlist', 'Instructor notification', 'Ot_Trigger_Plugin_EmailQueue', 1),
(99, 'Workshop_Add', 'WorkshopAdded', 'Ot_Trigger_Plugin_EmailQueue', 1),
(100, 'Event_Attendee_Final_Reminder', 'final email', 'Ot_Trigger_Plugin_EmailQueue', 1),
(101, 'Event_Attendee_First_Reminder', 'first email', 'Ot_Trigger_Plugin_EmailQueue', 1),
(102, 'Event_Evaluation_Reminder', 'evaluation reminder', 'Ot_Trigger_Plugin_EmailQueue', 1),
(103, 'Event_Instructor_Final_Reminder', 'instructor notification', 'Ot_Trigger_Plugin_EmailQueue', 1),
(104, 'Event_Instructor_First_Reminder', 'instructor notification', 'Ot_Trigger_Plugin_EmailQueue', 1),
(105, 'Event_LowAttendance', 'Instructor notification', 'Ot_Trigger_Plugin_EmailQueue', 1);

-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_trigger_helper_email`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_trigger_helper_email` (
  `triggerActionId` int(11) NOT NULL DEFAULT '0',
  `to` varchar(255) NOT NULL DEFAULT '',
  `from` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  PRIMARY KEY (`triggerActionId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oit_tbl_ot_trigger_helper_email`
--

INSERT INTO `oit_tbl_ot_trigger_helper_email` (`triggerActionId`, `to`, `from`, `subject`, `body`) VALUES
(14, '[[emailAddress]]', 'webapps_admin@ncsu.edu', 'Thanks for signing up!', 'Hey [[firstName]]!  Welcome to The System.\r\n\r\nYour user id:  [[username]]\r\nYou password: [[password]]'),
(15, '[[emailAddress]]', 'admin@webapps.ncsu.edu', 'Your password has been reset', 'Thanks [[firstName]] [[lastName]]\r\n\r\nYour password for [[username]] has been reset.  Go here [[resetUrl]] to change your password.'),
(16, '[[emailAddress]]', 'admin@webapps.ncsu.edu', 'You''ve been given an account', 'Hey [[firstName]], You''ve been given a(n) [[role]] account!\r\n\r\n[[username]]\r\n[[password]]'),
(17, '[[emailAddress]]', 'admin@webapps.ncsu.edu', 'You''ve got a new account!', 'Hey [[firstName]] [[lastName]]\r\n\r\nYou''ve been given a new [[role]] [[loginMethod]] account.\r\n\r\nYour username is [[username]]');

-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_ot_trigger_helper_emailqueue`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_ot_trigger_helper_emailqueue` (
  `triggerActionId` int(11) NOT NULL DEFAULT '0',
  `to` varchar(255) NOT NULL DEFAULT '',
  `from` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  PRIMARY KEY (`triggerActionId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oit_tbl_ot_trigger_helper_emailqueue`
--

INSERT INTO `oit_tbl_ot_trigger_helper_emailqueue` (`triggerActionId`, `to`, `from`, `subject`, `body`) VALUES
(18, '[[studentEmail]]', 'classreg@ncsu.edu', 'OIT Workshops Confirmation: [[workshopName]]', 'Dear [[studentFirstName]],\r\n\r\nYou have been moved off of the waitlist and onto the roll for the [[workshopName]] workshop. The details of your workshop registration are below:\r\n\r\n[[workshopName]]\r\n[[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\nInstructors:  [[instructorNames]] ([[instructorEmails]])\r\n\r\nParticipants are expected to print and bring a copy of the workshop handouts to class. Workshop materials can be found by clicking the My Appointments link in our Classmate Registration system. If your workshop does not have a link for handouts, any materials for that particular workshop will be provided by the instructor at the time of the class. Thank you in advance for your co-operation.\r\n\r\nYou will receive a reminder prior to the workshop. If you are unable to attend this workshop, please use our online registration system to cancel the workshop from your schedule immediately so that others may register for your seat.\r\n\r\n\r\nNOTE:  If less than [[workshopMinimumEnrollment]] people register for this workshop it may be canceled.  In this event, you will be notified in advance by email.\r\n\r\n-- OIT Training Coordinator\r\nclassreg@ncsu.edu\r\n\r\nTo return to OIT''s Classmate Registration System, click the underlined link below. If your email system does not support URL links, you can copy this link and paste it into your web browser.\r\n\r\nhttp://webapps.ncsu.edu/classmate\r\n\r\nThank You!'),
(19, '[[instructorEmails]]', 'classreg@ncsu.edu', 'Instructor Waitlist Advancement Notification:  [[workshopName]]', '[[instructorNames]],\r\n\r\nThe following student has been moved from the waitlist and onto the class role for "[[workshopName]]":\r\n\r\n[[studentFirstName]] [[studentLastName]]\r\n[[studentEmail]]\r\n\r\nThank You\r\nOIT Education'),
(20, '[[studentEmail]]', 'classreg@ncsu.edu', 'OIT Workshop Registration Canceled', 'Dear [[studentFirstName]],\r\n\r\nYour reservation for the workshop "[[workshopName]]" scheduled for [[workshopDate]] has been canceled by the instructor.  If you have questions or comments please send OIT Workshops a note at classreg@ncsu.edu.\r\n\r\nThanks,\r\nTwanda  Baker\r\nOIT Training Coordinator\r\nclassreg@ncsu.edu\r\n\r\nTo return to OIT''s Classmate Registration System, click the underlined link below. If your email system does not support URL links, you can copy this link and paste it into your web browser.\r\n\r\nhttp://webapps.ncsu.edu/classmate'),
(21, '[[studentEmail]]', 'classreg@ncsu.edu', 'Signup Confirmation: [[workshopName]]', '[[studentFirstName]],\r\n\r\nYou have been signed up for the following workshop by its instructor:\r\n\r\n[[workshopName]]\r\n[[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\nInstructors:  [[instructorNames]] ([[instructorEmails]])\r\n\r\nThe minimum enrollment for this class is [[workshopMinimumEnrollment]].  If less than [[workshopMinimumEnrollment]] people sign up for this class, it may be canceled.  You will be notified via email if this happens.\r\n\r\nThank You\r\nOIT Education'),
(22, '[[studentEmail]]', 'classreg@ncsu.edu', 'Waitlist Confirmation:  [[workshopName]]', '[[studentFirstName]],\r\n\r\nYou have successfully been put on the waitlist by the instructor for the following workshop:\r\n\r\n[[workshopName]]\r\n[[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\nInstructors:  [[instructorNames]] ([[instructorEmails]])\r\n\r\nIf someone who is attending the workshop cancels, your position on the waitlist will automatically be updated.  If you get put into the class, you will be notified via email.\r\n\r\nThank You\r\nOIT Education'),
(23, '[[studentEmail]]', 'classreg@ncsu.edu', 'You have been put into the workshop [[workshopName]]', '[[studentFirstName]],\r\n\r\nWhen you signed up for "[[workshopName]]", you were put on the waitlist.  You have now been moved into the workshop by the instructor.\r\n\r\n[[workshopName]]\r\n[[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\nInstructors:  [[instructorNames]] ([[instructorEmails]])\r\n\r\nIf you need to cancel, you can.\r\n\r\nThank You\r\nOIT Education'),
(87, '[[studentEmail]]', 'classreg@ncsu.edu', 'OIT Workshops Confirmation: [[workshopName]]', 'Dear [[studentName]],\r\nThank you for booking an appointment with OIT Workshops. The details of your appointment are below:\r\n\r\nWorkshop: [[workshopName]]\r\nDate: [[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\nInstructors:  [[instructorNames]] ([[instructorEmails]])\r\n\r\nParticipants are expected to print and bring a copy of the workshop handouts to class. Workshop materials can be found by clicking the My Appointments link in our Classmate Registration system. If your workshop does not have a link for handsout, any materials for that particular workshop will be provided by the instructor at the time of the class. Thank you in advance for your co-operation.\r\n\r\nYou will receive a reminder prior to the workshop. If you are unable to attend this workshop, please use our online registration system to cancel the workshop from your schedule immediately so that others may register for your seat.\r\n\r\n\r\nNOTE:  If less than [[workshopMinimumEnrollment]] people register for this workshop it may be canceled.  In this event, you will be notified in advance by email.\r\n\r\n-- OIT Training Coordinator\r\nclassreg@ncsu.edu\r\n\r\nTo return to OIT''s Classmate Registration System, click the underlined link below. If your email system does not support URL links, you can copy this link and paste it into your web browser.\r\n\r\nhttp://webapps.ncsu.edu/classmate\r\n\r\nThank You'),
(88, '[[studentEmail]]', 'classreg@ncsu.edu', 'Waitlist Confirmation:  [[workshopName]]', 'Dear [[studentName]],\r\n\r\nYou have been placed on a waitlist for the following workshop:\r\n\r\n[[workshopName]]\r\n[[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\nInstructors:  [[instructorNames]] ([[instructorEmails]])\r\n\r\nYou are number [[waitlistPosition]] on the waitlist. Your position will automatically be updated as space becomes available.  If you are moved off of the list and into the class, you will be notified via email.\r\n\r\nThank You,\r\nTwanda Baker\r\nOIT Training Coordinator'),
(89, '[[instructorEmails]]', 'classreg@ncsu.edu', 'Instructor Signup Notification:  [[workshopName]]', '[[instructorNames]],\r\n\r\nThe following student has signed up for your workshop "[[workshopName]]" on [[workshopDate]]:\r\n\r\n[[studentName]]\r\n[[studentEmail]]\r\n\r\n\r\n\r\nThank You\r\nOIT Training Coordinator'),
(90, '[[studentEmail]]', 'classreg@ncsu.edu', 'OIT Workshops Cancellation: [[workshopName]]', 'Dear [[studentName]],\r\n\r\nYour reservation for the workshop "[[workshopName]]" scheduled for [[workshopDate]] has been canceled.  \r\n\r\nThanks,\r\nTwanda Baker\r\nOIT Training Coordinator\r\nclassreg@ncsu.edu\r\n\r\nTo return to OIT''s Classmate Registration System, click the underlined link below. If your email system does not support URL links, you can copy this link and paste it into your web browser.\r\n\r\nhttp://webapps.ncsu.edu/classmate'),
(91, '[[instructorEmails]]', 'classreg@ncsu.edu', 'Instructor Cancellation Notification:  [[workshopName]]', '[[instructorNames]],\r\n\r\nFYI - \r\nUser [[studentName]] canceled their reservation for [[workshopName]] scheduled for [[workshopDate]].\r\n\r\n\r\nTwanda Baker\r\nOIT Training Coordinator\r\nTwanda_Baker@ncsu.edu'),
(92, '[[studentEmail]]', 'classreg@ncsu.edu', 'OIT Workshops Confirmation: [[workshopName]]', 'Dear [[studentName]],\r\n\r\nYou have been moved off of the waitlist and onto the roll for the [[workshopName]] workshop. The details of your workshop registration are below:\r\n\r\n[[workshopName]]\r\n[[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\nInstructors:  [[instructorNames]] ([[instructorEmails]])\r\n\r\nParticipants are expected to print and bring a copy of the workshop handouts to class. Workshop materials can be found by clicking the My Appointments link in our Classmate Registration system. If your workshop does not have a link for handsout, any materials for that particular workshop will be provided by the instructor at the time of the class. Thank you in advance for your co-operation.\r\n\r\nYou will receive a reminder prior to the workshop. If you are unable to attend this workshop, please use our online registration system to cancel the workshop from your schedule immediately so that others may register for your seat.\r\n\r\n\r\nNOTE:  If less than [[workshopMinimumEnrollment]] people register for this workshop it may be canceled.  In this event, you will be notified in advance by email.\r\n\r\n-- OIT Training Coordinator\r\nclassreg@ncsu.edu\r\n\r\nTo return to OIT''s Classmate Registration System, click the underlined link below. If your email system does not support URL links, you can copy this link and paste it into your web browser.\r\n\r\nhttp://webapps.ncsu.edu/classmate\r\n\r\nThank You!'),
(93, '[[instructorEmails]]', 'classreg@ncsu.edu', 'Instructor Waitlist Advancement Notification:  [[workshopName]]', '[[instructorNames]],\r\n\r\nThe following student has been moved from the waitlist and onto the class role for "[[workshopName]]":\r\n\r\n[[studentName]]\r\n[[studentEmail]]\r\n\r\nThank You\r\nOIT Education'),
(94, '[[studentEmail]]', 'classreg@ncsu.edu', 'OIT Workshop Registration Canceled', 'Dear [[studentFirstName]],\r\n\r\nYour reservation for the workshop "[[workshopName]]" scheduled for [[workshopDate]] has been canceled by the instructor.  If you have questions or comments please send OIT Workshops a note at classreg@ncsu.edu.\r\n\r\nThanks,\r\nTwanda  Baker\r\nOIT Training Coordinator\r\nclassreg@ncsu.edu\r\n\r\nTo return to OIT''s Classmate Registration System, click the underlined link below. If your email system does not support URL links, you can copy this link and paste it into your web browser.\r\n\r\nhttp://webapps.ncsu.edu/classmate'),
(95, '[[studentEmail]]', 'classreg@ncsu.edu', 'Signup Confirmation: [[workshopName]]', '[[studentName]],\r\n\r\nYou have been signed up for the following workshop by its instructor:\r\n\r\n[[workshopName]]\r\n[[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\nInstructors:  [[instructorNames]] ([[instructorEmails]])\r\n\r\nThe minimum enrollment for this class is [[workshopMinimumEnrollment]].  If less than [[workshopMinimumEnrollment]] people sign up for this class, it may be canceled.  You will be notified via email if this happens.\r\n\r\nThank You\r\nOIT Education'),
(96, '[[studentEmail]]', 'classreg@ncsu.edu', 'Waitlist Confirmation:  [[workshopName]]', '[[studentName]],\r\n\r\nYou have successfully been put on the waitlist by the instructor for the following workshop:\r\n\r\n[[workshopName]]\r\n[[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\nInstructors:  [[instructorNames]] ([[instructorEmails]])\r\n\r\nYou are number [[waitlistPosition]] on the waitlist.  If someone who is attending the workshop cancels, your position will automatically be updated.  If you get put into the class, you will be notified via email.\r\n\r\nThank You\r\nOIT Education'),
(97, '[[studentEmail]]', 'classreg@ncsu.edu', 'You have been put into the workshop [[workshopName]]', '[[studentName]],\r\n\r\nWhen you signed up for "[[workshopName]]", you were put on the waitlist.  You have now been moved into the workshop by the instructor.\r\n\r\n[[workshopName]]\r\n[[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\nInstructors:  [[instructorNames]] ([[instructorEmails]])\r\n\r\nIf you need to cancel, you can.\r\n\r\nThank You\r\nOIT Education'),
(98, '[[instructorEmails]]', 'classreg@ncsu.edu', 'Instructor Waitlist Notification: [[workshopName]]', '[[instructorNames]],\r\n\r\nThe following registrant has been put on the waitlist for: [[workshopName]] on [[workshopDate]].\r\n\r\n[[studentName]]\r\n[[studentEmail]]\r\n\r\nThank You,\r\nTwanda Baker\r\nOIT Training Coordinator'),
(99, 'tjbaker@ncsu.edu', 'classreg@ncsu.edu', '[ClassMate Notification]', 'The following workshop has just been added to ClassMate.\r\nTitle:  [[title]] \r\nDescription:  [[description]]\r\nPre-reqs:  [[prerequisites]]'),
(100, '[[studentEmail]]', 'classreg@ncsu.edu', 'OIT Workshops [[workshopName]]', 'Dear [[studentName]],\r\n\r\nWe are excited to have you in our upcoming workshop:\r\n\r\nWorkshop: [[workshopName]]\r\nDate: [[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\nInstructors:  [[instructorNames]] ([[instructorEmails]])\r\n\r\n\r\nIf there are handouts available for this workshop, participants are expected to print and bring a copy to class. Please check the "Workshop Handous" section on the description page of this workshop. \r\n\r\nThank you in advance for your co-operation.\r\n\r\nTwanda Baker\r\nOIT Training Coordinator\r\nclassreg@ncsu.edu\r\n\r\n\r\nTo return to OIT''s Classmate Registration System, click the underlined link below. If your email system does not support URL links, you can copy this link and paste it into your web browser.\r\n\r\nhttp://webapps.ncsu.edu/classmate'),
(101, '[[studentEmail]]', 'classreg@ncsu.edu', 'OIT Workshop Reminder: [[workshopName]]', 'Dear [[studentName]],\r\n\r\nThis is a friendly reminder that you are signed up to attend the following workshop:\r\n\r\nWorkshop: [[workshopName]]\r\nDate: [[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\nInstructor(s):  [[instructorNames]] ([[instructorEmails]])\r\n\r\nIf you are unable to attend, please use the ClassMate registration system to cancel the workshop from your schedule immediately so that others may register for your seat.\r\n\r\nParticipants are expected to print and bring a copy of the workshop handouts to class, if applicable. Please check the "Workshop Handouts" section of workshop''s description page on ClassMate. Thank you in advance for your co-operation.\r\n\r\nWe look forward to seeing you!\r\n\r\nThanks,\r\nTwanda Baker\r\nOIT Training Coordinator\r\nclassreg@ncsu.edu\r\n\r\n\r\nTo return to OIT''s Classmate Registration System, click the underlined link below. If your email system does not support URL links, you can copy this link and paste it into your web browser.\r\n\r\nhttp://webapps.ncsu.edu/classmate'),
(102, '[[studentEmail]]', 'classreg@ncsu.edu', 'Evaluation Reminder:  [[workshopName]]', 'Dear [[studentName]],\r\n\r\nYour feedback is important to us. Please visit the our website and complete your evaluation of the following workshop:\r\n\r\nWorkshop: [[workshopName]]\r\nDate: [[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\nInstructors:  [[instructorNames]] ([[instructorEmails]])\r\n\r\n\r\nThank you!\r\nTwanda Baker\r\nOIT Training Coordinator\r\nclassreg@ncsu.edu\r\n\r\n\r\nTo return to OIT''s Classmate Registration System, click the underlined link below. If your email system does not support URL links, you can copy this link and paste it into your web browser.\r\n\r\nhttp://webapps.ncsu.edu/classmate'),
(103, '[[instructorEmails]]', 'classreg@ncsu.edu', 'Instructor Reminder:  You are teaching [[workshopName]]', 'Dear Instructors,\r\n\r\nThis is a friendly reminder that you are signed up to teach the following workshop:\r\n\r\nWorkshop: [[workshopName]]\r\nDate: [[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\n\r\nThere are currently [[workshopCurrentEnrollment]] students signed up to take your workshop.\r\n\r\nThanks\r\nOIT Education'),
(104, '[[instructorEmails]]', 'classreg@ncsu.edu', 'Instructor Reminder:  You are teaching [[workshopName]]', 'Dear Instructors,\r\n\r\nThis is a friendly reminder that you are signed up to teach the following workshop:\r\n\r\nWorkshop: [[workshopName]]\r\nDate: [[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\n\r\nThere are currently [[workshopCurrentEnrollment]] students signed up to take your workshop.\r\n\r\nThanks\r\nOIT Education'),
(105, '[[instructorEmails]], classreg@ncsu.edu, twanda_baker@ncsu.edu,gail_rankin@ncsu.edu', 'classreg@ncsu.edu', 'Low Attendance for [[workshopName]]', 'Dear Instructor(s),\r\n\r\nThe attendance for the following workshop is lower than the minimum requirement:\r\n\r\nWorkshop: [[workshopName]]\r\nDate: [[workshopDate]] from [[workshopStartTime]] to [[workshopEndTime]]\r\nLocation:  [[locationName]]  ([[locationAddress]])\r\n\r\nThere are currently [[workshopCurrentEnrollment]] of the required [[workshopMinimumEnrollment]] students signed up to take your workshop.\r\n\r\n\r\nThanks\r\nOIT Education');

-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_search_term`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_search_term` (
  `term` varchar(255) NOT NULL DEFAULT '',
  `count` int(10) unsigned NOT NULL DEFAULT '0',
  `last` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`term`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oit_tbl_search_term`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_tag`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_tag` (
  `tagId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`tagId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_tag`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_tag_map`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_tag_map` (
  `attributeName` varchar(64) NOT NULL DEFAULT '',
  `attributeId` varchar(64) NOT NULL DEFAULT '',
  `tagId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`attributeName`,`attributeId`,`tagId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oit_tbl_tag_map`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_workshop`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_workshop` (
  `workshopId` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `prerequisites` mediumtext NOT NULL,
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `featured` binary(1) NOT NULL DEFAULT '0',
  `description` longtext NOT NULL,
  PRIMARY KEY (`workshopId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_workshop`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_workshop_document`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_workshop_document` (
  `workshopDocumentId` int(11) NOT NULL AUTO_INCREMENT,
  `workshopId` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `uploadDt` int(10) unsigned NOT NULL DEFAULT '0',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`workshopDocumentId`),
  KEY `workshopId` (`workshopId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_workshop_document`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_workshop_editor`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_workshop_editor` (
  `workshopId` int(10) unsigned NOT NULL DEFAULT '0',
  `accountId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`workshopId`,`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oit_tbl_workshop_editor`
--


-- --------------------------------------------------------

--
-- Table structure for table `oit_tbl_workshop_link`
--

CREATE TABLE IF NOT EXISTS `oit_tbl_workshop_link` (
  `workshopLinkId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `workshopId` int(10) unsigned NOT NULL DEFAULT '0',
  `url` mediumtext NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `order` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`workshopLinkId`),
  KEY `workshopId` (`workshopId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oit_tbl_workshop_link`
--

