<?php

$classOperatorIniSettings = eZINI::instance('owsimpleoperator.ini')->variable( 'ClassOperators', 'PermittedClassOperatorList' );
$eZTemplateOperatorArray = array(
    // This operator is added in owsimpleoperator.ini
     /*array(
        'script' => 'extension/owsimpleoperator/autoloads/exampleoperator.php',
        'class' => 'ExampleOperator',
        'operator_names' => array(
            'example_sum'
        ) ),*/
     array(
        'script' => 'extension/owsimpleoperator/autoloads/phpfunctionoperator.php',
        'class' => 'PHPFunctionOperator',
        'operator_names' => eZINI::instance('owsimpleoperator.ini')->variable( 'PHPFunctions', 'PermittedFunctionList' )
        )
);
$classMethods = array();

foreach( $classOperatorIniSettings as $settingEntryItems )
{
    $settingEntry = explode( ';', $settingEntryItems );
    $classFileName = $settingEntry[0];
    $className = $settingEntry[1];
    $classMethod = $settingEntry[2];

    if( !in_array( $className, $classMethods ) )
    {
        $classMethods[ $className ]['script'] = $classFileName;
        $classMethods[ $className ]['operator_names'][] = $classMethod;
    }
}

foreach( $classMethods as $className => $classParameters )
{
      $classFileName = $classParameters['script'];
      $classMethods = $classParameters['operator_names'];
      $eZTemplateOperatorArray[] = array( 'script' => $classFileName, 'class' => $className, 'operator_names' => $classMethods );
}

?>