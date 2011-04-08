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
 * @package    Workshop
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the workshop categories
 *
 * @package    Workshop
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Category extends Ot_Db_Table
{
	/**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_workshop_category';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'categoryId';
    
    public function form($values = array()) {
    	
    	$form = new Zend_Form();
        $form->setAttrib('id', 'categoryForm')
             ->setDecorators(array(
                     'FormElements',
                     array('HtmlTag', array('tag' => 'div', 'class' => 'zend_form')),
                     'Form',
             ));
             
        $name = $form->createElement('text', 'name', array('label' => 'Name:'));
        $name->setRequired(true)
              ->addFilter('StringTrim')
              ->addFilter('StripTags')
              ->setAttrib('maxlength', '64')
              ->setValue((isset($values['name']) ? $values['name'] : ''));
              
        $description = $form->createElement('textarea', 'description', array('label' => 'Description:')); 
        $description->setRequired(false)
                    ->addFilter('StringTrim')
                    ->setAttrib('style', 'width: 95%; height: 300px;')
                    ->setValue((isset($values['description']) ? $values['description'] : ''));
                    
        $submit = $form->createElement('submit', 'submitButton', array('label' => 'Submit'));
        $submit->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formSubmit'))
                 ));

        $cancel = $form->createElement('button', 'cancel', array('label' => 'Cancel'));
        $cancel->setAttrib('id', 'cancel');
        $cancel->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formButton'))
                ));
        
        $form->addElements(array($name, $description));

        $form->setElementDecorators(array(
                  'ViewHelper',
                  'Errors',
                  array('HtmlTag', array('tag' => 'div', 'class' => 'elm')),
                  array('Label', array('tag' => 'span')),
              ))
             ->addElements(array($submit, $cancel));

        if (isset($values['categoryId'])) {

            $categoryId = $form->createElement('hidden', 'categoryId');
            $categoryId->setValue($values['categoryId']);
            $categoryId->setDecorators(array(
                array('ViewHelper', array('helper' => 'formHidden'))
            ));

            $form->addElement($categoryId);
        }
        return $form;
    }
}