<?php
namespace IMLSdk\Filters;

abstract class Filter
{
	
	abstract public function filterCollection(array $collection):array;
	
}