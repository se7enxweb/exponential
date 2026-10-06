<?php
/**
 * The code of bin/php/preload.php, moved into a class (#207 stage 1). The file bin/php/preload.php is one call to it.
 * @description Warm the page caches of a site: request its section pages, then follow its links, and report the broken ones
 * Guide: doc/guides/preloading-caches.md
 */
/*
 * The original header of bin/php/preload.php:
 *
 *
 * File containing the preload.php script to preload your website cache files to speed up page loading of your website by siteaccess name parameter.
 *
 * Warms the main section pages (derived from site.ini [SiteSettings] SiteURL /
 * URLTranslationKeyword) then spiders the entire site via wget (recursive,
 * level 3) to warm all page caches.  Produces rich, colourised terminal output.
 *
 * Usage:
 *   ./bin/php/preload.php [--siteaccess <name>]
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 *
 */

namespace Exponential\Command\Kernel
{

/**
 * The preload from the shell and from cron. It is the run Setup > Preload starts in the background
 * (expPreloadJob::execute()): the same addresses, the same crawler, one lock for both, and the run is listed on the
 * page with the others. It used to spider the site with wget and curl, separately from the page, and agreed with
 * it neither on the address of a siteaccess nor on what counts as broken.
 */
class Preload extends \Exponential\Runnable\Command
{
    /** A run from the shell may go further than the page's default. */
    const SHELL_MAX_PAGES = 1000;

    public function run()
    {
        $cli = $this->cli();
        $script = $this->script( array(
            'description'    => "Exponential cache preloader\n\n" .
                                "Requests the section pages of a site, then follows its links, so the caches are warm\n" .
                                "before a visitor arrives, and reports the broken links with the pages that link to them.\n" .
                                "The site is the one of --siteaccess (or --target). One preload runs at a time; the run is\n" .
                                "listed in Setup > Preload.\n\n" .
                                "Usage: php bin/php/preload.php --siteaccess=<name> [--max-pages=<n>] [--max-depth=<n>] [--images] [--dry-run]",
            'use-session'    => false,
            'use-modules'    => true,
            'use-extensions' => true,
        ) );
        $options = $this->startup( '[target:][max-pages:][max-depth:][images][dry-run]', '', array(
            'target'    => 'the siteaccess whose site is preloaded (default: the one of --siteaccess)',
            'max-pages' => 'most pages to request (default ' . self::SHELL_MAX_PAGES . ', at most ' . \expPreloadJob::MAX_PAGES_LIMIT . ')',
            'max-depth' => 'most links to follow from a starting page (default ' . \expPreloadJob::DEFAULT_MAX_DEPTH . ')',
            'images'    => 'also request the images the warmed pages show, and report the missing ones',
            'dry-run'   => 'print the address, the starting pages and the limits, and request nothing',
        ) );

        $siteaccess = $options['target'] !== null ? (string)$options['target']
                    : ( isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '' );
        if ( $siteaccess !== '' && !in_array( $siteaccess, \expPreloadJob::knownSiteaccesses(), true ) )
        {
            $cli->error( 'Unknown siteaccess: ' . $siteaccess );
            $script->shutdown( 1 );
        }
        $runOptions = \expPreloadJob::options( array(
            'max_pages' => $options['max-pages'] !== null ? $options['max-pages'] : self::SHELL_MAX_PAGES,
            'max_depth' => $options['max-depth'],
            'images'    => (bool)$options['images'],
        ) );
        $plan = \expPreloadJob::plan( $siteaccess, $runOptions );

        if ( $options['dry-run'] )
        {
            $this->printPlan( $plan );
            $script->shutdown( $plan['base_url'] === '' ? 1 : 0 );
        }

        $cli->output( $cli->stylize( 'bold', 'Preloading ' . ( $plan['start_urls'] ? $plan['start_urls'][0] : '(no address)' ) )
                    . ( $siteaccess !== '' ? ' (siteaccess ' . $siteaccess . ')' : '' ) );

        $id = \expPreloadHistory::newID();
        $exit = \expPreloadJob::execute( $id, $siteaccess, $runOptions, array( $this, 'printEvent' ), 'shell' );

        $status = \expPreloadJob::history()->read( $id );
        if ( $status !== false && $exit !== 2 )
            $this->printSummary( $status );
        if ( $exit === 2 )
            $cli->error( 'Another preload is running (from Setup > Preload or another shell); try again when it has ended.' );
        $script->shutdown( $exit );
    }

