<?php
$Module = array( "name" => "Sitemaps",
                 "variable_params" => false,
                 "function" => array(
                     "script" => "index.php",
                     "params" => array( ) ) );

$ViewList = array();
$ViewList["index"] = array(
    "script" => "index.php",
    'params' => array(  ) );
$ViewList["robots"] = array(
    "script" => "robots.php",
    'params' => array(  ) );
?>
