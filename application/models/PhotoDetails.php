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
 * Photo Details
 *
 */
class PhotoDetails extends Yag_Db_Table
{
    protected $_name = 'photo_details';
    protected $_primary = 'photo_id';
    protected $_sequence = false;

    protected $_dependentTables = array('Photos');

    /**
     * Creates or updates from exif data, will also try to create a GeoTag row
     * if possible.
     *
     * @param Zend_Db_Table_Row_Abstract $photo
     * @param array $exif
     * @return Zend_Db_Table_Row_Abstract
     */
    public function createFromExif(Zend_Db_Table_Row_Abstract $photo, array $exif)
    {
        $select = $this->select()->where('photo_id = ?', $photo->id);
        if (null === ($photoDetail = $this->fetchRow($select))) {
            $photoDetail = $this->createRow();
            $photoDetail->photo_id = $photo->id;            
        }

		$photoDetail->make                 = @$exif['Make'];
		$photoDetail->model                = @$exif['Model'];
		$photoDetail->orientation          = @$exif['Orientation'];
		$photoDetail->exposure_time        = @$exif['ExposureTime'];
		$photoDetail->f_number             = @$exif['FNumber'];
		$photoDetail->iso_speed_ratings    = @$exif['ISOSpeedRatings'];
		$photoDetail->date_time_original   = @$exif['DateTimeOriginal'];
		$photoDetail->date_time_digitized  = @$exif['DateTimeDigitized'];
		$photoDetail->shutter_speed_value  = @$exif['ShutterSpeedValue'];
		$photoDetail->aperture_value       = @$exif['ApertureValue'];
		$photoDetail->light_source         = @$exif['LightSource'];
		$photoDetail->flash                = @$exif['Flash'];
		$photoDetail->exposure_mode        = @$exif['ExposureMode'];
		$photoDetail->white_balance        = @$exif['WhiteBalance'];
		$photoDetail->digital_zoom_ratio   = @$exif['DigitalZoomRatio'];
		$photoDetail->scene_capture_type   = @$exif['SceneCaptureType'];
		$photoDetail->gain_control         = @$exif['GainControl'];

        // Add lon and lat data if available
        if (true === isset($exif['GPSVersion'])) {
            $latitude = Yag_GeoCode::createFromExif($exif['GPSLatitude']);
            $longitude = Yag_GeoCode::createFromExif($exif['GPSLongitude']);

            $photoDetail->latitude  = $latitude->toDecimalDegrees();
            $photoDetail->longitude = $longitude->toDecimalDegrees();
        }

        $photoDetail->save();

        return $photoDetail;
    }
}
