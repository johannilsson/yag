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
 * GeoTags model
 *
 */
class GeoTags extends Yag_Db_Table
{
    /**
     * The table name
     *
     * @var string
     */
    protected $_name = 'geo_tags';

    /**
     * primary key
     *
     * @var string
     */
    protected $_primary = 'id';

    /**
     * map relaations
     *
     * @var string
     */
    protected $_dependentTables = array('Photos');

    protected $_filters = array(
			'*' => 'StringTrim',
    );

    protected $_validators = array(
        'name' => array(
            'Alnum', 
            'allowEmpty' => true,
            array('StringLength', 1, 50)),
    );

    public function findByName($name)
    {
        $where = $this->getAdapter()->quoteInto('name = ?', $name);
        return $this->fetchRow($where);
    }

    public function createFromExif(array $exif)
    {
        if (false === isset($exif['GPSVersion'])) {
            throw new RuntimeException('geo data is not present');
        }

        $latitude = new Yag_GeoCode($this->toDegreesMinutesSecondsFromExif($exif['GPSLatitude']));
        $longitude = new Yag_GeoCode($this->toDegreesMinutesSecondsFromExif($exif['GPSLongitude']));

        // TODO: validate if position already exists
        $geoTag = $this->createRow();
        $geoTag->latitude  = $latitude->toDecimalDegrees();
        $geoTag->longitude = $longitude->toDecimalDegrees();

        $geoTag->save();

        return $geoTag;
    }

    /**
     * Extracts and converts degrees minutes seconds from exif array.
     * 
     * Passed data should be either the GPSLatitude or GPSLongitude array.
     *
     * @param array $data
     * @return array
     */
	public function toDegreesMinutesSecondsFromExif(array $data)
	{
	    $parts = array();
	    foreach ($data as $part)
	    {
	        $values = explode('/', $part);
	        $s = $values[0] / $values[1];
	        $parts[] = $s;
	    }
	    return $parts;
	}
}
