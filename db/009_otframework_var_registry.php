<?php

/**
 * Updates the site to use a variable registry as opposed to xml files
 */

class Db_009_otframework_var_registry extends Ot_Migrate_Migration_Abstract
{
    public function up($dba)
    {
        $query ='CREATE TABLE IF NOT EXISTS `' . $this->tablePrefix . 'tbl_ot_var` (
              `varName` varchar(200) NOT NULL,
              `value` text NOT NULL,
              PRIMARY KEY (`varName`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
        $dba->query($query);
    }
    
    public function down($dba) {
    }
    
}