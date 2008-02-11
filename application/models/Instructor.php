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
 * @subpackage Instructor
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the instructors of events
 *
 * @package    Classmate
 * @subpackage Instructor
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Instructor extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_instructor';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = array('eventId', 'userId');
    
    public function getInstructorsForEvent($eventId)
    {
    	$where = $this->getAdapter()->quoteInto('eventId = ?', $eventId);
    	
    	$result = $this->fetchAll($where);
    	
    	$userIds = array();
    	foreach ($result as $r) {
    		$userIds[] = $r->userId;
    	}
    	
    	if (count($userIds) == 0) {
    		return array();
    	}
    	
    	$profile = new Profile();
    	$where = $profile->getAdapter()->quoteInto('userId IN (?)', $userIds);
    	
    	return $profile->fetchAll($where, array('lastName', 'firstName'))->toArray();    	
    }
    
    public function getEventsForInstructor($userId, $startDt = null, $endDt = null)
    {
        $dba = $this->getAdapter();
        
        $where = $dba->quoteInto('userId = ?', $userId);
           
        $result = $this->fetchAll($where);
        
        $eventIds = array();
        foreach ($result as $r) {
            $eventIds[] = $r->eventId;
        }
        
        if (count($eventIds) == 0) {
            return array();
        }
        
        $event = new Event();
        $workshop = new Workshop();
        
        $events = $event->getEvents(null, $eventIds, $startDt, $endDt, 'open')->toArray();

        foreach ($events as &$e) {
            $e['workshop'] = $workshop->find($e['workshopId'])->toArray();
        }
        
        return $events;    	
    }
    
}
