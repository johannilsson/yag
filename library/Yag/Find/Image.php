<?php

require_once 'Yag/Find/Abstract.php';
require_once 'Yag/Image.php';

class Yag_Find_Image extends Yag_Find_Abstract 
{
	public function __construct($path, $search)
	{
		parent::__construct($path, $search, new RecursiveDirectoryIterator($path));
	}
	
	public function accept()
	{
		return preg_match('/' . $this->getSearch() .	 '/', $this->current()->getFileInfo()->getFilename());
	}

	public function current()
	{
		return new Yag_Image(parent::current());
	}
}
