<?php

require_once 'Yag/Find/Abstract.php';
require_once 'Yag/Album.php';

class Yag_Find_Album extends Yag_Find_Abstract 
{
	public function __construct($path, $search)
	{
		parent::__construct($path, $search, new RecursiveDirectoryIterator($path));
	}

	public function accept()
	{
		if (parent::current()->isDir())
		{
			return preg_match('/' . $this->getSearch() .	 '/', parent::current()->getFileInfo()->getFilename());
		}
		return false;
	}

	public function current()
	{
		return new Yag_Album($this->getPath(), parent::current()->getFilename());
	}
}
