<?php

class Zend_View_Helper_PhotoStream
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
    public function photoStream($photo, $partial = null)
    {
        require_once APPLICATION_PATH . '/models/PhotoModel.php'; 
        $model = new PhotoModel; 

        $neighbours = $model->fetchNeighbours($photo);

        $params = array(        
            'previous' => $neighbours['previous'],
            'next'     => $neighbours['next']
        );

        $partial = is_null($partial) 
            ? '_stream.phtml' 
            : $partial;

        return $this->view->partial($partial, $params);
    }

}
