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
    protected $_rowClass = 'Yag_Db_Table_Row';
    protected $_rowsetClass = 'Yag_Db_Table_Rowset';

    public function getCols()
    {
        return $this->_cols;
    }

}

