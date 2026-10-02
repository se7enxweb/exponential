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
 *   php bin/php/preloadjob.php --id=<hex> [--target=<siteaccess>] [--max-pages=<n>] [--max-depth=<n>]
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
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'cli', 'dir', 'e', 'events', 'exit', 'handle', 'id', 'maxDepth', 'maxPages', 'options', 'runner', 'script', 'send', 'siteaccess', 'stopFile', 'stopped' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = $this->cli();
        $script = $this->script( array(
            'description'    => 'Runs one preload for Setup > Preload and writes its events.',
            'use-session'    => false,
            'use-modules'    => true,
            'use-extensions' => true,
        ) );
        $script->startup();
        $options = $this->options( '[id:][target:][max-pages:][max-depth:]', '', array(
            'id'        => 'the job id (hex), chosen by setup/preloadjob',
            'target'    => 'the siteaccess whose site is preloaded (default: DefaultAccess)',
            'max-pages' => 'most pages to fetch (default 250)',
            'max-depth' => 'most links to follow from a starting page (default 3)',
        ) );
        $script->initialize();

        $id = (string)$options['id'];
        if ( !preg_match( '#^[a-f0-9]{16}$#', $id ) )
        {
            $cli->error( 'A --id of 16 hex characters is needed.' );
            $script->shutdown( 1 );
        }
        $dir = \expPreloadJob::directory();
        $events = $dir . '/' . $id . '.jsonl';
        $stopFile = $dir . '/' . $id . '.stop';
        $handle = fopen( $events, 'a' );
        if ( !$handle )
        {
            $cli->error( "Cannot write $events." );
            $script->shutdown( 1 );
        }

        $send = function ( $type, $message, array $data = array() ) use ( $handle, $stopFile )
        {
            fwrite( $handle, json_encode( array( 'type' => $type, 'message' => $message, 'time' => time() ) + $data ) . "\n" );
            fflush( $handle );
            if ( $type !== 'done' && is_file( $stopFile ) )
                throw new \RuntimeException( 'stopped from the administration' );
        };

        $siteaccess = $options['target'] !== null ? (string)$options['target'] : '';
        $maxPages = $options['max-pages'] !== null ? max( 1, min( 5000, (int)$options['max-pages'] ) ) : 250;
        $maxDepth = $options['max-depth'] !== null ? max( 0, min( 10, (int)$options['max-depth'] ) ) : 3;

        $exit = 0;
        try
        {
            $send( 'info', 'Preloader started at ' . date( 'Y-m-d H:i:s T' ) . '.' );
            $runner = new \expPreloadRunner( $send, array(
                'siteaccess' => $siteaccess,
                'max_pages'  => $maxPages,
                'max_depth'  => $maxDepth,
            ) );
            $runner->run();
        }
        catch ( \Exception $e )
        {
            $stopped = is_file( $stopFile );
            fwrite( $handle, json_encode( array( 'type' => $stopped ? 'warn' : 'error', 'message' => 'Preloader stopped: ' . $e->getMessage(), 'time' => time() ) ) . "\n" );
            fwrite( $handle, json_encode( array( 'type' => 'done', 'message' => 'Stopped early.', 'time' => time() ) ) . "\n" );
            $exit = $stopped ? 0 : 1;
        }
        fwrite( $handle, json_encode( array( 'type' => 'end', 'message' => '', 'time' => time() ) ) . "\n" );
        fclose( $handle );
        @unlink( $stopFile );
        $script->shutdown( $exit );
    }
}

}
