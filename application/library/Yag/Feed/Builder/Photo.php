<?php

// TODO: fix me!

/**
 * @see Zend_Feed_Builder_Interface
 */
require_once 'Zend/Feed/Builder/Interface.php';

/**
 * @see Zend_Feed_Builder_Header
 */
require_once 'Zend/Feed/Builder/Header.php';

/**
 * @see Zend_Feed_Builder_Entry
 */
require_once 'Zend/Feed/Builder/Entry.php';


class Yag_Feed_Builder_Photo implements Zend_Feed_Builder_Interface
{
    /**
     * Header of the feed
     *
     * @var $_header Zend_Feed_Builder_Header
     */
    private $_header;

    /**
     * List of the entries of the feed
     *
     * @var $_entries array
     */
    private $_entries = array();

    private $_link;

    public function __construct($title, $link, $charset ='utf-8')
    {
        $this->_link = $link;
        $this->_createHeader($title, $link, $charset);
    }

    /**
     * Returns an instance of Zend_Feed_Builder_Header
     * describing the header of the feed
     *
     * @return Zend_Feed_Builder_Header
     */
    public function getHeader()
    {
        return $this->_header;
    }

    /**
     * Returns an array of Zend_Feed_Builder_Entry instances
     * describing the entries of the feed
     *
     * @return array of Zend_Feed_Builder_Entry
     */
    public function getEntries()
    {
        return $this->_entries;
    }

    /**
     * Create the Zend_Feed_Builder_Header instance
     *
     * @param  array $data
     * @throws Zend_Feed_Builder_Exception
     * @return void
     */
    private function _createHeader($title, $link, $charset)
    {
        $this->_header = new Zend_Feed_Builder_Header($title, $link, $charset);
    }

    public function addEntry($photo, $link, $content = "")
    {
        $entry = new Zend_Feed_Builder_Entry($photo->title, $this->_link . $link, $photo->description);
        $entry->setId($photo->id);
        $entry->setContent($content);
        $entry->addEnclosure($this->_link . $photo->image->url(), 'image/jpeg'); // TODO: Fix types

        $this->_entries[] = $entry;
        return $this;
    }
}
