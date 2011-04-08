<?php

/**
 * Adds support for the 'description' custom attribute
 *
 */
class Db_006_classmate_add_workshop_category extends Ot_Migrate_Migration_Abstract
{
    public function up($dba)
    {
        $query = "CREATE TABLE  `". $this->tablePrefix ."tbl_workshop_category` (
					`categoryId` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					`name` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`description` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
					) ENGINE = INNODB CHARACTER SET latin1 COLLATE latin1_bin;";
        $dba->query($query);
        
        $query = "ALTER TABLE  `". $this->tablePrefix ."tbl_workshop` ADD  `categoryId` INT( 11 ) NOT NULL AFTER  `status`";
        $dba->query($query);
    }
    
    public function down($dba)
    {       
        $query = "DROP TABLE `" . $this->tablePrefix . ".oit_tbl_workshop_category`";
        $dba->query($query);
        
        $query = "ALTER TABLE  `". $this->tablePrefix ."tbl_workshop` DROP  `categoryId`";
        $dba->query($query);
    }
}