<?php

namespace horn\apps\blog ;
use \horn\lib as h ;

class page_html
	extends h\page_html
{
	protected	$_menu ;
	protected	$_footer ;

	protected	function & _get_menu()
	{
		if(is_null($this->_menu))
		{
			$div = $this->canvas->create_div_element(h\c(array('id' => 'menu'))) ;
			$this->canvas->body->insertBefore($div, $this->target) ;
			$this->_menu = $div ;
		}

		return $this->_menu ;
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
		$this->_get_menu() ;

		return parent::_to_string() ;
	}
}
