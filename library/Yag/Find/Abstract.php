<?php

abstract class Yag_Find_Abstract extends FilterIterator
{
	private $_search;

	public function __construct($search, Iterator $iterator)
	{
		$this->_search = $search;

		parent::__construct($iterator);
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
