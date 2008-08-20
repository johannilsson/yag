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
class UploadForm extends Zend_Form
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

        $file = new Zend_Form_Element_File('file');
        $file->setLabel('File')
          ->setOrder(100)
          ->setRequired(true)
          ->addValidator('NotEmpty');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Upload')
		  ->setOrder(200);

		$this->addElements(array(
		  'title' => $title, 
		  'description' => $description,
		  'file' => $file, 
		  'submit' => $submit
        ));
	}
}
