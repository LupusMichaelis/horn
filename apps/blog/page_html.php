<?php

namespace horn\apps\blog ;
use \horn\lib as h ;

class page_html
	extends h\page_html
{
	protected	$_browser ;
	protected	$_footer ;

	protected	function & _get_browser()
	{
		if(is_null($this->_browser))
		{
			$div = $this->canvas->create_div_element(h\c(array('id' => 'browser'))) ;
			$this->canvas->body->insertBefore($div, $this->target) ;
			$this->_browser = $div ;

			$a = $this->canvas->create_anchor_element
				(h\c(array('Entry', 'href' => '/fakeroot'))) ;
			$div->appendChild($a) ;

			$div->appendChild($this->canvas->create_nbsp()) ;

			$a = $this->canvas->create_anchor_element
				(h\c(array('Stories', 'href' => '/fakeroot/stories'))) ;
			$div->appendChild($a) ;

			$div->appendChild($this->canvas->create_nbsp()) ;

			$a = $this->canvas->create_anchor_element
				(h\c(array('Accounts', 'href' => '/fakeroot/accounts'))) ;
			$div->appendChild($a) ;
		}

		return $this->_browser ;
	}

	protected	function & _get_footer()
	{
		if(is_null($this->_footer))
		{
			$div = $this->canvas->create_div_element(h\c(array('id' => 'footer'))) ;
			$this->canvas->body->appendChild($div) ;
			$this->_footer = $div ;
		}

		return $this->_footer ;
	}

	protected	function _to_string()
	{
		// Ensure widgets are rendered
		$this->_get_browser() ;

		return parent::_to_string() ;
	}
}
