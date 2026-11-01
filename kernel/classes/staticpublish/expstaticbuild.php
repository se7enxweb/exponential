<?php
/**
 * File containing the expStaticBuild class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One build of one target of static publishing: the target's siteaccess turned into a folder of static files that
 * any static host serves (doc/eng-us/features/6.0/static-publishing.md; the folder's format is
 * doc/eng-us/specifications/6.0/static-publishing-build-format.md).
 *
 *  1. The site is crawled as the anonymous user by expStaticCacheRunner in export mode (the crawl of the static
 *     cache generator, expPreloadRunner): no cookie is sent, so every page is what a visitor who is not signed in
 *     sees. Each page is stripped of debug output, form tokens, session ids and nonces (expStaticPostProcessor); a
 *     page that still carries one of them, or whose response set a session cookie, is refused and the build fails.
 *  2. The links of every page are read (expStaticLinkRewriter), and the files they name are collected
 *     (expStaticAssetCollector), with all images of var/<site>/storage the anonymous user may see when the target
 *     says Assets=referenced-and-storage.
 *  3. The pages are rewritten (relative links, or links below the base path) and written, with the extras
 *     (.nojekyll, CNAME, 404.html, robots.txt, sitemap.xml) and the manifest (expStaticManifest).
 *  4. The build is compared with the target's folder (the diff), its links are checked (expStaticLinkChecker), and,
 *     unless it is a dry run, it replaces the folder's content: changed and new files are copied, files the last
 *     manifest listed and this one does not are removed, nothing else in the folder is touched (so a git checkout
 *     there keeps its .git).
 *
 * Everything is built in a staging folder first (var/<site>/static-publish/.staging/<target>-<run>/), which is
 * removed afterwards; a failed build never changes the target's folder.
 */
class expStaticBuild
{
    /** The machine output of the exp:static commands */
    const REPORT = 'exp-static/1';

    /** The staging folder below the work directory */
    const STAGING = '.staging';

    private $target;
    private $options;
    private $emit;
    private $report;
    private $runId;
    private $context = array();

    /**
     * @param expStaticTarget $target
     * @param array $options emit (callable( $type, $message, $data )), dry_run, full, paths (page paths), nodes
     *        (node ids), crawl_url, max_pages, root (the installation, default the current directory), var_dir,
     *        work_dir, db (eZDBInterface or null: no node data, no storage images), can_read (callable( $objectId )),
     *        node_lookup (callable( $pagePath ) => array( node, modified, section )|null), fetcher (a request
     *        callable for the crawl and the files, as expPreloadRunner's), base_path (the siteaccess prefix of the
     *        crawl, instead of the one site.ini gives), context (expStaticTarget::siteAccessContext()), run_id,
     *        keep_staging, max_site_bytes, max_file_bytes, session_names
     */
    public function __construct( expStaticTarget $target, array $options = array() )
    {
        $this->target = $target;
        $this->options = $options + array(
            'emit' => null, 'dry_run' => false, 'full' => false, 'paths' => array(), 'nodes' => array(), 'crawl_url' => '',
            'max_pages' => 0, 'root' => (string)getcwd(), 'var_dir' => null, 'work_dir' => null, 'db' => null, 'can_read' => null,
            'node_lookup' => null, 'fetcher' => null, 'base_path' => null, 'context' => null, 'run_id' => '',
            'keep_staging' => false, 'max_site_bytes' => null, 'max_file_bytes' => null, 'session_names' => array(),
        );
        $this->emit = $this->options['emit'];
        $this->runId = $this->options['run_id'] !== '' ? (string)$this->options['run_id'] : self::newRunId();
    }

    /** @return string a run id: <date>-<time>-<6 hex digits> */
    public static function newRunId()
    {
        return gmdate( 'Ymd-His' ) . '-' . bin2hex( random_bytes( 3 ) );
    }

    private function say( $type, $message, array $data = array() )
    {
        if ( $this->emit !== null )
            call_user_func( $this->emit, $type, $message, $data );
    }

    private function error( $message )
    {
        $this->report['errors'][] = $message;
        $this->say( 'error', $message );
    }

    private function warning( $message )
    {
        $this->report['warnings'][] = $message;
        $this->say( 'warn', $message );
    }

    /** @return string the work directory of all targets */
    public function workDirectory()
    {
        $varDir = $this->options['var_dir'] !== null ? (string)$this->options['var_dir'] : eZSys::varDirectory();
        $workDir = $this->options['work_dir'];
        if ( $workDir === null )
        {
            $ini = eZINI::instance( 'staticcache.ini' );
            $workDir = $ini->hasVariable( 'StaticPublishSettings', 'WorkDir' ) ? (string)$ini->variable( 'StaticPublishSettings', 'WorkDir' ) : 'static-publish';
        }
        $dir = expStaticTarget::workDirectory( $varDir, $workDir );
        if ( $dir === '' || ( $dir[0] !== '/' && !preg_match( '#^[a-zA-Z]:/#', $dir ) ) )
            $dir = rtrim( str_replace( '\\', '/', (string)$this->options['root'] ), '/' ) . '/' . $dir;
        return $dir;
    }

