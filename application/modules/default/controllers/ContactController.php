<?php

/**
 * Cyclone
 *
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
 * @package    Cyclone (Default)
 * @subpackage IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: IndexController.php 197 2007-08-01 15:17:51Z gplocke@EOS.NCSU.EDU $
 */

/**
 * Contact Controller 
 * 
 * @package    Cyclone (Default)
 * @subpackage IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class ContactController extends Internal_Controller_Action 
{
    /**
     * Displays the contact form
     *
     */
    public function indexAction()
    {
        $this->view->title = 'Contact Classmate';
        
        if ($this->_request->isPost()) {
            
            $post   = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            
            $name    = $filter->filter($post['name']);
            $email   = $filter->filter($post['email']);
            $content = $filter->filter($post['content']);
           
            $uc = Zend_Registry::get('userConfig');
            
            $mail = new Zend_Mail();
            $mail->setBodyText($content)
                 ->setFrom($email, $name)
                 ->addTo($uc['contactEmailAddress']['value'], $uc['contactEmailName']['value'])
                 ->setSubject($uc['contactEmailSubject']['value'])
                 ->send();
                 
            $data = array(
                       'name'    => $name,
                       'email'   => $email,
                       'content' => $content
                    );
                 
            $trigger = new EmailTrigger();
            $data['userId'] = Zend_Auth::getInstance()->getIdentity();
            $trigger->setVariables($data);
            $trigger->dispatch('Contact_Email_Sent');
                 
            $this->_redirect('/contact/thanks');
        }
    }
    
    /**
     * The page people see after submitting an email
     * 
     */
    public function thanksAction()
    {
        $this->view->title = 'Thanks for your email!';
    }
}