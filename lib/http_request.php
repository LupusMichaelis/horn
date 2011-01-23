<?php

namespace horn\lib\http ;

require_once 'horn/lib/collection.php' ;

class request
{
	public	$header ;
	public	$body ;

	static $c_methods = array
		( 'POST' => self::POST
		, 'GET' => self::GET
		, 'PUT' => self::PUT
		, 'DELETE' => self::DELETE
		) ;

	const	POST = 'POST' ;
	const	GET = 'GET' ;
	const	PUT = 'PUT' ;
	const	DELETE = 'DELETE' ;

	public function __construct()
	{
		$this->header = new header ;
		$this->body = new body ;
	}

	static public function create_native()
	{
		$native = new self ;
		$native->header['host'] = $_SERVER['HTTP_HOST'] ;

		$native->method = self::$c_methods[$_SERVER['REQUEST_METHOD']] ;
		$native->uri = $_SERVER['REQUEST_URI'] ;
		$native->version = $_SERVER['SERVER_PROTOCOL'] ;

		return $native ;
	}
}

class header
	extends \horn\lib\collection
{
}

class body
{
	public	$content ;
}