    /** @return string the target's folder */
    public function targetDirectory()
    {
        return $this->workDirectory() . '/' . $this->target->id();
    }

    /** @return int an INI number of [StaticPublishSettings], or the option */
    private function limit( $option, $setting, $default )
    {
        if ( $this->options[$option] !== null )
            return (int)$this->options[$option];
        $ini = eZINI::instance( 'staticcache.ini' );
        return $ini->hasVariable( 'StaticPublishSettings', $setting ) ? (int)$ini->variable( 'StaticPublishSettings', $setting ) : $default;
    }

    /**
     * Builds.
     *
     * @return array the report (schema exp-static/1): mode, target, siteaccess, run_id, directory, counts, diff,
     *         pages, refused, files_failed, link_check, limits, applied, errors, warnings, exit
     */
    public function run()
    {
        $started = microtime( true );
        $partial = (bool)$this->options['paths'] || (bool)$this->options['nodes'];
        $this->report = array(
            'schema' => self::REPORT,
            'mode' => $this->options['dry_run'] ? 'dry-run' : 'build',
            'scope' => $partial ? 'partial' : 'full',
            'target' => $this->target->id(),
            'siteaccess' => $this->target->siteAccess(),
            'run_id' => $this->runId,
            'started_at' => gmdate( 'c' ),
            'duration_ms' => 0,
            'directory' => '',
            'links' => array( 'mode' => $this->target->linkMode(), 'base_path' => $this->target->basePath(), 'public_url' => $this->target->publicUrl() ),
            'counts' => array( 'pages' => 0, 'pages_refused' => 0, 'pages_skipped' => 0, 'pages_excluded' => 0, 'files' => 0,
                               'storage' => 0, 'storage_missing' => 0, 'files_failed' => 0, 'extras' => 0, 'links_rewritten' => 0, 'links_live' => 0,
                               'links_external' => 0, 'site_broken_links' => 0, 'bytes' => 0, 'stripped' => array() ),
            'diff' => null,
            'pages' => array(),
            'refused' => array(),
            'skipped' => array(),
            'files_failed' => array(),
            'link_check' => null,
            'limits' => array(),
            'applied' => false,
            'errors' => array(),
            'warnings' => array(),
            'exit' => 0,
        );

        $this->context = is_array( $this->options['context'] ) ? $this->options['context'] : expStaticTarget::siteAccessContext();
        $validation = $this->target->validate( $this->context );
        foreach ( $validation['warnings'] as $warning )
            $this->warning( $warning );
        foreach ( $validation['errors'] as $error )
            $this->error( $error );
        if ( $this->report['errors'] )
            return $this->finish( $started, 1 );

        $targetDir = $this->targetDirectory();
        $staging = $this->workDirectory() . '/' . self::STAGING . '/' . $this->target->id() . '-' . $this->runId;
        $this->report['directory'] = $targetDir;
        if ( !$this->makeDir( $staging ) || !$this->makeDir( $staging . '/.static-publish' ) )
        {
            $this->error( "The staging folder $staging cannot be created." );
            return $this->finish( $started, 1 );
        }

        $user = $this->becomeAnonymous();
        try
        {
            $this->build( $staging, $targetDir, $partial );
        }
        catch ( Exception $e )
        {
            $this->error( 'The build stopped: ' . $e->getMessage() );
        }
        $this->restoreUser( $user );

        if ( !$this->options['keep_staging'] )
            self::removeDirectory( $staging );
        else
            $this->report['staging'] = $staging;
        return $this->finish( $started, $this->report['errors'] ? 1 : 0 );
    }

    /** The report with its duration and exit code. */
    private function finish( $started, $exit )
    {
        $this->report['duration_ms'] = (int)round( ( microtime( true ) - $started ) * 1000 );
        $this->report['exit'] = (int)$exit;
        $this->report['errors'] = array_values( array_unique( $this->report['errors'] ) );
        $this->report['warnings'] = array_values( array_unique( $this->report['warnings'] ) );
        return $this->report;
    }

    /** The crawl options shared by the probe and the crawl. */
    private function crawlOptions()
    {
        $options = array( 'siteaccess' => $this->target->siteAccess(), 'timeout' => 30 );
        $crawlUrl = $this->options['crawl_url'] !== '' ? (string)$this->options['crawl_url'] : (string)$this->target->setting( 'CrawlURL' );
        if ( $crawlUrl !== '' )
            $options['base_url'] = $crawlUrl;
        if ( $this->options['base_path'] !== null )
            $options['base_path'] = (string)$this->options['base_path'];
        if ( $this->options['fetcher'] !== null )
            $options['fetcher'] = $this->options['fetcher'];
        return $options;
    }

