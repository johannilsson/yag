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

        // TODO: validate if position already exists
        $geoTag = $this->createRow();
        $geoTag->latitude  = new Yag_GeoCode($this->toDegreesMinutesSecondsFromExif($exif['GPSLatitude']));
        $geoTag->longitude = new Yag_GeoCode($this->toDegreesMinutesSecondsFromExif($exif['GPSLongitude']));
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

    /**
     * Degrees Minutes Seconds to Decimal Degrees
     * 
     * Decimal degrees = whole number of degrees, plus minutes divided by 60, plus seconds divided by 3600
     *
     * @param int|array $degrees
     * @param float $minutes
     * @param float $seconds
     * @return float
     */
    /*
    public function toDecimalDegrees($degrees, $minutes = 0, $seconds = 0)
    {
        if (is_array($degrees)) {
            $minutes = $degrees[1];
            $seconds = $degrees[2];
            $degrees = $degrees[0];
        }

	    $posNeg = 1;
	    if ($degrees < 0) { 
	        $posNeg = -1; 
	    }

        $decimalDegrees = $posNeg * $degrees + 1 * $minutes / 60 + 1 * $seconds / 3600;
        return $posNeg * $decimalDegrees;
    }
*/
    /**
     * calculates degrees minutes and seconds from supplied decimal degree value 
     *
     * @see http://geography.about.com/library/howto/htdegrees.htm
     * @param float $decimalDegrees
     * @return array
     */
    /*
    public function toDegreesMinutesSeconds($decimalDegrees)
    {
        // TODO: Fix error handling...
	    $pattern = '/^(-?[0-9]+)(.[0-9]+)/';
	    preg_match($pattern, $decimalDegrees, $matches);
	    $degrees = $matches[1];
	    preg_match($pattern, $matches[2] * 60, $matches);
	    $minutes = $matches[1];
	    $seconds = $matches[2] * 60;
	
	    return array($degrees, $minutes, $seconds);
    }
*/
}
