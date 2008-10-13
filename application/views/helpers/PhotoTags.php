<?php

class Zend_View_Helper_PhotoTags
{
    /**
     * Sets the view instance.
     *
     * @param  Zend_View_Interface $view View instance
     * @return Zend_View_Helper_PaginationControl
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Renders a photo stream
     *
     * @param  Zend_Db_Table_Row $photo
     * @return string
     */
    public function photoTags($photo)
    {
        require_once APPLICATION_PATH . '/models/PhotoModel.php'; 
        $model = new PhotoModel; 

        $tags = $model->fetchTags($photo);
        if (count($tags) > 0) {
            $params = array('tags' => $tags);
            return $this->view->partial('_tags.phtml', $params);
        }
        return '';
    }

}
