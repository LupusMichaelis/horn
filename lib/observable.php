<?php
/** observable class definition
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
require_once 'horn/lib/collection.php' ;
require_once 'horn/lib/callback.php' ;

/** Observable interface.
 *
 */
interface iObservable
{
	/** Notify listener that the $event happened.
	function	notify($event) ;
	 */

	/** Register a listener callback.
	 */
	function	register(callback $callback, $event = null) ;

	/** On destruction, listener must be notified.
	function	__destruct() ;
	 */
}

/** Observable object.
 *
 */
class observable
	extends		object_public
	implements	iObservable
{
	/** 
	 */
	public		function __construct()
	{
		parent::__construct() ;
		$this->callbacks = new collection ;
	}

	protected	function _add_event($event)
	{
		$this->callbacks[$event] = new collection ;
	}

	/** Register a listener callback.
	 */
	public		function register(callback $callback, $event = null)
	{
		if(is_null($event))
			foreach($this->callbacks as $event => $callbacks)
				$this->callbacks[$event][] = $callback ;
		elseif(isset($this->_callbacks[$event]))
			$this->callbacks[$event][] = $callback ;
		else
			throw new exception("Unhandled event '$event'") ;
	}

	/** Unregister a listener callback.
	 */
	public		function unregister(callback $callback, $event = null)
	{
		if(is_null($event))
			foreach($this->callbacks as $event => $callbacks)
				$this->callbacks[$event]->remove($callback) ;
		elseif(isset($this->_callbacks[$event]))
			$this->callbacks[$event]->remove($callback) ;
		else
			throw new exception("Unhandled event '$event'") ;
	}

	/** Notify listener that the $event happened.
	 */
	protected	function _notify($event)
	{
		$data = null ; // \todo

		if($this->callbacks[$event]->count())
			foreach($this->callbacks[$event] as $callback)
				$callback($event, $data) ;
	}

	/** Stack of callbacks.
	 */
	protected	$_callbacks ;
}






