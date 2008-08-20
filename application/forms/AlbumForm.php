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
 * Album form
 *
 */
class AlbumForm extends Zend_Form
{
	public function __construct($options = null)
	{
		$this->addElementPrefixPath('Yag', 'Yag/');

		parent::__construct($options);

		$this->setName('album');

		$description = new Zend_Form_Element_Text('description');
		$description->setLabel('Description')
			->setRequired(true)
			->addValidator('NotEmpty');

		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('Name')
			->setRequired(true)
			->addValidator('NotEmpty');

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Upload');

		$this->addElements(array($description, $name, $submit));
	}
}
