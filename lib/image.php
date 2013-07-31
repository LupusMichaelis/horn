<?php

/** \file
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2009, Lupus Michaelis
 *  License	AGPL <http://www.fsf.org/licensing/licenses/agpl-3.0.html>
 */

/*
 *  This file is part of Horn Framework.
 *
 *  Horn Framework is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Horn Framework is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero Public License for more details.
 *
 *  You should have received a copy of the GNU Affero Public License
 *  along with Horn Framework.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace horn;

import('lib/object');
import('lib/collection');
import('lib/graph_geometry');

const IMAGE_JPEG_TYPE = 'image/jpeg';

class pen
	extends object_public
{
	protected	$_target = null;
	protected	$_raw = null;
	protected	$_rgb = 0x0;

	/**
	 *	\param	$rgb	integer		0x00rrggbb
	 */
	public		function __construct(image $target, $rgb)
	{
		parent::__construct();

		$this->rgb = $rgb;
		$this->_target = $target;
	}

	protected	function _set_rgb($rgb)
	{
		if(!is_integer($rgb))
			$this->_throw_attribute_type_mismatch($rgb, 'integer');

		$this->_rgb = $rgb;
	}

	protected	function & _get_raw()
	{
		if(is_null($this->_raw))
		{
			$r = ($this->_rgb & 0xff0000 ) >> 4;
			$g = ($this->_rgb & 0xff00 ) >> 2;
			$b = $this->_rgb & 0xff;
			$this->_raw = imagecolorallocate($this->target->raw, $r, $g, $b);
		}

		return $this->_raw;
	}
}

class pen_factory
	extends collection
{
	protected	$_target = null;

	public		function __construct(image $target)
	{
		parent::__construct();
		$this->target = $target;
	}

	public		function _set_target(image $image)
	{
		$this->_target = $image;
	}

	/*
	protected	function _offset_set($offset, $value)
	{
		$this->_throw_readonly_collection();
	}
	*/

	protected	function _new_pen($color)
	{
		return new pen($this->_target, $color);
	}

	protected	function & _offset_get($offset)
	{
		$color = isset($this->_stack[$offset])
			? parent::_offset_get($offset)
			: $this->_new_pen($offset)
;

		var_dump($color);

		return $color;
	}
}

class image
	extends object_public
{
	protected	$_size;
	protected	$_pen_list;
	private		$_raw;

	const		BACKGROUND_COLOR = 0xffffff;
	protected	$_background;

	public		function __construct(point $size)
	{
		$this->_size = $size;
		$this->_pen_list = new pen_factory($this);
		$this->_raw = null;
		$this->_background = $this->_pen_list[0x0];

	}

	public		function draw_point(point $point, pen $pen)
	{
		$raw = $this->raw;
		$this->_throw_todo();
	}

	public		function draw_line(line $line, pen $pen)
	{
		$raw = $this->raw;

		imageline
			( $raw
			, $line->first->width 
			, $line->first->height 
			, $line->second->width 
			, $line->second->height 
			, $pen
			);
	}

	public		function draw_box(box $box, pen $pen)
	{
		$raw = $this->raw;
		$this->_throw_todo();
	}

	protected	function _set_raw()
	{
		$this->_throw_readonly_attribute();
	}

	protected	function &_get_raw()
	{
		if(is_null($this->_raw))
		{
			$size = $this->size;
			$this->_raw = @ /* Avoid Fatal Error */ imagecreatetruecolor($size->width, $size->height);
			$this->_check_raw();

			imagefill($raw, 0, 0, $this->background->raw);
		}

		return $this->_raw;
	}

	protected	function _unset_raw()
	{
		if(!is_null($this->_raw))
			imagedestroy($this->_raw);
	}

	protected	function _check_raw()
	{
		if(!$this->_raw)
			$this->_throw('Image creation failed \'% s\'.');
	}

	protected	function _set_background($color)
	{
		var_dump(__FUNCTION__);
		$this->_background = $this->_pen_list[$color];
	}

	protected	function &_get_background($color)
	{
		if(is_null($this->_background))
			$this->_background = $this->_pen_list[self::BACKGROUND_COLOR];

		return $this->_background;
	}

	/**
	 *	\todo	Duplicate raw image on cloning
	 */
	protected	function _clone()
	{
		return parent::_clone();
	}
	
	public		function dump()
	{
		ob_start();
		@/* Avoid fatal error */ imagegd2($this->raw);
		return ob_get_clean();
	}

}

class image_png
	extends image
{
	public		function dump()
	{
		ob_start();
		@/* Avoid fatal error */ imagepng($this->raw);
		return ob_get_clean();
	}
}

