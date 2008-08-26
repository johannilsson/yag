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

class AlbumsPhotos extends Yag_Db_Table
{
	protected $_name = 'albums_photos';

	protected $_referenceMap    = array(
				'Album' => array(
					'columns'           => array('album_id'),
					'refTableClass'     => 'Albums',
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
}