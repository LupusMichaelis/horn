<?php

require 'horn/lib/horn.php' ;

\horn\lib\import('apps/info') ;
//\horn\lib\import('lib/router') ;

$in = \horn\lib\http\request::create_native() ;
$out = new \horn\lib\http\response ;

// Everything is routed to info application
$routing = array
	( '\horn\apps\info'
	) ;

h\import('apps/info/controller') ;

// Everything is routed to info application
$config = array
	( 'app' => '\horn\lib\app'
	, 'scheme' => 'http'
	, 'domain' => 'horn.localhost'
	, 'base' => '/fakeroot'

	, 'controllers' => array
		( array
			( 'base' => '/stories'
			, 'controller' => '\horn\apps\info\controller'
			)
		)

	, 'views' => array()

	, 'content-types' => array
		( 'availables' => array
			( 'html' => h\string('text/html')
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


\horn\lib\run($in, $out, $config) ;
\horn\lib\render($out) ;
