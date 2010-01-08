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
 * @package    Workshop_EvaluateController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Handles all interaction with evalutaions
 *
 * @package    Workshop_EvaluateController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop_EvaluateController extends Zend_Controller_Action 
{   
    /**
     * Handles the evaluation for an event.  Shows the user the evaluation and
     * saves the data from the evaluation.
     *
     */
    public function indexAction()
    {
        $get = Zend_Registry::get('getFilter');
             
        if (!isset($get->eventId)) {
            throw new Ot_Exception_Input('msg-error-eventIdNotSet');
        }
        
        $event      = new Event();
        $eu         = new Evaluation_User();
        $evaluation = new Evaluation();

        $thisEvent = $event->find($get->eventId);
        if (is_null($thisEvent)) {
            throw new Ot_Exception_Data('msg-error-noEvent');
        }   
        $this->view->event = $thisEvent->toArray();
        
        $thisAccount = Zend_Auth::getInstance()->getIdentity();
        
        $status = $event->getStatusOfUserForEvent($thisAccount->accountId, $thisEvent->eventId);
            
        if ($status == "instructor") {
            throw new Ot_Exception_Access('msg-error-cannotEval');
        }
            
        if ($status != "attending") {
            throw new Ot_Exception_Access('msg-error-notAttended');
        }        
        
        $config = Zend_Registry::get('config');
        
        $endDt = strtotime($thisEvent->date . " " . $thisEvent->endTime);
        
        if (time() > ($endDt + ($config->user->numHoursEvaluationAvailability->val * 3600))) {
            throw new Ot_Exception_Access('msg-error-evalEnded');        
        }
        
        if ($eu->hasCompleted($thisAccount->accountId, $thisEvent->eventId)) {
        	throw new Ot_Exception_Access('msg-error-alreadyEval');
        }
                    
        $workshop = new Workshop();
        $thisWorkshop = $workshop->find($thisEvent->workshopId);        
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        $this->view->workshop = $thisWorkshop->toArray();
            
        $instructor = new Event_Instructor();
        $instructors = $instructor->getInstructorsForEvent($thisEvent->eventId);   

        $inst = array();
        foreach ($instructors as $i) {
        	$inst[] = $i['firstName'] . ' ' . $i['lastName'];
        }
        
        $this->view->instructors = $inst;
            
        // lookup the location of the event
        $location = new Location();
        $thisLocation = $location->find($thisEvent->locationId);
        if (is_null($thisLocation)) {
            throw new Ot_Exception_Data('msg-error-noLocation');
        }
        $this->view->location = $thisLocation->toArray();

        $form = $evaluation->form();

        if ($this->_request->isPost()) {
        	if ($form->isValid($_POST)) {
        	    
        		$custom = new Ot_Custom();
                $attributes = $custom->getAttributesForObject('evaluations');
        
                $data = array();
                foreach ($attributes as $a) {
                    $data[$a['attributeId']] = (is_null($form->getValue('custom_' . $a['attributeId']))) ? '' : $form->getValue('custom_' . $a['attributeId']);
                }                   
                    
	            // custom attributes is the custom array that will be save by the CustomAttributes model
	            $evaluation->saveEvaluation($thisEvent->eventId, $thisAccount->accountId, $data);
	            
	            $this->_helper->flashMessenger->addMessage('msg-info-evalThanks');
	        
	            $this->_redirect('/');        		
        	}
        }   

        $this->view->form = $form;
        $this->_helper->pageTitle('workshop-evaluate-index:title');
    }
}