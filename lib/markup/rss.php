<?php

namespace horn\lib\markup;
use \horn\lib as h;

h\import('lib/object');
h\import('lib/string');
h\import('lib/collection');
h\import('lib/markup/rss');

class rss
	extends xml
{
	protected	$_document;

	protected	function __construct(\domdocument $dom)
	{
		parent::__construct($dom);
		$this->_initialize();
	}

	static
	public		function create()
	{
		$document = new \domdocument('1.0', 'UTF-8');
		$rss = new static($document);
		return $rss;
	}

	protected	function _to_string()
	{
		return $this->document->saveXML();
	}

	protected	function _initialize()
	{
		$doc = $this->document;
		$root = $doc->createElementNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'rdf:RDF');
		$root->setAttribute('xmlns', 'http://purl.org/rss/1.0/');
		$doc->appendChild($root);
	}

	protected	function &_get_root()
	{
		$e = $this->document->firstChild;
		return $e;
	}

	protected	function &_get_title()
	{
		$e = null;
		return $e;
	}

	protected	function _set_title(h\string $text)
	{
		$c = $this->create_element(h\string('channel'), h\c(array('rdf:about' => '/')));
		$t = $this->create_element(h\string('title'), h\c(array($text)));

		$c->appendChild($t);

		return $this->root->appendChild($c);
	}

}


