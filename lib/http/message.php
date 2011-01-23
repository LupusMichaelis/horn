<?php

namespace horn\lib\http ;

require_once 'horn/lib/collection.php' ;

class message
	extends \horn\lib\object_public
{
	public	$header ;
	public	$body ;

	public function __construct()
	{
		parent::__construct() ;

		$this->header = new header ;
		$this->body = new body ;
	}
}

class request
	extends message
{
	const	POST = 'POST' ;
	const	GET = 'GET' ;
	const	PUT = 'PUT' ;
	const	DELETE = 'DELETE' ;

	public	$method ;
	public	$uri ;
	public	$version ;

	public		function __construct()
	{
		//$this->_uri = new url ;
		parent::__construct() ;
	}

	static public function create_native()
	{
		$native = new self ;
		$native->header['host'] = $_SERVER['HTTP_HOST'] ;

		$native->method = self::get_method($_SERVER['REQUEST_METHOD']) ;
		$native->uri = $_SERVER['REQUEST_URI'] ;
		$native->version = $_SERVER['SERVER_PROTOCOL'] ;

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
		return $methods[$candidate] ;
	}

}

class response
	extends message
{
}

class header
	extends \horn\lib\collection
{
}

class body
	extends \horn\lib\object_public
{
	public	$content ;
}



