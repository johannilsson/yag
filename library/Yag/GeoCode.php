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
    private $_degrees = 0;
    private $_minutes = 0;
    private $_seconds = 0;
    //private $_hemisphere; // TODO: could be calculated
    
    /**
     * Constructor
     *
     * @param float|array $degrees
     * @param int $minutes
     * @param int $seconds
     */
    public function __construct($degrees = 0, $minutes = 0, $seconds = 0)
    {
        if (is_array($degrees) && count($degrees) == 3) {
            $minutes = $degrees[1];
            $seconds = $degrees[2];
            $degrees = $degrees[0];
        }

        $this->_degrees = $degrees;
        $this->_minutes = $minutes;
        $this->_seconds = $seconds;
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
        // TODO: Fix error handling...
        $pattern = '/^(-?[0-9]+)(.[0-9]+)/';
        preg_match($pattern, $decimalDegrees, $matches);
        $degrees = $matches[1];
        preg_match($pattern, $matches[2] * 60, $matches);
        $minutes = $matches[1];
        $seconds = $matches[2] * 60;

        return new self($degrees, $minutes, $seconds);
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
        return sprintf("%s° %s' %s\"", $this->_degrees, $this->_minutes, $this->_seconds);
    }
}