    /** One request for a file or the 404 page, as the anonymous user. */
    public function request( $url )
    {
        if ( $this->options['fetcher'] !== null )
            return (array)call_user_func( $this->options['fetcher'], $url, false ) + array( 'status' => 0, 'type' => '', 'body' => '', 'cookies' => array() );
        return expPreloadRunner::request( $url, false, 60, 'Exponential static publishing' );
    }

    /** The work: crawl, collect, rewrite, extras, manifest, check, apply. */
    private function build( $staging, $targetDir, $partial )
    {
        $target = $this->target;
        $crawlOptions = $this->crawlOptions();
        $probe = new expPreloadRunner( function () {}, $crawlOptions );
        $base = $probe->baseUrl();
        if ( $base === false )
        {
            $this->error( 'There is no address to build from: the siteaccess has no SiteURL and the target no CrawlURL.' );
            return;
        }
        $sitePath = $probe->sitePath();
        $originUrl = parse_url( $base, PHP_URL_SCHEME ) . '://' . parse_url( $base, PHP_URL_HOST ) . ( parse_url( $base, PHP_URL_PORT ) ? ':' . parse_url( $base, PHP_URL_PORT ) : '' );
        $siteUrl = isset( $this->context['urls'][$target->siteAccess()] ) ? (string)$this->context['urls'][$target->siteAccess()] : '';
        $origins = array( $originUrl );
        if ( $siteUrl !== '' )
            $origins[] = $siteUrl;
        $liveUrl = (string)$target->setting( 'LiveURL' );
        if ( $liveUrl === '' && $siteUrl !== '' )
            $liveUrl = 'https://' . (string)parse_url( 'http://' . preg_replace( '#^[a-z]+://#i', '', $siteUrl ), PHP_URL_HOST );
        if ( $liveUrl !== '' )
        {
            $origins[] = $liveUrl;
            $liveUrl = preg_replace( '#^([a-z]+://[^/]+).*$#i', '$1', $liveUrl );
        }
        $this->say( 'phase', 'Static publishing: ' . $target->id() . ' (' . $target->siteAccess() . ')' );
        $this->say( 'phase-item', 'From:   ' . $originUrl . ( $sitePath === '' ? '/' : $sitePath . '/' ) );
        $this->say( 'phase-item', 'To:     ' . $targetDir . ( $this->options['dry_run'] ? ' (dry run: not changed)' : '' ) );
        $this->say( 'phase-item', 'Links:  ' . $target->linkMode() . ( $target->linkMode() === 'base-path' ? ' ' . $target->basePath() : '' )
                    . ( $liveUrl !== '' ? ', what is not exported goes to ' . $liveUrl : '' ) );

        // 1. the crawl
        $paths = array_values( array_map( 'strval', (array)$this->options['paths'] ) );
        foreach ( (array)$this->options['nodes'] as $nodeId )
        {
            $path = $this->nodePath( (int)$nodeId );
            if ( $path === null )
                $this->warning( "The node $nodeId has no address the anonymous user can open; it is not built." );
            else
                $paths[] = $path;
        }
        if ( $partial && !$paths )
        {
            $this->error( 'None of the pages asked for has an address.' );
            return;
        }
        $rawDir = $staging . '/.static-publish/raw';
        $this->makeDir( $rawDir );
        $pages = array();
        $self = $this;
        $sessionNames = (array)$this->options['session_names'];
        $maxDepth = $target->setting( 'MaxDepth' );
        if ( $maxDepth === '' )
        {
            $ini = eZINI::instance( 'staticcache.ini' );
            $maxDepth = $ini->hasVariable( 'CacheSettings', 'MaxCacheDepth' ) ? (int)$ini->variable( 'CacheSettings', 'MaxCacheDepth' ) : 12;
        }
        $export = function ( $url, $path, $relative, $body, array $info ) use ( $self, $rawDir, $sessionNames, &$pages )
        {
            return $self->exportPage( $url, $path, $relative, $body, $info, $rawDir, $sessionNames, $pages );
        };
        $emit = $this->emit;
        $runner = new expStaticCacheRunner( function ( $type, $message, array $data = array() ) use ( $emit )
        {
            if ( $emit !== null && $type !== 'done' )
                call_user_func( $emit, $type === 'phase' ? 'phase-item' : $type, $message, $data );
        }, array( 'siteaccess' => $target->siteAccess(),
                  'max_pages' => $this->options['max_pages'] ? (int)$this->options['max_pages'] : (int)$target->setting( 'MaxPages' ),
                  'max_depth' => (int)$maxDepth, 'paths' => $paths, 'export' => $export ) + $crawlOptions );
        $runner->run();
        $crawler = $runner->lastCrawler();
        if ( $crawler !== null )
        {
            $problems = $crawler->problems();
            $this->report['counts']['site_broken_links'] = count( $problems );
            if ( $problems )
                $this->warning( sprintf( 'The site itself has %d broken link(s) (Setup > Preload lists them with the pages that hold them).', count( $problems ) ) );
        }
        if ( $this->report['refused'] )
            $this->error( sprintf( '%d page(s) refused: they carry what a public copy must not (see "refused"); nothing is published.', count( $this->report['refused'] ) ) );
        if ( !$pages )
        {
            $this->error( 'No page was exported: the site did not answer, or answered with nothing a static copy can hold.' );
            return;
        }

        // the site's 404 page
        $notFound = null;
        if ( $target->hasExtra( '404' ) )
            $notFound = $this->notFoundPage( $originUrl . $sitePath . expStaticExtras::missingPath( $this->runId ), $sessionNames );

        // 2. the links and the files
        $old = expStaticManifest::load( $targetDir );
        $collector = null;
        $held = function ( $page, $query ) use ( &$pages, $partial, $old )
        {
            if ( $query !== '' )
                return false;
            if ( isset( $pages[$page] ) )
                return true;
            $file = expStaticLinkRewriter::pageFile( $page );
            return $partial && $old !== null && $file !== null && $old->has( $file ) && $old->get( $file )['kind'] === 'page';
        };
        $files = function ( $output ) use ( &$collector, $partial, $old )
        {
            if ( $collector !== null && $collector->has( $output ) )
                return true;
            return $partial && $old !== null && $old->has( $output );
        };
        $rewriterOptions = array( 'origins' => $origins, 'site_path' => $sitePath, 'mode' => $target->linkMode(), 'base_path' => $target->basePath(),
                                  'public_url' => $target->publicUrl(), 'live_url' => $liveUrl, 'pages' => $held, 'files' => $files );
        $rewriter = new expStaticLinkRewriter( $rewriterOptions );
        $collector = new expStaticAssetCollector( array( 'root' => $this->options['root'], 'output' => $staging, 'origin' => $originUrl,
                                                         'fetch' => array( $this, 'request' ), 'rewriter' => $rewriter, 'emit' => $this->emit,
                                                         'max_file_bytes' => $this->limit( 'max_file_bytes', 'MaxFileBytes', 100000000 ) ) );
        $wanted = array();
        foreach ( $pages as $page )
        {
            $rewriter->reset();
            $rewriter->rewriteHtml( (string)file_get_contents( $page['raw'] ), $page['path'], $page['file'] );
            $wanted += $rewriter->foundFiles();
        }
        if ( $notFound !== null )
        {
            $rewriter->reset();
            $rewriter->rewriteHtml( $notFound['html'], $notFound['path'], '404.html' );
            $wanted += $rewriter->foundFiles();
        }
        $this->say( 'phase-item', sprintf( 'Files:  %d named by the pages', count( $wanted ) ) );
        $collector->collect( $wanted, 'file' );

        if ( $target->setting( 'Assets' ) === 'referenced-and-storage' )
        {
            if ( $this->options['db'] === null )
                $this->warning( 'Assets=referenced-and-storage: without a database the images of the storage are not added; only the files the pages name are.' );
            else
            {
                $varDir = $this->options['var_dir'] !== null ? (string)$this->options['var_dir'] : eZSys::varDirectory();
                $canRead = is_callable( $this->options['can_read'] ) ? $this->options['can_read'] : array( __CLASS__, 'anonymousCanRead' );
                $storage = array();
                foreach ( expStaticAssetCollector::storageImages( $this->options['db'], $canRead, $varDir ) as $file => $objectId )
                    $storage[$file] = '/' . $file;
                $this->say( 'phase-item', sprintf( 'Storage: %d image file(s) of content the anonymous user may see', count( $storage ) ) );
                $collector->collect( $storage, 'storage' );
            }
        }
        $collector->writeStyleSheets();

        // 3. the pages, the extras, the manifest
        $manifest = new expStaticManifest( array(
            'target' => $target->id(), 'siteaccess' => $target->siteAccess(), 'run_id' => $this->runId, 'built_at' => gmdate( 'c' ),
            'links' => $target->linkMode(), 'base_path' => $target->basePath(), 'public_url' => $target->publicUrl(), 'scope' => $partial ? 'partial' : 'full',
        ) );
        $rewriter->reset();
        $sitemap = array();
        foreach ( $pages as $relative => $page )
        {
            $html = $rewriter->rewriteHtml( (string)file_get_contents( $page['raw'] ), $page['path'], $page['file'] );
            if ( !$this->write( $staging . '/' . $page['file'], $html ) )
            {
                $this->error( 'Cannot write ' . $page['file'] );
                continue;
            }
            $manifest->add( $page['file'], 'page', hash( 'sha256', $html ), strlen( $html ),
                            array( 'url' => $page['path'], 'node' => $page['node'], 'modified' => $page['modified'] ) );
            $sitemap[expStaticLinkRewriter::pageUrl( $page['file'] )] = $page['modified'];
            $this->report['pages'][] = array( 'path' => $relative, 'file' => $page['file'], 'bytes' => strlen( $html ), 'node' => $page['node'],
                                              'stripped' => array_sum( $page['removed'] ) );
        }
        $counts = $rewriter->counts();
        foreach ( $collector->results() as $output => $result )
        {
            if ( $result['ok'] )
                $manifest->add( $output, $result['kind'], $result['sha256'], $result['bytes'], array( 'url' => $result['source'] ) );
            else if ( $result['kind'] === 'storage' )
                $this->report['counts']['storage_missing']++; // a file the image table lists that no longer exists; no page names it
            else
                $this->report['files_failed'][] = array( 'file' => $output, 'url' => $result['source'], 'error' => $result['error'] );
        }
        if ( $this->report['files_failed'] )
            $this->warning( sprintf( '%d file(s) named by the pages could not be collected; their links go to the live site (see "files_failed").', count( $this->report['files_failed'] ) ) );

        $this->writeExtras( $staging, $manifest, $notFound, $rewriterOptions, $sitemap );
        if ( $partial && $old !== null )
            $manifest->keepFrom( $old );

        $this->report['counts']['pages'] = $manifest->count( 'page' );
        $this->report['counts']['files'] = $manifest->count( 'file' );
        $this->report['counts']['storage'] = $manifest->count( 'storage' );
        $this->report['counts']['extras'] = $manifest->count( 'extra' );
        $this->report['counts']['files_failed'] = count( $this->report['files_failed'] );
        $this->report['counts']['links_rewritten'] = $counts['rewritten'];
        $this->report['counts']['links_live'] = $counts['live'];
        $this->report['counts']['links_external'] = $counts['external'];
        $this->report['counts']['bytes'] = $manifest->bytes();

        // 4. diff, limits, check, apply
        $diff = expStaticManifest::diff( $old, $manifest );
        if ( $partial )
            $diff['removed'] = array();
        $this->report['diff'] = array( 'added' => count( $diff['added'] ), 'changed' => count( $diff['changed'] ), 'removed' => count( $diff['removed'] ),
                                       'unchanged' => $diff['unchanged'], 'bytes_added' => $diff['bytes_added'], 'bytes_removed' => $diff['bytes_removed'],
                                       'files' => array( 'added' => array_slice( $diff['added'], 0, 500 ), 'changed' => array_slice( $diff['changed'], 0, 500 ),
                                                         'removed' => array_slice( $diff['removed'], 0, 500 ) ) );
        $this->checkLimits( $manifest );
        $manifest->save( $staging );

        if ( $this->report['errors'] )
        {
            $this->say( 'info', 'The target folder was not changed.' );
            return;
        }
        if ( $this->options['dry_run'] )
        {
            if ( !$partial )
                $this->report['link_check'] = expStaticLinkChecker::check( $staging, $target->basePath() );
            $this->say( 'info', 'Dry run: the target folder was not changed.' );
        }
        else
        {
            $this->apply( $staging, $targetDir, $manifest, $old, $diff );
            $this->report['link_check'] = expStaticLinkChecker::check( $targetDir, $target->basePath() );
        }
        if ( $this->report['link_check'] !== null && $this->report['link_check']['broken_count'] > 0 )
            $this->warning( sprintf( 'The link check of the build found %d broken link(s) under %s (see "link_check").',
                                     $this->report['link_check']['broken_count'], $target->basePath() ) );
    }

