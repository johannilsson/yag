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
class PhotoForm extends UploadForm
{
	public function __construct($options = null)
	{
		$takenOn = new Zend_Form_Element_Text('taken_on');
		$takenOn->setLabel('Taken on')
			->setRequired(false);
			/*->addValidator('Date');*/

		$id = new Zend_Form_Element_Hidden('id');
		$id->setRequired(true)
			->addValidator('NotEmpty')
			->addValidator('Int');

        $albums = new Zend_Form_Element_Text('albums');
        $albums->setLabel('Albums (Separeted by comma)')
            ->setOrder(5)
            ->setRequired(false)
            ->addValidator('NotEmpty');

		parent::__construct($options);

        $this->addElements(array(
                'id' => $id,
                'taken_on' => $takenOn,
                'albums' => $albums,
            )
        );

		$file = $this->getElement('file');
		$file->setLabel('Upload new');
		$file->setRequired(false);
		
		$this->setName('photo');
	}
}
