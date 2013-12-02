<?php
/**
 *
 *  \project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  \copyright	2009, Lupus Michaelis
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

/* Before you mess URI, please read http://www.w3.org/TR/uri-clarification/
   http://www.ietf.org/rfc/rfc3986.txt
 */


namespace horn\lib;
use \horn\lib as h;

h\import('lib/string');
h\import('lib/collection');
h\import('lib/regex');
h\import('lib/regex-defs');

h\import('lib/uri/factory');

h\import('lib/uri/absolute');
h\import('lib/uri/scheme');
h\import('lib/uri/scheme_specific_part');
h\import('lib/uri/port');
h\import('lib/uri/query');

