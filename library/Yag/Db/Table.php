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

require_once 'Zend/Db/Table/Abstract.php';
require_once 'Zend/Filter/Input.php';

class Yag_Db_Table extends Zend_Db_Table_Abstract
{
    protected $_filters = array();
    protected $_validators = array();
    protected $_rowClass = 'Yag_Db_Table_Row';
    protected $_rowsetClass = 'Yag_Db_Table_Rowset';

    public function findOne()
    {
        $set = parent::find(func_get_args());

        if (count($set) == 0) {
            throw new Zend_Db_Table_Exception('Does not exist');
        }

        return $set->current();
    }

    public function getFilters()
    {
        return $this->_filters;
    }

    public function getValidators()
    {
        return $this->_validators;
    }

    public function filterData(array $data)
    {
        return new Zend_Filter_Input($this->getFilters(), $this->getValidators(), $data);
    }

}

