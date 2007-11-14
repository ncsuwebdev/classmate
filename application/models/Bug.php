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
 * @subpackage Bug
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: Bug.php 156 2007-07-20 12:57:10Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Model to do deal with bug reports
 *
 * @package    
 * @subpackage Bug
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 *
 */
class Bug extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_bug';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'bugId';

    /**
     * Inserts a new row into the table
     *
     * @param array $data
     * @return Result from Zend_Db_Table::insert()
     */
    public function insert(array $data)
    {
        $data['submitDt'] = time();
        $data['submittedByUserId'] = Zend_Auth::getInstance()->getIdentity();
        $data['status'] = 'new';
        
        return parent::insert($data);
    }

    /**
     * Gets all the bugs, with options to only show new bugs
     *
     * @param boolean $newOnly
     * @return result from fetchAll
     */
    public function getBugs($newOnly = true)
    {
        if ($newOnly) {
            $where = $this->getAdapter()->quoteInto('status = ?', 'new');
        } else {
            $where = null;
        }

        return parent::fetchAll($where, 'submitDt DESC');
    }
}
?>
