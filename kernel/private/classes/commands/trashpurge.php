<?php
/**
 * The code of bin/php/trashpurge.php, moved into a class (#207 stage 1). The file bin/php/trashpurge.php is one call to it.
 * @description Permanently delete all objects in the trash (dry run unless --apply)
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/trashpurge.php:
 *
 *
 * Trash purge script
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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
        foreach ( array( 'options', 'script' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $script = $this->script(
            array(
                'description' =>
                    "Empty Exponential trash.\n" .
                    "Permanently deletes all objects in the trash.\n" .
                    "Without --apply nothing is deleted: the script counts and lists what it would purge. --dry-run\n" .
                    "means the same as no flag; --apply together with --dry-run is a usage error (exit 2). The cronjob\n" .
                    "part trashpurge purges as before.\n" .
                    "\n" .
                    "./bin/php/trashpurge.php\n" .
                    "./bin/php/trashpurge.php --apply",
                'use-session' => false,
                'use-modules' => false,
                'use-extensions' => true,
            )
        );

        $options = $this->startup(
            "[apply][dry-run][iteration-sleep:][iteration-limit:][memory-monitoring][trashed-days:]",
            "",
            array(
                'apply' => 'Purge the objects (the default is a dry run that lists them and deletes nothing)',
                'dry-run' => 'The default: count and list what would be purged, change nothing',
                'iteration-sleep' => 'Amount of seconds to sleep between each iteration when performing a purge operation, can be a float. Default is one second.',
                'iteration-limit' => 'Amount of items to remove in each iteration when performing a purge operation. Default is 100.',
                'memory-monitoring' => 'If set, memory usage will be logged in var/log/trashpurge.log.',
                'trashed-days'      => 'If set, only objects that has been trashed for at least the specified amount of days will be purged.'
            )
        );

        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $this->cli()->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $script->shutdown( 2 );
        }
        $dryRun = !$apply;
        $days = $options['trashed-days'] ? (int)$options['trashed-days'] : null;
        $trashed = \eZScriptTrashPurge::trashedBefore( $days );
        if ( $trashed === false )
        {
            $this->cli()->error( "FAIL: cannot purge items trashed at least $days days ago: the date is out of range" );
            $script->shutdown( 1 );
        }
        $count = (int)\eZContentObjectTrashNode::trashListCount( array( 'Trashed' => $trashed ) );
        if ( $dryRun )
        {
            foreach ( (array)\eZContentObjectTrashNode::trashList( array( 'Limit' => 50, 'Trashed' => $trashed ), false ) as $trashNode )
            {
                $object = $trashNode->attribute( 'object' );
                if ( $object )
                    $this->cli()->output( sprintf( 'would purge object %d  %s', $object->attribute( 'id' ), $object->attribute( 'name' ) ) );
            }
            if ( $count > 50 )
                $this->cli()->output( '... and ' . ( $count - 50 ) . ' more' );
            $this->cli()->output( "PASS: $count object(s) in the trash" . ( $days ? " for at least $days days" : '' )
                                  . ' would be purged (dry run, nothing changed; --apply writes)' );
            $script->shutdown();
            return;
        }

        $script->setIterationData( '.', '~' );

        if (
            !$dryRun &&
            \Exponential\Service\Trash::purge(
                $this->cli(), false, (bool)$options['memory-monitoring'], $script,
                $options['iteration-limit'] ? (int)$options['iteration-limit'] : null,
                $options['iteration-sleep'] ? (int)$options['iteration-sleep'] : null,
                $options['trashed-days']    ? (int)$options['trashed-days'] : null
            )
        )
        {
            $left = (int)\eZContentObjectTrashNode::trashListCount( array( 'Trashed' => $trashed ) );
            if ( $left )
            {
                $this->cli()->error( "FAIL: $left object(s) still in the trash" );
                $script->shutdown( 1 );
            }
            $this->cli()->output( "PASS: $count object(s) purged from the trash" );
            $script->shutdown();
        }
        else
        {
            $script->shutdown( 1 );
        }
    }
}

}
