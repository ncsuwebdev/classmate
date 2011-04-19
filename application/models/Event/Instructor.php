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
 * @package    Instructor
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the instructors of events
 *
 * @package    Instructor
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Event_Instructor extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_event_instructor';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = array('eventId', 'accountId');
    
    public function getInstructorsForEvent($eventId)
    {
    	$where = $this->getAdapter()->quoteInto('eventId = ?', $eventId);
    	
    	$result = $this->fetchAll($where);
    	
    	$accountIds = array();
    	foreach ($result as $r) {
    		$accountIds[] = $r->accountId;
    	}
    	
    	if (count($accountIds) == 0) {
    		return array();
    	}
    	
    	$account = new Ot_Account();
    	$where = $account->getAdapter()->quoteInto('accountId IN (?)', $accountIds);
    	
    	return $account->fetchAll($where, array('lastName', 'firstName'))->toArray();    	
    }
    
    public function getEventsForInstructor($accountId, $startDt = null, $endDt = null)
    {
        $dba = $this->getAdapter();
        
        $where = $dba->quoteInto('accountId = ?', $accountId);
           
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
        
        $events = $event->getEvents(null, $eventIds, null, $startDt, $endDt, 'open')->toArray();

        foreach ($events as &$e) {
            $e['workshop'] = $workshop->find($e['workshopId']);//->toArray();
        }
        
        return $events;    	
    }
    
    public function addAttendeeForm($values = array())
    {
        
        if (!isset($values['eventId'])) {
            throw new Ot_Exception_Input('The event ID must be provided.');            
        }
        
        $form = new Zend_Form();
        $form->setAttrib('id', 'locationForm')
             ->setDecorators(array(
                     'FormElements',
                     array('HtmlTag', array('tag' => 'div', 'class' => 'zend_form')),
                     'Form',
             ));
             
             
        $eventId = $form->createElement('hidden', 'eventId');
        $eventId->setValue($values['eventId']);
        $eventId->setDecorators(array(
            array('ViewHelper', array('helper' => 'formHidden'))
        ));

        $form->addElement($eventId);
        
        $type = $form->createElement('select', 'type', array('label' => 'How to Add:'));
        $type->addMultiOption('firstAvailable', 'First Available Spot')
             ->addMultiOption('attending', 'Add To Attending List')
             ->setValue(isset($values['type']) ? $values['type'] : '');
        
        // get all the users available for the instructor list
        $otAccount = new Ot_Account();
        $accounts = $otAccount->fetchAll(null, array('lastName', 'firstName'))->toArray();
        $userList = array();
        foreach ($accounts as $a) {
            $userList[$a['accountId']] = $a['lastName'] . ", " . $a['firstName'];            
        }
        
        // remove anyone who's either in the attendee list, waitlist, or an instructor
        // for this event so they can't be added to the list
        $attendee = new Event_Attendee();
        $attendeeList = $attendee->getAttendeesForEvent($values['eventId'], 'attending');
        $waitlist = $attendee->getAttendeesForEvent($values['eventId'], 'waitlist');
        $instructors = $this->getInstructorsForEvent($values['eventId']);
        
        foreach ($attendeeList as $a) {
            unset($userList[$a['accountId']]);
        }
        
        foreach ($waitlist as $w) {
            unset($userList[$w['accountId']]);
        }

        foreach ($instructors as $i) {
            unset($userList[$i['accountId']]);
        }
        
        $users = $form->createElement('multiselect', 'users', array('label' => 'User Search:'));
        $users->setMultiOptions($userList)
              ->setAttrib('size', 10)
              ->setValue(isset($values['accountIds']) ? $values['accountIds'] : '');
                  
        $submit = $form->createElement('submit', 'submitButton', array('label' => 'Submit'));
        $submit->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formSubmit'))
                 ));

        $cancel = $form->createElement('button', 'cancel', array('label' => 'Cancel'));
        $cancel->setAttrib('id', 'cancel');
        $cancel->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formButton'))
                ));
        
        $form->addElements(array($type, $users));

        $form->setElementDecorators(array(
                  'ViewHelper',
                  'Errors',
                  array('HtmlTag', array('tag' => 'div', 'class' => 'elm')),
                  array('Label', array('tag' => 'span')),
              ))
             ->addElements(array($submit, $cancel));
        
        return $form;
    }
}