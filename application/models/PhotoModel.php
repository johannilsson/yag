<?php

require_once 'AbstractModel.php';

class PhotoModel extends AbstractModel
{
    protected $_fields = array();

    protected $_imageVariants = array(
	    'square'  => array('geometry' => 'c75x75'),
	    'small'   => array('geometry' => '240x240'), 
	    'medium'  => array('geometry' => '500x500'),
	    'large'   => array('geometry' => '1024x1024'),
    );

    /** 
     * Retrieve table object 
     * 
     * @return 
     */  
     public function getTable() 
     {
        if (null === $this->_table) { 
            require_once APPLICATION_PATH . '/models/DbTable/Photos.php'; 
            $this->_table = new Photos; 
        } 
        return $this->_table; 
    } 

     public function getForm() 
     {
        if (null === $this->_form) { 
            require_once APPLICATION_PATH . '/forms/PhotoForm.php'; 
            $this->_form = new PhotoForm; 
        } 
        return $this->_form; 
    }

    public function getFields()
    {
        if (null === $this->_fields) {
            $this->_fields = $this->getTable()->info('cols');
        }
        return $this->_fields;
    }

    public function getImageVariants()
    {
        return $this->_imageVariants;
    }

    public function add(array $data, $file)
    {
        $values = array_merge(
            $this->_getValues($data), 
            $this->getImageDetails($file));

        // If created on is provided force it to be set.
        // This alows for import scripts to have previous created dates set.
        if (isset($data['created_on'])) {
            $values['created_on'] = $data['created_on'];
        }

        // If a clean title is not provided or is empty set it.
        if (!isset($values['clean_title']) || $values['clean_title'] = '') {
            $values['clean_title'] = $values['title'];
        }
        $sanitizeFilter = new Yag_Filter_SanitizeString();
        $values['clean_title'] = $sanitizeFilter->filter($values['clean_title']);

        $id = $this->getTable()->insert(
            $this->_getTableValues($values));

        $this->saveImage($id, $file);

        return $id;
    }

    public function update(array $data, $id)
    {
        $values = $this->_getValues($data);

        // If a clean title is not provided or is empty set it.
        if (!isset($values['clean_title']) || $values['clean_title'] = '') {
            $values['clean_title'] = $values['title'];
        }
        $sanitizeFilter = new Yag_Filter_SanitizeString();
        $values['clean_title'] = $sanitizeFilter->filter($values['clean_title']);

        $where = $this->getTable()->getAdapter()->quoteInto('id = ?', $id);

        $this->getTable()->update(
            $this->_getTableValues($values),
            $where);

        // csv list of tags
        if (isset($values['tags'])) {
            $this->addTags($id, explode(',', $values['tags']));
        }

        return $id;
    }

    public function addTags($photo, array $tags)
    {
        require_once APPLICATION_PATH . '/models/DbTable/TaggedPhotos.php';
        $taggedPhotos = new TaggedPhotos;

        return $taggedPhotos->assocciatePhotoWith($photo, $tags);
    }

    public function fetchTags($photo)
    {
        if (is_numeric($photo)) {
            $photo = $this->fetchEntry($photo);
        }
    
        $tags = $photo->findTagsViaTaggedPhotosByPhoto();

        return $tags;
    }

    public function saveImage($photo, $file)
    {
        if (!file_exists($file)) {
            throw new InvalidArgumentException('Provided file does not exists');
        }

        if (is_numeric($photo)) {
            $photo = $this->fetchEntry($photo);
        }

        $photo->image = basename($file);

        $imageFileName = $this->getImageFileName($photo);

        // Create destination directory if not exists
        // If passed file is the same we can skip this
        if ($file != $imageFileName) {
            $destination = dirname($imageFileName);
            if (false == is_dir($destination)) {
                if (@mkdir($destination, 0755, true) == false) {
                    throw new Exception('Could not create: ' . $destination);
                }
            }
            copy($file, $imageFileName);
        }

        $photo->save();

        $this->applyManipulations($imageFileName);

        return $photo;
    }

    public function applyManipulations($file)
    {
        if (false == file_exists($file)) {
            throw new Exception('File does not exists');
        }

        $storePath = dirname($file);
        $baseName  = basename($file);

        $manipulator = new Yag_Manipulator('ImageTransform');
        foreach ($this->_imageVariants as $variant => $options) {
            $manipulator->manipulate(
                $file, 
                $storePath . DIRECTORY_SEPARATOR. $this->getImageBaseName($baseName, $variant), 
                $options);
        }
    }

    public function getImageFileName($photo, $variant = null)
    {
        $path = array(
            PUBLIC_PATH, 
            'photo', 
            date('Y', strtotime($photo->created_on)),
            date('m', strtotime($photo->created_on)),
            date('d', strtotime($photo->created_on)),
            $this->getImageBaseName($photo->image, $variant)
        );
        return implode(DIRECTORY_SEPARATOR, $path);
    }

    private function getImageBaseName($baseName, $variant = '')
    {
        if ('' == $variant) {
            return $baseName;
        }
        return $variant . '-' . $baseName;
    }

