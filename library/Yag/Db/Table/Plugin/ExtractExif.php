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
 * @package    Yag_Db_Table
 * @subpackage Yag_Db_Table_Plugin
 * @copyright  Copyright (c) 2008 Johan Nilsson. (http://www.markupartist.com)
 * @license    New BSD License
 */

/**
 * Plugin that extracts photo details from a photo when saved
 *
 */
class Yag_Db_Table_Plugin_ExtractExif extends Zend_Db_Table_Plugin_Abstract
{
    private $_extractExif = false;

    public function preSaveRow(Zend_Db_Table_Row_Abstract $photo)
    {
        $this->_extractExif = $photo->isModified('file');
    }

    /**
     * 
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return void
     */
    public function postSaveRow(Zend_Db_Table_Row_Abstract $photo)
    {
        if (false === $this->_extractExif 
            || 'Photos' != get_class($photo->getTable())) {
            return;
        }

        $exif = $photo->getTable()->readExif($photo);

        $photoDetails = new PhotoDetails();
        $photoDetails->createFromExif($photo, $exif);
    }
}
