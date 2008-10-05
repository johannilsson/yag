<?php

class Yag_View_Helper_Map
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
     * Render map for the provided photo
     *
     * @param  Zend_Db_Table_Row $photo
     * @return string
     */
    public function map($photo)
    {
        if ($photo->latitude && $photo->longitude) {
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configuration/map.ini', ENVIRONMENT);
            $params = array(
                'latitude'  => $photo->latitude,
                'longitude' => $photo->longitude,
                'apiKey'    => $config->map->apiKey,
            );

            return $this->view->partial('_map.phtml', $params);
        }
    }
}
