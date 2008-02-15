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
 * @package    Cyclone
 * @subpackage Internal_Auth_Adapter_DbAuth
 * @category   Authenticaiton Adapter
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: DbAuth.php 42 2007-05-22 12:28:29Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * This adapter uses a local database to authenticate a user.  This version of DbAuth
 * requires that "dbAdapter" be set with the PDO database adapter in the Zend
 * registry, however if a different database is required, a new adapter can be setup
 * in the constructor and assigned to the $_db class variable.
 *
 * @package    Cyclone
 * @subpackage Internal_Auth_Adapter_DbAuth
 * @category   Authenticaiton Adapter
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 *
 */
class Internal_Auth_Adapter_DbAuth implements Zend_Auth_Adapter_Interface, Ot_Auth_Adapter_Interface, Ot_Auth_Adapter_InterfaceLocal
{

    /**
     * Username of the user to authenticate
     *
     * @var string
     */
    protected $_userId = '';

    /**
     * Password of the user to authenticate
     *
     * @var string
     */
    protected $_password = '';

    /**
     * Default from address to be added to the email
     *
     * @var unknown_type
     */
    protected $_defaultFrom = 'no-reply@ncsu.edu';

    /**
     * Database adapter
     *
     * @var object
     */
    protected $_db = null;
    
    protected $_adapterName = 'local';

    /**
     * Constructor to create new object
     *
     * @param string $username
     * @param string $password
     */
    public function __construct($userId = '', $password = '')
    {
        $this->_userId   = $userId;
        $this->_password = $password;
        $this->_db       = Zend_Registry::get('dbAdapter');
    }

    /**
     * Authenticates the user passed by the constructor.
     *
     * @return new Zend_Auth_Result object
     */
    public function authenticate()
    {

        $select = $this->_db->select();

        $select->from('tbl_auth_user')
               ->where ('userId = ?', $this->_userId)
               ->where ('password = ?', md5($this->_password));

        $result = $this->_db->fetchAll($select);

        if (count($result) == 0) {
            return new Zend_Auth_Result(false, null, array('User "' . $this->_userId . '" with password not found.'));
        }

        return new Zend_Auth_Result(true, $result[0]['userId'], array());
	}

	/**
	 * Sets the autologin to false so that it uses native login mechanism
	 *
	 * @return boolean
	 */
	public static function autoLogin()
	{
	    return false;
	}

	/**
	 * Does nothing on logout since Zend_Auth handles it all
	 *
	 */
	public static function autoLogout()
	{
	    Zend_Auth::getInstance()->clearIdentity();
	}

	/**
	 * Flag to tell the app where the authenticaiton is managed
	 *
	 * @return boolean
	 */
	public static function manageLocally()
	{
	    return true;
	}

    /**
     * flag to tell the app whether a user can sign up or not
     *
     * @return boolean
     */	
    public static function allowUserSignUp()
    {
        return true;
    }	
    
	/**
	 * Tells the application whenter the user has an account or not
	 *
	 * @param int $userId
	 * @return boolean
	 */
	public function hasAccount($userId)
	{

        $result = $this->getUser($userId);

        if (count($result) == 0) {
            return false;
        }

        return true;
	}

	/**
	 * Gets a user by ID from the system
	 *
	 * @param int $userId
	 * @return array with userId, password, and email
	 */
	public function getUser($userId)
	{
        $select = $this->_db->select();

        $select->from('tbl_auth_user');

        if ($userId != '') {
            $select->where('userId = ?', $userId);
        } else {
            $select->where('1 = 1');
        }

        $select->order('userId');

        return $this->_db->fetchAll($select);
	}

	/**
	 * Adds an account to the system, emailing the generated password to the user
	 *
	 * @param int    $userId
	 * @param string $password
	 * @param string $email
	 */
	public function addAccount($userId, $password, $email)
	{
	    $config = Zend_Registry::get('config');

	    if ($password == '') {
	        $password = substr(md5(date('r')), 2, 8);
	    }

        $data = array(
            'userId'  => $userId,
            'password' => $this->encryptPassword($password),
            'email'    => $email
            );

        $this->_db->insert('tbl_auth_user', $data);

        $mail = new Zend_Mail();
        $mail->addTo($email);
        $mail->setFrom($this->_defaultFrom);
        $mail->setSubject('You have been given a new ' . $config->branding->appTitle . ' Account');

        $body = "A new ClassMate account has been created for you.\n\n" .
                "Login Type: ClassMate Account\n" . 
                "User ID: " . preg_replace('/@.*/i', '', $userId) . "\n" .
                "Password: " . $password . "\n" .
                "\n\n" .
                "To log in, go to " . $this->_getUrl() . " and enter in this information.";

        $mail->setBodyText($body);
        $mail->send();
	}

	/**
	 * Edits an account in the system
	 *
	 * @param int    $userId
	 * @param string $password
	 * @param string $email
	 * @return results from Zend_Db::update
	 */
	public function editAccount($userId, $password, $email)
	{
        $data = array(
            'email'    => $email
            );

        if ($password != '') {
            $data['password'] = $this->encryptPassword($password);
        }
        $where = $this->_db->quoteInto('userId = ?', $userId);

        return $this->_db->update('tbl_auth_user', $data, $where);
	}

	/**
	 * Deletes an account from the system
	 *
	 * @param int $userId
	 * @return results from Zend_Db::delete
	 */
	public function deleteAccount($userId)
	{
	    $where = $this->_db->quoteInto('userId = ?', $userId);

	    return $this->_db->delete('tbl_auth_user', $where);
	}

	/**
	 * Resets a users password and emails them the new pass
	 *
	 * @param int $userId
	 */
	public function resetPassword($userId)
	{
	    $config = Zend_Registry::get('config');

	    $password = substr(md5(date('r')), 2, 8);

        $data = array(
            'password' => $this->encryptPassword($password)
            );

        $where = $this->_db->quoteInto('userId = ?', $userId);

        $this->_db->update('tbl_auth_user', $data, $where);

        $user = $this->getUser($userId);

        if (count($user) != 1) {
            throw new Exception('User entered not found');
        }

        $user = $user[0];

        $mail = new Zend_Mail();
        $mail->addTo($user['email']);
        $mail->setFrom($this->_defaultFrom);
        $mail->setSubject('Your password for ' . $config->branding->appTitle . ' has been reset.');

        $body = "Your password for ClassMate has been reset.  Use the following credentials:\n\n" .
                "Login Type: ClassMate Account\n" . 
                "User ID: " . preg_replace('/@.*/i', '', $userId) . "\n" .
                "Password: " . $password . "\n" .
                "\n\n" .
                "To log in, go to " . $this->_getUrl() . " and enter in this information.";

        $mail->setBodyText($body);
        $mail->send();
	}


	/**
	 * Encrypts the password
	 *
	 * @param string $password
	 * @return string
	 */
	public function encryptPassword($password)
	{
	    return md5($password);
	}

	/**
	 * Gets the current URL
	 *
	 * @return string
	 */
	protected function _getUrl()
	{
        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";

        $protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;

        $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);

        $url = $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/index.php')) . '/';

        return $url;
	}

}