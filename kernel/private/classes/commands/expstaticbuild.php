<?php
/**
 * @description Build a static copy of a target's siteaccess into its folder (static publishing): crawled as the anonymous user, links rewritten, files collected, extras and manifest written
 * @alias exp:static:build
 *
 * File containing the exp:static:build command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expstaticbuild extends ExpstaticBase
{
    protected function name()
    {
        return 'exp:static:build';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Build a static copy of a site\n" .
                             "Crawls the target's siteaccess as the anonymous user, strips debug output, form tokens and\n" .
                             "session ids, refuses pages that still carry one, rewrites the links (relative, or below the base\n" .
                             "path), collects the files the pages name (and the storage's images with Assets=referenced-and-storage),\n" .
                             "writes .nojekyll, 404.html, robots.txt, sitemap.xml and the manifest, checks the links and replaces the\n" .
                             "content of var/<site>/static-publish/<target>/. Targets are staticcache.ini [Target_<id>] blocks.\n" .
                             "\n" .
                             "./console exp:static:build github-public\n" .
                             "./console exp:static:build github-public --dry-run          report what would change\n" .
                             "./console exp:static:build github-public --paths=/about-us  only this page\n" .
                             "./console exp:static:build github-public --format=json --report=var/log/static.json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[full][paths:*][nodes:*][dry-run][report:][format:][crawl-url:][max-pages:][keep-staging]', '[target]', array(
            'full' => 'Crawl the whole site (the default without --paths or --nodes); not with --paths or --nodes',
            'paths' => 'Build only these pages, by their path in the site (/about-us; a comma list or repeated); the rest of the last build stays',
            'nodes' => 'Build only the pages of these nodes (node ids; a comma list or repeated); the rest of the last build stays',
            'dry-run' => 'Build into the staging folder, report what would change and leave the target folder as it is',
            'report' => 'Also write the report (exp-static/1, JSON) to this file',
            'format' => 'text (default) or json (the machine output exp-static/1)',
            'crawl-url' => 'Fetch the pages from this address instead of the target\'s CrawlURL or the siteaccess\'s SiteURL (http://127.0.0.1:8080)',
            'max-pages' => 'Stop the crawl after this many pages (default the target\'s MaxPages)',
            'keep-staging' => 'Keep the staging folder of the build (its path is in the report) instead of removing it',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $paths = isset( $options['paths'] ) && $options['paths'] !== false ? self::listOption( $options['paths'] ) : array();
        $nodes = isset( $options['nodes'] ) && $options['nodes'] !== false ? self::listOption( $options['nodes'] ) : array();
        if ( !empty( $options['full'] ) && ( $paths || $nodes ) )
            $this->usage( '--full builds the whole site; leave it out with --paths or --nodes' );
        foreach ( $paths as $path )
            if ( $path[0] !== '/' )
                $this->usage( "--paths takes paths of the site starting with /, not $path" );
        foreach ( $nodes as $node )
            if ( !ctype_digit( $node ) )
                $this->usage( "--nodes takes node ids, not $node" );
        $crawlUrl = isset( $options['crawl-url'] ) && $options['crawl-url'] !== false ? trim( (string)$options['crawl-url'] ) : '';
        if ( $crawlUrl !== '' && !preg_match( '#^https?://[^/\s]+#i', $crawlUrl ) )
            $this->usage( "--crawl-url must be an http or https address, not $crawlUrl" );
        $maxPages = isset( $options['max-pages'] ) && $options['max-pages'] !== false ? (string)$options['max-pages'] : '';
        if ( $maxPages !== '' && ( !ctype_digit( $maxPages ) || (int)$maxPages < 1 ) )
            $this->usage( "--max-pages must be a number, not $maxPages" );
        $reportFile = isset( $options['report'] ) && $options['report'] !== false ? (string)$options['report'] : '';
        if ( $reportFile !== '' && ( !is_dir( dirname( $reportFile ) ) || is_dir( $reportFile ) ) )
            $this->usage( "--report must name a file in a folder that exists, not $reportFile" );

        $target = $this->target( $options, $format );
        if ( !\expStaticTarget::enabled() )
            $this->finish( array( 'mode' => 'build', 'target' => $target->id(), 'warnings' => array(),
                                  'errors' => array( 'Static publishing is switched off: staticcache.ini [StaticPublishSettings] Enabled=disabled' ) ), $format );
        $db = $this->database( $format );

        $command = $this;
        $build = new \expStaticBuild( $target, array(
            'emit' => $format === 'json' ? null : function ( $type, $message, array $data = array() ) use ( $command )
            {
                $command->progress( $type, $message );
            },
            'dry_run' => !empty( $options['dry-run'] ),
            'full' => !empty( $options['full'] ),
            'paths' => $paths,
            'nodes' => array_map( 'intval', $nodes ),
            'crawl_url' => $crawlUrl,
            'max_pages' => $maxPages !== '' ? (int)$maxPages : 0,
            'keep_staging' => !empty( $options['keep-staging'] ),
            'db' => $db,
            'root' => defined( 'EXP_ROOT_DIR' ) ? EXP_ROOT_DIR : (string)getcwd(),
        ) );
        $report = $build->run();
        if ( $reportFile !== '' )
            file_put_contents( $reportFile, json_encode( array( 'schema' => \expStaticBuild::REPORT, 'command' => $this->name() ) + $report,
                                                         JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n" );
        $this->finish( $report, $format );
    }

    /**
     * A line of the build's progress (text output). Public for the closure.
     *
     * @param string $type phase, phase-item, ok, info, warn, error
     * @param string $message
     */
    public function progress( $type, $message )
    {
        if ( $type === 'warn' || $type === 'error' || $type === 'report' )
            return; // listed once at the end
        $this->output( ( $type === 'phase' ? '' : '  ' ) . $message );
    }

    protected function lines( array $report )
    {
        return self::buildLines( $report );
    }
}

}
