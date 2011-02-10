<?php

require_once 'horn/apps/blog.php' ;
require_once 'horn/lib/router.php' ;

$in = \horn\lib\http\request::create_native() ;
$out = new \horn\lib\http\response ;

// Everything is routed to info application
$routing = array('\horn\apps\blog') ;

$main = \horn\lib\run($in, $out, $routing) ;
$main->run() ;

\horn\lib\render($out) ;

