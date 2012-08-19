<?php

namespace horn\lib ;
use horn\lib as h ;

h\import('lib/object') ;
h\import('lib/http/message') ;

function render(http\response $out)
{
	ob_start() ;
	echo $out->body->content ;

	header($out->status) ;
	foreach($out->header as $name => $value)
		header("$name: $value") ; // XXX escape

	ob_end_flush() ;
}

function run(http\request $in, http\response $out, &$config)
{
	if(array_key_exists('locale', $config))
		setlocale(LC_ALL, $config['locale']) ;

	if(array_key_exists('routing', $config))
	{
		$routing = &$config['routing'];

		ksort($routing) ;
		foreach($routing as $key => $value)
			if($key === 0)
				$main = new $value($in, $out, $config) ;
			elseif(400 < $key && $key < 600) // XXX refine that
				$main->add_error_handler($key, $value) ;
			else
				$main->add_route($key, $value) ;
	}

	return $main ;
}

abstract
class app
	extends object_public
{
	protected	$_request ;
	protected	$_response ;

	protected	$_db ;

	protected	$_config ;

	protected	$_template ;
	protected	$_resource ;


	public		function __construct(http\request $in, http\response $out, $config)
	{
		$this->_config = $config ;
		$this->_request = $in ;
		$this->_response = $out ;

		$this->_resource = h\c(array('type' => null, 'model' => null)) ;
		$this->_template = h\c(array('display' => null, 'mode' => h\string('show'))) ;

		parent::__construct() ;
	}

	static
	public		function desired_mime_type(h\http\request $in = null)
	{
		$types = array
			( 'html' => h\string('text/html')
			, 'rss' => h\string('application/rss+xml')
			) ;
		$suffix = 'html' ;
		if(!is_null($in))
		{
			$path = h\string($in->uri->path) ;
			$offset = $path->search('.') ;
			$offset > -1 and $suffix = $path->tail(++$offset) ;
		}
		return $types[(string) $suffix] ;
	}

	abstract
	protected	function do_control() ;

	public		function run()
	{
		$this->do_control() ;
		$this->set_view() ;
		$this->do_render() ;
		return $this ;
	}

	protected	function &_get_db()
	{
		if(is_null($this->_db))
			$this->_db = h\db\open($this->config['db']);

		return $this->_db ;
	}

	protected	function get_canvas_by_mime_type(h\string $type)
	{
		static $types = array
			( 'text/html' => '\horn\lib\page_html'
			, 'application/rss+xml' => '\horn\lib\feed_rss'
			) ;

		$doc = new $types[(string) $type] ;

		return $doc ;
	}

	protected	function set_view()
	{
		$mime_type = static::desired_mime_type($this->request) ;
		$this->response->body->content = $this->get_canvas_by_mime_type($mime_type) ;
		$this->response->header['Content-type'] = h\string::format('%s;encoding=%s', $mime_type, 'utf-8') ;
	}
	public		function do_render()
	{
		// XXX need an actual state that means the model rendering must not be done
		if(!is_null($this->_resource['model']))
			$this->response->body->content->render($this->_template, $this->_resource) ;
		else
			$this->not_found() ;
	}

	public		function not_found()
	{
		$this->status('404', 'Not found') ;
	}

	public		function redirect_to_created($to)
	{
		$this->status('201', 'Created') ;
		$this->response->header['Location'] = sprintf
			( '%s://%s%s'
			, $this->config['scheme']
			, $this->config['domain']
			, $to
			) ;
	}

	public		function redirect_to($to)
	{
		$this->status('301', 'Moved Permanently') ;
		$this->response->header['Location'] = sprintf
			( '%s://%s%s'
			, $this->config['scheme']
			, $this->config['domain']
			, $to
			) ;
	}

	public		function status($code, $message)
	{
		$this->response->status = sprintf('%s %s %s', $this->request->version, $code, $message) ;
	}
}


