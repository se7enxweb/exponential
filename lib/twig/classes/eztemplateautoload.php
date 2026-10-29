<?php

// Operator autoloading: the stub a .twig is parsed as calls exp_twig_render (see expTwigRenderer)

$eZTemplateOperatorArray = array();

$eZTemplateOperatorArray[] = array( 'class' => 'expTwigOperator',
                                    'operator_names' => array( 'exp_twig_render' ) );

?>
