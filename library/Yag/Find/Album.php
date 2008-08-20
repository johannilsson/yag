<?php

require_once 'Yag/Find/Abstract.php';
require_once 'Yag/Album.php';

class Yag_Find_Album extends Yag_Find_Abstract 
{
	public function __construct($search, Iterator $iterator)
	{
		parent::__construct($search, $iterator);
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
		return new Yag_Album(parent::current());
	}

	public static function find($path, $search)
	{
		$iterator = new RecursiveDirectoryIterator($path);
		return new self($search, $iterator); 
	}
}
