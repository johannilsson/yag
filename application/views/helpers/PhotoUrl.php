<?php

class Zend_View_Helper_PhotoUrl
{
    /**
     * Return the url to a photo
     *
     * @param  Zend_Db_Table_Row $photo
     * @return string
     */
    public function photoUrl($photo, $variant = '')
    {
        require_once APPLICATION_PATH . '/models/PhotoModel.php'; 
        $model = new PhotoModel; 

        $path = $model->getImageFileName($photo, $variant);
        return substr($path, stripos($path, 'public') + 6);
    }

}
