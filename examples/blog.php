<?php

use horn\lib as h ;

require 'horn/lib/horn.php' ;

h\import('apps/blog/app') ;

// Everything is routed to info application
$config = array
	( 'routing' => array('\horn\apps\blog')
	, 'locale' => 'fr_FR.UTF-8'
	, 'db' => array
		( 'type' => \horn\lib\db\MYSQL
		, 'host' => 'localhost'
		, 'user' => 'horn'
		, 'password' => 'horn'
		, 'base' => 'horn'
		, 'charset' => 'utf8'
		)
	) ;

$in = \horn\lib\http\request::create_native() ;
$out = new \horn\lib\http\response ;

$main = \horn\lib\run($in, $out, $config) ;
$main->run() ;

\horn\lib\render($out) ;

