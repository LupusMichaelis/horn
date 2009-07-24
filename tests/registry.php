<?php

require_once 'horn/lib/registry.php' ;

$registry = new \horn\registry ;
assert(null === $registry->get('/')) ;

$registry->set('/', 'magic') ;
assert($registry->get('/') === 'magic') ;
$registry->set('', 'oniric') ;
assert($registry->get('/') === 'oniric') ;

$registry->set('/', 'even') ;
assert($registry->get('/') === 'even') ;

$registry->set('/leaf', 'nerv') ;

assert($registry->get('/') === 'even') ;
assert($registry->get('/leaf') === 'nerv') ;

assert($registry->get() === 'even') ;
$registry->cd('/leaf') ;
assert($registry->get() === 'nerv') ;



