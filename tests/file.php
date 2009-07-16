<?php

require_once 'template-odt.php' ;

$pairs = array('target' => 'PHP redeemer') ;

$template = file_text::load('dummy.txt') ;
$translator = new translator($template) ;
$translator->add_tanslators($pairs) ;

$output = $translator->process() ;
$output->set_name('output.txt') ;
$output->write() ;

$template = file_odt::load('dummy.odt') ;
$translator->set_template($template) ;
$translator->add_tanslators($pairs) ;

$output = $translator->process() ;
$output->set_name('output.odt') ;
$output->write() ;



