<?php

return array
	( 'components' => array
		// That's an HTTP app
		( 'http'

		// We want to log and debug
		, 'logging'
		, 'debug'
		, 'error'

		// HTTP headers
		, 'http_cache'

		// View renderer
		, 'content_type'
		, 'template'

		// Routing
		, 'routing'

		// Data layouts
		, 'services'
		, 'model'

		// The actual action controller
		, 'http_controllers'
		)
	, 'install_path' => null
	, 'title' => 'Blog'

	, 'locale' => 'en_US.UTF-8'

	, 'scheme' => 'http'
	, 'domain' => 'horn.localhost'
/* XXX legacy
	, 'base' => '/fakeroot'
*/

	, 'db' => array
		( 'type' => 'mysql' //\horn\lib\db\MYSQL
		, 'host' => 'localhost'
		, 'account' => null
		, 'password' => null
		, 'base' => null
		, 'charset' => 'utf8'
		)
	, 'cache' => array
		( 'type' => 'memcache'
		, 'host' => 'localhost'
		, 'port' => 11211
		)
/* XXX legacy
	, 'datas' => array
		( 'read' => array
			( 'type' => \horn\lib\db\MYSQL
			, 'host' => 'localhost'
			, 'account' => null
			, 'password' => null
			, 'base' => null
			, 'charset' => 'utf8'
			)
		, 'write' => 'read'
		, 'cache' => array
			( 'type' => \horn\lib\db\MEMCACHE
			, 'host' => 'localhost'
			)
		)
*/
/* XXX legacy
	, 'renderer' => array
		( 'text/html' => '\horn\apps\blog\page_html'
		, 'application/rss+xml' => '\horn\lib\feed_rss'
		)
*/

	, 'routes' => array
		( '/'		=> '\horn\apps\blog\portal_controller'
		, '/stories/(?<title>.+)'
					=> '\horn\apps\blog\story_controller'
		, '/stories'
					=> '\horn\apps\blog\stories_controller'
		, '/accounts/(?<name>.+)'
					=> '\horn\apps\blog\account_controller'
		, '/accounts'
					=> '\horn\apps\blog\accounts_controller'
		, null		=> '\horn\apps\blog\legacy_controller'
		)
	, 'content_type' => array
		( 'catalog' => array
			( 'html' => array
				( 'mime_type'	=> 'text/html'
				, 'encoding'	=> 'utf-8'
				, 'engine'		=> '\horn\lib\render\html'
				)
			, array
				( 'mime_type'	=> 'application/rss+xml'
				, 'encoding'	=> 'utf-8'
				, 'engine'		=> '\horn\lib\render\html'
				)
			)
		, 'default' => 'html'
		, 'mime_type'	=> 'text/html'
		, 'encoding'	=> 'utf-8'
		, 'engine'		=> '\horn\lib\render\html'
		)
	, 'template' => array
		( 'engine'	=> '\horn\lib\render\php_include_strategy'
		, 'path'	=> 'horn/apps/blog/templates'
		, 'name'	=> 'page.html'
		, 'action'	=> 'read'
		)
/* XXX legacy
	, 'template' => array
		( 'type' => 'mustache'
		, 'store_path' => 'apps/blog/templates'
		, 'skins' => array('default')
		, 'default_skin' => 'default'
		)
*/
	, 'log' => array
		( 'service' => 'logger'
		, 'type' => 'file'
		, 'filename' => null
		)
	, 'model' => '\horn\apps\blog\model'
	, 'scripts' => array()
	, 'styles' => array()
	);
