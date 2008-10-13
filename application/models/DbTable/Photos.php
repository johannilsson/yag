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
 * @package    Models
 * @copyright  Copyright (c) 2008 Johan Nilsson. (http://www.markupartist.com)
 * @license    New BSD License
 */

require_once APPLICATION_PATH . '/models/DbTable/TaggedPhotos.php';

/**
 * Photo Model
 */
class Photos extends Yag_Db_Table
{
    protected $_name = 'photos';
    protected $_primary = 'id';

    protected $_dependentTables = array('TaggedPhotos');


    public function insert(array $data)
    {
        $data['created_on'] = date('Y-m-d H:i:s');
        return parent::insert($data);
    }

    /**
     * Creates a temporary file.
     *
     * @param mixed $data
     * @return string
     */
    public function createTmpFile($data)
    {
        //$size = getimagesize($data);
        // TODO: Get correct extension
        // TODO: fixed tmp path
        $filename = '/tmp/app-' . date('Ymhis', time()) . '.jpg';
        file_put_contents($filename, $data);
        return $filename;
    }

    /**
     * Get prevoious or next nighbour of a photo. 
     *
     * @param Zend_Db_Table_Row_Abstract $photo
     * @param unknown_type $direction
     * @param unknown_type $album
     * @return unknown
     */
    public function getNeighbour(Zend_Db_Table_Row_Abstract $photo, $direction, $album = '')
    {
        $direction = ('next' == $direction) ? '>' : '<';
        $order     = ('>' == $direction) ? 'ASC' : 'DESC';

        $select = $this->select()->where('created_on ' . $direction  . ' ?', $photo->created_on)->order('created_on ' . $order)->limit(1);
        return $this->fetchRow($select);
    }
}
