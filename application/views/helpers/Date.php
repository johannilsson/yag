<?php

class Zend_View_Helper_Date
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
     * Renders a from another date
     *
     * @param  string $date
     * @return string
     */
    public function date($date, $format = Zend_Date::DATE_LONG)
    {
      $date = new Zend_Date($date);
      return $date;
    }
}
