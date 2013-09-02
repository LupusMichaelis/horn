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

h\import('lib/pair');

# XXX Doesn't handle UTF8 correctly yet
class result
	extends h\object\public_
{
	protected	$_expression;
	protected	$_subject;

	private		$results;		/**< used to store preg_match_all results */

	private		$captures;		/**< collection of iterators by capture index or name */
	private		$records;		/**< collection of matching records */

	public		function __construct(expression $expression, h\string $subject)
	{
		$this->_expression = clone $expression;
		$this->_subject = clone $subject;

		parent::__construct();

		$this->results = null;
		$this->captures = null;
		$this->records = null;

		$this->do_execute();
	}

	private		function do_execute()
	{
		$success = preg_match_all
			( $this->expression->pattern
			, $this->subject
			, $this->results
			, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

		if(false === $success)
			throw $this->_exception_preg_failed();
	}

	public		function is_match()
	{
		return $this->has_captured(0);
	}

	public		function has_captured($index)
	{
		return isset($this->results[0][$index]);
	}

	public		function iterate_records()
	{
		if(is_null($this->records))
		{
			$this->records = h\collection();
			foreach($this->results as $record)
			{
				$row = h\collection();
				foreach($record as $index_name => $match)
				{
					$pair = new h\pair;
					$pair->begin = $match[1];
					$pair->end = $pair->begin + strlen($match[0]);

					$row[$index_name] = $pair;
				}

				$this->records[] = $row;
			}
		}

		return clone $this->records;
	}

	public		function iterate_matches()
	{
		return $this->iterate_captures_by_index(0);
	}

	public		function iterate_captures_by_index($index)
	{
		$capture = new h\collection;
		if(!isset($this->results[0][$index]))
			throw $this->_exception_format('Capture \'%s\' doesn\'t exist', $index);

		$records = $this->iterate_records();
		return $records->get_column($index);
	}

	public		function iterate_captures_by_name(h\string $name)
	{
		return $this->iterate_captures_by_index($name);
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
