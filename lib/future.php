<?php
/** Handy functions that exists in future releases of PHP
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

if(!function_exists('\array_column'))
{
	function array_column($input, $column_key, $index_key=null)
	{
		$column = array();
		foreach($input as $key => $row)
		{
			if(!isset($row[$column_key]))
				continue;

			$value = $row[$column_key];
			if(is_null($index_key))
			{
				$column[] = $value;
				continue;
			}

			if(!isset($row[$index_key]))
			{
				$column[] = $value;
				continue;
			}

			$column[$row[$index_key]] = $value;
		}
		return $column;
	}
}
