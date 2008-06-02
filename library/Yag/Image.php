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
			$path = $this->getFileInfo()->getPath();
			$parts = preg_split('/\//', $path);
			if (isset($parts[count($parts) - 1]))
			{
				$this->_album = new Yag_Album($path, $parts[count($parts) - 1]);
			}			
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