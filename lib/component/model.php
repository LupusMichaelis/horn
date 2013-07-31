<?php
/** Backend model
 *
 *	\copyright      Caturday Games (2013)
 *	\author         <mickael@caturday-games.com>
 *	\project        DogStyle
 */

namespace horn\lib\component;
use \horn\lib as h;

h\import('lib/component');

class model
	extends base
{
	protected		function do_before(context $ctx)
	{
		$model_class = $this->configuration['model'];
		$ctx->model = new $model_class($ctx->services);
	}

	protected		function do_after(context $ctx)
	{
		// flush
	}
}

