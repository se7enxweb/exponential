<?php
/**
 * The code of bin/php/clusterpurge.php, moved into a class (#207 stage 1). The file bin/php/clusterpurge.php is one call to it.
 * @description Physically purge the cluster files of the given scopes (--scopes=scope1,scope2; dry run unless --apply)
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/clusterpurge.php:
 *
 *
 * Cluster files purge script
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

class Clusterpurge extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'cli', 'options', 'purgeHandler', 'script', 'sys' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = $this->cli();
        $script = $this->script( array( 'description' => ( "Exponential cluster files purge\n" .
                                                                "Physically purges files\n" .
                                                                "Without --apply nothing is purged: the script lists the files it would purge.\n" .
                                                                "--dry-run is still accepted and means the same as no flag; --apply together\n" .
                                                                "with --dry-run is a usage error (exit 2).\n" .
                                                                "\n" .
                                                                "./bin/php/clusterpurge.php --scopes=scope1,scope2\n" .
                                                                "./bin/php/clusterpurge.php --scopes=scope1,scope2 --apply" ),
                                             'use-session' => false,
                                             'use-modules' => false,
                                             'use-extensions' => true ) );

        $script->startup();

        $options = $this->options( "[apply][dry-run][iteration-sleep:][iteration-limit:][memory-monitoring][scopes:][expiry:]",
        "",
        array( 'apply' => 'Purge the files (the default is a dry run that lists them and purges nothing)',
               'dry-run' => 'The default, kept for scripts that pass it: list the affected files without removing them',
               'iteration-sleep' => 'Amount of seconds to sleep between each iteration when performing a purge operation, can be a float. Default is one second.',
               'iteration-limit' => 'Amount of items to remove in each iteration when performing a purge operation. Default is 100.',
               'memory-monitoring' => 'Generates memory monitoring output. If set, memory usage will be logged in var/log/clusterpurge.log.',
               'scopes' => 'Comma separated list of file types to purge. Possible values are: classattridentifiers, classidentifiers, content, expirycache, statelimitations, user-info-cache, viewcache, wildcard-cache-index, image, binaryfile, media.',
               'expiry' => 'Number of days since the file was expired. Only files older than this will be purged. Default is 30, minimum is 1.' ) );
        $sys = \eZSys::instance();

        // before initialize(): a usage error ends the script before any database access
        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $script->shutdown( 2 );
        }
        $dryRun = !$apply;

        $script->initialize();

        if ( !\eZScriptClusterPurge::isRequired() )
        {
            $cli->error( "Your current cluster handler does not require files purge" );
            $script->shutdown( 1 );
        }

        $purgeHandler = new \eZScriptClusterPurge();
        $purgeHandler->optDryRun = $dryRun;

        if ( $options['iteration-sleep'] )
        {
            $purgeHandler->optIterationSleep = (int)( $options['iteration-sleep'] * 1000000 );
        }

        if ( $options['iteration-limit'] )
        {
            $purgeHandler->optIterationLimit = (int)$options['iteration-limit'];
        }

        if ( $options['memory-monitoring'] )
        {
            $purgeHandler->optMemoryMonitoring = true;
        }

        if ( $options['scopes'] )
        {
            $purgeHandler->optScopes = explode( ',', $options['scopes'] );
        }

        if ( $options['expiry'] )
        {
            $purgeHandler->optExpiry = (int)$options['expiry'] * 86400; // 60*60*24
        }

        $purgeHandler->run();

        if ( $dryRun )
        {
            $cli->output( "PASS: {$purgeHandler->listed} expired file(s) would be purged (dry run, nothing changed; --apply writes)" );
            $script->shutdown();
            return;
        }
        // Check what was purged: no expired file of the scopes is left.
        $left = \eZClusterFileHandler::instance()->fetchExpiredItems( $purgeHandler->optScopes, array( 0, 1 ), $purgeHandler->optExpiry );
        if ( $left )
        {
            $cli->error( 'FAIL: expired files are still there, e.g. ' . reset( $left ) );
            $script->shutdown( 1 );
        }
        $cli->output( "PASS: {$purgeHandler->listed} expired file(s) purged" );
        $script->shutdown();
    }
}

}
