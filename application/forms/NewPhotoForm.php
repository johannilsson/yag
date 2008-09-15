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
 * Upload form
 *
 * @see http://akrabat.com/2008/05/16/simple-zend_form-file-upload-example-revisited/
 */
class NewPhotoForm extends Zend_Form
{
	public function __construct($options = null)
	{
		$this->addElementPrefixPath('Yag', 'Yag/');

		parent::__construct($options);

		$this->setName('upload');
		$this->setAttrib('enctype', 'multipart/form-data');

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title')
            ->setOrder(1)
            ->setRequired(false)
            ->addValidator('NotEmpty');

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description')
            ->setOrder(2)
            ->setRequired(false)
            ->addValidator('NotEmpty');

        $file1 = new Zend_Form_Element_File('file1');
        $file1->setLabel('1')
          ->setOrder(101)
          ->setRequired(true)
          ->addValidator('NotEmpty');
/*
        $file2 = new Zend_Form_Element_File('file2');
        $file2->setLabel('2')
          ->setOrder(102)
          ->setRequired(true)
          ->addValidator('NotEmpty');

        $file3 = new Zend_Form_Element_File('file3');
        $file3->setLabel('3')
          ->setOrder(103)
          ->setRequired(true)
          ->addValidator('NotEmpty');

        $file4 = new Zend_Form_Element_File('file4');
        $file4->setLabel('4')
          ->setOrder(104)
          ->setRequired(true)
          ->addValidator('NotEmpty');

        $file5 = new Zend_Form_Element_File('file5');
        $file5->setLabel('5')
          ->setOrder(105)
          ->setRequired(true)
          ->addValidator('NotEmpty');

        $file6 = new Zend_Form_Element_File('file6');
        $file6->setLabel('6')
          ->setOrder(106)
          ->setRequired(true)
          ->addValidator('NotEmpty');
*/
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Upload')
		  ->setOrder(200);

		$this->addElements(array(
		  'title' => $title, 
		  'description' => $description,
		  'file1' => $file1,
/*		  'file2' => $file2, 
		  'file3' => $file3,
 		  'file4' => $file4,
 		  'file5' => $file5,
 		  'file6' => $file6,*/
		  'submit' => $submit
        ));
	}
}
