<?php

require_once 'horn/apps/info.php' ;
require_once 'horn/apps/blog.php' ;
require_once 'horn/lib/router.php' ;

$in = \horn\lib\http\request::create_native() ;
$out = new \horn\lib\http\response ;

$routing = array
	( '\horn\lib\simple_router'
	, '/' => '\horn\apps\portal'
	, '/blog' => '\horn\apps\blog'
	, '/info' => '\horn\apps\info'
	) ;

$main = \horn\lib\run($in, $out, $routing) ;
$main->run() ;

\horn\lib\render($out) ;
//var_dump($main, $in, $out) ;