    /**
     * The export callback of the crawl: one page checked, stripped and kept for rewriting. Public for the closure.
     *
     * @return bool whether the page was kept
     */
    public function exportPage( $url, $path, $relative, $body, array $info, $rawDir, array $sessionNames, array &$pages )
    {
        if ( parse_url( $url, PHP_URL_QUERY ) !== null )
        {
            $this->report['counts']['pages_skipped']++;
            $this->report['skipped'][] = array( 'path' => $relative, 'reason' => 'an address with a query string' );
            return false;
        }
        if ( $this->target->excludesPath( $relative ) )
        {
            $this->report['counts']['pages_excluded']++;
            return false;
        }
        $file = expStaticLinkRewriter::pageFile( $relative );
        if ( $file === null )
        {
            $this->report['counts']['pages_skipped']++;
            $this->report['skipped'][] = array( 'path' => $relative, 'reason' => 'a path that cannot be a file name on every system' );
            return false;
        }
        $node = $this->nodeData( $relative );
        if ( $node !== null && $node['section'] !== null && in_array( (string)$node['section'], (array)$this->target->setting( 'ExcludeSections' ), true ) )
        {
            $this->report['counts']['pages_excluded']++;
            return false;
        }
        $stripped = expStaticPostProcessor::strip( $body, $sessionNames );
        $reasons = expStaticPostProcessor::refusals( $stripped['html'], isset( $info['cookies'] ) ? (array)$info['cookies'] : array(), $sessionNames );
        if ( $reasons )
        {
            $this->report['counts']['pages_refused']++;
            $this->report['refused'][] = array( 'path' => $relative, 'url' => $url, 'reasons' => $reasons );
            $this->say( 'error', sprintf( '%-52s refused: %s', $relative, implode( ', ', $reasons ) ) );
            return false;
        }
        foreach ( $stripped['removed'] as $what => $count )
            if ( $count )
                $this->report['counts']['stripped'][$what] = ( isset( $this->report['counts']['stripped'][$what] ) ? $this->report['counts']['stripped'][$what] : 0 ) + $count;
        $raw = $rawDir . '/' . md5( $relative ) . '.html';
        if ( file_put_contents( $raw, $stripped['html'] ) === false )
            return false;
        $pages[$relative] = array( 'path' => $path, 'file' => $file, 'raw' => $raw, 'removed' => $stripped['removed'],
                                   'node' => $node !== null ? $node['node'] : null, 'modified' => $node !== null ? $node['modified'] : null );
        $this->say( 'ok', sprintf( '%-52s %s', $relative, self::bytes( strlen( $stripped['html'] ) ) ) );
        return true;
    }

