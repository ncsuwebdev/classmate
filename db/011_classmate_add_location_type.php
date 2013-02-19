<?php

/**
 * Adds support for the 'description' custom attribute
 *
 */
class Db_011_classmate_add_location_type extends Ot_Migrate_Migration_Abstract
{
    public function up($dba)
    {
        $query = "CREATE TABLE `". $this->tablePrefix ."tbl_location_type` (
            `typeId` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
            `description` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL
            ) ENGINE = InnoDB DEFAULT CHARSET=latin1;";
        
        $dba->query($query);
    }
    
    public function down($dba)
    {       
        $query = "DROP TABLE `" . $this->tablePrefix . "tbl_location_type`";
        $dba->query($query);
    }
}