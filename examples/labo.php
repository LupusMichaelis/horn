<?php

require 'horn/lib/horn.php' ;

\horn\lib\import('apps/info') ;
\horn\lib\import('apps/blog/app') ;
\horn\lib\import('lib/router') ;

$in = \horn\lib\http\request::create_native() ;
$out = new \horn\lib\http\response ;

$routing = array
	( '\horn\lib\simple_router'
	, '/' => '\horn\apps\portal'
	, '/blog' => '\horn\apps\blog'
	, '/info' => '\horn\apps\info'
	) ;

$config = array('routing' => $routing) ;

$main = \horn\lib\run($in, $out, $config) ;
$main->run() ;

\horn\lib\render($out) ;
//var_dump($main, $in, $out) ;
