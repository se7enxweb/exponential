<?php
/**
 * Runs one view of the ezoe module in a process of its own and prints what the browser would receive: the
 * output of a view that ends with cleanExit() (ezoe/load, ezoe/upload), or the content of a view that returns a
 * result (ezoe/engine). Used by the ezoe PHPUnit tests, which cannot call a view that exits in their own process.
 *
 * Usage: php tests/tests/extension/ezoe/helpers/run_ezoe_view.php <view> '<params json>' '<post json>' [user, default admin]
 * Needs the live installation; changes nothing unless the view does (ezoe/engine saves a preference).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$root = dirname( __DIR__, 5 );
chdir( $root );
require_once $root . '/autoload.php';

$view   = isset( $argv[1] ) ? $argv[1] : 'engine';
$params = isset( $argv[2] ) ? (array) json_decode( $argv[2], true ) : array();
$post   = isset( $argv[3] ) ? (array) json_decode( $argv[3], true ) : array();
$name   = isset( $argv[4] ) ? $argv[4] : 'admin';

$script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
$script->startup();
$script->setUseSiteAccess( 'admin' );
$script->initialize();

if ( $name === 'anonymous' )
{
    $id = eZUser::anonymousId();
    eZUser::setCurrentlyLoggedInUser( eZUser::fetch( $id ), $id );
}
else
{
    $user = eZUser::fetchByName( $name );
    eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
}

$http = eZHTTPTool::instance();
foreach ( $post as $key => $value )
    $http->setPostVariable( $key, $value );

$module = eZModule::exists( 'ezoe' );
$result = $module->run( $view, $params );
if ( is_array( $result ) && isset( $result['content'] ) )
    echo $result['content'];
eZExecution::cleanExit();
