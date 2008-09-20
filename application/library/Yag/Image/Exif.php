<?php

class Yag_Image_Exif
{
	protected $exif = null;

	public function __construct(Yag_Image $image)
	{
		// TODO: Add initializer to check if the exif extension exists.
		$exifData = exif_read_data($image->getFileInfo()->getRealPath());
		// TODO: Add check if there is any data.
		$this->exif = new ArrayObject($exifData);
	}

	// TODO: Use __get?
	public function __get($name)
	{
		if ($this->exif->offsetExists($name))
		{
			return $this->exif->offsetGet($name);
		}
	}
}