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

require_once APPLICATION_PATH . '/models/DbTable/TaggedPhotos.php';

/**
 * Tag Model
 *
 */
class Tags extends Yag_Db_Table
{
    /**
     * Table name
     *
     * @var string
     */
    protected $_name = 'tags';

    /**
     * Primary key
     *
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Dependent tables
     *
     * @var unknown_type
     */
    protected $_dependentTables = array('TaggedPhotos');

    /**
     * Find by name
     *
     * @param string $name
     * @return unknown
     */
    public function findByName($name)
    {
        $where = $this->getAdapter()->quoteInto('name = ?', $name);
        return $this->fetchRow($where);
    }

    /**
     * Create tags from supplied array of tag names, if tag already exists it 
     * will just add it to the returned arrays.
     *
     * @param array $tagNames
     * @return array
     */
    public function createTags(array $tagNames)
    {
        $tags = array();
        foreach ($tagNames as $tagName) {
            if ($tagName != '') {
                $tag = $this->findByName($tagName);
                if (!$tag instanceof App_Db_Table_Row) {
                    $tag = $this->createRow();
                    $tag->name = trim($tagName);
                    $tag->save();
                }
                $tags[] = $tag;
            }
        }
        return $tags;
    }
}
