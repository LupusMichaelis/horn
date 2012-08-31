<?php

namespace horn\lib ;
use horn\lib as h ;

h\import('lib/object') ;
h\import('lib/http/message') ;
h\import('lib/controller') ;

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
	if(isset($config['locale']))
		setlocale(LC_ALL, $config['locale']) ;

	if(isset($config['app']))
	{
		$app = &$config['app'];
		$main = new $app($in, $out, $config) ;
	}
	else
		throw new exception('App is not set.', $config) ;

	$main->run() ;
	return $main ;
}

class app
	extends object_public
{
	protected	$_request ;
	protected	$_response ;

	protected	$_db ;

	protected	$_config ;

	protected	$_controllers ;

	public		function __construct(http\request $in, http\response $out, $config)
	{
		$this->_config = $config ;
		$this->_request = $in ;
		$this->_response = $out ;

		parent::__construct() ;
	}

	public		function desired_mime_type()
	{
		$types = $this->config['content-types']['availables'] ;
		$suffix = $this->config['content-types']['default'] ;

		$path = h\string($this->request->uri->path) ;
		$offset = $path->search('.') ;
		$offset > -1 and $suffix = $path->tail(++$offset) ;

		return $types[(string) $suffix] ;
	}

	public		function run()
	{
		$this->set_controllers() ;
		$ctrl = $this->do_control() ;
		$this->set_view() ;

		is_null($ctrl) ? $this->not_found() : $ctrl->do_render() ;

		return $this ;
	}

	private		function set_controllers()
	{
		foreach($this->config['controllers'] as $ctrl_config)
		{
			$class_name = $ctrl_config['controller'] ;
			$this->controllers[] = new $class_name($this, $ctrl_config) ;
		}
	}

	private		function do_control()
	{
		foreach($this->controllers as $ctrl)
			if($ctrl->do_control())
				return $ctrl ; ; // We found the responsible
	}

	protected	function &_get_db()
	{
		if(is_null($this->_db))
			$this->_db = h\db\open($this->config['db']);

		return $this->_db ;
	}

	protected	function get_canvas_by_mime_type(h\string $type)
	{
		return new $this->config['renderer'][(string) $type] ;
	}

	protected	function set_view()
	{
		$mime_type = h\string($this->desired_mime_type()) ;
		$this->response->body->content = $this->get_canvas_by_mime_type($mime_type) ;
		$this->response->header['Content-type'] = h\string::format('%s;encoding=%s', $mime_type, 'utf-8') ;
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


