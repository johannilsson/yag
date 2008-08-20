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
 */

class PhotoDetails extends Yag_Db_Table
{
    protected $_name = 'photo_details';
    protected $_primary = 'photo_id';
    protected $_sequence = false;

    protected $_referenceMap = array(
        'Photo' => array(
            'columns'           => array('photo_id'),
            'refTableClass'     => 'Photos',
            'refColumns'        => array('id')
        ));

}
