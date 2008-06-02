<?php

abstract class Yag_Find_Abstract extends FilterIterator
{
	private $_search;
	private $_path;

	public function __construct($path, $search, Iterator $iterator)
	{
		$this->_search = $search;
		$this->_path = $path;

		parent::__construct($iterator);
	}

	public function getPath()
	{
		return $this->_path;
	}

	public function getSearch()
	{
		return $this->_search;
	}

	public function get()
	{
		foreach ($this as $found)
		{
			return $found;
		}
		throw new RuntimeException('does not exists');
	}
}
