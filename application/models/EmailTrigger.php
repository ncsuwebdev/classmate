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
 * @subpackage EmailTrigger
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the email triggers
 *
 * @package    RSPM
 * @subpackage EmailTrigger
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class EmailTrigger extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_email_trigger';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'triggerId';
    
    
    /**
     * The variables to be replaced in the email
     *
     * @var unknown_type
     */
    protected $_emailVars = array();
    
       
    /**
     * Overrides the set method so that we can wrap the variables for the email
     * in a nice package. 
     *
     * @param unknown_type $name
     * @param unknown_type $value
     */
    public function __set($name, $value)
    {
        $this->_emailVars[$name] = $value;        
    }
    
    /**
     * Sets an array of email variables
     *
     * @param array $data
     */
    public function setVariables(array $data)
    {
    	$this->_emailVars = array_merge($this->_emailVars, $data);
    }
    
    /**
     * Dispatches (sends) the email specified 
     * 
     * @param int $triggerId
     */
    public function dispatch($triggerId)
    {
        
        $emailTemplate        = new EmailTemplate();
        $emailTriggerVariable = new EmailTriggerVariable();
        
        $where = $emailTriggerVariable->getAdapter()->quoteInto('triggerId = ?', $triggerId);
        $expectedVars = $emailTriggerVariable->fetchAll($where)->toArray();
        
        foreach ($expectedVars as $e) {
            if (!isset($this->_emailVars[$e['variable']])) {
                throw new Internal_Exception_Data($e['variable'] . " must be set");
            }
        }

        $where = $emailTemplate->getAdapter()->quoteInto('triggerId = ?', $triggerId);
        $templates = $emailTemplate->fetchAll($where)->toArray();
        
        $userConfig = Zend_Registry::get('userConfig');
        
        $fromEmail = $userConfig['fromEmailAddress'];
        $fromName  = $userConfig['fromEmailName'];
                    
        foreach ($templates as $template) {
            
            foreach ($this->_emailVars as $key=>$value) {
                
                if (is_array($value)) {
                    $value = implode(", ", $value);
                }
                    
                $template['to'] = str_replace("[[$key]]", $value, $template['to']);
                $template['subject'] = str_replace("[[$key]]", $value, $template['subject']);
                $template['body'] = str_replace("[[$key]]", $value, $template['body']);                        
            }

            $mail = new Zend_Mail();
            $mail->setBodyText($template['body'])
                 ->setFrom($fromEmail, $fromName)
                 ->setSubject($template['subject']);
                 
            $to = explode(',', $template['to']);
            foreach ($to as $t) {
            	$mail->addTo(trim($t));
            }
            
            $mail->send();
        }            
    }    
}

