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
 * @package    Evaluation
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the evaluations
 *
 * @package    Evaluation
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Evaluation extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_evaluation';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'evaluationId';
    
    public function saveEvaluation($eventId, $accountId, $customAttributes)
    {
        
        $dba = $this->getAdapter();
        
        $inTransaction = false;
        
        try {
            $dba->beginTransaction();
        } catch (Exception $e) {
            $inTransaction = true;
        }
        
        $data = array('eventId' => $eventId,
                      'timestamp' => time()
                     );

        try {
            $evaluationId = $this->insert($data);
        } catch(Exception $e) {
            if (!$inTransaction) {
                $dba->rollBack();
            }
            throw $e;
        }
        
        $eu = new Evaluation_User();
        
        $data = array('eventId'   => $eventId,
                      'accountId' => $accountId
                     );
                     
        try {
            $eu->insert($data);
        } catch (Exception $e) {
            if (!$inTransaction) {
                $dba->rollBack();
            }
            throw $e;
        }
        
        $ca = new Ot_Custom();
        
        try {
            $ca->saveData('evaluations', $evaluationId, $customAttributes);
        } catch (Exception $e) {
            if (!$inTransaction) {
                $dba->rollBack();
            }
            throw $e;    
        }
        
        if (!$inTransaction) {
            $dba->commit();
        }
    }
    
    public function form()
    {
        $form = new Zend_Form();
        $form->setAttrib('id', 'workshopForm')
             ->setDecorators(array(
                     'FormElements',
                     array('HtmlTag', array('tag' => 'div', 'class' => 'eval_form')),
                     'Form',
             ));

		$ca = new Ot_Custom();
        $custom = $ca->getAttributesForObject('evaluations', 'Zend_Form');                
                    
        foreach ($custom as $c) {
        	$form->addElement($c['formRender']);
        }
        
        $submit = $form->createElement('submit', 'submitButton', array('label' => 'Submit Evaluation'));
        $submit->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formSubmit'))
                 ));

        $cancel = $form->createElement('button', 'cancel', array('label' => 'Cancel'));
        $cancel->setAttrib('id', 'cancel');
        $cancel->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formButton'))
                ));
        
        $form->setElementDecorators(array(
                  'ViewHelper',
                  'Errors',
                  array('HtmlTag', array('tag' => 'div', 'class' => 'elm')),
                  array('Label', array('tag' => 'span')),
              ))
             ->addElements(array($submit, $cancel));

        return $form;    	 	
    }
}