    /** The site's own 404 page, stripped; a plain one when the site has none a static copy can hold. */
    private function notFoundPage( $url, array $sessionNames )
    {
        $response = $this->request( $url );
        $path = (string)parse_url( $url, PHP_URL_PATH );
        if ( (int)$response['status'] === 404 && stripos( (string)$response['type'], 'text/html' ) === 0 && $response['body'] !== '' )
        {
            $stripped = expStaticPostProcessor::strip( (string)$response['body'], $sessionNames );
            if ( !expStaticPostProcessor::refusals( $stripped['html'], (array)$response['cookies'], $sessionNames ) )
                return array( 'html' => $stripped['html'], 'path' => $path, 'own' => true );
            $this->warning( 'The site\'s 404 page carries what a public copy must not; 404.html is a plain page.' );
        }
        else
            $this->warning( sprintf( 'The site answered %d for a missing page, not its 404 page; 404.html is a plain page.', (int)$response['status'] ) );
        return null;
    }

    /** .nojekyll, CNAME, 404.html, robots.txt, sitemap.xml as the target asks. */
    private function writeExtras( $staging, expStaticManifest $manifest, $notFound, array $rewriterOptions, array $sitemap )
    {
        $target = $this->target;
        $extras = array();
        if ( $target->hasExtra( 'nojekyll' ) )
            $extras['.nojekyll'] = expStaticExtras::nojekyll();
        if ( $target->setting( 'CustomDomain' ) !== '' )
        {
            $cname = expStaticExtras::cname( (string)$target->setting( 'CustomDomain' ) );
            if ( $cname !== null )
                $extras['CNAME'] = $cname;
        }
        if ( $target->hasExtra( '404' ) )
        {
            // served at any missing address, at any depth: its links start at the base path, never relative
            $rewriter = new expStaticLinkRewriter( array( 'mode' => expStaticLinkRewriter::MODE_BASE_PATH ) + $rewriterOptions );
            $extras['404.html'] = $notFound !== null
                                ? $rewriter->rewriteHtml( $notFound['html'], $notFound['path'], '404.html' )
                                : expStaticExtras::fallbackNotFound( $target->basePath() );
        }
        $publicUrl = $target->publicUrl();
        $writeSitemap = $target->hasExtra( 'sitemap' ) && $publicUrl !== '';
        if ( $target->hasExtra( 'robots' ) )
            $extras['robots.txt'] = expStaticExtras::robots( $publicUrl, $writeSitemap );
        if ( $writeSitemap )
            $extras['sitemap.xml'] = (string)expStaticExtras::sitemap( $publicUrl, $sitemap );
        foreach ( $extras as $file => $content )
        {
            if ( $manifest->has( $file ) )
                $this->warning( "The site has a page or file $file of its own; the extra replaces it." );
            if ( $this->write( $staging . '/' . $file, $content ) )
                $manifest->add( $file, 'extra', hash( 'sha256', $content ), strlen( $content ) );
        }
    }

