<?php

// Operator autoloading

$eZTemplateOperatorArray = array();

$eZTemplateOperatorArray[] =
   array( 'script' => 'extension/enhancedezbinaryfile/autoloads/templateparsexmloperator.php',
                                    'class' => 'TemplateParseXMLOperator',
                                    'operator_names' => array( 'parsexml' ) );

?>