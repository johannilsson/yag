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

class GeoTaggedPhotos extends Yag_Db_Table
{
    /**
     * Name of the table
     *
     * @var string
     */
    protected $_name = 'geo_tagged_photos';

    /**
     * map relations
     *
     * @var array
     */
    protected $_referenceMap    = array(
        'GeoTag' => array(
            'columns'           => array('geo_tag_id'),
            'refTableClass'     => 'GeoTags',
            'refColumns'        => array('id')
        ),
        'Photo' => array(
            'columns'           => array('photo_id'),
            'refTableClass'     => 'Photos',
            'refColumns'        => array('id')
        ),
    );

    public function deleteByPhotoId($id)
    {
        $where  = $this->getAdapter()->quoteInto('photo_id = ?', $id);
        $resultSet = $this->fetchAll($where);

        foreach ($resultSet as $relation)
        {
            $relation->delete();
        }
    }

    public function assocciateFromExif(Zend_Db_Table_Row_Abstract $photo, array $exif)
    {
        $geoTags = new GeoTags();
        try {
            $geoTag = $geoTags->createFromExif($exif);

            $geoTaggedPhoto = $this->createRow(array(
                'geo_tag_id' => $geoTag->id, 
                'photo_id' => $photo->id)
            );
            $geoTaggedPhoto->save();

        } catch (RuntimeException $e) {
            ;
        }    
    }
}