    /** The size limits of the host (GitHub Pages: 1 GB a site, 100 MB a file); warnings above 90 %. */
    private function checkLimits( expStaticManifest $manifest )
    {
        $maxSite = $this->limit( 'max_site_bytes', 'MaxSiteBytes', 1000000000 );
        $maxFile = $this->limit( 'max_file_bytes', 'MaxFileBytes', 100000000 );
        $bytes = $manifest->bytes();
        $large = array();
        foreach ( $manifest->entries() as $path => $entry )
            if ( $maxFile > 0 && $entry['size'] > $maxFile )
                $large[] = $path;
        $this->report['limits'] = array( 'bytes' => $bytes, 'max_site_bytes' => $maxSite, 'max_file_bytes' => $maxFile, 'too_large' => $large );
        if ( $maxSite > 0 && $bytes > $maxSite )
            $this->warning( sprintf( 'The build is %s, more than the %s the host allows a site.', self::bytes( $bytes ), self::bytes( $maxSite ) ) );
        else if ( $maxSite > 0 && $bytes > $maxSite * 0.9 )
            $this->warning( sprintf( 'The build is %s, more than 90 %% of the %s the host allows a site.', self::bytes( $bytes ), self::bytes( $maxSite ) ) );
        if ( $large )
            $this->warning( sprintf( '%d file(s) are larger than the %s the host allows a file: %s', count( $large ), self::bytes( $maxFile ), implode( ', ', array_slice( $large, 0, 5 ) ) ) );
    }

