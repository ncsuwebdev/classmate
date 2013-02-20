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
        
        $event      = new App_Model_DbTable_Event();
        $eu         = new App_Model_DbTable_Evalutaion_User();
        $evaluation = new App_Model_DbTable_Evalutaion();

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
        
        $vr = new Ot_Var_Register();
        
        $endDt = strtotime($thisEvent->date . " " . $thisEvent->endTime);
        
        if (time() > ($endDt + ($vr->getVar('numHoursEvaluationAvailability')->getValue() * 3600))) {
            throw new Ot_Exception_Access('msg-error-evalEnded');        
        }
        
        if ($eu->hasCompleted($thisAccount->accountId, $thisEvent->eventId)) {
            throw new Ot_Exception_Access('msg-error-alreadyEval');
        }
                    
        $workshop = new App_Model_DbTable_Workshop();
        $thisWorkshop = $workshop->find($thisEvent->workshopId);        
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        $this->view->workshop = $thisWorkshop->toArray();
            
        $instructor = new App_Model_DbTable_EventInstructor();
        $instructors = $instructor->getInstructorsForEvent($thisEvent->eventId);   

        $inst = array();
        foreach ($instructors as $i) {
            $inst[] = $i['firstName'] . ' ' . $i['lastName'];
        }
        
        $this->view->instructors = $inst;
            
        // lookup the location of the event
        $location = new App_Model_DbTable_Location();
        $thisLocation = $location->find($thisEvent->locationId);
        if (is_null($thisLocation)) {
            throw new Ot_Exception_Data('msg-error-noLocation');
        }
        $this->view->location = $thisLocation->toArray();
        
        if ($thisEvent->evaluationType == 'custom') {
            $form = $evaluation->form();
            $this->view->form = $form;
        }
        
        if ($this->_request->isPost()) {
            
            if ($thisEvent->evaluationType == 'custom') {
            
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
                
            } elseif ($thisEvent->evaluationType == 'google' && isset($_POST['googleSubmit'])) {

                $eu = new App_Model_DbTable_Evalutaion_User();
                $dba = $eu->getAdapter();
                
                $dba->beginTransaction();
            
                $data = array('eventId'   => $get->eventId,
                              'accountId' => $thisAccount->accountId
                        );
                             
                try {
                    $eu->insert($data);
                } catch (Exception $e) {
                       $dba->rollBack();
                    throw $e;
                }
                
                $dba->commit();
                
                $this->_helper->flashMessenger->addMessage('msg-info-evalThanks');
                
                $this->_redirect('/');
            }
        }   

        if ($thisEvent->evaluationType == 'google') {
            $evaluationKeys = new App_Model_DbTable_Evalutaion_Key();
            
            $keys = $evaluationKeys->find($get->eventId);
            
            if (is_null($keys)) {
                throw new Ot_Exception_Data('msg-error-noFormKey');
            }
            
            $this->view->keys = $keys->toArray();
        }
        
        $this->_helper->pageTitle('workshop-evaluate-index:title');
    }
}