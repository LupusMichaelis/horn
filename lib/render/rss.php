<?php

namespace horn\lib ;

import('lib/object') ;
import('lib/string') ;
import('lib/collection') ;

class feed_rss
	extends object_public
{
	protected	$_canvas ;
	private		$_helpers ;

	public		function __construct()
	{
		$this->_canvas = new \horn\lib\rss;
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

		if(!isset($template['display']))
			$this->_throw_format('No display in template specification.') ;

		$renderer = $this->_helpers[$resource['type']] ;
		$h = new $renderer($this->_canvas->root) ;
		$h->{(string)$template['display']}($resource['model'], $template['mode']) ;
	}

	protected	function _to_string()
	{
		return (string) $this->canvas;
	}
}

class rss
	extends object_public
{
	protected	$_document ;
	private		$_helpers ;

	public		function __construct()
	{
		$this->_document = new \domdocument('1.0', 'UTF-8') ;
		$this->_document->formatOutput = true ;
		$this->_helpers = new collection ;

		parent::__construct() ;

		$this->_initialize() ;
	}

	public		function __to_string()
	{
		return $this->document->saveXML() ;
	}

	public		function register($name, $callback)
	{
		$this->_helpers[$name] = $callback ;
	}

	public		function render($name, $thing)
	{
		$render = $this->_helpers[$name] ;
		$render($this->document->firstChild, $thing) ;
	}

	protected	function _initialize()
	{
		$doc = $this->document ;
		$root = $doc->createElementNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'rdf:RDF') ;
		$root->setAttribute('xmlns', 'http://purl.org/rss/1.0/') ;
		$doc->appendChild($root) ;
	}

	protected	function &_get_root()
	{
		$e = $this->document->firstChild ;
		return $e ;
	}

	protected	function &_get_title($text)
	{
		$e = null ;
		return $e ;
	}

	protected	function _set_title($text)
	{
		$od = $this->document ;
		$c = $od->createElement('channel') ;
		$c->setAttribute('rdf:about', '/') ;
		$t = $od->createElement('title', $text) ;
		$c->appendChild($t) ;
		//$l = $od->createElement('link', render_post_link($post)) ;
		//$c->appendChild($l) ;
		return $od->firstChild->appendChild($c) ;
	}

	protected	function _to_string()
	{
		return $this->document->saveXML() ;
	}
}


