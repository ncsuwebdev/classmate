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
 * @package    ErrorController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @version    SVN: $Id: $
 */

/**
 * Processes any error thrown inthe application
 *
 * @package    ErrorController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 */
class ErrorController extends Zend_Controller_Action
{
    /**
     * Error action to process any errors coming from the application
     *
     */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        $config = Zend_Registry::get('config');
        
        $this->getResponse()->clearBody();
                
        $title = '';
        $message = '';
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
                $message = 'default-index-error:404:message';
                $title = 'default-index-error:404:header';
                break;
            default:
                $exception = $errors->exception;
                if ($exception instanceof Ot_Exception) {
                    $title = $exception->getTitle();
                } else {
                    $title = 'default-index-error:generic';
                }
                
                $this->view->showTrackback = $config->user->showTrackbackOnErrors->val;
                $this->view->trackback     = $exception->getTrace();
                $message = $exception->getMessage();
                break;
        }
        
        $this->_helper->pageTitle($title);
        
        $this->view->title = $this->view->translate('default-index-error:title') . ' ' . $this->view->title;
        $this->view->message = $message;
    }
}