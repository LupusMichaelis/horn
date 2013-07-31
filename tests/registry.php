<?php

use \horn\lib as h;
h\import('lib/registry');

try
{
	$registry = new \horn\registry;
	assert(null === $registry['/']);

	assert(is_array($registry->childs()));
	assert(0 == count($registry->childs()));
	assert(count($registry->childs()) === 0);

	$registry['/'] = 'magic';
	assert($registry['/'] === 'magic');
	$registry[''] = 'oniric';
	assert($registry['/'] === 'oniric');

	$registry['/'] = 'even';
	assert($registry['/'] === 'even');

	$registry['/leaf'] = 'nerv';

	assert($registry[''] === 'even');
	assert($registry['/'] === 'even');
	assert($registry['/leaf'] === 'nerv');

	assert(is_array($registry->childs('leaf')));
	assert(0 == count($registry->childs('leaf')));
	assert(count($registry->childs('leaf')) === 0);

	$registry->cd('/leaf');
	assert($registry[''] === 'nerv');

	$registry->cd('/');
	assert($registry[''] === 'even');

	$registry->cd('leaf');
	assert($registry[''] === 'nerv');

	$registry['/leaf/apple'] = array();
	assert(is_array($registry['/leaf/apple']));
	assert(is_array($registry['apple']));

	$registry->cd('/');
	assert($registry['/leaf'] === 'nerv');
	assert(is_array($registry['/leaf/apple']));

	assert(is_array($registry->childs()));
	assert(count($registry->childs()));
	assert(count($registry->childs()) === 1);
}
catch(exception $e)
{
	var_dump($registry);
}

$registry = null;

try
{
	$array = array
		( 'branch' => array
			( 'value branch'
			, 'leaf' => array('green')
			)
		);

	$registry = \horn\registry::load($array);

	assert(is_array($registry->childs()));
	assert(count($registry->childs()));
	assert(count($registry->childs()) === 1);

	assert($registry['/branch'] === 'value branch');
	assert($registry['branch'] === 'value branch');

	assert($registry['/branch/leaf'] === 'green');
	assert($registry['branch/leaf'] === 'green');

	unset($registry['branch/leaf']);
	assert(!$registry->offsetExists('branch/leaf'));
	assert(!isset($registry['branch/leaf']));

}
catch(exception $e)
{
	var_dump($registry);
}


