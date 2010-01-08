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
 * @package    Attendee
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the Attendees
 *
 * @package    Attendee
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Event_Attendee extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_event_attendee';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = array('eventId', 'accountId');
    
    public function getAttendeesForEvent($eventId, $status='all')
    {
        $where = $this->getAdapter()->quoteInto('eventId = ?', $eventId);
        
        if ($status != 'all') {
        	$where .= ' AND ' . 
        	   $this->getAdapter()->quoteInto('status = ?', $status);
        }
        
        $result = $this->fetchAll($where, 'timestamp ASC')->toArray();
        
        $accountIds = array();
        foreach ($result as $r) {
            $accountIds[] = $r['accountId'];
        }
        
        if (count($accountIds) == 0) {
            return array();
        }
        
        $account = new Ot_Account();
        $where = $account->getAdapter()->quoteInto('accountId IN (?)', $accountIds);
        
        $accounts = $account->fetchAll($where, array('lastName', 'firstName'))->toArray();

        $accountMap = array();
        foreach ($accounts as $a) {
        	$accountMap[$a['accountId']] = $a;
        }
        
        foreach ($result as &$r) {
            if (!isset($accountMap[$r['accountId']])) {
        	   $accountMap[$r['accountId']] = array('firstName' => 'Unavailable', 'lastName' => 'Unavailable', 'username' => 'Unavailable');   
            }
            
            $r = array_merge($r, $accountMap[$r['accountId']]);
        }
        
        return $result;
    }
    
    public function getEventsForAttendee($accountId, $startDt = null, $endDt = null, $status='all')
    {
    	$dba = $this->getAdapter();
    	
    	$where = $dba->quoteInto('accountId = ?', $accountId);
    	
    	if ($status == 'all') {
    		$where .= ' AND ' . 
    		   $dba->quoteInto('status != ?', 'canceled');
    	} else {
    		$where .= ' AND ' . 
    		   $dba->quoteInto('status = ?', $status);
    	}
    	
    	$result = $this->fetchAll($where);
    	
    	$eventIds = array();
    	foreach ($result as $r) {
    		$eventIds[$r->eventId] = array(
    		    'status' => $r->status,
    		    'attended' => $r->attended,
    		);
    	}
    	
    	if (count($eventIds) == 0) {
    		return array();
    	}
    	
    	$event = new Event();
    	$workshop = new Workshop();
    	
    	$events = $event->getEvents(null, array_keys($eventIds), null, $startDt, $endDt, 'open')->toArray();

    	foreach ($events as &$e) {
    		$e['workshop'] = $workshop->find($e['workshopId'])->toArray();
    		$e['status'] = $eventIds[$e['eventId']]['status'];
    		$e['attended'] = $eventIds[$e['eventId']]['attended'];
    	}
    	
    	return $events;
    }
    
    public function fillEvent($eventId)
    {
        $event = new Event();
        $thisEvent = $event->find($eventId);
        
        if (($thisEvent->roleSize >= $thisEvent->maxSize) || $thisEvent->waitlistTotal == 0) {
            return true;                
        }
        
        $openSpots = $thisEvent->maxSize - $thisEvent->roleSize;
        
        $where = $this->getAdapter()->quoteInto('eventId = ?', $eventId)
               . " AND "
               . $this->getAdapter()->quoteInto('status = ?', 'waitlist');
                 
        $allWaitlisted = $this->fetchAll($where, 'timestamp', $openSpots);
        
        foreach ($allWaitlisted as $u) {
            $this->makeReservation($u->accountId, $eventId);                        
        }
        
        return true;
    }

    public function makeReservation($accountId, $eventId, $overrideStatus = 'firstAvailable')
    {
    	$event = new Event();
    	$status = $event->getStatusOfUserForEvent($accountId, $eventId);
    	
    	if ($status == 'restricted') {
    		throw new Ot_Exception_Data('Reservation not made because class is restricted');
    	}
    	
    	if ($status == 'instructor') {
    		throw new Ot_Exception_Data('Reservation not made because user is an instructor for this class');    		
    	}
        
    	$thisEvent = $event->find($eventId);
        /*
        $eventTime = strtotime($thisEvent->date . ' ' . $thisEvent->startTime);
        if ($eventTime < time()) {
            throw new Ot_Exception_Data('The signup for this class is closed');
        }*/
        
        if ($overrideStatus == 'firstAvailable') {
	        if ($thisEvent->roleSize < $thisEvent->maxSize) {
	        	$status = 'attending';
	        } elseif ($thisEvent->waitlistSize != 0 && $thisEvent->waitlistSize > $thisEvent->waitlistTotal) {
	        	$status = 'waitlist';
	        } else {
	        	throw new Ot_Exception_Data('The class is full and no waitlist spot is available');
	        }
        } else {
        	$status = $overrideStatus;
        }
        
        $dba = $this->getAdapter();
        
        $inTransaction = false;
        try {
            $dba->beginTransaction();
        } catch (Exception $e) {
            $inTransaction = true;
        }
        
        $where = $dba->quoteInto('eventId = ?', $eventId) . 
            ' AND ' . 
            $dba->quoteInto('accountId = ?', $accountId);
        
    	$current = $this->fetchAll($where);
            	
    	if ($current->count() != 0) {
    		$data = $current->current()->toArray();
    		
    		if ($data['status'] != 'canceled') {
    			$this->cancelReservation($accountId, $eventId);
    		}
    		
    		$data['status'] = $status;
    		$data['timestamp'] = time();
    		
    		try {
    			$this->update($data, null);
    		} catch (Exception $e) {
	    		if (!$inTransaction) {
	                $dba->rollBack();
	            }
    			throw $e;
    		}
    	} else {
    		$data = array(
    		  'eventId'   => $eventId,
    		  'accountId' => $accountId,
    		  'status'    => $status,
    		  'timestamp' => time(),
    		);
    		
    	    try {
                $this->insert($data);
            } catch (Exception $e) {
	            if (!$inTransaction) {
	                $dba->rollBack();
	            }
                throw $e;
            }
    	}
    	
        $data = array(
            'eventId' => $eventId,
        );
        
        if ($status == 'attending') {
        	$data['roleSize'] = $thisEvent->roleSize + 1;
        } else {
        	$data['waitlistTotal'] = $thisEvent->waitlistTotal + 1;
        }
        
        try {
        	$event->update($data, null);
        } catch (Exception $e) {
            if (!$inTransaction) {
                $dba->rollBack();
            }
        	throw $e;
        }
        
        if (!$inTransaction) {
            $dba->commit();
        }
        
        return $status;    	
    }
    
    public function cancelReservation($accountId, $eventId)
    {
        $event = new Event();
        $status = $event->getStatusOfUserForEvent($accountId, $eventId);
        
        if ($status == 'restricted') {
            throw new Ot_Exception_Data('Reservation not made because class is restricted');
        }
        
        if ($status == 'instructor') {
            throw new Ot_Exception_Data('Reservation not made because user is an instructor for this class');         
        }
        
        if ($status == '') {
        	throw new Ot_Exception_Data('User is not on the role for this class');
        }
        
        $thisEvent = $event->find($eventId);
        
        $eventTime = strtotime($thisEvent->date . ' ' . $thisEvent->startTime);
        if ($eventTime < time()) {
            throw new Ot_Exception_Data('The signup for this class is closed');
        }
        
        $dba = $this->getAdapter();
        
        $inTransaction = false;
        try {
            $dba->beginTransaction();
        } catch (Exception $e) {
            $inTransaction = true;
        }

        $data = array(
            'eventId'   => $eventId,
            'accountId' => $accountId,
            'status'    => 'canceled',
        );

        try {
            $this->update($data, null);
        } catch (Exception $e) {
        	if (!$inTransaction) {
                $dba->rollBack();
        	}
            throw $e;
        }
        
        $data = array(
            'eventId' => $eventId,
        );
        
        if ($status == 'attending') {
            $data['roleSize'] = $thisEvent->roleSize - 1;
        } else {
            $data['waitlistTotal'] = $thisEvent->waitlistTotal - 1;
        }

        try {
            $event->update($data, null);
        } catch (Exception $e) {
            if (!$inTransaction) {
                $dba->rollBack();
            }
            throw $e;
        }
        
        if (!$inTransaction) {
            $dba->commit();
        } 
        
        return $status;      	
    }
}