    // TODO: Move to a file model...
    private function getImageDetails($file)
    {
        if (false == file_exists($file)) {
            throw new Exception('File does not exists');
        }
        $exifSupport = array(IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM);
        if (!in_array(exif_imagetype($file), $exifSupport)) {
            return array();
        }

        // TODO: Verify that we are dealing with an exif friendly file...
        $exif = exif_read_data($file);

        // Add lon and lat data if available
        $latitude = $longitude = null;
        if (true === isset($exif['GPSVersion'])) {
            $codes = Yag_GeoCode::createFromExif($exif);

            $latitude = $codes['lat'];
            $longitude = $codes['lon'];

            $latitude  = $latitude->toDecimalDegrees();
            $longitude = $longitude->toDecimalDegrees();
        }

        $data = array(
		    'make'              => @$exif['Make'],
		    'model'             => @$exif['Model'],
		    'exposure'          => @$exif['ExposureTime'],
		    'focal_length'      => @$exif['FNumber'],
		    'iso_speed'         => @$exif['ISOSpeedRatings'],
		    'taken_on'          => @$exif['DateTimeOriginal'],
		    'shutter_speed'     => @$exif['ShutterSpeedValue'],
		    'aperture'          => @$exif['ApertureValue'],
		    'flash'             => @$exif['Flash'],
		    'exposure'          => @$exif['ExposureMode'],
		    'white_balance'     => @$exif['WhiteBalance'],
		    'image_mime_type'   => mime_content_type($file),
            'image_filesize'    => filesize($file),
		    'latitude'          => $latitude,
            'longitude'         => $longitude,
        );

        return $data;
    }

    public function fetchEntriesByTag($id, $page = null)
    {
        $select = $this->getTable()->select()
          ->setIntegrityCheck(false)
          ->from('photos')
          ->join('tagged_photos', 'photos.id = tagged_photos.photo_id')
          ->where('tagged_photos.tag_id = ?', $id)
          ->order('taken_on desc')
          ->order('created_on desc');

        $entries = $this->getTable()->fetchAll(
          $select
        );

        if (null !== $page) {
            $entries = $this->_paginateResult($entries, $page);
        }

        return $entries;
    }

    public function fetchEntries($page = null)
    {
        $entries = $this->getTable()->fetchAll(
            $this->getTable()->select()->order('created_on desc')
        );

        if (null !== $page) {
            $entries = $this->_paginateResult($entries, $page);
        }

        return $entries;
    }

    public function fetchEntriesByCreated(array $date, $page = null)
    {
        $dateString = $date['year'] . $date['month'] . $date['day'] . '%';

        $entries = $this->getTable()->fetchAll(
            $this->getTable()->select()
            ->where('date_format(created_on, "%Y%m%d") like ?', $dateString)
            ->order('created_on desc')
        );

        if (null !== $page) {
            $entries = $this->_paginateResult($entries, $page);
        }

        return $entries;
    }

    public function fetchEntriesByTakenAt(array $date, $page = null)
    {
        $dateString = $date['year'] . $date['month'] . $date['day'] . '%';
        $select = $this->getTable()->select()
            ->where('date_format(taken_on, "%Y%m%d") like ?', $dateString)
            ->order('taken_on desc');
        $entries = $this->getTable()->fetchAll(
          $select
        );

        if (null !== $page) {
            $entries = $this->_paginateResult($entries, $page);
        }

        return $entries;
    }

    // TODO: Ok split this up, dont like the concating here at all...
    public function fetchArchive($orderBy = "created_on")
    {
        $sql = 'select date_format(' . $orderBy . ', "%Y-%m") as date, count(1) as `count`' 
             . 'from photos group by date_format(' . $orderBy . ', "%Y-%m") '
             . 'order by ' . $orderBy . ' desc';
        $db = $this->getTable()->getAdapter();
        return $db->fetchAll($sql);
    }

    private function _paginateResult($entries, $page)
    {
        $entries  = Zend_Paginator::factory($entries);
        $entries->setItemCountPerPage(9)
            ->setPageRange(8)
            ->setCurrentPageNumber($page);    
        return $entries;
    }

    public function fetchEntry($id)
    {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException('id is not numeric was "' . gettype($id) . '"');
        }

        $table = $this->getTable();
        $select = $table->select()->where('id = ?', $id);

        return $table->fetchRow($select);
    }

    public function fetchEntryByCleanTitle($title)
    {
        $table = $this->getTable();
        $select = $table->select()->where('clean_title = ?', $title);

        return $table->fetchRow($select);
    }

    public function fetchNeighbours($photo)
    {
        return array(
            'previous' => $this->getTable()->getNeighbour($photo, 'previous'),
            'next'     => $this->getTable()->getNeighbour($photo, 'next')
        );
    }

    public function delete($photo)
    {
        if (is_numeric($photo)) {
            $photo = $this->fetchEntry($photo);
        }

        foreach ($this->_imageVariants as $variant => $options) {
            if (false == @unlink($this->getImageFileName($photo, $variant))) {
                ; // just move on to the next
            }
        }
        unlink($this->getImageFileName($photo));
        $photo->delete();        
    }
}
