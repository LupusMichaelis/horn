<?php
/** 
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


define('RE_0_199', '1?\d?\d');
define('RE_200_255', '2(?:5[0-5]|[0-4]\d)');
define('RE_0_255', sprintf('%s|%s', RE_0_199, RE_200_255));

define('RE_INET4', sprintf('(%1$s)\.(%1$s)\.(%1$s)\.(%1$s)', RE_0_255));

define('RE_INET4_S', sprintf('(?:%1$s)\.(?:%1$s)\.(?:%1$s)\.(?:%1$s)', RE_0_255));
// define('RE_INET6_S', '[\da-f]{1,4}(?:::[\da-f]{1,4}){7}');

define('RE_SCHEME',		'\w+');
define('RE_USER',		'\w+');
define('RE_PASS',		'\w+');
define('RE_HOST',		'[\.\w]+');
define('RE_PORT',		'\d{1,5}');
define('RE_PATH',		'(?:/\w*)+');
define('RE_SEARCH',		'\?[\w&=]*') ; //< \bug to improve
define('RE_ID',			'#[\w]*');
define('RE_URL', sprintf
		( '(?<scheme>%s)'.'://'
		. '(?:(?<user>%s)(?::(?<password>%s))?@)?'
		. '(?:(?<host>%s)|(?<inet4>%s)' /* .'|\\[(?<inet6>%s)]'. */ .')'
		. '(?::(?<port>%s))?'
		. '(?<path>%s)?'
		. '(?<search>%s)?'
		. '(?<id>%s)?'
		, RE_SCHEME
		, RE_USER, RE_PASS
		, RE_HOST, RE_INET4 //, RE_INET6
		, RE_PORT
		, RE_PATH
		, RE_SEARCH
		, RE_ID
		)
	);
define('RE_URL_U', sprintf
		( '(%s)'.'://'
		. '(?:(%s):(%s)@)?'
		. '(?:(%s)|(%s)' /* .'|\\[(%s)]'. */ .')'
		. '(?::(%s))?'
		. '(%s)?'
		. '(%s)?'
		, RE_SCHEME
		, RE_USER, RE_PASS
		, RE_HOST, RE_INET4 //, RE_INET6
		, RE_PORT
		, RE_PATH
		, RE_SEARCH
		)
	);

?>
