<?php

require_once 'horn/apps/blog.php' ;
require_once 'horn/lib/router.php' ;


// Everything is routed to info application
$config = array
	( 'routing' => array('\horn\apps\blog')
	, 'locale' => 'fr_FR.UTF-8'
	) ;

$in = \horn\lib\http\request::create_native() ;
$out = new \horn\lib\http\response ;

$main = \horn\lib\run($in, $out, $config) ;
$main->run() ;

\horn\lib\render($out) ;

