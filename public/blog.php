<?php


namespace horn\lib\apps\blog;
use horn\lib as h;

$config = require '../blog.config.php';
ini_set('include_path', sprintf('%s:%s'
			, ini_get('include_path')
			, $config['install_path']
			));

require 'horn/lib/horn.php';
h\import('apps/blog/controllers');

h\import('lib/configuration');
h\import('lib/controller');
h\import('lib/component');

h\import('lib/render');

main();

function main()
{
	$ctx = new h\component\context;
	@$ctx->in;
	@$ctx->out;
	@$ctx->services;
	@$ctx->results;
	@$ctx->content_type;
	@$ctx->template;
	@$ctx->error_handling;

	$config = h\make_configuration(require '../blog.config.php');
	$component = h\component\build($config, $ctx);
	$component->do_process($ctx);
}