    /**
     * One event of the run, as a line of the terminal.
     */
    public function printEvent( $type, $message, array $data = array() )
    {
        $cli = $this->cli();
        switch ( $type )
        {
            case 'phase':
                $cli->output();
                $cli->output( $cli->stylize( 'cyan', '== ' . $message ) );
                break;
            case 'phase-item':
            case 'ok':
            case 'image':
                $cli->output( '  ' . $cli->stylize( 'green', $type === 'image' ? 'img' : 'ok ' ) . ' ' . $message );
                break;
            case 'warn':
                $cli->output( '  ' . $cli->stylize( 'yellow', '!  ' ) . ' ' . $message );
                break;
            case 'error':
                $cli->output( '  ' . $cli->stylize( 'red', 'ERR' ) . ' ' . $message );
                break;
            case 'report':
                foreach ( explode( "\n", $message ) as $line )
                    $cli->output( $line );
                break;
            case 'done':
                break;
            default:
                $cli->output( $cli->stylize( 'gray', $message ) );
        }
    }

    /**
     * The closing lines; the last three say what the run did, for a log that keeps only the tail.
     */
    protected function printSummary( array $status )
    {
        $cli = $this->cli();
        $c = $status['counts'];
        $cli->output();
        $cli->output( sprintf( 'Preload %s in %ss: run %s, listed in Setup > Preload.', $status['state'],
                               $status['seconds'] === null ? '?' : $status['seconds'], $status['id'] ) );
        if ( $status['message'] !== '' )
            $cli->output( $status['message'] );
        $cli->output( sprintf( 'Pages warmed %d, broken %d, denied %d, skipped %d.', $c['fetched'], $c['broken'], $c['denied'], $c['skipped'] ) );
        $cli->output( sprintf( 'Image aliases made %s%s.', $status['aliases'] === null ? 'not counted' : (int)$status['aliases'],
                               !empty( $status['options']['images'] ) ? sprintf( ', images checked %d, missing %d', $c['images'], $c['images_broken'] ) : '' ) );
    }

    protected function printPlan( array $plan )
    {
        $cli = $this->cli();
        if ( $plan['base_url'] === '' )
        {
            $cli->error( 'Cannot determine the site url: SiteSettings/SiteURL is not set in site.ini.' );
            return;
        }
        $cli->output( 'Dry run: nothing is requested.' );
        $cli->output( 'Siteaccess:     ' . ( $plan['siteaccess'] !== '' ? $plan['siteaccess'] : '(default)' ) );
        $cli->output( 'Site address:   ' . $plan['base_url'] . ( $plan['prefix'] !== '' ? ' with the prefix ' . $plan['prefix'] : '' ) );
        if ( !$plan['reached'] )
            $cli->warning( 'The siteaccess matching in site.ini does not send this address to the siteaccess; the pages warmed may be those of another one.' );
        $cli->output( sprintf( 'Limits:         %d pages, link depth %d%s', $plan['options']['max_pages'], $plan['options']['max_depth'],
                               $plan['options']['images'] ? ', images checked' : '' ) );
        $cli->output( 'Starting pages:' );
        foreach ( $plan['start_urls'] as $url )
            $cli->output( '  ' . $url );
        $cli->output( 'Then every link on them to the same site, up to the limits.' );
    }
}

}