    /**
     * The build into the target folder: new and changed files copied, files the last manifest listed and this one
     * does not removed (and the folders they leave empty), the manifests written. Nothing else is touched.
     */
    private function apply( $staging, $targetDir, expStaticManifest $manifest, $old, array $diff )
    {
        if ( !$this->makeDir( $targetDir ) )
        {
            $this->error( "The target folder $targetDir cannot be created." );
            return;
        }
        $copied = 0;
        foreach ( array_merge( $diff['added'], $diff['changed'] ) as $path )
        {
            $from = $staging . '/' . $path;
            $to = $targetDir . '/' . $path;
            if ( !is_file( $from ) )
                continue;
            if ( !$this->makeDir( dirname( $to ) ) || !copy( $from, $to ) )
            {
                $this->error( "Cannot write $to" );
                return;
            }
            $copied++;
        }
        // a file the manifest lists that is missing from the folder (removed by hand) comes back
        foreach ( $manifest->entries() as $path => $entry )
            if ( !is_file( $targetDir . '/' . $path ) && is_file( $staging . '/' . $path ) )
            {
                $this->makeDir( dirname( $targetDir . '/' . $path ) );
                if ( copy( $staging . '/' . $path, $targetDir . '/' . $path ) )
                    $copied++;
            }
        $removed = 0;
        foreach ( $diff['removed'] as $path )
        {
            $file = $targetDir . '/' . $path;
            if ( is_file( $file ) && unlink( $file ) )
            {
                $removed++;
                $dir = dirname( $file );
                while ( strlen( $dir ) > strlen( $targetDir ) && is_dir( $dir ) && count( (array)scandir( $dir ) ) === 2 && rmdir( $dir ) )
                    $dir = dirname( $dir );
            }
        }
        if ( $old !== null )
            $old->save( $targetDir, expStaticManifest::PREVIOUS_FILE );
        $manifest->save( $targetDir );
        $this->report['applied'] = true;
        $this->say( 'info', sprintf( 'Written to %s: %d file(s) copied, %d removed, %d unchanged.', $targetDir, $copied, $removed, $diff['unchanged'] ) );
    }

    /**
     * The node, its modification time and section of a page path; null when there is no database or no node.
     *
     * @param string $relative
     * @return array|null node, modified, section
     */
    private function nodeData( $relative )
    {
        if ( is_callable( $this->options['node_lookup'] ) )
        {
            $data = call_user_func( $this->options['node_lookup'], $relative );
            return is_array( $data ) ? $data + array( 'node' => null, 'modified' => null, 'section' => null ) : null;
        }
        if ( $this->options['db'] === null || !class_exists( 'eZURLAliasML' ) )
            return null;
        try
        {
            $nodeId = $this->nodeIdOf( $relative );
            if ( !$nodeId )
                return null;
            $node = eZContentObjectTreeNode::fetch( $nodeId );
            if ( !$node instanceof eZContentObjectTreeNode )
                return null;
            $object = $node->object();
            return array( 'node' => (int)$nodeId,
                          'modified' => $object ? (int)$object->attribute( 'modified' ) : null,
                          'section' => $object ? (int)$object->attribute( 'section_id' ) : null );
        }
        catch ( Exception $e )
        {
            return null;
        }
    }

