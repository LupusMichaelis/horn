<?php

namespace horn\lib ;

import('lib/object') ;
import('lib/string') ;
import('lib/collection') ;
import('lib/markup') ;
import('lib/markup/html') ;

class page_html
	extends object_public
{
	protected	$_canvas ;
	private		$_helpers ;

	public		function __construct()
	{
		$this->_canvas = \horn\lib\markup\html4::create_strict() ;
		$this->_helpers = new collection ;
		parent::__construct() ;
	}

	public		function register($name, $callback)
	{
		$this->_helpers[$name] = $callback ;
	}

	public		function render($template, $resource)
	{
		if(is_null($resource['type']))
			$this->_throw('Type not set for resource') ;

		if(!isset($this->_helpers[$resource['type']]))
			$this->_throw_format('No resource for \'%s\'.', $resource['type']) ;

		$renderer = $this->_helpers[$resource['type']] ;
		$h = new $renderer($this->_canvas->body) ;
		$h->{(string)$template['display']}($resource['model'], $template['mode']) ;
	}

	protected	function _to_string()
	{
		return (string) $this->canvas;
	}

}

