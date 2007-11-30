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
 * @package    RSPM
 * @subpackage Admin_EmailController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Manages the emails that are sent from the application based on set event
 * triggers.
 *
 * @package    RSPM
 * @subpackage Admin_EmailController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 */
class Admin_EmailController extends Internal_Controller_Action  
{	
    /**
     * Shows all availabe email triggers
     */
    public function indexAction()
    {
        $this->view->title = "Email Admin";
        
        $this->view->acl = array(
            'details'   => $this->_acl->isAllowed($this->_role, $this->_resource, 'details'),
            );
            
        $et = new EmailTrigger();
       
        $emailTriggers = $et->fetchAll()->toArray();
        
        $this->view->emailTriggers = $emailTriggers;
    }
    
    /**
     * Shows email templates setup for the selected trigger
     *
     */
    public function detailsAction()
    {
        $this->view->acl = array(
            'add'    => $this->_acl->isAllowed($this->_role, $this->_resource, 'add'),
            'edit'   => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
            'delete' => $this->_acl->isAllowed($this->_role, $this->_resource, 'delete'),
            );        
        
        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
            
        $this->view->triggerId = $triggerId = $filter->filter($get['triggerId']);
        
        $this->view->title = "Email Templates for $triggerId";        
        
        $et = new EmailTemplate();

        $where = $et->getAdapter()->quoteInto('triggerId = ?', $triggerId);
        $this->view->templates = $et->fetchAll($where)->toArray();
    }
    
    /**
     * Edit a template
     *
     */
    public function editAction()
    {       
        $filter = Zend_Registry::get('inputFilter');       

        if ($this->_request->isPost()) {
            
            $post = Zend_Registry::get('post');

            $triggerId = $filter->filter($post['triggerId']);
            
            $data = array(
                        'emailTemplateId' => $filter->filter($post['emailTemplateId']),
                        'name'            => $filter->filter($post['name']),
                        'to'              => $filter->filter($post['emailTo']),
                        'subject'         => $filter->filter($post['emailSubject']),
                        'body'            => $filter->filter($post['emailBody'])
                    );
                    
            $et = new EmailTemplate();
            $et->update($data, null);
            
            $this->_logger->setEventItem('attributeName', 'emailTemplateId');
            $this->_logger->setEventItem('attributeId', $data['emailTemplateId']);
            $this->_logger->info('Template Modified');             
                        
            
            $this->_redirect("admin/email/details/?triggerId=$triggerId");            
            
        } else {
        
            $get = Zend_Registry::get('get');
                
            $emailTemplateId = $filter->filter($get['emailTemplateId']);
            
            $this->view->title = "Edit Email Template";        
                      
            $et = new EmailTemplate();
            $template = $et->find($emailTemplateId)->toArray();
            
            $this->view->template = $template;
            
            $triggerVars = new EmailTriggerVariable();
            
            $where = $triggerVars->getAdapter()->quoteInto('triggerId = ?', $template['triggerId']);
            $this->view->templateVars = $triggerVars->fetchAll($where)->toArray();
        }
    }
    
    /**
     * delete a template
     *
     */
    public function deleteAction()
    {
        $filter = Zend_Registry::get('inputFilter');
        $get    = Zend_Registry::get('get');
        
        $emailTemplateId = $filter->filter($get['emailTemplateId']);
        $triggerId       = $filter->filter($get['triggerId']);
        
        $et = new EmailTemplate();

        $where = $et->getAdapter()->quoteInto('emailTemplateId = ?', $emailTemplateId);
        $et->delete($where);
        
        $this->_logger->setEventItem('attributeName', 'triggerId');
        $this->_logger->setEventItem('attributeId', $triggerId);
        $this->_logger->info('Trigger deleted');           
        
        $this->_redirect("admin/email/details/?triggerId=$triggerId");
    }
    
    /**
     * Add a new template
     *
     */
    public function addAction()
    {       
        $filter = Zend_Registry::get('inputFilter');       

        if ($this->_request->isPost()) {
            
            $post = Zend_Registry::get('post');

            $triggerId = $filter->filter($post['triggerId']);
            
            $data = array(
                        'triggerId'       => $triggerId,
                        'name'            => $filter->filter($post['name']),
                        'to'              => $filter->filter($post['emailTo']),
                        'subject'         => $filter->filter($post['emailSubject']),
                        'body'            => $filter->filter($post['emailBody'])
                    );
                    
            $et = new EmailTemplate();
            $et->insert($data);
            
            $this->_logger->setEventItem('attributeName', 'triggerId');
            $this->_logger->setEventItem('attributeId', $triggerId);
            $this->_logger->info('Trigger added');  
            
            $this->_redirect("admin/email/details/?triggerId=$triggerId");            
            
        } else {
        
            $get = Zend_Registry::get('get');
            
            $this->view->title = "Add Email Template";
                
            $this->view->triggerId = $triggerId = $filter->filter($get['triggerId']);
            
            $triggerVars = new EmailTriggerVariable();
            
            $where = $triggerVars->getAdapter()->quoteInto('triggerId = ?', $triggerId);
            $this->view->templateVars = $triggerVars->fetchAll($where)->toArray();
        }
    }
}