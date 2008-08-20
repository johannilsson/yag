<?php

class Yag_Image  
{
	private $_fileInfo;
	private $_album = null;

	public function __construct(SplFileInfo $fileInfo)
	{
		$this->_fileInfo = $fileInfo;
	}

	public function getFileInfo()
	{
		return $this->_fileInfo;
	}

	public function belongsTo()
	{
		if (null === $this->_album)
		{
				$this->_album = new Yag_Album(new SplFileInfo($this->getFileInfo()->getPath()));			
		}
		return $this->_album;
	}

	public function getBaseName()
	{
		return basename($this->_fileInfo->getFilename());
	}

	public function getFileName()
	{
		return pathinfo($this->_fileInfo->getFilename(), PATHINFO_FILENAME);
	}

	public function __toString()
	{
		return $this->_fileInfo->getFilename();
	}

}