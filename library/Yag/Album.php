<?php

require_once 'Yag/Find/Image.php';

class Yag_Album implements IteratorAggregate 
{
	private $_fileInfo;
	private $_imageIterator = null;

	public function __construct(SplFileInfo $fileInfo)
	{ 
		$this->_fileInfo = $fileInfo;
		//$this->_imageIterator = new CachingIterator(new RecursiveDirectoryIterator($this->_path . DIRECTORY_SEPARATOR . $this->_name), CachingIterator::FULL_CACHE);
		$this->_imageIterator = new RecursiveDirectoryIterator($fileInfo->getRealPath());
	}

	public function getIterator()
	{
		// TODO: Fix image formats...
		return new Yag_Find_Image('jpg|JPG', $this->_imageIterator);
	}

	public function __toString()
	{
		return pathinfo($this->_fileInfo->getFilename(), PATHINFO_FILENAME);
	}

	public function getImage($fileName)
	{
		$search = new Yag_Find_Image($fileName, $this->_imageIterator);
		return $search->get();
	}

}