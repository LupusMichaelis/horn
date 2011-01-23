<?php

namespace horn\lib ;

require_once 'horn/lib/object.php' ;

class html
	extends object_public
{
	protected	$_document ;

	public		function __construct()
	{
		$template = '<html><head><title></title></head><body></body></html>' ;
		$this->_document = \DomDocument::loadHTML($template) ;
	}

	public		function __tostring()
	{
		return $this->document->saveHTML() ;
	}
}
