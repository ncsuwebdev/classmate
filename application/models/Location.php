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
 * @package    Location
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the locations
 *
 * @package    Location
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Location extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_location';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'locationId';
    
    /**
     * Gets the form for adding and editing a location
     *
     * @param array $values
     * @return Zend_Form
     */
    public function form($values = array())
    {
        $form = new Zend_Form();
        $form->setAttrib('id', 'locationForm')
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
              
        $status = $form->createElement('select', 'status', array('label' => 'Status:'));
        $status->addMultiOption('enabled', 'Enabled');
        $status->addMultiOption('disabled', 'Disabled');
        $status->setValue((isset($values['status']) ? $values['status'] : 'enabled'));        
        
        $capacity = $form->createElement('text', 'capacity', array('label' => 'Capacity:'));
        $capacity->setRequired(true)
                 ->addFilter('StringTrim')
                 ->addFilter('StripTags')
                 ->addValidator('Digits')
                 ->setAttrib('maxlength', '64')
                 ->setValue((isset($values['capacity']) ? $values['capacity'] : ''));

        $address = $form->createElement('text', 'address', array('label' => 'Address:'));
        $address->setRequired(true)
                ->addFilter('StringTrim')
                ->addFilter('StripTags')
                ->setAttrib('maxlength', '255')
                ->setValue((isset($values['address']) ? $values['address'] : ''));                 
              
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
        
        $form->addElements(array($name, $status, $capacity, $address, $description));

        $form->setElementDecorators(array(
                  'ViewHelper',
                  'Errors',
                  array('HtmlTag', array('tag' => 'div', 'class' => 'elm')),
                  array('Label', array('tag' => 'span')),
              ))
             ->addElements(array($submit, $cancel));

        if (isset($values['locationId'])) {

            $locationId = $form->createElement('hidden', 'locationId');
            $locationId->setValue($values['locationId']);
            $locationId->setDecorators(array(
                array('ViewHelper', array('helper' => 'formHidden'))
            ));

            $form->addElement($locationId);
        }
        return $form;
    }
}