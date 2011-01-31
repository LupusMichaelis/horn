<?php

namespace horn\lib\test ;
use horn\lib as h ;

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/test/context.php' ;

interface renderer
{
	function message($fmt) ;
	function log(context $test) ;
}

