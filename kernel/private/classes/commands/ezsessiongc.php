<?php
/**
 * The code of bin/php/ezsessiongc.php, moved into a class (#207 stage 1). The file bin/php/ezsessiongc.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/ezsessiongc.php:
 *
 *
 * File containing the ezsessiongc.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{


// Functions for session to make sure baskets are cleaned up
function eZSessionBasketGarbageCollector( $db, $time )
{
    eZBasket::cleanupExpired( $time );
}
}

namespace Exponential\Command\Kernel
{

class Ezsessiongc extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'cli', 'options', 'script' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = \eZCLI::instance();
        $script = \eZScript::instance( array( 'description' => ( "Exponential Session Garbage Collector\n\n" .
                                                                "Allows manual cleaning up expired sessions as defined by site.ini[Session]SessionTimeout\n" .
                                                                "\n" .
                                                                "./bin/php/ezsessiongc.php" ),
                                             'use-session' => false,
                                             'use-modules' => false,
                                             'use-extensions' => true ) );

        $script->startup();

        $options = $script->getOptions( "",
                                        "[]",
                                        array() );
        $script->initialize();

        $cli->output( "Cleaning up expired sessions." );

        // Fill in hooks
        \eZSession::addCallback( 'gc_pre', 'eZSessionBasketGarbageCollector');

        \eZSession::garbageCollector();

        $script->shutdown();
    }
}

}
