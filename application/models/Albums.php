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

class Albums extends Yag_Db_Table
{
    protected $_name = 'albums';
    protected $_primary = 'id';

    protected $_dependentTables = array('AlbumsPhotos');

    protected $_filters = array(
			'*' => 'StringTrim',
    );

    protected $_validators = array(
        'name' => array(
            'Alnum', 
            'allowEmpty' => false,
            array('StringLength', 1, 50)),
        'description' => array(
            'Alpha', 
            'allowEmpty' => true, 
            array('StringLength', 1, 5),
    ),
    );

    public function findByName($name)
    {
        $where = $this->getAdapter()->quoteInto('name = ?', $name);
        return $this->fetchRow($where);
    }

}
