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
		return '' ;
	}
}

class message
	extends h\object_public
{
	public	$header ;
	public	$body ;

	public function __construct()
	{
		$this->header = new header ;
		$this->body = new body ;

		parent::__construct() ;
	}
}

class request
	extends message
{
	const		POST = 'POST' ;
	const		GET = 'GET' ;
	const		PUT = 'PUT' ;
	const		DELETE = 'DELETE' ;

	public		$method ;
	protected	$_uri ;
	public		$version ;

	public		$body ;

	public		function __construct()
	{
		$this->_uri = new uri(h\string('/')) ;
		parent::__construct() ;
	}

	static public function create_native()
	{
		$native = new self ;
		$native->header['host'] = $_SERVER['HTTP_HOST'] ;

		$native->method = self::get_method($_SERVER['REQUEST_METHOD']) ;
		$native->uri = new uri(h\string($_SERVER['REQUEST_URI'])) ;
		$native->version = h\string($_SERVER['SERVER_PROTOCOL']) ;

		$native->body = body::create_native() ;

		return $native ;
	}

	static public function get_method($candidate)
	{
		static $methods = array
			( 'POST' => self::POST
			, 'GET' => self::GET
			, 'PUT' => self::PUT
			, 'DELETE' => self::DELETE
			) ;
		return $methods[strtoupper($candidate)] ;
	}

}

class response
	extends message
{
	public		$status ;
}

class header
	extends \horn\lib\collection
{
}

class body
	extends \horn\lib\object_public
{
	private $parts ;
	public	$content ;

	static public function create_native()
	{
		$native = new self ;

		/* XXX Get data from raw input when it is not a multipart
		$received = file_get_contents('php://input') ;
		if(strlen($received) === 0)
		{
		*/
			$native->parts->join($_POST) ;
			$native->parts->join($_FILES) ;
		/* XXX
		}
		else
		{
		}
		*/

		return $native ;
	}

	public		function __construct()
	{
		$this->parts = h\collection() ;
	}

	public		function get(h\string $key)
	{
		return h\string($this->parts[(string) $key]) ;
	}
}



