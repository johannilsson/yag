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

require_once APPLICATION_PATH . '/models/DbTable/Photos.php';
require_once APPLICATION_PATH . '/models/DbTable/Tags.php';

/**
 * Tagged Photos
 *
 */
class TaggedPhotos extends Yag_Db_Table
{
    /**
     * Table name
     *
     * @var string
     */
    protected $_name = 'tagged_photos';

    /**
     * Reference map
     *
     * @var array
     */
    protected $_referenceMap    = array(
        'Tag' => array(
            'columns'           => array('tag_id'),
            'refTableClass'     => 'Tags',
            'refColumns'        => array('id')
        ),
        'Photo' => array(
            'columns'           => array('photo_id'),
            'refTableClass'     => 'Photos',
            'refColumns'        => array('id')
        ),
    );

    /**
     * Delete photo by its id
     *
     * @param int $id
     */
    public function deleteByPhotoId($id)
    {
        $where  = $this->getAdapter()->quoteInto('photo_id = ?', (int) $id);
        $resultSet = $this->fetchAll($where);

        foreach ($resultSet as $relation) {
            $relation->delete();
        }
    }

    /**
     * Assocciates photo with supplied tags, if tags does not exists 
     * they will be created.
     *
     * @param Zend_Db_Row $photo
     * @param array $tagNames
     */
    public function assocciatePhotoWith($photo, array $tagNames)
    {
        if ($photo instanceof Yag_Db_Table_Row) {
            $photoId = $photo->id;
        }
        $photoId = $photo;

        // Remove all references        
        $this->deleteByPhotoId($photoId);

        $tags = new Tags();

        $assoccTags = array();
        foreach ($tagNames as $name) {
            $name = trim($name); // TODO: should be fixed by a filter automagic
            if ($name == '') {
                continue; // skip bogus names in array.
            }
            $tag = $tags->findByName($name);

            if (null == $tag) {
                $tag = $tags->createRow();
                $tag->name = (string) $name;
                $tag->created_on = date('Y-m-d H:i:s'); // TODO: Should be handle behind
                $tag->save();
            }

            $taggedPhotos = $this->createRow(array(
                'tag_id'   => $tag->id, 
                'photo_id' => $photoId));

            $taggedPhotos->save();
            
            $assoccTags[] = $tag;
        }
        return $assoccTags;
    }    
}
