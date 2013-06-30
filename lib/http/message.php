<?php

namespace horn\lib\http ;

use horn\lib as h ;

h\import('lib/collection') ;

class searchpart
	extends h\collection_mutltivalue
{
	static
	public		function from_string(string $s)
	{
		$new = new static ;

		$parts = $s->explode('&') ;
		foreach($parts as $part)
		{
			$pos = $part->search('=') ;
			if($pos !== false)
			{
				$name = $part->head($pos) ;
				$value = $part->tail($pos + 1) ;
			}
			else
			{
				$name = $part ;
				$value = null ;
			}

			$pos = $name->search('[]') ;
			if($pos !== false)
				$name = $name->head(-2) ;

			$new[$name] = urldecode($value) ;
		}
	}
}

class uri
	extends h\object_public
{
	protected	$_path ;
	protected	$_searchpart ;

	public		function __construct(h\string $raw = null)
	{
		if($raw === null)
			$raw = h\string('') ;

		$qmark = $raw->search('?') ;
		if($qmark > -1)
		{
			$this->_path = $raw->head($qmark - 1) ;
			$this->_searchpart = $raw->tail($qmark) ;
		}
		else
		{
			$this->_path = $raw ;
			$this->_searchpart = h\string('') ;
		}

		parent::__construct() ;
	}

	public		function _to_string()
	{
		return $this->_path ;
	}
}

class message
	extends h\object_public
{
	public	$head ;
	public	$body ;

	public function __construct()
	{
		$this->head = new head ;
		$this->body = new body ;

		parent::__construct() ;
	}
}

class request
	extends message
{
	public		$method ;
	protected	$_uri ;
	public		$version ;

	public		$body ;

	public		function __construct()
	{
		$this->_uri = new uri(h\string('')) ;
		parent::__construct() ;
	}

}

class response
	extends message
{
	public		$status ;
}

class head
	extends \horn\lib\collection
{
}

class body
	extends \horn\lib\object_public
{
	public	$content ;

	public		function __construct()
	{
	}
}



