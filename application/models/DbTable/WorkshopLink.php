<?php
/**
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
 * @package    WorkshopLink
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @version    SVN: $Id: Bug.php 156 2007-07-20 12:57:10Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Model to do deal with workshop links
 *
 * @package    WorkshopLink
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 *
 */
class App_Model_DbTable_WorkshopLink extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_WorkshopLink';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'workshopLinkId';
    
    public function getLinksForWorkshop($workshopId)
    {
        $where = $this->getAdapter()->quoteInto('workshopId = ?', $workshopId);
        return $this->fetchAll($where, 'order');
    }
    
    /**
     * Updates the display order of the URLs from a workshop.
     *
     * @param int $workshopId
     * @param array $order
     */
    public function updateLinkOrder($workshopId, $order)
    {
        $db = $this->getAdapter();
        $db->beginTransaction();

        $i = 1;
        foreach ($order as $o) {

            if (!is_integer($o)) {
                $db->rollback();
                throw new Internal_Exception_Data('New position was not an integer.');
            }

            $data = array("order" => $i);

            $where = $db->quoteInto('workshopLinkId = ?', $o) .
                     " AND " .
                     $db->quoteInto('workshopId = ?', $workshopId);

            try {
                $this->update($data, $where);
            } catch(Exception $e) {
                $db->rollback();
                throw $e;
            }
            $i++;
        }
        $db->commit();

        return true;
    }

   /**
     * Gets the form for adding and editing a document
     *
     * @param array $values
     * @return Zend_Form
     */
    public function form($values = array())
    {
        $form = new Zend_Form();
        $form->setAttrib('id', 'documentForm')
             ->setAttrib('enctype', 'multipart/form-data')
             ->setDecorators(array(
                     'FormElements',
                     array('HtmlTag', array('tag' => 'div', 'class' => 'zend_form')),
                     'Form',
             ));
             
        $name = $form->createElement('text', 'name', array('label' => 'Link Name:'));
        $name->setRequired(true)
              ->addFilter('StringTrim')
              ->addFilter('StripTags')
              ->setAttrib('maxlength', '255')
              ->setValue((isset($values['name']) ? $values['name'] : ''));
        
        $url = $form->createElement('text', 'url', array('label' => 'URL:'));
        $url->setRequired(true)
              ->addFilter('StringTrim')
              ->addFilter('StripTags')
              ->setAttrib('maxlength', '5000')
              ->setValue((isset($values['url']) ? $values['url'] : ''));
                     
        $form->addElements(array($name, $url));
                    
        $submit = $form->createElement('submit', 'submitButton', array('label' => 'Submit'));
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

        if (isset($values['workshopLinkId'])) {

            $workshopLinkId = $form->createElement('hidden', 'workshopLinkId');
            $workshopLinkId->setValue($values['workshopLinkId']);
            $workshopLinkId->setDecorators(array(
                array('ViewHelper', array('helper' => 'formHidden'))
            ));

            $form->addElement($workshopLinkId);
        }
        return $form;
    }        
}