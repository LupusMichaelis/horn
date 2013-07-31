<?php

namespace horn\lib\markup;
use \horn\lib as h;

h\import('lib/object');
h\import('lib/string');
h\import('lib/collection');

class xml
	extends h\object_protected
{
	protected	$_document;

	protected	function __construct(\DomDocument $document)
	{
		$this->_document = $document;
		parent::__construct();
	}

	protected	function _to_string()
	{
		return $this->document->saveHTML();
	}

	public		function create_element(h\string $element_name, h\collection $attrs = null)
	{
		$od = $this->document;
		$e = $od->createElement($element_name);

		foreach($attrs as $name => $value)
			if(is_integer($name))
				$e->appendChild($od->createTextNode($value));
			else
				$e->appendChild($od->createAttribute($name))->value = $value;

		return $e;
	}

	public		function create_entity_reference(h\string $name)
	{
		return $this->document->createEntityReference($name);
	}

	public		function create_nbsp()
	{
		return $this->create_entity_reference(h\string('nbsp'));
	}

}
