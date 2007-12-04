<?php
class ErrorController extends Internal_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        $this->getResponse()->clearBody();
        
        switch ($errors->type) {
	        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
	        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
	            // 404 error -- controller or action not found
	            $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
	            $this->view->title = 'ERROR! 404 Error: Page not found';
	            $this->view->message      = 'The requested page was not found';
	            break;
	        default:
	            $exception = $errors->exception;
	            if ($exception instanceof Ot_Exception) {
	            	$this->view->title = 'ERROR! ' . $exception->getTitle();
	            } else {
	            	$this->view->title = 'ERROR! Processing request failed.';
	            	$this->view->showTrackback = true;
	            	$this->view->trackback = $exception->getTrace();
	            }
	            
	            $this->view->message      = $exception->getMessage();
	            break;
        }
    }
}
?>