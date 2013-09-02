<?php
/** 
 *
 *  \project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  \copyright	2013, Lupus Michaelis
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

namespace horn\lib\regex;
use \horn\lib as h;


class result
	extends h\object\public_
{
	protected	$_expression;
	protected	$_subject;

	private		$results;

	public		function __construct(expression $expression, h\string $subject)
	{
		$this->_expression = clone $expression;
		$this->_subject = clone $subject;

		parent::__construct();

		$this->do_execute();
	}

	public		function do_execute()
	{
		$success = preg_match_all
			( $this->expression->pattern
			, $this->subject
			, $this->results
			, PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);

		if(false === $success)
			throw $this->_exception_preg_failed();
	}

	public		function is_match()
	{
		return (bool) !empty($this->results[0]);
	}

	protected	function _exception_preg_failed()
	{
		static $errors;
		if(is_null($errors))
			$errors = array
				( PREG_NO_ERROR => 'PREG_NO_ERROR'
				, PREG_INTERNAL_ERROR => 'PREG_INTERNATL_ERROR'
				, PREG_BACKTRACK_LIMIT_ERROR => 'PREG_BACKTRACK_LIMIT_ERROR'
				, PREG_RECURSION_LIMIT_ERROR => 'PREG_RECURSION_LIMIT_ERROR'
				, PREG_BAD_UTF8_ERROR => 'PREG_BAD_UTF8_ERROR'
				, PREG_BAD_UTF8_OFFSET_ERROR => 'PREG_BAD_UTF8_OFFSET_ERROR'
				);

		$error = preg_last_error();
		$error = isset($errors[$error]) ? $errors[$error] : 'Unknown error';
		$exception = $this->_exception_format('PREG failed \'%s\'', $error);

		return $exception;
	}
}

if(false): ?>
	public		function get_result($offset)
	{
		$submatches = new h\collection;
		if(isset($this->matches[$offset]))
			foreach($this->matches[$offset] as $name => $match)
			{
				$begin = $match[1];
				if($begin < 0)
					$submatches[$name] = null;
				else
				{
					$end = $begin + strlen($match[0]);
					$submatches[$name] = new h\collection($begin, $end);
				}
			}

		return $submatches;
	}

	public		function get_pieces_by_match($offset)
	{
		$pieces = new h\collection;

		foreach($this->matches as $name => $result)
		{
			$pieces[$name] = null;

			// no matches for this set, so proceding
			if(!is_array($result))
				continue;

			if(!isset($result[$offset]))
				continue;

			$match = $result[$offset];
			if(!is_array($match))
				continue;

			$begin = $match[1];
			$end = $begin + strlen($match[0]);
			if($begin > -1 and $end > -1)
				$pieces[$name] = new h\collection($begin, $end);
			else
				$pieces[$name] = null;
		}

		return $pieces;
	}
<?php endif /* false */ ;
