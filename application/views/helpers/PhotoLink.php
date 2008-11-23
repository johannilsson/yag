<?php

class Zend_View_Helper_PhotoLink
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
     * Return the url to a photo
     *
     * @param  Zend_Db_Table_Row $photo
     * @return string
     */
    public function photoLink($photo)
    {
        return $this->view->url(array(
            'title'    => $photo->clean_title, 
            'year'  => date('Y', strtotime($photo->created_on)), 
            'month' => date('m', strtotime($photo->created_on)), 
            'day'   => date('d', strtotime($photo->created_on))
        ), 'photo');
    }

}
