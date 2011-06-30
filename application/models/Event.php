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
 * @package    Event
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the events
 *
 * @package    Event
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
    
    public function getEvents($workshopId = null, $eventId = null, $locationId = null, $startDt = null, $endDt = null, $status = null, $count = null)
    {
    	$dba = $this->getAdapter();
    	
    	$where = '';
    	
    	if (!is_null($workshopId)) {
    	   $where .= $dba->quoteInto('workshopId = ?', $workshopId);
    	}
    	
    	if (!is_null($eventId)) {
    		if ($where != '') {
    			$where .= ' AND ';
    		}
    		
    		if (is_array($eventId)) {
    			$where .= $dba->quoteInto('eventId IN (?)', $eventId);
    		} else {
    			$where .= $dba->quoteInto('eventId = ?', $eventId);
    		}
    	}
    	
        if (!is_null($locationId)) {
            if ($where != '') {
                $where .= ' AND ';
            }
            
            $where .= $dba->quoteInto('locationId = ?', $locationId);
        }
    	
    	if (!is_null($status)) {
    		if ($where != '') {
    			$where .= ' AND ';
    		}
    		
    		$where .= $dba->quoteInto('status IN (?)', $status);
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
    		if ($where != '') {
    			$where .= ' AND ';
    		}
    		
            $where .=
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
                        $dba->quoteInto('endTime >= ?', $startTime) . 
                    ')' . 
                    ' OR ' . 
                    '(' . 
                        $dba->quoteInto('date = ?', $endDate) . 
                        ' AND ' .
                        $dba->quoteInto('endTime <= ?', $endTime) . 
                    ')' . 
                ')';                        
    	} elseif (!is_null($startDt)) {
    	    if ($where != '') {
                $where .= ' AND ';
            }
                		
    		$where .= 
    		    '(' . 
                    '(' . 
                        $dba->quoteInto('date > ?', $startDate) .
                    ')' .                       
                    ' OR ' . 
                    '(' . 
                        $dba->quoteInto('date = ?', $startDate) . 
                        ' AND ' .
                        $dba->quoteInto('endTime >= ?', $startTime) . 
                    ')' .    
                ')';
                         		
    	} elseif (!is_null($endDt)) {
    	    if ($where != '') {
                $where .= ' AND ';
            }
            
            $where .= 
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
    	
    	return $this->fetchAll($where, array('date', 'startTime'), $count);
    }
    
    public function isEditable($eventId)
    {
        $thisEvent = $this->find($eventId);
        
        if (is_null($thisEvent)) {
            throw new Ot_Exception_Data('Event not found');    
        }
        
        $time = time();
        
        $endDt   = new Zend_Date(strtotime($thisEvent->date . ' ' . $thisEvent->endTime));
        
        $config = Zend_Registry::get('config');
        
        $endDt->addHour($config->user->numHoursEvaluationAvailability->val);
        
        return ($endDt->getTimestamp() > $time);        
    }
    
    public function isEvaluatable($eventId)
    {
        $thisEvent = $this->find($eventId);
        
        if (is_null($thisEvent)) {
            throw new Ot_Exception_Data('Event not found');    
        }
        
        $time = time();
        
        $startDt = new Zend_Date(strtotime($thisEvent->date . ' ' . $thisEvent->startTime));
        $endDt   = new Zend_Date(strtotime($thisEvent->date . ' ' . $thisEvent->endTime));
        
        $config = Zend_Registry::get('config');
        
        $endDt->addHour($config->user->numHoursEvaluationAvailability->val);
        
        return ($startDt->getTimestamp() < $time && $endDt->getTimestamp() > $time);        
    }
    
    public function isReservationCancelable($eventId)
    {
        $thisEvent = $this->find($eventId);
        
        if (is_null($thisEvent)) {
            throw new Ot_Exception_Data('Event not found');    
        }
        
        $time = time();
        
        $config = Zend_Registry::get('config');
        
        $startDt = new Zend_Date(strtotime($thisEvent->date . ' ' . $thisEvent->startTime));
                        
        $startDt->subHour($config->user->numHoursEventCancel->val);
        return ($startDt->getTimestamp() > $time);
    }
    
    public function getStatusOfUserForEvent($accountId, $eventId)
    {
    	
    	$dba = $this->getAdapter();
    	
    	$instructor = new Event_Instructor();
    	$where = $dba->quoteInto('accountId = ?', $accountId) . 
    	   ' AND ' . 
    	   $dba->quoteInto('eventId = ?', $eventId);
    	   
    	$res = $instructor->fetchAll($where);
    	if ($res->count() != 0) {
    		return 'instructor';
    	}
    	
    	$where .= ' AND ' . 
    	   $dba->quoteInto('status != ?', 'canceled');
    	   
    	$attendee = new Event_Attendee();
    	
    	$res = $attendee->fetchAll($where);
    	
    	if ($res->count() != 0) {
    	    if ($res->current()->status == 'waitlist') {
    	    	return 'waitlist';
    	    }
    	    
    	    return 'attending';
    	}
    	
    	return '';
    }
    
    public function getEventsForUser($accountId)
    {
       	$config = Zend_Registry::get('config');
   		
   		$attendee   = new Event_Attendee();
   		$instructor = new Event_Instructor();
   		$location   = new Location(); 
   		$document   = new Workshop_Document();
   		$account    = new Ot_Account();
   		$eu         = new Evaluation_User();
   		       		
       	$stayOpen = new Zend_Date();
       	$stayOpen->subHour($config->user->numHoursEvaluationAvailability->val);

        $locationCache = array(); 
        
        $reservations = $attendee->getEventsForAttendee($accountId);

        $time = time();
        	        
        foreach ($reservations as &$r) {
        	
        	$r['active'] = false;
        	
        	// we determine if the class is open
            $startDt = new Zend_Date(strtotime($r['date'] . ' ' . $r['startTime']));
            $endDt   = new Zend_Date(strtotime($r['date'] . ' ' . $r['endTime']));
            $endDt->addHour($config->user->numHoursEvaluationAvailability->val);
            
            $r['evaluatable'] = $this->isEvaluatable($r['eventId']);
            
            // checks to see if its possible that the class is open for evaluation
            if ($r['evaluatable'] && !$eu->hasCompleted($accountId, $r['eventId'])) {
            	$r['active'] = true;
            } elseif ($startDt->getTimestamp() > $time) {
            	$r['active'] = true;
            }
            
            $r = array_merge(array('startDt' => $startDt->getTimestamp()), $r);
            
            $r['hasHandouts'] = false;
            
            $documents = $document->getDocumentsForWorkshop($r['workshopId']);
            if (count($documents) > 0) {
                $r['hasHandouts'] = true;
            }	            
            
            $r['cancelable'] = false;
            
            if ($r['active']) {
                $startDt->subHour($config->user->numHoursEventCancel->val);
                $r['cancelable']  = ($startDt->getTimestamp() > $time); 
                           
                if ($r['status'] == 'waitlist') {
            	
	                $waiting = $attendee->getAttendeesForEvent($r['eventId'], 'waitlist');
	                $position = 1;
	                
	                foreach ($waiting as $w) {
	                	if ($accountId == $w['accountId']) {
	                		break;
	                	}
	                	$position++;
	                }
	                $r['waitlistPosition'] = $position;
                }
            }
        }
            
        // Get presently taught classes
        $teaching = $instructor->getEventsForInstructor($accountId);
        
        foreach ($teaching as &$t) {
        	$startDt = new Zend_Date(strtotime($t['date'] . ' ' . $t['startTime']));
        	$endDt   = new Zend_Date(strtotime($t['date'] . ' ' . $t['endTime']));
            $endDt->addHour($config->user->numHoursEvaluationAvailability->val);
            
        	$t = array_merge(array('startDt' => $startDt->getTimestamp()), $t);
        	
        	$t['active'] = false;
        	
        	// checks to see if its possible that the class is open for evaluation
            if ($endDt->getTimestamp() > $time) {
            	$t['active'] = true;
            }
            	        	
        	$t['status'] = 'instructor';
        }
        
        $events = array_merge($reservations, $teaching);
        
        // sort by event order
        $eDates = array();
        foreach ($events as $key => $value) {
        	$eDates[$key] = $value['startDt'];
        }
        
        asort($eDates);
        
        $newEvents = array();
        foreach (array_keys($eDates) as $key) {
        	$newEvents[] = $events[$key];
        }
        
        $events = $newEvents;
        
        $currentEvents = array();
        $pastEvents    = array();
        
        foreach ($events as $e) {
            $where = $instructor->getAdapter()->quoteInto('eventId = ?', $e['eventId']);
            $instructors = $instructor->fetchAll($where);
            
            $instructorIds = array();
            foreach ($instructors as $i) {
                $instructorIds[] = $i->accountId;
            }                
            
            if (count($instructorIds) != 0) {
            	$accounts = $account->fetchAll($account->getAdapter()->quoteInto('accountId IN (?)', $instructorIds), array('lastName', 'firstName'));
            	
            	foreach ($accounts as $a) {
            		$e['instructors'][] = $a->firstName . ' ' . $a->lastName;
            	}
            }	        
            
            if (isset($locationCache[$e['locationId']])) {
                $e['location'] = $locationCache[$e['locationId']];
            } else {
                $thisLocation = $location->find($e['locationId']);        
                if (is_null($thisLocation)) {
                    throw new Ot_Exception_Data('Location not found');
                }
                $e['location'] = $thisLocation->toArray();      
                $locationCache[$e['locationId']] = $e['location'];
            } 	
            
            if ($e['active']) {
            	$currentEvents[] = $e;
            } else {
            	if ($e['status'] != 'waitlist') {
            		$pastEvents[] = $e;
            	}
            }
        } 	
        
        $ret = array(
        	'currentEvents' => $currentEvents,
        	'pastEvents'    => array_reverse($pastEvents),
        );
        
        return $ret;
    }
    
    public function insert(array $data) {
    	
    	$keys = array();
    	
    	if($data['evaluationType'] == 'google') {
    		$keys['formKey'] = $data['formKey'];
    		$keys['answerKey'] = $data['answerKey'];
    	}
    	
    	unset($data['formKey'], $data['answerKey']);
    	
    	$dba = $this->getAdapter();
    	
    	$inTransaction = false;
    	
    	try {
    		$dba->beginTransaction();
    	} catch (Exception $e) {
    		$inTransaction = true;
    	}
    	
    	try {
    		$eventId = parent::insert($data);
    	} catch (Exception $e) {
    		if ($inTransaction) {
    			$dba->rollBack();
    		}
    		throw $e;
    	}

    	if(count($keys) > 0) {
    		$keys['eventId'] = $eventId;
    		
    		$evaluationKey = new Evaluation_Key();
    		
    		try {
    			$evaluationKey->insert($keys);
    		} catch (Exception $e) {
    			if (!$inTransaction) {
    				$dba->rollBack();
    			}
    			throw $e;
    		}
    	}
    	
    	if (!$inTransaction) {
    		$dba->commit();
    	}
    	
    	return $eventId;
    }
    
    public function update(array $data, $where) {
    	
    	$keys = array();
    	
    	$dba = $this->getAdapter();
    	$inTransaction = false;
    	
    	try {
    		$dba->beginTransaction();
    	} catch (Exception $e) {
    		$inTransaction = true;
    	}
    	
    	$evaluationKey = new Evaluation_Key();
    	
    	if($data['evaluationType'] == 'google') {
    		$keys['eventId'] = $data['eventId'];
    		$keys['formKey'] = $data['formKey'];
    		$keys['answerKey'] = $data['answerKey'];
    		
    		try {
    			$evaluationKey->update($keys, null);
    		} catch (Exception $e) {
    			$dba->rollBack();
    			throw $e;
    		}
    		
    	} elseif ($data['evaluationType'] == 'default') {
    		$where = $evaluationKey->getAdapter()->quoteInto('eventId = ?', $data['eventId']);
    		
    		try {
    			$evaluationKey->delete($where);
    		} catch (Exception $e) {
    			$dba->rollBack();
    			throw $e;
    		}
    	}
    	
    	unset($data['formKey'], $data['answerKey']);
    	
    	try {
    		parent::update($data, $where);
    	} catch (Exception $e) {
    		$dba->rollBack();
    		throw $e;
    	}
    	
    	if (!$inTransaction) {
    		$dba->commit();
    	}
    }
    
    public function form($values = array())
    {
        $config = Zend_Registry::get('config');
        
        $form = new Zend_Form();
        $form->setAttrib('id', 'eventForm')
             ->setDecorators(array(
                     'FormElements',
                     array('HtmlTag', array('tag' => 'div', 'class' => 'zend_form')),
                     'Form',
             ));

        $workshop = new Workshop();
        $where = $workshop->getAdapter()->quoteInto('status = ?', 'enabled');
        $workshops = $workshop->fetchAll($where, 'title');
        
        $workshopList = array();
        foreach ($workshops as $w) {
            $workshopList[$w->workshopId] = $w->title;
        }
        
        $workshopElement = $form->createElement('select', 'workshop', array('label' => 'Workshop:'));
        $workshopElement->setMultiOptions($workshopList)
                        ->setValue(isset($values['workshopId']) ? $values['workshopId'] : '');
        
        $location = new Location();
        $where = $location->getAdapter()->quoteInto('status = ?', 'enabled');
        $locations = $location->fetchAll($where, 'name');
        
        $locationList = array();
        $locationCapacity = array();
        foreach($locations as $l) {
            $locationList[$l->locationId] = $l->name;
            $locationCapacity['loc_' . $l->locationId] = $l->capacity;
        }
        
        $locationIds = array_keys($locationList);
        
        // add the location capacities to the page in js so we can process it as a json object for the "live" max size changing with location selection
        Zend_Layout::getMvcInstance()->getView()->headScript()->appendScript('var locationCapacitiesString = ' . Zend_Json::encode($locationCapacity) . ';');
        
        $locationElement = $form->createElement('select', 'location', array('label' => 'Location:'));
        $locationElement->setMultiOptions($locationList)
                        ->setValue(isset($values['locationId']) ? $values['locationId'] : $locationCapacity['loc_' . $locationIds[0]]);
                        
        $date = $form->createElement('text', 'date', array('label' => 'Date:'));
        $date->setRequired(true)
              ->addFilter('StringTrim')
              ->addFilter('StripTags')
              ->setAttrib('maxlength', '128')
              ->setAttrib('style', 'width: 200px')
              ->setValue(isset($values['date']) ? strftime('%A, %B %e, %Y', strtotime($values['date'])) : '');
              
        $password = $form->createElement('text', 'password', array('label' => 'Event Password:'));
        $password->addFilter('StringTrim')
        		->addFilter('StripTags')
        		->setAttrib('maxlength', '100')
        		->setValue(isset($values['password']) ? $values['password'] : '');
              
        // add the start time selector
        $startTimeSub = new Zend_Form_SubForm();
        $startTimeSub->setDecorators(array(
                     'FormElements',
                     array('HtmlTag', array('tag' => 'div', 'class' => 'zend_form'))
                ));
        $startTimeSub->setAttrib('class', 'sub');
        $startTimeHour = $startTimeSub->createElement('select', 'hour', array('label' => 'Start Time:'));
        for ($i = 1; $i <= 12; $i++) {
            $startTimeHour->addMultiOption($i, $i);
        }
        $startTimeHour->setDecorators(array(
                array('ViewHelper', array('helper' => 'formSelect')),
                array('Label')
            ));
        $startTimeHour->setValue((isset($values['startTime'])) ? date('g', strtotime($values['startTime'])) : date('g'));
        
        $startTimeMinute = $startTimeSub->createElement('select', 'minute');
        for ($i = 0; $i < 60; $i += 5) {
            $startTimeMinute->addMultiOption(str_pad($i, 2, '0', STR_PAD_LEFT), str_pad($i, 2, '0', STR_PAD_LEFT));
        }
        $startTimeMinute->setDecorators(array(
                array('ViewHelper', array('helper' => 'formSelect'))
            ));
        $startTimeMinute->setValue((isset($values['startTime'])) ? date('i', strtotime($values['startTime'])) : date('i'));
            
        $startTimeMeridian = $startTimeSub->createElement('select', 'meridian');
        $startTimeMeridian->addMultiOption('am', 'AM')
               ->addMultiOption('pm', 'PM')
               ->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formSelect'))
               ));
        $startTimeMeridian->setValue((isset($values['startTime'])) ? date('a', strtotime($values['startTime'])) : date('a'));       
               
        $startTimeSub->addElements(array($startTimeHour, $startTimeMinute, $startTimeMeridian));

        // add the end time selector
        $endTimeSub = new Zend_Form_SubForm();
        $endTimeSub->setDecorators(array(
                     'FormElements',
                     array('HtmlTag', array('tag' => 'div', 'class' => 'zend_form'))
                     ));
        $endTimeSub->setAttrib('class', 'sub');
        $endTimeHour = $endTimeSub->createElement('select', 'hour', array('label' => 'End Time:'));
        for ($i = 1; $i <= 12; $i++) {
            $endTimeHour->addMultiOption($i, $i);
        }
        $endTimeHour->setDecorators(array(
                array('ViewHelper', array('helper' => 'formSelect')),
                array('Label')
            ));
        $endTimeHour->setValue((isset($values['endTime'])) ? date('g', strtotime($values['endTime'])) : date('g'));
        
        $endTimeMinute = $endTimeSub->createElement('select', 'minute');
        for ($i = 0; $i < 60; $i += 5) {
            $endTimeMinute->addMultiOption(str_pad($i, 2, '0', STR_PAD_LEFT), str_pad($i, 2, '0', STR_PAD_LEFT));
        }
        $endTimeMinute->setDecorators(array(
                array('ViewHelper', array('helper' => 'formSelect'))
            ));
        $endTimeMinute->setValue((isset($values['endTime'])) ? date('i', strtotime($values['endTime'])) : date('i'));
            
        $endTimeMeridian = $endTimeSub->createElement('select', 'meridian');
        $endTimeMeridian->addMultiOption('am', 'AM')
               ->addMultiOption('pm', 'PM')
               ->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formSelect'))
               ));
        $endTimeMeridian->setValue((isset($values['endTime'])) ? date('a', strtotime($values['endTime'])) : date('a'));       
               
        $endTimeSub->addElements(array($endTimeHour, $endTimeMinute, $endTimeMeridian));
        
        // get all the users available for the instructor list
        $otAccount = new Ot_Account();
        $accounts = $otAccount->fetchAll(null, array('lastName', 'firstName'))->toArray();
        $instructorList = array();
        foreach ($accounts as $a) {
            $instructorList[$a['accountId']] = $a['lastName'] . ", " . $a['firstName'];            
        }
        
        $instructorElement = $form->createElement('multiselect', 'instructors', array('label' => 'Instructor(s):'));
        $instructorElement->setMultiOptions($instructorList)
                          ->setAttrib('size', 10)
                          ->setValue(isset($values['instructorIds']) ? $values['instructorIds'] : '');
                          
		
        
        $minSize = $form->createElement('text', 'minSize', array('label' => 'Min Size:'));
        $minSize->setRequired(true)
                ->addFilter('StringTrim')
                ->addFilter('StripTags')
                ->setAttrib('maxlength', '64')
                ->setAttrib('style', 'width: 50px;')
                ->setValue((isset($values['minSize']) ? $values['minSize'] : $config->user->defaultMinWorkshopSize->val));
        
        $maxSize = $form->createElement('text', 'maxSize', array('label' => 'Max Size:'));
        $maxSize->setRequired(true)
                ->addFilter('StringTrim')
                ->addFilter('StripTags')
                ->setAttrib('maxlength', '64')
                ->setAttrib('style', 'width: 50px;')
                ->setValue((isset($values['maxSize']) ? $values['maxSize'] : $locationElement->getValue()));
              
        $waitlistSize = $form->createElement('text', 'waitlistSize', array('label' => 'Waitlist Size:'));
        $waitlistSize->setRequired(true)
                     ->addFilter('StringTrim')
                     ->addFilter('StripTags')
                     ->setAttrib('maxlength', '64')
                     ->setAttrib('style', 'width: 50px;')
                     ->setValue((isset($values['waitlistSize']) ? $values['waitlistSize'] : $config->user->defaultWorkshopWaitlistSize->val));
                     
        $evaluationType = $form->createElement('select', 'evaluationType', array('label' => 'Evaluation Type:'));
        $evaluationType->setMultiOptions( array('default' =>'Default', 'google' => 'Google Form'))
        		->setRequired(true)
        		->setValue((isset($values['evaluationType']) ? $values['evaluationType'] : 'default'));
        		
        $formKey = $form->createElement('textarea', 'formKey', array('label' => 'Google Form Question Key:'));
        $formKey->setAttribs(array(
        			'cols' => '10',
        			'rows' => '5',
        			'style'=> 'width : 250px;'
	       		))
        		->addDecorators(array(
        			'ViewHelper',
        			'Errors',
        			'HtmlTag',
        			array('Label', array('tag' => 'span')),
        			array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'id' => 'formKey', 'class' => 'elm'))
        		))
        		->setValue((isset($values['formKey']) ? $values['formKey'] : ''));
        		
        $answerKey = $form->createElement('textarea', 'answerKey', array('label' => 'Google Form Answer Key:'));
        $answerKey->addFilter('StringTrim')
        		->addFilter('StripTags')
        		->setAttribs(array(
        			'cols' => '10',
        			'rows' => '3',
        			'style'=> 'width : 250px;'	
	       		))
        		->addDecorators(array(
        			'ViewHelper',
        			'Errors',
        			'HtmlTag',
        			array('Label', array('tag' => 'span')),
        			array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'id' => 'answerKey', 'class' => 'elm'))
        		))
        		->setValue((isset($values['answerKey']) ? $values['answerKey'] : ''));
        		
        $submit = $form->createElement('submit', 'submitButton', array('label' => 'Submit'));
        $submit->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formSubmit'))
                 ));

        $cancel = $form->createElement('button', 'cancel', array('label' => 'Cancel'));
        $cancel->setAttrib('id', 'cancel');
        $cancel->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formButton'))
                ));
        
        $form->addElements(array($workshopElement, $locationElement, $password, $date, $evaluationType))
             ->addSubForms(array('startTime' => $startTimeSub, 'endTime' => $endTimeSub))
             ->addElements(array($minSize, $maxSize, $waitlistSize, $instructorElement));
             
		$form->addDisplayGroup(array('instructors'), 'instructors-group', array('legend' => 'Instructors'));
		$form->addDisplayGroup(array('workshop', 'password', 'location', 'minSize', 'maxSize', 'waitlistSize'), 'generalInformation', array('legend' => 'General Information'));

        $form->setElementDecorators(array(
                  'ViewHelper',
                  'Errors',
                  array('HtmlTag', array('tag' => 'div', 'class' => 'elm')),
                  array('Label', array('tag' => 'span')),
              ))
             ->addElements(array($submit, $cancel));
             
        $form->addElements(array($evaluationType, $formKey, $answerKey));
		$form->addDisplayGroup(array('evaluationType', 'formKey', 'answerKey'), 'evaluationTypes', array('legend' => 'Evaluations'));
		$form->addDisplayGroup(array('submitButton', 'cancel'), 'buttons');
		
		$form->setDisplayGroupDecorators(array(
			'FormElements',
			array('HtmlTag',
				array(
					'tag' => 'div',
					'class' => 'widget-content'
				)
			),
			array(
				array('elementDiv' => 'HtmlTag'),
				array(
					'tag' => 'div',
					'class' => array('widget-footer','ui-corner-bottom'),
					'placement' => Zend_Form_Decorator_Abstract::APPEND,
				)
			),
			array('FieldSet',array('class' => 'formField'))
		));
		
		$buttons = $form->getDisplayGroup('buttons');
		$buttons->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'div', 'style' => 'clear : both;')
		)));

        if (isset($values['eventId'])) {

            $eventId = $form->createElement('hidden', 'eventId');
            $eventId->setValue($values['eventId']);
            $eventId->setDecorators(array(
                array('ViewHelper', array('helper' => 'formHidden'))
            ));

            $form->addElement($eventId);
        }
        
        return $form;
    }
    
    public function contactForm($values = array())
    {
        
        $form = new Zend_Form();
        $form->setAttrib('id', 'contactForm')
             ->setDecorators(array(
                     'FormElements',
                     array('HtmlTag', array('tag' => 'div', 'class' => 'zend_form')),
                     'Form',
             ));
       
        $recipients = $form->createElement('select', 'recipients', array('label' => 'Recipients:'));
        $recipients->addMultiOption('attending', 'Only Attendees')
                   ->addMultiOption('waitlist', 'Only Waitlist')
                   ->addMultiOption('all', 'Both Attendees and Waitlist')
                   ->setValue(isset($values['recipients']) ? $values['recipients'] : '');
                   
        $instructor = $form->createElement('checkbox', 'emailInstructors', array('label' => 'Email Instructors:'));
        $instructor->setValue(isset($values['emailInstructors']) ? $values['emailInstructors'] : '');                   
                        
        $subject = $form->createElement('text', 'subject', array('label' => 'Subject:'));
        $subject->setRequired(true)
                ->addFilter('StringTrim')
                ->addFilter('StripTags')
                ->setAttrib('maxlength', '128')
                ->setAttrib('style', 'width: 200px')
                ->setValue(isset($values['subject']) ? $values['subject'] : '');

        $message = $form->createElement('textarea', 'message', array('label' => 'Message:'));
        $message->setRequired(true)
                ->addFilter('StringTrim')
                ->addFilter('StripTags')
                ->setAttrib('style', 'width: 400px; height: 250px;')
                ->setValue(isset($values['message']) ? $values['message'] : '');                
        
        $submit = $form->createElement('submit', 'submitButton', array('label' => 'Send'));
        $submit->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formSubmit'))
                 ));

        $cancel = $form->createElement('button', 'cancel', array('label' => 'Cancel'));
        $cancel->setAttrib('id', 'cancel');
        $cancel->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formButton'))
                ));
        
        $form->addElements(array($recipients, $instructor, $subject, $message));

        $form->setElementDecorators(array(
                  'ViewHelper',
                  'Errors',
                  array('HtmlTag', array('tag' => 'div', 'class' => 'elm')),
                  array('Label', array('tag' => 'span')),
              ))
             ->addElements(array($submit, $cancel));

        if (isset($values['eventId'])) {

            $eventId = $form->createElement('hidden', 'eventId');
            $eventId->setValue($values['eventId']);
            $eventId->setDecorators(array(
                array('ViewHelper', array('helper' => 'formHidden'))
            ));

            $form->addElement($eventId);
        }
        return $form;
    }
    
    public function rollForm($values = array())
    {

        if (!isset($values['eventId'])) {
            throw new Ot_Exception_Data('Event ID must be provided');
        }
        
        $form = new Zend_Form();
        $form->setAttrib('id', 'rollForm')
             ->setDecorators(array(
                     'FormElements',
                     array('HtmlTag', array('tag' => 'div', 'class' => 'zend_form')),
                     'Form',
             ));

        $attendee = new Event_Attendee();
        
        $attendees = $attendee->getAttendeesForEvent($values['eventId'], 'attending');
        
        $otAccount = new Ot_Account();
        
        $attendeeList = array();
        foreach ($attendees as $a) {
            $thisAccount = $otAccount->find($a['accountId']);
            
            if (!is_null($thisAccount)) {
                $attendeeList[$a['accountId']] = $thisAccount->firstName . ' ' . $thisAccount->lastName . ' (' . $thisAccount->username . ')';
            }
        }
        
        $attendeeElement = $form->createElement('multiCheckbox', 'attendees');
        $attendeeElement->setMultiOptions($attendeeList)
                        ->setValue(isset($values['attendees']) ? $values['attendees'] : '');
                               
        $submit = $form->createElement('submit', 'submitButton', array('label' => 'Submit'));
        $submit->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formSubmit'))
                 ));

        $cancel = $form->createElement('button', 'cancel', array('label' => 'Cancel'));
        $cancel->setAttrib('id', 'cancel');
        $cancel->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formButton'))
                ));
        
        $form->addElements(array($attendeeElement));

        $form->setElementDecorators(array(
                  'ViewHelper',
                  'Errors',
                  array('HtmlTag', array('tag' => 'div', 'class' => 'elm')),
                  array('Label', array('tag' => 'span')),
              ))
             ->addElements(array($submit, $cancel));
             
        $eventId = $form->createElement('hidden', 'eventId');
        $eventId->setValue($values['eventId']);
        $eventId->setDecorators(array(
            array('ViewHelper', array('helper' => 'formHidden'))
        ));
        
        $form->addElement($eventId);

        return $form;
    }
}
