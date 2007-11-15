<?php
/**
 * 
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * This license is also available via the world-wide-web at
 * http://itdapps.ncsu.edu/bsd.txt
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to itappdev@ncsu.edu so we can send you a copy immediately.
 *
 * @package    
 * @subpackage Building
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: Building.php 156 2007-07-20 12:57:10Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Model to do all searching through the building information.  The building table
 * contains all the information regarding a building: it's id, x and y gps coordinates,
 * the type of building it is (greek, residenc, or academic), and the sector of campus
 * in which the building is located.
 *
 * @package    
 * @subpackage Building
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 *
 */
class Building extends Ot_Db_Table 
{
    /**
     * Database table name
     *
     * @var string
     */
    public $_name = 'tbl_building';

    /**
     * Primary key for the database
     *
     * @var string
     */
    protected $_primary = 'bldgId';

    protected $_types = array('residence', 'greek', 'academic');

    public function getTypes()
    {
        return $this->_types;
    }
    
    /**
     * Gets a building by its ID, which is assigned from facilities
     *
     * @param string $bldgId
     * @return Zend_Db_Table_Row object
     */
    public function getBuildingByBuildingId($bldgId)
    {
        $where = $this->getAdapter()->quoteInto('bldgId = ?', $bldgId);

        $result = $this->fetchAll($where, null, 1);

        if ($result->count() == 0) {
            return null;
        }

        return $result->current();
    }

    public function getBuildingByAbbreviation($abbrev)
    {
        $dba = $this->getAdapter();

        $where = $dba->quoteInto('abbreviation = ?', $abbrev);

        return $this->fetchAll($where);
    }

}
