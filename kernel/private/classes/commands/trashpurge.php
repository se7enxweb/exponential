<?php
/**
 * The code of bin/php/trashpurge.php, moved into a class (#207 stage 1). The file bin/php/trashpurge.php is one call to it.
 * @description Permanently delete all objects in the trash
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/trashpurge.php:
 *
 *
 * Trash purge script
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

class Trashpurge extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'options', 'purgeHandler', 'script' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $script = \eZScript::instance(
            array(
                'description' =>
                    "Empty Exponential trash.\n" .
                    "Permanently deletes all objects in the trash.\n" .
                    "\n" .
                    "./bin/php/trashpurge.php",
                'use-session' => false,
                'use-modules' => false,
                'use-extensions' => true,
            )
        );

        $script->startup();

        $options = $script->getOptions(
            "[iteration-sleep:][iteration-limit:][memory-monitoring][trashed-days:]",
            "",
            array(
                'iteration-sleep' => 'Amount of seconds to sleep between each iteration when performing a purge operation, can be a float. Default is one second.',
                'iteration-limit' => 'Amount of items to remove in each iteration when performing a purge operation. Default is 100.',
                'memory-monitoring' => 'If set, memory usage will be logged in var/log/trashpurge.log.',
                'trashed-days'      => 'If set, only objects that has been trashed for at least the specified amount of days will be purged.'
            )
        );

        $script->initialize();

        $script->setIterationData( '.', '~' );

        $purgeHandler = new \eZScriptTrashPurge( \eZCLI::instance(), false, (bool)$options['memory-monitoring'], $script );

        if (
            $purgeHandler->run(
                $options['iteration-limit'] ? (int)$options['iteration-limit'] : null,
                $options['iteration-sleep'] ? (int)$options['iteration-sleep'] : null,
                $options['trashed-days']    ? (int)$options['trashed-days'] : null
            )
        )
        {
            $script->shutdown();
        }
        else
        {
            $script->shutdown( 1 );
        }
    }
}

}
