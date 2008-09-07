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
class UploadPhotoForm extends Zend_Form
{
	public function __construct($options = null)
	{
		$this->addElementPrefixPath('Yag', 'Yag/');

		parent::__construct($options);

		$this->setName('upload');
		$this->setAttrib('enctype', 'multipart/form-data');

		$id = new Zend_Form_Element_Text('id');
		$id->setRequired(true)
			->addValidator('NotEmpty')
			->addValidator('Int');

        $file = new Zend_Form_Element_File('file');
        $file->setLabel('File')
          ->setOrder(100)
          ->setRequired(true)
          ->addValidator('NotEmpty');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Upload')
		  ->setOrder(200);

		$this->addElements(array(
            'id'     => $id,		  
            'file'   => $file, 
            'submit' => $submit
        ));
	}
}
