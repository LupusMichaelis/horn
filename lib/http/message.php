<?php

namespace horn\lib\http;

use horn\lib as h;

h\import('lib/collection');
h\import('lib/http/url');

class message
	extends h\object_public
{
	public	$head;
	public	$body;

	public function __construct()
	{
		$this->head = new head;
		$this->body = new body;

		parent::__construct();
	}
}

class request
	extends message
{
	public		$method;
	protected	$_uri;
	public		$version;

	public		$body;

	public		function __construct()
	{
		$this->_uri = new url(h\string(''));
		parent::__construct();
	}

}

class response
	extends message
{
	public		$status;
}

class head
	extends h\collection
{
}

class body
	extends h\object_public
{
	public	$content;

	public		function __construct()
	{
	}
}


final
class response_methods
{
	const			version = '1.1';

	static public	function ok(response $response)
	{
		self::status($response, 200, 'OK');
	}

	static public	function no_content(response $response)
	{
		self::status($response, 204, 'Not found');
	}

	static public	function forbidden(response $response)
	{
		self::status($response, 403, 'Forbidden');
	}

	static public	function not_found(response $response)
	{
		self::status($response, 404, 'Not found');
	}

	static public	function method_not_allowed(response $response)
	{
		self::status($response, '405', 'Method Not Allowed');
	}

	static public	function http_conflict(response $response)
	{
		self::status($response, 409, 'Conflict');
	}

	static public	function status(response $response, $code, $message)
	{
		$response->status = sprintf('HTTP/%s %s %s'
				, self::version, $code, $message);
	}

	static public	function location(response $response, url $url)
	{
		$response->head['Location'] = $url;
	}
}


