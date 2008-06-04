<?php

require_once 'Yag/Find/Abstract.php';
require_once 'Yag/Image.php';

class Yag_Find_Image extends Yag_Find_Abstract 
{
	public function __construct($search, Iterator $iterator)
	{
		parent::__construct($search, $iterator);
	}

	public function accept()
	{
		return preg_match('/' . $this->getSearch() .	 '/', parent::current()->getFilename());
	}

	public function current()
	{
		return new Yag_Image(parent::current());
	}

}
