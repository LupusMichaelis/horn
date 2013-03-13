<?php

use horn\lib as h ;

require 'horn/lib/horn.php' ;

h\import('apps/blog/controller') ;
h\import('apps/user/controller') ;

// Everything is routed to info application
$config = array
	( 'app' => '\horn\lib\app'
	, 'scheme' => 'http'
	, 'domain' => 'horn.localhost'
	, 'base' => '/fakeroot'

	, 'controllers' => array
		( array
			( 'base' => '/stories'
			, 'controller' => '\horn\apps\blog\story_controller'
			)
		, array
			( 'base' => '/users'
			, 'controller' => '\horn\apps\user\controller'
			)
		, array
			( 'base' => null
			, 'controller' => '\horn\apps\blog\legacy_controller'
			)
		, array
			( 'base' => null
			, 'controller' => '\horn\apps\blog\portal_controller'
			)
		)

	, 'renderer' => array
		( 'text/html' => '\horn\apps\blog\page_html'
		, 'application/rss+xml' => '\horn\lib\feed_rss'
		)

	, 'views' => array()

	, 'content-types' => array
		( 'availables' => array
			( 'html' => 'text/html'
			, 'rss' => 'application/rss+xml'
			)
		, 'default' => 'html'
		)

	, 'locale' => 'fr_FR.UTF-8'
	// , 'locale' => 'en_US.UTF-8'

	, 'datas' => array
		( 'read' => array
			( 'type' => \horn\lib\db\MYSQL
			, 'host' => 'localhost'
			, 'user' => 'horn'
			, 'password' => 'horn'
			, 'base' => 'horn'
			, 'charset' => 'utf8'
			)
		, 'write' => 'read'
		, 'cache' => array
			( 'type' => \horn\lib\db\MEMCACHE
			, 'host' => 'localhost'
			)
		)
	) ;

$in = \horn\lib\http\request::create_native() ;
$out = new \horn\lib\http\response ;

$user = \horn\lib\http\user::create_native() ;

\horn\lib\run($user, $in, $out, $config) ;
\horn\lib\render($out) ;

