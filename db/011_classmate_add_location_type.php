<?php

/**
 * Adds support for the 'description' custom attribute
 *
 */
class Db_013_classmate_add_location_typeid extends Ot_Migrate_Migration_Abstract
{
    public function up($dba)
    {
        $query = "ALTER TABLE  `" . $this->tablePrefix . "tbl_location` ADD  `locationTypeId` INT UNSIGNED NOT NULL AFTER  `locationId`";
        
        $dba->query($query);
    }
    
    public function down($dba)
    {       
        $query = "";
        $dba->query($query);
    }
}