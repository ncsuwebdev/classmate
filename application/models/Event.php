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
 * @subpackage Event
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the events
 *
 * @package    Classmate
 * @subpackage Event
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Event extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_event';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'eventId';
    
    public function getEventsForWorkshop($workshopId, $startDt = null, $endDt = null, $status = null)
    {
    	$dba = $this->getAdapter();
    	
    	$where = $dba->quoteInto('workshopId = ?', $workshopId);
    	
    	if (!is_null($status)) {
    		$where .= ' AND ' . $dba->quoteInto('status = ?', $status);
    	}
    	
    	if (!is_null($startDt)) {
    		$startDate = date('Y-m-d', $startDt);
    		$startTime = date('H:i:s', $startDt);
    	}
    	
    	if (!is_null($endDt)) {
    		$endDate = date('Y-m-d', $endDt);
    		$endTime   = date('H:i:s', $endDt);
    	}
    	
    	if (!is_null($startDt) && !is_null($endDt)) {
            $where .= ' AND ' . 
                '(' .
	                '(' . 
	                    $dba->quoteInto('date > ?', $startDate) . 
	                    ' AND ' . 
	                    $dba->quoteInto('date < ?', $endDate) . 
	                ')' . 	                    
	                ' OR ' . 
                    '(' . 
                        $dba->quoteInto('date = ?', $startDate) . 
                        ' AND ' .
                        $dba->quoteInto('startTime >= ?', $startTime) . 
                    ')' . 
                    ' OR ' . 
                    '(' . 
                        $dba->quoteInto('date = ?', $endDate) . 
                        ' AND ' .
                        $dba->quoteInto('endTime <= ?', $endTime) . 
                    ')' . 
                ')';                        
    	} elseif (!is_null($startDt)) {
    		$where .= ' AND ' . 
    		    '(' . 
                    '(' . 
                        $dba->quoteInto('date > ?', $startDate) .
                    ')' .                       
                    ' OR ' . 
                    '(' . 
                        $dba->quoteInto('date = ?', $startDate) . 
                        ' AND ' .
                        $dba->quoteInto('startTime >= ?', $startTime) . 
                    ')' .    
                ')';
                         		
    	} elseif (!is_null($endDt)) {
            $where .= ' AND ' . 
                '(' . 
                    '(' . 
                        $dba->quoteInto('date > ?', $endDate) .
                    ')' .                       
                    ' OR ' . 
                    '(' . 
                        $dba->quoteInto('date = ?', $endDate) . 
                        ' AND ' .
                        $dba->quoteInto('endTime >= ?', $endTime) . 
                    ')' .    
                ')';    		
    	}
    	
    	return $this->fetchAll($where, array('date', 'startTime'));
    }
    
    public function getStatusOfUserForEvent($userId, $eventId)
    {
    	
    	$dba = $this->getAdapter();
    	
    	$instructor = new Instructor();
    	$where = $dba->quoteInto('userId = ?', $userId) . 
    	   ' AND ' . 
    	   $dba->quoteInto('eventId = ?', $eventId);
    	   
    	$res = $instructor->fetchAll($where);
    	if ($res->count() != 0) {
    		return 'instructor';
    	}
    	
    	$where .= ' AND ' . 
    	   $dba->quoteInto('status != ?', 'canceled');
    	   
    	$attendees = new Attendees();
    	
    	$res = $attendees->fetchAll($where);
    	
    	if ($res->count() != 0) {
    	    if ($res->current()->status == 'waitlist') {
    	    	return 'waitlist';
    	    }
    	    
    	    return 'attending';
    	}
    	
    	$eventRestriction = new EventRestriction();
    	$where = $dba->quoteInto('eventId = ?', $eventId);
    	
    	$res = $eventRestriction->fetchAll($where);
    	if ($res->count() != 0) {
    		$realm = strtolower(preg_replace('/^[^@]*@/', '', $userId));
    		
    		$res = $res->current();
    		
    		if ($realm == strtolower($res->realm) && preg_match('/' . preg_replace('/@.*/', '', $userId) . '/i', $res->users)) {
    			return '';
    		}
    		
    		return 'restricted';    		
    	}
    	
    	return '';
    }
}
