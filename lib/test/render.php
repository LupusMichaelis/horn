<?php

namespace horn\lib\test ;
use horn\lib as h ;

import('lib/object') ;
import('lib/test/context') ;

interface renderer
{
	function message($fmt) ;
	function log(context $test) ;
}

