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
 * @subpackage Workshop_EvaluateController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Handles all interaction with evalutaions
 *
 * @package    Classmate
 * @subpackage Workshop_EvaluateController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop_EvaluateController extends Internal_Controller_Action 
{   
    /**
     * Handles the evaluation for an event.  Shows the user the evaluation and
     * saves the data from the evaluation.
     *
     */
    public function indexAction()
    {
        
        $filter = Zend_Registry::get('inputFilter');        
        $userId = Zend_Auth::getInstance()->getIdentity();
        
        $ca = new CustomAttribute();

        if (!$this->_request->isPost()) {
        
            $get = Zend_Registry::get('get');
            
            if (!isset($get['eventId'])) {
                throw new Internal_Exception_Input('Event ID not set');
            }
            
            $eventId = $filter->filter($get['eventId']);
            
            if ($eventId == '') {
                throw new Internal_Exception_Input('Event ID has no value');
            }
            
            $this->view->eventId = $eventId;
            
            $event = new Event();
            $thisEvent = $event->find($eventId);
            
            if (is_null($thisEvent)) {
                throw new Internal_Exception_Data('Event not found');
            }
            
            $this->view->event = $thisEvent->toArray();
            
            $status = $event->getStatusOfUserForEvent($userId, $eventId);
            
            if ($status != "attending") {
                throw new Internal_Exception_Access('You are not on record as having attended this event');
            }
            
            $evalUser = new EvaluationUser();
            $where = $evalUser->getAdapter()->quoteInto('eventId = ?', $eventId);
            $where .= " AND ";
            $where .= $evalUser->getAdapter()->quoteInto('userId = ?', $userId);
            $alreadyEvaluated = $evalUser->fetchAll($where);
            
            if ($alreadyEvaluated->count() > 0) {
                throw new Internal_Exception_Access('You appear to have already evaluated this event');
            }
            
            $workshop = new Workshop();
            $thisWorkshop = $workshop->find($thisEvent->workshopId);        
            if (is_null($thisWorkshop)) {
                throw new Internal_Exception_Data('Workshop not found');
            }
            $this->view->workshop = $thisWorkshop->toArray();
            
            $instructor = new Instructor();
            $instructorList = $instructor->getInstructorsForEvent($eventId);
            if (is_null($instructorList)) {
                throw new Internal_Exception_Data('Instructors not found');
            }
            $this->view->instructors = $instructorList;
            
            // lookup the location of the event
            $location = new Location();
            $thisLocation = $location->find($thisEvent->locationId);
            if (is_null($thisLocation)) {
                throw new Internal_Exception_Data('Location not found');
            }
            $this->view->location = $thisLocation->toArray();
            
            // lookup the workshop category
            $wc = new WorkshopCategory();
            $category = $wc->find($thisWorkshop->workshopCategoryId);
            if (is_null($category)) {
                throw new Internal_Exception_Data('Category not found');
            }
            $this->view->category = $category->toArray();
            
            $this->view->title = "Evaluate " . $thisWorkshop->title;
            $this->view->hideTitle = true;
            
            $this->view->javascript = array('slidingTabs.js');
            
            $this->view->custom = $ca->getData('evaluations', $userId, 'form');
            
        } else {
            
            $post = Zend_Registry::get('post');
            
            $eventId = $filter->filter($post['eventId']);

            $customAttributes = $post['custom'];
                    
            foreach ($customAttributes as &$c) {
                $c = $filter->filter($c);
            }
            
            $evaluation = new Evaluation();
            
            // custom attributes is the custom array that will be save by the CustomAttributes model
            $evaluation->saveEvaluation($eventId, $userId, $customAttributes);
            
            $this->_redirect('workshop/evaluate/thanks');
        }        
    }
    
    /**
     * The page the user is redirected to after taking the evaluation.
     *
     */
    public function thanksAction()
    {
        $this->view->title = "Thanks for your evaluation!";
    }
    
    /**
     * Displays teh results of an evaluation as long as the user requesting the
     * page is an instructor of the event.
     *
     */
    public function resultsAction()
    {
        $filter = Zend_Registry::get('inputFilter');        
        $userId = Zend_Auth::getInstance()->getIdentity();
        
        $get = Zend_Registry::get('get');
        
        if (!isset($get['eventId'])) {
            throw new Internal_Exception_Input('Event ID not set');
        }
        
        $eventId = $filter->filter($get['eventId']);
        
        if ($eventId == '') {
            throw new Internal_Exception_Input('Event ID has no value');
        }
        
        $this->view->eventId = $eventId;
        
        $event = new Event();
        $thisEvent = $event->find($eventId);
        
        if (is_null($thisEvent)) {
            throw new Internal_Exception_Data('Event not found');
        }
        
        $this->view->event = $thisEvent->toArray();
        
        $workshop = new Workshop();
        $thisWorkshop = $workshop->find($thisEvent->workshopId);        
        if (is_null($thisWorkshop)) {
            throw new Internal_Exception_Data('Workshop not found');
        }
        $this->view->workshop = $thisWorkshop->toArray();
        
        $instructor = new Instructor();
        $instructorList = $instructor->getInstructorsForEvent($eventId);
        
        if (is_null($instructorList)) {
            throw new Internal_Exception_Data('Instructors not found');
        }
        $this->view->instructors = $instructorList;
        
        $instructors = array();
        foreach ($instructorList as $i) {
            $instructors[] = $i['userId'];
        }
        
        if (!in_array($userId, $instructors)) {
            throw new Internal_Exception_Data('You do not appear to be an instructor for this event.');
        }
        
        // lookup the location of the event
        $location = new Location();
        $thisLocation = $location->find($thisEvent->locationId);
        if (is_null($thisLocation)) {
            throw new Internal_Exception_Data('Location not found');
        }
        $this->view->location = $thisLocation->toArray();
        
        // lookup the workshop category
        $wc = new WorkshopCategory();
        $category = $wc->find($thisWorkshop->workshopCategoryId);
        if (is_null($category)) {
            throw new Internal_Exception_Data('Category not found');
        }
        $this->view->category = $category->toArray();
        
        // get the evaluationId from the eventId
        $evaluation = new Evaluation();
        $where = $evaluation->getAdapter()->quoteInto('eventId = ?', $eventId);
        $evaluations = $evaluation->fetchAll($where);
        if (is_null($evaluations) || $evaluations->count() == 0) {
            throw new Internal_Exception_Data('No evaluations found for this event');
        }
        
        $evaluationResults = array();
        
        
        $ca = new CustomAttribute();        
        
        
        $this->view->title = "Evaluation results for " . $thisWorkshop->title;
        $this->view->hideTitle = true;
    }
}