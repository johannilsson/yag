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
 * @package    Yag_Db
 * @subpackage Yag_Db_Table
 * @copyright  Copyright (c) 2008 Johan Nilsson. (http://www.markupartist.com)
 * @license    New BSD License
 */

require_once 'Zend/Db/Table/Row.php';

class Yag_Db_Table_Row extends Zend_Db_Table_Row
{
    protected $_filterInput = null;

    public function getErrorMessages()
    {
        return $this->_filterInput->getMessages();
    }

    public function save()
    {
        $this->_filterInput = $this->_getTable()->filterData($this->_data);

        if (false === $this->_filterInput->isValid()) {
            throw new RuntimeException('invalid data');
        }
        parent::save();
    }

}
