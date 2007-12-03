<?php
/**
 * 
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
 * @package    
 * @subpackage Ot_Auth_Adapter_Interface
 * @category   Authentication Interface
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: Interface.php 42 2007-05-22 12:28:29Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Interface to build all Authentication Adapters
 *
 * @package    
 * @subpackage Ot_Auth_Adapter_Interface
 * @category   Authentication Interface
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
interface Ot_Auth_Adapter_Interface
{
    /**
     * Tells the adapter if it can auto login or not.
     *
     * @return boolean
     */
    public static function autoLogin();

    /**
     * If the adapter can auto login, it needs to be able to perform some action
     * on logout.  This function does any cleaning up needed by the authentication.
     *
     */
    public static function autoLogout();

	/**
	 * Flag to tell the app where the authenticaiton is managed.  If set to true,
	 * the application will use its oww interface to interact with the adapter.
	 *
	 * @return boolean
	 */
    public static function manageLocally();
    
    
    /**
     * Flag to tell the app whether a user can sign up on their own
     * 
     * @return boolean
     *
     */
    public static function allowUserSignUp();
}