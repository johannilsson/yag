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

class Photos extends Yag_Db_Table
{
    protected $_name = 'photos';
    protected $_primary = 'id';

    protected $_dependentTables = array('AlbumsPhotos', 'PhotoDetails');

    protected $_attachment = array(
		'column'      => 'file', 
		'store_path'  => PUBLIC_PATH,
        'manipulator' => 'ImageTransform',
		'styles' => array(
			'small' => array( 
			     'width' => 200), 
			'medium' => array( 
			     'width' => 400),
			'large' => array( 
			     'width' => 650),
        ),
    );

    /**
     * Load plugins
     *
     */
    protected function _setupPlugins()
    {
        $attachment = new Gem_Db_Table_Plugin_Attachment($this->_attachment);
        $extractExif = new Yag_Db_Table_Plugin_ExtractExif();

        $this->addPlugin($attachment);
        $this->addPlugin($extractExif);
    }

    /**
     * Creates a new photo
     *
     * @param array $params
     * @return Zend_Db_Table_Row_Abstract
     */
    public function createPhoto(array $params)
    {
        $params = new ArrayObject($params);

		$photo = $this->createRow();

		$photo->file        = $params->offsetGet('file');
		$photo->created_on  = date('Y-m-d H:i:s', time());
		$photo->title       = $params->offsetGet('title');
		$photo->description = $params->offsetGet('description');
		$photo->save();

		return $photo;
    }
    
    /**
     * Assocciates this photo with supplied albums, if albums does not exists 
     * they will be created.
     *
     * @param Zend_Db_Row $photo
     * @param array $albumNames
     */
    public function assocciateWith($photo, array $albumNames)
    {
        $albums = new Albums();
        $albumsPhotos = new AlbumsPhotos();
        $albumsPhotos->deleteByPhotoId($photo->id);

        foreach ($albumNames as $albumName) {
            if ($albumName == '') {
                continue; // skip bogus names in array.
            }
            $album = $albums->findByName(trim($albumName));

            if (null == $album) {
                $album = $albums->createRow();
                $album->name = (string)$albumName;
                $album->save();
            }

            $albumPhoto = $albumsPhotos->createRow(array(
                'album_id' => $album->id, 
                'photo_id' => $photo->id)
            );
            $albumPhoto->save();
        }
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
     * Reads exif data from photo
     *
     * @param Zend_Db_Table_Row_Abstract $photo
     * @return array
     */
    public function readExif(Zend_Db_Table_Row_Abstract $photo)
    {
        $exif = exif_read_data($photo->file->realPath());
        return $exif;
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
