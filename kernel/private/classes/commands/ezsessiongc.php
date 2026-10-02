<?php
/**
 * The code of bin/php/ezsessiongc.php, moved into a class (#207 stage 1). The file bin/php/ezsessiongc.php is one call to it.
 * @description Remove expired sessions as defined by site.ini [Session] SessionTimeout
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/ezsessiongc.php:
 *
 *
 * File containing the ezsessiongc.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

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

        $cli = $this->cli();
        $script = $this->script( array( 'description' => ( "Exponential Session Garbage Collector\n\n" .
                                                                "Allows manual cleaning up expired sessions as defined by site.ini[Session]SessionTimeout\n" .
                                                                "\n" .
                                                                "./bin/php/ezsessiongc.php" ),
                                             'use-session' => false,
                                             'use-modules' => false,
                                             'use-extensions' => true ) );

        $options = $this->startup( "",
                                        "[]",
                                        array() );

        $cli->output( "Cleaning up expired sessions." );

        // expired sessions and the baskets they leave
        \Exponential\Service\SessionGarbageCollector::collect();

        $script->shutdown();
    }
}

}
