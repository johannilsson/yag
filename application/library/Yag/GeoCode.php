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
 * @package    Yag_GeoCode
 * @copyright  Copyright (c) 2008 Johan Nilsson. (http://www.markupartist.com)
 * @license    New BSD License
 */

/**
 * Geo Code representation
 *
 * Example, will output 59° 17' 48.6276", 18° 0' 2.4804"
 * <code>
 * $lat = Yag_GeoCode::createFromDecimalDegrees(59.296841);
 * echo $lat . ", "; 
 * $lon = Yag_GeoCode::createFromDecimalDegrees(18.000689); 
 * echo $lon . "\n";
 * </code> 
 */
class Yag_GeoCode
{
    private $_degrees;
    private $_minutes;
    private $_seconds;
    private $_hemisphere;

    /**
     * Constructor
     *
     * @param float|array $degrees
     * @param int $minutes
     * @param int $seconds
     */
    public function __construct($degrees, $minutes = 0, $seconds = 0, $hemisphere = '')
    {
        if (is_array($degrees) && count($degrees) == 3) {
            $hemisphere = $minutes;
            $minutes    = $degrees[1];
            $seconds    = $degrees[2];
            $degrees    = $degrees[0];
        }
        $this->_degrees    = $degrees;
        $this->_minutes    = $minutes;
        $this->_seconds    = $seconds;
        $this->_hemisphere = $hemisphere;
    }

    /**
     * calculates degrees minutes and seconds from supplied decimal degree value 
     *
     * @see http://geography.about.com/library/howto/htdegrees.htm
     * @param float $decimalDegrees
     * @return this
     */
    public static function createFromDecimalDegrees($decimalDegrees)
    {
        // TODO: needs to know if lon or lat to calculate hemisphere... 
        // TODO: Fix error handling and validation, isset is temporary for now...
        $pattern = '/^(-?[0-9]+)(.[0-9]+)/';
        preg_match($pattern, $decimalDegrees, $matches);
        $degrees = isset($matches[1]) ? $matches[1] : 0;

        preg_match($pattern, $matches[2] * 60, $matches);
        $minutes = isset($matches[1]) ? $matches[1] : 0;
        $seconds = isset($matches[2]) ? $matches[2] : 0;
        $seconds = $seconds * 60;

        return new self($degrees, $minutes, $seconds);
    }

    /**
     * Extracts and converts degrees minutes seconds from exif array.
     *
     * Passed data should be the full exif data, will internally read from 
     * GPSLatitude, GPSLatitudeRef, GPSLongitude and GPSLongitudeRef.
     *
     * Example: 
     * <code>
     *
     * </code>
     *
     * @param array $data
     * @return array with lon and lat keys representing longitude and latitude.
     */
    public static function createFromExif(array $data)
    {
        $lonParts = self::readFromExif($data['GPSLongitude'], $data['GPSLongitudeRef']);
        $latParts = self::readFromExif($data['GPSLatitude'], $data['GPSLatitudeRef']);
        return array (
            'lon' => new self($lonParts, $data['GPSLongitudeRef']), 
            'lat' => new self($latParts, $data['GPSLatitudeRef'])
        );
    }

    public static function readFromExif(array $cordinatates, $hemisphere) 
    {
        $parts = array();        
        foreach ($cordinatates as $part)
        {
            $values = explode('/', $part);
            $s = $values[0] / $values[1];
            $parts[] = $s;
        }

        if (in_array($hemisphere, array('W', 'S'))) {
            $parts[0] = -1 * $parts[0];
        }

        return $parts;
    }

    /**
     * Degrees Minutes Seconds to Decimal Degrees
     * 
     * Decimal degrees = whole number of degrees, plus minutes divided by 60, plus seconds divided by 3600
     *
     * @return float
     */
    public function toDecimalDegrees()
    {
        $posNeg = 1;
        if ($this->_degrees < 0) { 
            $posNeg = -1; 
        }

        $decimalDegrees = $posNeg * $this->_degrees + 1 * $this->_minutes / 60 + 1 * $this->_seconds / 3600;
        return $posNeg * $decimalDegrees;
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString()
    {
        if (($degrees = $this->_degrees) < 0) {
            $degrees *= -1;
        }

        return sprintf("%s° %s' %s\" %s", 
            $degrees, 
            $this->_minutes, 
            $this->_seconds, 
            $this->_hemisphere);
    }
}
