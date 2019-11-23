<?php

namespace horn\lib;

import('lib/object');
import('lib/text');
import('lib/collection');
import('lib/markup/rss');

class feed_rss
	extends object_public
{
	protected	$_canvas;
	private		$_helpers;

	public		function __construct()
	{
		$this->_canvas = markup\rss::create();
		$this->_helpers = new collection;
		parent::__construct();
	}

	public		function register($name, $callback)
	{
		$this->_helpers[$name] = $callback;
	}

	public		function render($template, $resource)
	{
		if(is_null($resource['type']))
			throw $this->_exception('Type not set for resource');

		if(!isset($this->_helpers[$resource['type']]))
			throw $this->_exception_format('No resource for \'%s\'.', $resource['type']);

		if(!isset($template['display']))
			throw $this->_exception_format('No display in template specification.');

		$renderer = $this->_helpers[$resource['type']];
		$h = new $renderer($this->_canvas->root);
		$h->{(string)$template['display']}($resource['model'], $template['mode']);
	}

	protected	function _to_string()
	{
		return (string) $this->canvas;
	}
}

