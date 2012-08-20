<?php

use horn\lib as h ;

require 'horn/lib/horn.php' ;

h\import('apps/blog/controller') ;

// Everything is routed to info application
$config = array
	( 'app' => '\horn\lib\app'
	, 'scheme' => 'http'
	, 'domain' => 'horn.localhost'
	, 'base' => '/fakeroot'

	, 'controllers' => array
		( array
			( 'base' => '/stories'
			, 'controller' => '\horn\apps\blog\controller'
			)
		)

	, 'content-types' => array
		( 'availables' => array
			( 'html' => h\string('text/html')
			, 'rss' => h\string('application/rss+xml')
			)
		, 'default' => 'html'
		)

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

