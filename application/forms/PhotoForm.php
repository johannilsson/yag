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
class PhotoForm extends Zend_Form
{
	public function __construct($options = null)
	{
		$this->addElementPrefixPath('Yag', 'Yag/');

		parent::__construct($options);

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

		$takenOn = new Zend_Form_Element_Text('taken_on');
		$takenOn->setLabel('Taken on')
			->setRequired(false);
			/*->addValidator('Date');*/

		$id = new Zend_Form_Element_Hidden('id');
		$id->setRequired(true)
			->addValidator('NotEmpty')
			->addValidator('Int');

        $tags = new Zend_Form_Element_Text('tags');
        $tags->setLabel('Tags (Separeted by comma)')
            ->setOrder(5)
            ->setRequired(false)
            ->addValidator('NotEmpty');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save')
		  ->setOrder(200);

        $this->addElements(array(
                'id'          => $id,
                'title'       => $title,
                'description' => $description,
                'taken_on'    => $takenOn,
                'tags'        => $tags,
                'submit'      => $submit,
            )
        );
		
		$this->setName('photo');
	}
}
