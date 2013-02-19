<?php

/**
 * Adds support for the 'description' custom attribute
 *
 */
class Db_002_classmate_initial_structure extends Ot_Migrate_Migration_Abstract
{
    public function up($dba)
    {
        if (APPLICATION_ENV == 'production') {
            return;
        }
        
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->tablePrefix . "tbl_evaluation` (
            `evaluationId` int(11) NOT NULL auto_increment,
            `eventId` int(11) NOT NULL default '0',
            `timestamp` int(11) NOT NULL default '0',
            PRIMARY KEY  (`evaluationId`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $dba->query($query);
        
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->tablePrefix . "tbl_evaluation_user` (
            `eventId` int(11) NOT NULL default '0',
            `accountId` int(10) unsigned NOT NULL default '0',
            PRIMARY KEY  (`eventId`,`accountId`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $dba->query($query);
        
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->tablePrefix . "tbl_event` (
            `eventId` int(11) NOT NULL auto_increment,
            `workshopId` int(11) NOT NULL default '0',
            `locationId` int(11) NOT NULL default '0',
            `date` date NOT NULL default '0000-00-00',
            `startTime` time NOT NULL default '00:00:00',
            `endTime` time NOT NULL default '00:00:00',
            `maxSize` int(11) NOT NULL default '0',
            `minSize` int(11) NOT NULL default '0',
            `roleSize` int(10) unsigned NOT NULL default '0',
            `waitlistSize` int(11) NOT NULL default '0',
            `waitlistTotal` int(11) NOT NULL default '0',
            `totalAttendance` int(11) NOT NULL default '0',
            `status` enum('open','closed','canceled') NOT NULL default 'open',
            PRIMARY KEY  (`eventId`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $dba->query($query);
        
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->tablePrefix . "tbl_event_attendee` (
            `eventId` int(11) NOT NULL default '0',
            `accountId` int(10) unsigned NOT NULL default '0',
            `status` enum('attending','waitlist','canceled') NOT NULL default 'attending',
            `timestamp` int(11) NOT NULL default '0',
            `attended` binary(1) NOT NULL default '0',
            PRIMARY KEY  (`eventId`,`accountId`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $dba->query($query);
        
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->tablePrefix . "tbl_event_instructor` (
            `eventId` int(11) NOT NULL default '0',
            `accountId` int(11) unsigned NOT NULL default '0',
            KEY `eventId` (`eventId`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $dba->query($query);
        
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->tablePrefix . "tbl_event_restriction` (
            `eventId` int(11) NOT NULL default '0',
            `realm` varchar(64) NOT NULL default '',
            `users` longtext NOT NULL,
            PRIMARY KEY  (`eventId`,`realm`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $dba->query($query);
        
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->tablePrefix . "tbl_location` (
            `locationId` int(11) NOT NULL auto_increment,
            `name` varchar(255) NOT NULL default '',
            `status` enum('enabled','disabled') NOT NULL default 'enabled',
            `description` mediumtext NOT NULL,
            `address` varchar(255) NOT NULL default '',
            `capacity` int(11) NOT NULL default '0',
            PRIMARY KEY  (`locationId`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $dba->query($query);
        
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->tablePrefix . "tbl_search_term` (
            `term` varchar(255) NOT NULL default '',
            `count` int(10) unsigned NOT NULL default '0',
            `last` int(10) unsigned NOT NULL default '0',
            PRIMARY KEY  (`term`)
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $dba->query($query);
        
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->tablePrefix . "tbl_tag` (
            `tagId` int(11) NOT NULL auto_increment,
            `name` varchar(64) NOT NULL default '',
            PRIMARY KEY  (`tagId`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $dba->query($query);
        
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->tablePrefix . "tbl_tag_map` (
            `attributeName` varchar(64) NOT NULL default '',
            `attributeId` varchar(64) NOT NULL default '',
            `tagId` int(11) NOT NULL default '0',
            PRIMARY KEY  (`attributeName`,`attributeId`,`tagId`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $dba->query($query);
        
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->tablePrefix . "tbl_workshop` (
            `workshopId` int(11) NOT NULL auto_increment,
            `title` varchar(255) NOT NULL default '',
            `prerequisites` mediumtext NOT NULL,
            `status` enum('enabled','disabled') NOT NULL default 'enabled',
            `featured` binary(1) NOT NULL default '0',
            `description` longtext NOT NULL,
            PRIMARY KEY  (`workshopId`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $dba->query($query);
        
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->tablePrefix . "tbl_workshop_document` (
            `workshopDocumentId` int(11) NOT NULL auto_increment,
            `workshopId` int(10) unsigned NOT NULL default '0',
            `name` varchar(64) NOT NULL default '',
            `description` mediumtext NOT NULL,
            `type` varchar(255) NOT NULL default '',
            `uploadDt` int(10) unsigned NOT NULL default '0',
            `filesize` int(10) unsigned NOT NULL default '0',
            PRIMARY KEY  (`workshopDocumentId`),
            KEY `workshopId` (`workshopId`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $dba->query($query);
        
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->tablePrefix . "tbl_workshop_editor` (
            `workshopId` int(10) unsigned NOT NULL default '0',
            `accountId` int(10) unsigned NOT NULL default '0',
            PRIMARY KEY  (`workshopId`,`accountId`)
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $dba->query($query);
        
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->tablePrefix . "tbl_workshop_link` (
            `workshopLinkId` int(10) unsigned NOT NULL auto_increment,
            `workshopId` int(10) unsigned NOT NULL default '0',
            `url` mediumtext NOT NULL,
            `name` varchar(64) NOT NULL default '',
            `order` int(10) unsigned NOT NULL default '0',
            PRIMARY KEY  (`workshopLinkId`),
            KEY `workshopId` (`workshopId`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
        $dba->query($query);
    }
    
    public function down($dba)
    {       
        $query = "";
        $dba->query($query);
    }
}