<?php

namespace horn\lib ;

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/string.php' ;

class html
	extends object_public
{
	protected	$_document ;
	protected	$_title ;

	public		function __construct()
	{
		$template = '<html><head><title></title></head><body></body></html>' ;
		$this->_document = \DomDocument::loadHTML($template) ;
		$this->_title = new string ;
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
}
