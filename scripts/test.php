<?php
set_include_path('./../library');

require_once 'Yag/Album.php';
require_once 'Yag/Find/Album.php';

$path = $argv[1];

echo $path;

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$iterator = new RecursiveDirectoryIterator($path);
//$iterator = new CachingIterator(new RecursiveDirectoryIterator($path), CachingIterator::FULL_CACHE);

for ($i = 0; $i < 10; $i++)
{
	$start = microtime_float();

	$albums = new Yag_Find_Album('', $iterator);
	//$albums = Yag_Find_Album::find($path, '');
	
	foreach ($albums as $album)
	{
		foreach ($album as $image)
		{
			$image->getFileInfo()->getFilename();
			$image->belongsTo();
		}
		//$album->getImage(65);
	}
	
	$end = microtime_float();

	echo $i . ':' . ($end - $start) . "\n";
}
