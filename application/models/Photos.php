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

/**
 * Photo Model
 */
class Photos extends Yag_Db_Table
{
    protected $_name = 'photos';
    protected $_primary = 'id';

    protected $_dependentTables = array('TaggedPhotos');

    protected $_referenceMap    = array(
        'PhotoDetail' => array(
            'columns'           => 'id',
            'refTableClass'     => 'PhotoDetails',
            'refColumns'        => 'photo_id'
        ),
    );

    /**
     * Gem configuration
     */
    protected $_attachment = array(
		'column'      => 'image', 
		'store_path'  => PUBLIC_PATH,
        'manipulator' => 'ImageTransform',
		'styles' => array(
			'square' => array( 
			     'size' => 'c75x75'),
			'small' => array( 
			     'size' => '240x240'), 
			'medium' => array( 
			     'size' => '500x500'),
			'large' => array( 
			     'size' => '1024x1024'),
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

		$photo->image       = $params->offsetGet('image');
		$photo->created_on  = date('Y-m-d H:i:s', time());
		$photo->title       = $params->offsetGet('title');
		$photo->description = $params->offsetGet('description');
		$photo->save();

		return $photo;
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
        $exif = exif_read_data($photo->image->realPath());
        return $exif;
    }

    /**
     *
     * @param Zend_Db_Table_Row_Abstract $photo
     * @param array $exif
     * @return Zend_Db_Table_Row_Abstract
     */
    public function addExif(Zend_Db_Table_Row_Abstract $photo, array $exif)
    {
		$photo->make                 = @$exif['Make'];
		$photo->model                = @$exif['Model'];
		$photo->exposure             = @$exif['ExposureTime'];
		$photo->focal_length         = @$exif['FNumber'];
		$photo->iso_speed            = @$exif['ISOSpeedRatings'];
		$photo->taken_on             = @$exif['DateTimeOriginal'];
		$photo->shutter_speed        = @$exif['ShutterSpeedValue'];
		$photo->aperture             = @$exif['ApertureValue'];
		$photo->flash                = @$exif['Flash'];
		$photo->exposure             = @$exif['ExposureMode'];
		$photo->white_balance        = @$exif['WhiteBalance'];

        // Add lon and lat data if available
        if (true === isset($exif['GPSVersion'])) {
            $latitude = Yag_GeoCode::createFromExif($exif['GPSLatitude']);
            $longitude = Yag_GeoCode::createFromExif($exif['GPSLongitude']);

            $photo->latitude  = $latitude->toDecimalDegrees();
            $photo->longitude = $longitude->toDecimalDegrees();
        }

        $photo->save();

        return $photo;
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
