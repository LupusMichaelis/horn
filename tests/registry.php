<?php

require_once 'horn/lib/registry.php' ;

try
{
	$registry = new \horn\registry ;
	assert(null === $registry->get('/')) ;

	assert(is_array($registry->childs())) ;
	assert(0 == count($registry->childs())) ;
	assert(count($registry->childs()) === 0) ;

	$registry->set('/', 'magic') ;
	assert($registry->get('/') === 'magic') ;
	$registry->set('', 'oniric') ;
	assert($registry->get('/') === 'oniric') ;

	$registry->set('/', 'even') ;
	assert($registry->get('/') === 'even') ;

	$registry->set('/leaf', 'nerv') ;

	assert($registry->get() === 'even') ;
	assert($registry->get('/') === 'even') ;
	assert($registry->get('/leaf') === 'nerv') ;

	assert(is_array($registry->childs('leaf'))) ;
	assert(0 == count($registry->childs('leaf'))) ;
	assert(count($registry->childs('leaf')) === 0) ;

	$registry->cd('/leaf') ;
	assert($registry->get() === 'nerv') ;

	$registry->cd('/') ;
	assert($registry->get() === 'even') ;

	$registry->cd('leaf') ;
	assert($registry->get() === 'nerv') ;

	$registry->set('/leaf/apple', array() ) ;
	assert(is_array($registry->get('/leaf/apple'))) ;
	assert(is_array($registry->get('apple'))) ;

	$registry->cd('/') ;
	assert($registry->get('/leaf') === 'nerv') ;
	assert(is_array($registry->get('/leaf/apple'))) ;

	assert(is_array($registry->childs())) ;
	assert(count($registry->childs())) ;
	assert(count($registry->childs()) === 1) ;
}
catch(exception $e)
{
	var_dump($registry) ;
}


