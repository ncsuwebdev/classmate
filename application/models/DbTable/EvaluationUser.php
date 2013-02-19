<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file _LICENSE.txt.
 *
 * This license is also available via the world-wide-web at
 * http://itdapps.ncsu.edu/bsd.txt
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to itappdev@ncsu.edu so we can send you a copy immediately.
 *
 * @package    EvaluationUser
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the evaluations
 *
 * @package    EvaluationUser
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class App_Model_DbTable_EvaluationUser extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_EvaluationUser';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = array('eventId', 'accountId');
    
    public function hasCompleted($accountId, $eventId)
    {
        $dba = $this->getAdapter();
        
        $where = $dba->quoteInto('accountId = ?', $accountId) . 
           ' AND ' . 
           $dba->quoteInto('eventId = ?', $eventId);
           
        $result = $this->fetchAll($where);
        
        return ($result->count() != 0);
        
    }
    
    public function getCompleted($eventId)
    {
        $where = $this->getAdapter()->quoteInto('eventId = ?', $eventId);
        
        $result = $this->fetchAll($where);
        
        $ret = array();
        foreach ($result as $r) {
            $ret[] = $r->accountId;
        }
        
        return $ret;
    }
}