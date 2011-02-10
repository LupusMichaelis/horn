<?php

namespace horn\lib ;

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/string.php' ;
require_once 'horn/lib/collection.php' ;

class html
	extends object_public
{
	protected	$_document ;
	protected	$_title ;

	private		$_helpers ;

	public		function __construct()
	{
		$template = '<html><head><title></title></head><body></body></html>' ;
		$this->_document = \DomDocument::loadHTML($template) ;
		$this->_document->formatOutput = true ;
		$this->_title = new string ;
		$this->_helpers = new collection ;
	}

	protected	function _set_title(string $text)
	{
		$titleElement = $this->document->getElementsByTagName('title')->item(0) ;
		$newTitleTxt = $this->document->createTextNode($text) ;
		$oldTitleTxt = $titleElement->childNodes->item(0) ;
		if($oldTitleTxt)
			$titleElement->replaceChild($newTitleTxt, $oldTitleTxt) ;
		else
			$titleElement->appendChild($newTitleTxt) ;
		$this->_title = $text ;
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
		$render($this->document->getElementsByTagName('body')->item(0), $thing) ;
	}

}
