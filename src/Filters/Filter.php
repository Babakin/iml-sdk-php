<?php
namespace IMLSdk\Filters;

abstract class Filter
{
	protected $collection;
	
	public function __construct(array $collection)
	{
		$this->collection = $collection;
	}
	
	abstract public function filterCollection():array;

	
}