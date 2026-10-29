<?php
/**
 * The code of bin/php/cleanuprss.php, moved into a class (#207 stage 1). The file bin/php/cleanuprss.php is one call to it.
 * @description Trim RSS-imported content, keeping the newest items of each active import (dry run unless --apply)
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/cleanuprss.php:
 *
 *
 * File containing the cleanuprss.php script.
 *
 * Trims the content the RSS import has created, keeping the newest items of
 * each feed and removing the rest.
 *
 * Ported from the bccleanuprss extension by Brookins Consulting.
 *
 * @copyright Copyright (C) 1999 - 2011 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or later)
 * @package kernel
 *
 */

namespace Exponential\Command\Kernel
{

class Cleanuprss extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'cleanup', 'cli', 'counts', 'options', 'script' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = $this->cli();
        $script = $this->script(
            array(
                'description' => "Exponential RSS Import Cleanup\n" .
                                 "Keeps the newest items of each active RSS import and removes the rest.\n" .
                                 "\n" .
                                 "Configured in content.ini [RSSImportCleanupSettings]; it removes nothing\n" .
                                 "until that names the classes it is allowed to remove.\n" .
                                 "Without --apply nothing is removed: the script lists the items it would remove.\n" .
                                 "--dry-run is still accepted and means the same as no flag; --apply together with\n" .
                                 "--dry-run is a usage error (exit 2). The cronjob part cleanuprss removes as before.\n" .
                                 "\n" .
                                 "./bin/php/cleanuprss.php\n" .
                                 "./bin/php/cleanuprss.php --apply",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );

        $options = $this->startup(
            "[apply][dry-run][keep:]",
            "",
            array( 'apply'   => 'Remove the items (the default is a dry run that lists them and removes nothing)',
                   'dry-run' => 'The default, kept for scripts that pass it: list what would be removed, remove nothing',
                   'keep'    => 'Keep this many items per feed instead of the configured number' ) );

        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $script->shutdown( 2 );
        }
        $dryRun = !$apply;

        $cleanup = new \expCleanupRSS(
            array( 'dry-run' => $dryRun,
                   'keep'    => $options['keep'] !== null ? (int)$options['keep'] : null ) );

        if ( !$cleanup->isEnabled() )
        {
            // Not an error: an installation that has not asked for this is the normal
            // case, and the run says which setting is holding it rather than going
            // quiet and leaving the operator to guess.
            $cli->warning( 'Nothing was removed: ' . $cleanup->reason() );
            $script->shutdown( 0 );
        }

        $cleanup->cleanup();

        $counts = $cleanup->counts();

        $cli->output();
        if ( $dryRun )
        {
            $cli->output( sprintf( 'PASS: %d feed(s), %d item(s) above the limit would be removed (dry run, nothing changed; --apply writes)',
                                   $counts['feeds'], $counts['examined'] ) );
            $script->shutdown( 0 );
        }
        if ( $counts['removed'] !== $counts['examined'] )
        {
            $cli->error( sprintf( 'FAIL: %d feed(s), %d item(s) above the limit, only %d removed',
                                  $counts['feeds'], $counts['examined'], $counts['removed'] ) );
            $script->shutdown( 1 );
        }
        $cli->output( sprintf( 'PASS: %d feed(s), %d item(s) above the limit, %d removed',
                               $counts['feeds'], $counts['examined'], $counts['removed'] ) );

        $script->shutdown( 0 );
    }
}

}
