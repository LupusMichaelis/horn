<?php

namespace horn\lib ;

import('lib/object') ;
import('lib/string') ;
import('lib/collection') ;

class html
	extends object_public
{
	protected	$_document ;
	private		$_helpers ;

	public		function __construct()
	{
		$template = '<html><head><title><body>' ;
		$this->_document = \DomDocument::loadHTML($template) ;
		$this->_document->formatOutput = true ;
		$this->_helpers = new collection ;

		parent::__construct() ;
	}

	protected	function & _get_title()
	{
		$title = $this->document->getElementsByTagName('title')->item(0) ;
		return $title ;
	}

	protected	function _set_title(string $text)
	{
		$titleElement = $this->title ;
		$newTitleTxt = $this->document->createTextNode($text) ;
		$oldTitleTxt = $titleElement->childNodes->item(0) ;
		if($oldTitleTxt)
			$titleElement->replaceChild($newTitleTxt, $oldTitleTxt) ;
		else
			$titleElement->appendChild($newTitleTxt) ;
	}

	public		function __tostring()
	{
		return $this->document->saveHTML() ;
	}

	public		function register($name, $callback)
	{
		$this->_helpers[$name] = $callback ;
	}

	public		function render($name, $thing)
	{
		$render = $this->_helpers[$name] ;
		$render($this->body, $thing) ;
	}

	protected	function & _get_body()
	{
		$body = $this->document->getElementsByTagName('body')->item(0) ;
		return $body ;
	}

}
