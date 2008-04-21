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
 * @package    Classmate
 * @subpackage EvaluationUser
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the evaluations
 *
 * @package    Classmate
 * @subpackage EvaluationUser
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class EvaluationUser extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_evaluation_user';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = array('eventId', 'userId');
    
    public function hasCompleted($userId, $eventId)
    {
    	$dba = $this->getAdapter();
    	
    	$where = $dba->quoteInto('userId = ?', $userId) . 
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
    		$ret[] = $r->userId;
    	}
    	
    	return $ret;
    }
}