    /** The node id of a page path: content/view/<mode>/<id>, the front page, or the URL alias. */
    private function nodeIdOf( $relative )
    {
        $path = trim( preg_replace( '#/\([^)/]*\)/[^/]*#', '', (string)$relative ), '/' );
        if ( preg_match( '#^content/view/[a-z]+/([0-9]+)#', $path, $m ) )
            return (int)$m[1];
        $siteIni = eZSiteAccess::getIni( $this->target->siteAccess(), 'site.ini' );
        if ( $path === '' )
        {
            $index = $siteIni->hasVariable( 'SiteSettings', 'IndexPage' ) ? (string)$siteIni->variable( 'SiteSettings', 'IndexPage' ) : '';
            if ( preg_match( '#content/view/[a-z]+/([0-9]+)#', $index, $m ) )
                return (int)$m[1];
            return $siteIni->hasVariable( 'NodeSettings', 'RootNode' ) ? (int)$siteIni->variable( 'NodeSettings', 'RootNode' ) : 2;
        }
        $prefix = $siteIni->hasVariable( 'SiteAccessSettings', 'PathPrefix' ) ? trim( (string)$siteIni->variable( 'SiteAccessSettings', 'PathPrefix' ), '/' ) : '';
        $nodeId = $prefix !== '' ? eZURLAliasML::fetchNodeIDByPath( $prefix . '/' . $path ) : false;
        if ( !$nodeId )
            $nodeId = eZURLAliasML::fetchNodeIDByPath( $path );
        return $nodeId ? (int)$nodeId : 0;
    }

    /** The page path of a node, for --nodes; null when there is none. */
    private function nodePath( $nodeId )
    {
        if ( $this->options['db'] === null || !class_exists( 'eZContentObjectTreeNode' ) )
            return null;
        $node = eZContentObjectTreeNode::fetch( $nodeId );
        if ( !$node instanceof eZContentObjectTreeNode || !$node->canRead() )
            return null;
        $alias = (string)$node->urlAlias();
        $siteIni = eZSiteAccess::getIni( $this->target->siteAccess(), 'site.ini' );
        $prefix = $siteIni->hasVariable( 'SiteAccessSettings', 'PathPrefix' ) ? trim( (string)$siteIni->variable( 'SiteAccessSettings', 'PathPrefix' ), '/' ) : '';
        if ( $prefix !== '' && stripos( $alias, $prefix . '/' ) === 0 )
            $alias = substr( $alias, strlen( $prefix ) + 1 );
        else if ( $prefix !== '' && strcasecmp( $alias, $prefix ) === 0 )
            $alias = '';
        return '/' . trim( $alias, '/' );
    }

    /**
     * Whether the anonymous user may read an object (the build runs as the anonymous user).
     *
     * @param int $objectId
     * @return bool
     */
    public static function anonymousCanRead( $objectId )
    {
        $object = eZContentObject::fetch( (int)$objectId );
        return $object instanceof eZContentObject && (bool)$object->canRead();
    }

    /** @return array|null the user to go back to, when the anonymous user was not the current one */
    private function becomeAnonymous()
    {
        if ( $this->options['db'] === null || !class_exists( 'eZUser' ) )
            return null;
        $current = (int)eZUser::currentUserID();
        $anonymous = (int)eZUser::anonymousId();
        if ( $current === $anonymous )
            return null;
        $user = eZUser::fetch( $anonymous );
        if ( !$user instanceof eZUser )
            return null;
        eZUser::setCurrentlyLoggedInUser( $user, $anonymous, eZUser::NO_SESSION_REGENERATE );
        return array( 'id' => $current );
    }

    private function restoreUser( $user )
    {
        if ( $user === null )
            return;
        $previous = eZUser::fetch( $user['id'] );
        if ( $previous instanceof eZUser )
            eZUser::setCurrentlyLoggedInUser( $previous, $user['id'], eZUser::NO_SESSION_REGENERATE );
    }

    private function write( $file, $content )
    {
        return $this->makeDir( dirname( $file ) ) && file_put_contents( $file, $content ) !== false;
    }

    private function makeDir( $dir )
    {
        return is_dir( $dir ) || mkdir( $dir, 0775, true ) || is_dir( $dir );
    }

    /**
     * Removes a folder with everything in it (the staging folder of a build).
     *
     * @param string $dir
     */
    public static function removeDirectory( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        $items = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
        foreach ( $items as $item )
        {
            if ( $item->isDir() && !$item->isLink() )
                rmdir( $item->getPathname() );
            else
                unlink( $item->getPathname() );
        }
        rmdir( $dir );
    }

    /** @return string 12.3K, 4.5M */
    public static function bytes( $bytes )
    {
        if ( $bytes < 1024 )
            return $bytes . 'B';
        if ( $bytes < 1048576 )
            return round( $bytes / 1024, 1 ) . 'K';
        if ( $bytes < 1073741824 )
            return round( $bytes / 1048576, 1 ) . 'M';
        return round( $bytes / 1073741824, 2 ) . 'G';
    }
}
