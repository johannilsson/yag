<?php
/**
 * YAG - Yet Another Gallery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @category   Yag
 * @package    Controllers
 * @copyright  Copyright (c) 2008 Johan Nilsson. (http://www.markupartist.com)
 * @license    New BSD License
 * @version
 */

/**
 * Photo form
 *
 */
class PhotoForm extends Zend_Form
{
	public function init()
	{
		$this->addElementPrefixPath('Yag', 'Yag/');

        $this->setName('photoform')
             ->setElementsBelongTo('photoform');

        $this->addElement('text', 'title', array(
            'label' => 'Title:', 
            'required' => true, 
            'filters' => array('StringTrim'), 
            )); 

        $this->addElement('text', 'description', array(
            'label' => 'Description:', 
            'required' => false, 
            'filters' => array('StringTrim'), 
            ));
 
        $this->addElement('text', 'taken_on', array(
            'label' => 'Taken on:', 
            'required' => false, 
            'filters' => array('StringTrim'), 
            )); 

        $this->addElement('text', 'tags', array(
            'label' => 'Tags:', 
            'required' => false, 
            'filters' => array('StringTrim'), 
            )); 

        $this->addElement('submit', 'submit', array(
            'label' => 'Save',
            'ignore' => true, // Will not end up in the request params 
            ));
	}
}
