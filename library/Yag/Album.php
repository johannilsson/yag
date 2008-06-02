<?php

require_once 'Yag/Find/Image.php';

class Yag_Album implements IteratorAggregate 
{
	private $_name;
	private $_path;

	public function __construct($path, $name)
	{
		$this->_name = $name;
		$this->_path = $path;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function getIterator()
	{
		return new Yag_Find_Image($this->_path . DIRECTORY_SEPARATOR . $this->_name, 'jpg|JPG');
	}

	public function __toString()
	{
		return $this->getName();
	}

	public function getImage($fileName)
	{
		$search = new Yag_Find_Image($this->_path . DIRECTORY_SEPARATOR . $this->_name, $fileName);
		return $search->get();
	}

}