<?php
class Internal_Controller_Action extends Zend_Controller_Action 
{
	protected $_acl = null;
	
	protected $_role = null;
	
	protected $_resource = null;
	
	protected $_logger = null;
	
	public function init()
	{
        $zcf = Zend_Controller_Front::getInstance();

        $this->_acl      = $zcf->getParam('acl');
        $this->_role     = Ot_Authz::getInstance()->getRole();
        $this->_resource = strtolower($zcf->getRequest()->module . '_' . $zcf->getRequest()->controller);

        $this->_logger = Zend_Registry::get('logger');		
        
	    if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->view->loggedInUser = Zend_Auth::getInstance()->getIdentity();
        }        
	}
}
?>