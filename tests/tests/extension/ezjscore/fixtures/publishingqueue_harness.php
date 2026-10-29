<?php
/**
 * Calls ezpublishingqueue::status with stand-ins for the kernel classes it uses, in a process of its own
 * (ezjscServerFunctionsPublishingQueueStatusTest). The first argument is a JSON object:
 *
 *   args     the arguments of the call
 *   version  null when version 1 of object 7 does not exist, else array( 'creator_id' => ... )
 *   canRead  whether the current user can read object 7
 *   user     array( 'registered' => bool, 'id' => int ), the current user
 *   process  null for no publishing process, else its status (1 working, 2 finished, 3 pending, 4 deferred)
 *
 * Prints one JSON object: array( 'return' => the answer ) or array( 'error' => the exception's message ).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$GLOBALS['harnessCase'] = json_decode( $argv[1], true );

// The stand-ins are declared through eval(): written as class declarations here, the autoload generator (which
// walks tests/ with -s) would map the kernel's class names to this script, and loading a class would run it.
eval( <<<'STANDINS'
class ezjscServerFunctions
{
}

class ezcBaseFunctionalityNotSupportedException extends Exception
{
    public function __construct( $message, $reason )
    {
        parent::__construct( "{$message} is not supported. Reason: {$reason}." );
    }
}

class eZURI
{
    public static function transformURI( &$uri ) { $uri = '/index.php/' . $uri; }
}

class eZUser
{
    public static function currentUser() { return new self(); }
    public function isRegistered() { return $GLOBALS['harnessCase']['user']['registered']; }
    public function attribute( $name ) { return $name === 'contentobject_id' ? $GLOBALS['harnessCase']['user']['id'] : null; }
}

class eZContentObjectTreeNode
{
    public function attribute( $name ) { return $name === 'url_alias' ? 'secret/draft-title' : null; }
}

class eZContentObject
{
    public function canRead() { return $GLOBALS['harnessCase']['canRead']; }
    public function attribute( $name ) { return $name === 'main_node' ? new eZContentObjectTreeNode() : null; }
}

class eZContentObjectVersion
{
    public static function fetchVersion( $version, $contentObjectId )
    {
        if ( $GLOBALS['harnessCase']['version'] === null || (string)$contentObjectId !== '7' || (string)$version !== '1' )
            return null;
        return new self();
    }

    public function attribute( $name )
    {
        if ( $name === 'contentobject' )
            return new eZContentObject();
        return isset( $GLOBALS['harnessCase']['version'][$name] ) ? $GLOBALS['harnessCase']['version'][$name] : null;
    }
}

class ezpContentPublishingProcess
{
    const STATUS_WORKING = 1;
    const STATUS_FINISHED = 2;
    const STATUS_PENDING = 3;
    const STATUS_DEFERRED = 4;

    public static function fetchByContentObjectVersion( $contentObjectId, $version )
    {
        if ( $GLOBALS['harnessCase']['process'] === null || (string)$contentObjectId !== '7' || (string)$version !== '1' )
            return false;
        return new self();
    }

    public function attribute( $name )
    {
        if ( $name === 'status' )
            return $GLOBALS['harnessCase']['process'];
        if ( $name === 'version' )
            return eZContentObjectVersion::fetchVersion( 1, 7 );
        return null;
    }
}
STANDINS
);

require dirname( __DIR__, 5 ) . '/extension/ezjscore/classes/ezjscserverfunctionspublishingqueue.php';

try
{
    echo json_encode( array( 'return' => ezjscServerFunctionsPublishingQueue::status( $GLOBALS['harnessCase']['args'] ) ) );
}
catch ( ezcBaseFunctionalityNotSupportedException $e )
{
    echo json_encode( array( 'error' => $e->getMessage() ) );
}
