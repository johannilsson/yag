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

require_once 'Zend/Db/Table/Rowset.php';

class Yag_Db_Table_Rowset extends Zend_Db_Table_Rowset
{
    protected $_rowClass = 'Yag_Db_Table_Row';
}
