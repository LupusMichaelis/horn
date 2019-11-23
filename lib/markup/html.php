<?php

namespace horn\lib\markup;
use \horn\lib as h;

class html4
	extends xml
{
	protected	function __construct(\domdocument $doc)
	{
		parent::__construct($doc);

		$this->_document->formatOutput = true;
		$this->_document->encoding = 'utf-8';
	}

	protected	function & _get_title()
	{
		$title = $this->document->getElementsByTagName('title')->item(0);
		return $title;
	}

	protected	function _set_title(h\text $text)
	{
		$titleElement = $this->title;
		$newTitleTxt = $this->document->createTextNode($text);
		$oldTitleTxt = $titleElement->childNodes->item(0);
		if($oldTitleTxt)
			$titleElement->replaceChild($newTitleTxt, $oldTitleTxt);
		else
			$titleElement->appendChild($newTitleTxt);
	}

	protected	function & _get_body()
	{
		$body = $this->document->getElementsByTagName('body')->item(0);
		return $body;
	}

	public		function create_div_element(h\collection $attrs = null)
	{
		return $this->create_element(h\text('div'), $attrs);
	}

	public		function create_anchor_element(h\collection $attrs = null)
	{
		return $this->create_element(h\text('a'), $attrs);
	}

	static
	public		function new_from_file(h\text $template_filename)
	{
		$document = new \domdocument('1.1', 'UTF-8');
		$document->loadHtmlFile($template_filename);
		return new static($document);
	}

	static
	public		function new_from_text(h\text $template)
	{
		$document = new \domdocument('1.1', 'UTF-8');
		$document->loadHTML($template);

		return new static($document);
	}

	static
	public		function create_strict()
	{
		$template = h\text(
			'<!DOCTYPE HTML PUBLIC ' . "\n"
			. '	"-//W3C//DTD HTML 4.01//EN"' . "\n"
			. '	"http://www.w3.org/TR/html4/strict.dtd">' . "\n"
			. '<html><head><title><body>');
		return self::new_from_text($template);
	}
}

class xhtml11
	extends html4
{
	static
	public		function create()
	{
		$template = h\text('<!DOCTYPE html><html><head><title><body>');
		return self::new_from_text($template);
	}

	static
	public		function new_from_file(h\text $template_filename)
	{
		$document = \domdocument::loadFile($template_filename);
		return new static($document);
	}

	static
	public		function new_from_text(h\text $template)
	{
		$document = \domdocument::load($template);
		return new static($document);
	}

}

class html5
	extends html4
{
	static
	public		function create()
	{
		$template = h\text('<!DOCTYPE html><html><head><title><body>');
		return self::new_from_text($template);
	}

}

