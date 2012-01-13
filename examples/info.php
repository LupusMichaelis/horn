<?php

require 'horn/lib/horn.php' ;

\horn\lib\import('apps/info') ;
\horn\lib\import('lib/router') ;

$in = \horn\lib\http\request::create_native() ;
$out = new \horn\lib\http\response ;

// Everything is routed to info application
$routing = array('\horn\apps\info') ;

$main = \horn\lib\run($in, $out, $routing) ;
$main->run() ;

\horn\lib\render($out) ;
