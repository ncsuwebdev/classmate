<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * This license is also available via the world-wide-web at
 * http://itdapps.ncsu.edu/bsd.txt
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to itappdev@ncsu.edu so we can send you a copy immediately.
 *
 * @package    IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @version    SVN: $Id: IndexController.php 197 2007-08-01 15:17:51Z gplocke@EOS.NCSU.EDU $
 */

/**
 * Main index controller
 *
 * @package    IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class IndexController extends Zend_Controller_Action 
{
    /**
     * shows the homepage
     *
     */
    public function indexAction()
    {                        
        $get = Zend_Registry::get('getFilter');
        
        if (isset($get->shelf)) {
            $this->view->hideFeature = true;
        }
        
        $event = new App_Model_DbTable_Event();
        $upcoming = $event->getEvents(null, null, null, time(), null, 'open', 5)->toArray();
        
        $workshop = new App_Model_DbTable_Workshop();
        
        foreach ($upcoming as &$u) {
            $u['workshop'] = $workshop->find($u['workshopId'])->toArray();
            if (Zend_Auth::getInstance()->hasIdentity()) {
                $u['status'] = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity()->accountId, $u['eventId']);
            } else {
                $u['status'] = '';
            }
        }
        
        $this->view->upcoming = $upcoming;
        
        $searchTerm = new App_Model_DbTable_SearchTerm();
        
        $this->view->popularSearchTerms = $searchTerm->getTopSearchTerms(5)->toArray();
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->view->loggedIn = true;
        
            $myEvents = $event->getEventsForUser(Zend_Auth::getInstance()->getIdentity()->accountId);
        
            $this->view->myEvents = $myEvents['currentEvents'];
            $this->view->account = Zend_Auth::getInstance()->getIdentity()->toArray();
        }
        
        $this->_helper->layout->setLayout('homepage');
        $this->view->messages = $this->_helper->flashMessenger->getMessages();        
    }
    
    public function historyAction()
    {
        $account = new Ot_Account();
        
        $thisAccount = Zend_Auth::getInstance()->getIdentity();
        
        $get = Zend_Registry::get('getFilter');
        if (isset($get->accountId)) {
            if ($this->_helper->hasAccess('edit-all-reservations', 'workshop_signup')) {
                $thisAccount = $account->find($get->accountId);
            }
            
            if (is_null($thisAccount)) {
                throw new Ot_Exception_Data('msg-error-accountNotFound');
            }
        }
        
        if (is_null($thisAccount)) {
            throw new Ot_Exception_Data('msg-error-notLoggedIn');
        }
        
        $this->view->acl = array(
            'editAllReservations' => $this->_helper->hasAccess('edit-all-reservations', 'workshop_signup'),
        );
        
        $form = new Zend_Form();
        $form->setAttrib('id', 'accountForm')
             ->setMethod(Zend_Form::METHOD_GET)
             ->setDecorators(array(
                     'FormElements',
                     array('HtmlTag', array('tag' => 'div', 'class' => 'filterForm')),
                     'Form',
             ));
             
        $accountSelect = $form->createElement('select', 'accountId', array('label' => 'default-index-history:viewReservations'));
        $accountSelect->setRequired(false);
                
        $account = new Ot_Account();
        $accounts = $account->fetchAll(null, array('lastName', 'firstName'));
        
        foreach ($accounts as $a) {
            $accountSelect->addMultiOption($a->accountId, $a->firstName . ' ' . $a->lastName . ' (' . $a->username . ')');
        }
                    
        $submit = $form->createElement('submit', 'submitButton', array('label' => 'default-index-history:lookup'));
        $submit->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formSubmit'))
                 ));
        
        $form->addElements(array($accountSelect));

        $form->setElementDecorators(array(
                  'ViewHelper',
                  'Errors',
                  array('HtmlTag', array('tag' => 'div', 'class' => 'elm')),
                  array('Label', array('tag' => 'span')),
              ))
             ->addElements(array($submit));
             
        $this->view->form = $form;
        
        $event = new App_Model_DbTable_Event();
        
        $myEvents = $event->getEventsForUser($thisAccount->accountId);
        
        $this->view->myEvents = $myEvents['currentEvents'];
        $this->view->myPastEvents = $myEvents['pastEvents'];
        $this->view->account = $thisAccount->toArray();

        $this->view->hideFeature = true;
        $this->_helper->layout->setLayout('my');
        $this->view->messages = $this->_helper->flashMessenger->getMessages();    

        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.autocomplete.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.autocomplete.css');         
    }
}
