<?php

namespace horn\lib\http ;

use horn\lib as h ;

h\import('lib/object') ;

class user
	extends h\object_public
{
	protected	$_cookie ;
	protected	$_ip ;

	public		function __construct()
	{
		$this->_ip = h\string('') ;
		$this->_cookie = h\collection() ;

		parent::__construct() ;
	}

	static
	public		function create_native()
	{
		$u = new self ;
		$u->ip = h\string($_SERVER['REMOTE_ADDR']) ;
		$u->cookie = h\collection($_COOKIE) ;

		return $u ;
	}
}

