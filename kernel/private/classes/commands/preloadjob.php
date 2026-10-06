<?php
/**
 * The code of bin/php/preloadjob.php, moved into a class (#207 stage 1). The file bin/php/preloadjob.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/preloadjob.php:
 *
 *
 * File containing the preloadjob.php script.
 *
 * Runs one preload for Setup > Preload in the background: the page starts it
 * (setup/preloadjob) and follows its progress from the events this script
 * writes, one JSON line each, to var/<var dir>/preload/<id>.jsonl. No web
 * request stays open while the site is crawled, so neither a web server's
 * request time limit nor a proxy that buffers streamed answers can stop it.
 *
 * A file <id>.stop next to the events asks the run to end; it is looked at
 * between two events.
 *
 * Usage (started by setup/preloadjob, not meant to be typed):
 *   php bin/php/preloadjob.php --id=<hex> [--target=<siteaccess>] [--max-pages=<n>] [--max-depth=<n>] [--images]
 *
 * @copyright Copyright (C) 1998 - 2026 7x and others. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 *
 */

namespace Exponential\Command\Kernel
{

class Preloadjob extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $script = $this->script( array(
            'description'    => 'Runs one preload for Setup > Preload and writes its events and status.',
            'use-session'    => false,
            'use-modules'    => true,
            'use-extensions' => true,
        ) );
        $options = $this->startup( '[id:][target:][max-pages:][max-depth:][images]', '', array(
            'id'        => 'the job id (hex), chosen by setup/preloadjob',
            'target'    => 'the siteaccess whose site is preloaded (default: DefaultAccess)',
            'max-pages' => 'most pages to fetch (default 250)',
            'max-depth' => 'most links to follow from a starting page (default 3)',
            'images'    => 'also request the images the warmed pages show, and report the missing ones',
        ) );

        $id = (string)$options['id'];
        if ( !\expPreloadJob::isID( $id ) )
        {
            $cli->error( 'A --id of 16 hex characters is needed.' );
            $script->shutdown( 1 );
        }
        $siteaccess = $options['target'] !== null ? (string)$options['target'] : '';
        if ( $siteaccess !== '' && !in_array( $siteaccess, \expPreloadJob::knownSiteaccesses(), true ) )
        {
            $cli->error( 'Unknown siteaccess: ' . $siteaccess );
            $script->shutdown( 1 );
        }

        // The run itself, its lock, events and status: the same as bin/php/preload.php from the shell.
        $exit = \expPreloadJob::execute( $id, $siteaccess, array(
            'max_pages' => $options['max-pages'],
            'max_depth' => $options['max-depth'],
            'images'    => (bool)$options['images'],
        ), null, 'page' );
        $script->shutdown( $exit );
    }
}

}
