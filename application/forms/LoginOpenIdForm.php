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
 * OpenId Login Form 
 *
 * From zoup
 */
class LoginOpenIdForm extends Zend_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setName('login');
        $openidIdentifier = new Zend_Form_Element_Text('openid_identifier');
        $openidIdentifier->setLabel('Open id')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty');

        $submit = new Zend_Form_Element_Submit('login');
        $submit->setAttrib('id', 'submitbutton');
        $this->addElements(array($openidIdentifier, $submit));
    }
}
