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

namespace horn\lib;
use \horn\lib as h;

import('lib/text');
import('lib/regex/expression');
import('lib/regex/result');

function regex($pattern)
{
	$pattern = h\text($pattern);
	return new h\regex\expression($pattern);
}

function regex_execute($pattern, $subject)
{
	$re = regex($pattern);
	$subject = h\text($subject);
	return $re->do_execute($subject);
}

function regex_match($pattern, $subject)
{
	$re = regex($pattern);
	$subject = h\text($subject);
	return $re->is_matching($subject);
}

function regex_find($pattern, $haystack)
{
	$re = regex($pattern);
	$haystack = h\text($haystack);
	return $re->find($haystack);
}
