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
 * @package    Reporting_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Handles all reporting functionality
 *
 * @package    Reporting_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Reporting_IndexController extends Zend_Controller_Action 
{   
    /**
     * The main workshop page.  It has the list of all the workshops that are 
     * available in the system.
     *
     */
    public function indexAction()
    {   
        $messages = array();
                     
        $report = new App_Model_Report();
        $form = $report->form();
        
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                
                $fromDate = strtotime($form->getValue('fromDate'));
                $toDate = strtotime($form->getValue('toDate'));
                
                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setNeverRender();
                $report->getReportCsv($fromDate, $toDate);                                
            } else {
                $messages[] = $this->view->translate('msg-error-formSubmitProblem');
            }
        }
        
        $this->view->messages = $messages;
        $this->view->form = $form;
        $this->_helper->pageTitle("reporting-index-index:title");
    }
}