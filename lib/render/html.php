<?php

namespace horn\lib ;

require_once 'horn/lib/object.php' ;

class html
	extends object_public
{
	protected	$_document ;
	protected	$_title ;

	public		function __construct()
	{
		$template = '<html><head><title></title></head><body></body></html>' ;
		$this->_document = \DomDocument::loadHTML($template) ;
	}

	protected	function _set_title($text)
	{
		$this->_title = $text ;
		$titleElement = $this->document->getElementsByTagName('title')->item(0) ;
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
}
