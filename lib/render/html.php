<?php

namespace horn\lib ;

import('lib/object') ;
import('lib/string') ;
import('lib/collection') ;

class page_html
	extends object_public
{
	protected	$_canvas ;
	private		$_helpers ;

	public		function __construct()
	{
		$this->_canvas = new \horn\lib\html;
		$this->_helpers = new collection ;
		parent::__construct() ;
	}

	public		function register($name, $callback)
	{
		$this->_helpers[$name] = $callback ;
	}

	public		function render_control($name, $control, $thing)
	{
		$render = $this->_helpers[$name] ;
		$h = new $render($this->_canvas->body) ;
		$h->$control($thing) ;
	}

	public		function render($resource)
	{
		$skins = c(array('story' => 'full', 'stories' => 'collection')) ;
		$skin = $skins[$resource['type']] ;
		$this->render_control('story', $skin, $resource['stories']) ;
	}

	protected	function _to_string()
	{
		return (string) $this->canvas;
	}

}

class html
	extends object_public
	//extends object_protected
{
	protected	$_document ;

/*
	static
	public		function new_open_file(string $template_filename)
	{
		$document = \DomDocument::loadHtmlFile($template_filename) ;
		return new self($document) ;
	}

	static
	public		function new_from_string(string $template)
	{
		$document = \DomDocument::loadHTML($template) ;
		return new self($document) ;
	}

	static
	public		function new_4(string $template)
	{
		$template = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
		$template .= '<html><head><title><body>' ;
		$document = \DomDocument::loadHTML($template) ;
		return new self($document) ;
	}

	static
	public		function new_x11(string $template)
	{
		$template = '<!DOCTYPE html><html><head><title><body>' ;
		$document = \DomDocument::loadHTML($template) ;
		return new self($document) ;
	}

	static
	public		function new_5(string $template)
	{
		$template = '<!DOCTYPE html><html><head><title><body>' ;
		$document = \DomDocument::loadHTML($template) ;
		return new self($document) ;
	}

	protected	function __construct(\DomDocument $template)
*/
	public		function __construct()
	{
		$template = '<!DOCTYPE html><html><head><title><body>' ;
		$this->_document = new \DomDocument ;
		$this->_document->loadHTML($template) ;
		$this->_document->formatOutput = true ;

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

	protected	function _to_string()
	{
		return $this->document->saveHTML() ;
	}

	protected	function & _get_body()
	{
		$body = $this->document->getElementsByTagName('body')->item(0) ;
		return $body ;
	}

}
