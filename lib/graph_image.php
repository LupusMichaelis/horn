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

namespace horn ;

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/graph.php' ;
require_once 'horn/lib/image.php' ;

/**
 *	\todo	color aware
 *	\todo	display ordinate's lables
 */
class graph_image
	extends object_public
{
	protected	$_graph ;
	protected	$_size ;	/// forward attribute to $image->size
	protected	$_margin ;
	protected	$_image ;

	public		function __construct(graph $graph, point $size, point $margin)
	{
		$this->_graph = $graph ;
		$this->_margin = $margin ;
		$this->image = new image($size) ;
	}

	public		function __tostring()
	{
		return $this->dump() ;
	}

	public		function dump()
	{
		// La zone de l'image dans laquelle sera dessiné le graphique
		$viewport = $this->compute_viewport() ;
		$projector = $this->create_projector($viewport) ;

		$abscisse_line = new line
			( new point($this->_graph->min_x, 0)
			, new point($this->_graph->max_x, 0)
			) ;
		$abscisse_line = $projector->line($abscisse_line) ;

		$ordinate_line = new line
			( new point(0, $this->_graph->min_y)
			, new point(0, $this->_graph->max_y)
			) ;
		$ordinate_line = $projector->line($ordinate_line) ;

		$datas = array() ;
		foreach($this->_graph->datas as $point)
			$datas[] = $projector->point($point) ;

		// GD

		$pen = $this->image->pen_list[0x000000] ;

		$this->image->background = $this->image->pen_list[0xffffff] ;
		$this->image->draw_line($abscisse_line, $pen) ;
		$this->image->draw_line($ordinate_line, $pen) ;

		$first_point = array_shift($datas) ;
		foreach($datas as $second_point)
		{
			$image->draw_line(new line($first_point, $second_point), $pen) ;
			$first_point = $second_point ;
		}

		return $image->dump() ;
	}

	public		function create_projector(box $viewport)
	{
		$reference = new point(0, 0) ;
		$projector = new projector($reference, $this->graph, $viewport) ;

		return $projector ;
	}

	public		function compute_viewport()
	{
		$margin = $this->margin ;
		$size = $this->size ;

		$top_left = new point
			( $margin->width
			, $margin->height
			) ;
		$bottom_right = new point
			( $size->width - $margin->width
			, $size->height - $margin->height
			) ;
		$viewport = new box($top_left, $bottom_right) ;

		return $viewport ;
	}

	protected	function &_get_size()
	{
		/// \bug	PHP : getter return qualifier not checked (eg, it dosn't see if
		///			__get returns a reference or don't) Check it out with some test case.
		return $this->_image->__get('size') ;
	}

	protected	function _set_size(point $size)
	{
		$this->_image->size = $size ;
	}

}

class image_jpeg
	extends image
{
 /// \todo quality
}

class graph_image_style
	extends object_public
{
	public		function __construct()
	{
	}
}

/**	Projects geometric objects to a viewport
 */
class projector
	extends object_public
{
	protected	$_reference ;
	protected	$_graph ;
	protected	$_viewport ;

	/**
	 *	\param	$reference	point	The coordinates of origin point from the origin system
	 *									in the target system.
	 */
	public		function __construct(point $reference, graph $origin, box $target)
	{
		$this->_reference = $reference ;
		$this->_graph = $origin ;
		$this->_viewport = $target ;
	}

	public		function ratio()
	{
	}

	public		function reference()
	{
	}

	public		function point(point $from)
	{
		$to = clone $from ;

		$to->width = $this->width($from->width) ;
		$to->height = $this->height($from->height) ;

		return $to ;
	}

	public		function line(line $from)
	{
		$to = clone $from ;

		$to->first = $this->point($from->first) ;
		$to->second = $this->point($from->second) ;

		return $to ;
	}

	public		function box(box $from)
	{
		$to = clone $from ;

		$to->top_left = $this->box($from->top_left) ;
		$to->bottom_right = $this->box($from->bottom_right) ;

		return $to ;
	}

	protected	function height($height)
	{
		$ratio = 
		$height *= $this->_viewport->bottom_right->height - $this->_viewport->top_left->height ;
		$height /= $this->_graph->max_y - $this->_graph->min_y ;
		$height += $this->_viewport->top_left->height ;
		$height -= $this->_viewport->bottom_right->height ;

#		echo 'height', $height, '</br>' ;

		return (int) $height ;
	}

	protected	function width($width)
	{
		$ratio = 

		$this->_viewport->bottom_right->width - $this->_viewport->top_left->width ;
			$this->_graph->max_x - $this->_graph->min_x ;

		$this->_viewport->top_left->width ;

#		echo 'width', $width, '</br>' ;

		return (int) $width ;
	}
}


