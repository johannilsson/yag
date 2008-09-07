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
 * Places model
 *
 */
class Places extends Yag_Db_Table
{
    /**
     * The table name
     *
     * @var string
     */
    protected $_name = 'places';

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
        'name' => array('Alnum', 'allowEmpty' => true, array('StringLength', 1, 50)),
    );

    public function findByName($name)
    {
        $where = $this->getAdapter()->quoteInto('name = ?', $name);
        return $this->fetchRow($where);
    }

    /**
     * Creates a new place from exif data
     *
     * @param array $exif
     */
    public function createFromExif(array $exif)
    {
        if (false === isset($exif['GPSVersion'])) {
            return null;
        }

        $latitude = Yag_GeoCode::createFromExif($exif['GPSLatitude']);
        $longitude = Yag_GeoCode::createFromExif($exif['GPSLongitude']);
        $latitudeDd = $latitude->toDecimalDegrees();
        $longitudeDd = $longitude->toDecimalDegrees();

        $place = null;
        try {
            $place = $this->createRow();
            $place->latitude  = sprintf('%10.6f', $latitudeDd);
            $place->longitude = sprintf('%10.6f', $longitudeDd);
            $place->save();
        } catch (Zend_Db_Statement_Exception $e) {
            preg_match('/Duplicate entry \'([0-9.]+)-([0-9.]+)\'/', $e->getMessage(), $matches);

            if (empty($matches)) {
                return null;
            }

            $select = $this->select()
                ->where('longitude = ?', (float) $matches[1])
                ->where('latitude = ?', (float) $matches[2]);
            $place = $this->fetchRow($select);
        }

        return $place;
    }

}
