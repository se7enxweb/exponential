<?php
/**
 * File containing the expViewCountUpdater class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Counts the page views of web server access logs into ezview_counter: the work of the cronjob updateviewcount.php
 * and of ./console exp:viewcount:update.
 *
 *   $config = expViewCountConfiguration::fromINI();
 *   $report = ( new expViewCountUpdater( $config, array( 'dry_run' => true ) ) )->run();
 *
 * For each log (a job of the configuration): take its lock, read the lines each of its siteaccesses has not counted
 * yet (expViewCountLogReader), keep the requests that are page views (expViewCountRequestFilter), give each line to
 * its siteaccess (the kernel's siteaccess matching when several share the log), and count the paths. Then the paths
 * are turned into nodes (expViewCountPathResolver), the nodes that no longer exist are left out, and all views of
 * the run are added to ezview_counter in one transaction (eZViewCounter::addCounts()). Only after that commit is the
 * resume state of each log written, so a run that fails counts nothing and the next run reads the same lines again.
 *
 * The report (format exp-viewcount/1) says per log what was read, why lines were not counted, and per siteaccess
 * how many views reached how many nodes. It holds no client addresses and no user agents.
 */
class expViewCountUpdater
{
    const FORMAT = 'exp-viewcount/1';

    /** Exit codes of the command. */
    const EXIT_OK = 0;
    const EXIT_ERROR = 1;
    const EXIT_LOCKED = 3;

    /** The reasons a line is not counted, in the order of the report. */
    const REASONS = array( 'malformed', 'too-long', 'method', 'status', 'bot', 'asset', 'excluded', 'other-host',
                           'other-siteaccess', 'before-resume', 'not-content', 'no-node' );

    /** @var expViewCountConfiguration */
    private $config;

    /** @var array */
    private $options;

    /** @var callable host, port, string[] elements => array( name, consumed element count ) */
    private $matcher;

    /** @var expViewCountPathResolver */
    private $resolver;

    /** @var callable int[] node id => count => bool */
    private $writer;

    /** @var callable int[] node ids => int[] those that exist */
    private $nodeExists;

    /** @var callable int node id => string name */
    private $nodeName;

    /** @var callable|null string message => void, progress for the terminal */
    private $progress;

    /** @var array the match cache */
    private $matchCache = array();

    /**
     * @param expViewCountConfiguration $config
     * @param array $options dry_run (bool), from_start (bool, with dry_run only), reset_state (bool), skip_to_end
     *                       (bool), top (int, the most viewed nodes to list), legacy_dir (string, where the state
     *                       of the old cronjob is looked for; default eZSys::logDirectory()), progress (callable)
     * @param array $services matcher, resolver, writer, node_exists, node_name: replacements for tests
     */
    public function __construct( expViewCountConfiguration $config, array $options = array(), array $services = array() )
    {
        $this->config = $config;
        $this->options = $options + array( 'dry_run' => false, 'from_start' => false, 'reset_state' => false,
                                           'skip_to_end' => false, 'top' => 0, 'legacy_dir' => null, 'progress' => null );
        $settings = $config->settings();
        $this->matcher = isset( $services['matcher'] ) ? $services['matcher'] : array( $this, 'matchSiteAccess' );
        $this->resolver = isset( $services['resolver'] ) ? $services['resolver'] : new expViewCountPathResolver( null, $settings['view_modes'] );
        $this->writer = isset( $services['writer'] ) ? $services['writer'] : function ( array $counts )
        {
            return eZViewCounter::addCounts( $counts );
        };
        $this->nodeExists = isset( $services['node_exists'] ) ? $services['node_exists'] : array( __CLASS__, 'existingNodes' );
        $this->nodeName = isset( $services['node_name'] ) ? $services['node_name'] : function ( $nodeID )
        {
            $node = eZContentObjectTreeNode::fetch( $nodeID );
            return $node instanceof eZContentObjectTreeNode ? (string)$node->getName() : '';
        };
        $this->progress = $this->options['progress'];
    }

    /**
     * Does the run.
     *
     * @return array the report: format, mode, dry_run, action, started, finished, seconds, logs, sites, skipped_siteaccesses,
     *               views, nodes, written, top, errors, warnings, exit_code
     */
    public function run()
    {
        $started = microtime( true );
        $settings = $this->config->settings();
        $source = $this->config->source();
        $report = array( 'format' => self::FORMAT, 'mode' => $source['mode'], 'dry_run' => (bool)$this->options['dry_run'],
                         'action' => $this->action(), 'started' => date( 'c', (int)$started ), 'finished' => '', 'seconds' => 0,
                         'state_dir' => $settings['state_dir'], 'logs' => array(), 'sites' => array(),
                         'skipped_siteaccesses' => $this->config->skipped(), 'views' => 0, 'nodes' => 0, 'written' => false,
                         'top' => array(), 'unresolved' => array(), 'errors' => array(), 'warnings' => array(),
                         'exit_code' => self::EXIT_OK );
        if ( $this->options['from_start'] && !$this->options['dry_run'] )
        {
            $report['errors'][] = '--from-start reads lines that may have been counted already: it is allowed with --dry-run only';
            return $this->finish( $report, $started, self::EXIT_ERROR );
        }
        if ( !$this->config->jobs() )
        {
            $report['warnings'][] = 'no siteaccess has an access log to count: set logfile.ini [AccessLogFileSettings] StorageDir and LogFileName';
            return $this->finish( $report, $started, self::EXIT_OK );
        }

        try
        {
            $parser = new expViewCountLogParser( $settings['line_regexp'] );
        }
        catch ( InvalidArgumentException $e )
        {
            $report['errors'][] = $e->getMessage();
            return $this->finish( $report, $started, self::EXIT_ERROR );
        }
        $filter = new expViewCountRequestFilter( $settings['filter'] );

        $exit = self::EXIT_OK;
        $runs = array();
        foreach ( $this->config->jobs() as $job )
        {
            $state = new expViewCountState( $settings['state_dir'], $job['log'] );
            $log = array( 'log' => $job['log'], 'state_file' => $state->file(), 'match' => $job['match'], 'sites' => array_keys( $job['sites'] ),
                          'events' => array(), 'files' => array(), 'lines' => 0, 'bytes' => 0, 'views' => 0,
                          'skipped' => array_fill_keys( self::REASONS, 0 ), 'state' => 'unchanged', 'warnings' => array() );
            $locked = $this->options['dry_run'] && $this->action() === 'count' ? true : $state->lock( $settings['lock_timeout'] );
            if ( !$locked )
            {
                $log['state'] = 'locked';
                $log['warnings'][] = 'another run holds the lock of this log (' . $state->file() . '.lock); it was not read';
                $exit = max( $exit, self::EXIT_LOCKED );
                $report['logs'][] = $log;
                continue;
            }
            $reader = new expViewCountLogReader( $job['log'], $settings['rotated_suffixes'], $settings['max_line_length'] );
            $current = $reader->stat();
            if ( $this->action() === 'reset' )
            {
                foreach ( $job['sites'] as $name => $site )
                    $state->remove( $name );
                $log['state'] = $this->options['dry_run'] ? 'dry-run' : ( $state->save() ? 'reset' : 'not-saved' );
                $state->unlock();
                $report['logs'][] = $log;
                continue;
            }
            if ( $current === null )
            {
                $log['state'] = 'missing';
                $report['errors'][] = "the access log {$job['log']} does not exist or cannot be read (logfile.ini [AccessLogFileSettings] StorageDir, LogFileName; the user the cronjob runs as)";
                $exit = max( $exit, self::EXIT_ERROR );
                $state->unlock();
                $report['logs'][] = $log;
                continue;
            }
            if ( $this->action() === 'skip' )
            {
                $end = self::completeEnd( $job['log'], $current['size'] );
                foreach ( $job['sites'] as $name => $site )
                    $state->set( $name, $this->entry( $job['log'], $current, $end, $state->entry( $name ), 0, null ) );
                $log['state'] = $this->options['dry_run'] ? 'dry-run' : ( $state->save() ? 'skipped-to-end' : 'not-saved' );
                $log['bytes'] = $end;
                $state->unlock();
                $report['logs'][] = $log;
                continue;
            }
            $runs[] = $this->readJob( $job, $state, $reader, $current, $parser, $filter, $log );
        }

        // the paths of every log to nodes, per siteaccess
        $nodeCounts = array();
        $unresolved = array();
        foreach ( $runs as $index => $run )
        {
            foreach ( $run['paths'] as $siteName => $paths )
            {
                $site = $run['job']['sites'][$siteName];
                foreach ( $paths as $path => $count )
                {
                    $node = $this->resolver->resolve( $site, $path === '' ? array() : explode( '/', $path ) );
                    if ( !$node )
                    {
                        $runs[$index]['log']['skipped']['not-content'] += $count;
                        $key = $siteName . "\n/" . $path;
                        $unresolved[$key] = ( isset( $unresolved[$key] ) ? $unresolved[$key] : 0 ) + $count;
                        continue;
                    }
                    $runs[$index]['nodes'][$siteName][$node] = ( isset( $runs[$index]['nodes'][$siteName][$node] ) ? $runs[$index]['nodes'][$siteName][$node] : 0 ) + $count;
                }
            }
        }
        $this->resolver->restoreLanguages();
        $all = array();
        foreach ( $runs as $run )
        {
            foreach ( $run['nodes'] as $nodes )
                $all += array_fill_keys( array_keys( $nodes ), true );
        }
        $existing = $all ? array_flip( call_user_func( $this->nodeExists, array_keys( $all ) ) ) : array();
        foreach ( $runs as $index => $run )
        {
            foreach ( $run['nodes'] as $siteName => $nodes )
            {
                if ( !isset( $report['sites'][$siteName] ) )
                    $report['sites'][$siteName] = array( 'views' => 0, 'nodes' => 0 );
                foreach ( $nodes as $node => $count )
                {
                    if ( !isset( $existing[$node] ) )
                    {
                        $runs[$index]['log']['skipped']['no-node'] += $count;
                        continue;
                    }
                    $nodeCounts[$node] = ( isset( $nodeCounts[$node] ) ? $nodeCounts[$node] : 0 ) + $count;
                    $report['sites'][$siteName]['views'] += $count;
                    ++$report['sites'][$siteName]['nodes'];
                    $runs[$index]['log']['views'] += $count;
                }
            }
        }
        ksort( $report['sites'] );
        $report['views'] = array_sum( $nodeCounts );
        $report['nodes'] = count( $nodeCounts );

        $written = true;
        if ( !$this->options['dry_run'] && $nodeCounts )
        {
            try
            {
                $written = (bool)call_user_func( $this->writer, $nodeCounts );
            }
            catch ( Exception $e )
            {
                $written = false;
                $report['errors'][] = 'the view counts were not written: ' . $e->getMessage();
            }
            if ( !$written && !$report['errors'] )
                $report['errors'][] = 'the view counts were not written (the database refused the transaction)';
            $report['written'] = $written;
        }

        foreach ( $runs as $run )
        {
            $log = $run['log'];
            if ( $this->options['dry_run'] )
                $log['state'] = 'dry-run';
            elseif ( !$written )
            {
                $log['state'] = 'unchanged';
                $exit = max( $exit, self::EXIT_ERROR );
            }
            else
            {
                foreach ( $run['entries'] as $name => $entry )
                    $run['state']->set( $name, $entry );
                if ( $run['state']->save() )
                    $log['state'] = 'saved';
                else
                {
                    $log['state'] = 'not-saved';
                    $report['errors'][] = 'the resume state ' . $run['state']->file() . ' could not be written: the next run counts these lines again';
                    $exit = max( $exit, self::EXIT_ERROR );
                }
            }
            $run['state']->unlock();
            $report['logs'][] = $log;
        }

        if ( (int)$this->options['top'] > 0 && $nodeCounts )
        {
            arsort( $nodeCounts );
            foreach ( array_slice( $nodeCounts, 0, (int)$this->options['top'], true ) as $node => $count )
                $report['top'][] = array( 'node_id' => (int)$node, 'views' => $count, 'name' => (string)call_user_func( $this->nodeName, $node ) );
        }
        // the paths most asked for that are no node: a missing PathPrefix, a module view to add to SkipPathPrefixes[]
        if ( (int)$this->options['top'] > 0 && $unresolved )
        {
            arsort( $unresolved );
            foreach ( array_slice( $unresolved, 0, (int)$this->options['top'], true ) as $key => $count )
            {
                list( $siteName, $path ) = explode( "\n", $key, 2 );
                $report['unresolved'][] = array( 'siteaccess' => $siteName, 'path' => $path, 'requests' => $count );
            }
        }
        return $this->finish( $report, $started, $report['errors'] ? max( $exit, self::EXIT_ERROR ) : $exit );
    }

    /** @return string count, reset or skip */
    private function action()
    {
        if ( $this->options['reset_state'] )
            return 'reset';
        if ( $this->options['skip_to_end'] )
            return 'skip';
        return 'count';
    }

    /**
     * Reads the new lines of one log.
     *
     * @return array hash job, state, log, paths (site => path => count), nodes, entries (site => new state entry)
     */
    private function readJob( array $job, expViewCountState $state, expViewCountLogReader $reader, array $current,
                              expViewCountLogParser $parser, expViewCountRequestFilter $filter, array $log )
    {
        $plans = array();
        $notBefore = array();
        $migrated = array();
        foreach ( $job['sites'] as $name => $site )
        {
            $entry = $this->options['from_start'] ? null : $state->entry( $name );
            $plan = $reader->plan( $entry, $current );
            if ( $entry === null && !$this->options['from_start'] )
            {
                $legacy = $this->legacyStart( $job['log'], $current );
                if ( $legacy !== null )
                {
                    $plan['segments'][0]['start'] = $legacy['offset'];
                    $plan['event'] = 'migrated';
                    $migrated[$name] = $legacy['file'];
                    if ( $legacy['not_before'] !== null )
                        $notBefore[$name] = $legacy['not_before'];
                }
            }
            $plans[$name] = $plan;
            $log['events'][$name] = $plan['event'];
            if ( $plan['event'] === 'truncated' )
                $log['warnings'][] = "$name: the log is shorter than where the last run stopped and no rotated copy was found (RotatedSuffixes[]): lines written in between are lost";
            if ( $plan['event'] === 'migrated' )
                $log['warnings'][] = "$name: resumed from the state of the earlier cronjob ({$migrated[$name]})" . ( isset( $notBefore[$name] ) ? ', by its time, as its last line is no longer in the log' : '' );
        }
        if ( $this->progress )
            call_user_func( $this->progress, 'Reading ' . $job['log'] );

        $paths = array();
        $skipped = &$log['skipped'];
        $port = null;
        $host = '';
        foreach ( $job['sites'] as $site )
        {
            if ( $host === '' && $site->host !== '' )
                $host = $site->host;
            if ( $port === null && $site->port !== null )
                $port = $site->port;
        }
        $single = count( $job['sites'] ) === 1 ? reset( $job['sites'] ) : null;
        $onLine = function ( $line, array $sites, $file, $offset ) use ( $job, $parser, $filter, &$paths, &$skipped, $notBefore, $host, $port, $single )
        {
            $record = $parser->parse( $line );
            if ( $record === null )
            {
                ++$skipped['malformed'];
                return;
            }
            if ( $notBefore )
            {
                $time = expViewCountLogParser::timestamp( $record['time'] );
                foreach ( $sites as $i => $name )
                {
                    if ( isset( $notBefore[$name] ) && ( $time === null || $time <= $notBefore[$name] ) )
                        unset( $sites[$i] );
                }
                if ( !$sites )
                {
                    ++$skipped['before-resume'];
                    return;
                }
            }
            $reason = $filter->classify( $record );
            if ( $reason !== null )
            {
                ++$skipped[$reason];
                return;
            }
            $elements = expViewCountPathResolver::elements( expViewCountLogParser::path( $record['target'] ) );
            $lineHost = $record['host'] !== '' ? $record['host'] : $host;
            if ( $job['match'] )
            {
                list( $name, $consumed ) = call_user_func( $this->matcher, $lineHost, $record['port'] !== null ? $record['port'] : ( $port !== null ? $port : 80 ), $elements );
                if ( !isset( $job['sites'][$name] ) )
                {
                    ++$skipped['other-siteaccess'];
                    return;
                }
                if ( !in_array( (string)$name, $sites, true ) )
                {
                    // counted for this siteaccess by an earlier run already
                    ++$skipped['before-resume'];
                    return;
                }
                $site = $job['sites'][$name];
                $elements = array_slice( $elements, (int)$consumed );
            }
            else
            {
                $site = $single;
                $elements = expViewCountPathResolver::stripPrefix( $elements, $site->sitePrefixes );
            }
            if ( !$site->acceptsHost( $record['host'] ) )
            {
                ++$skipped['other-host'];
                return;
            }
            if ( $elements && $filter->isExcluded( '/' . implode( '/', $elements ) ) )
            {
                ++$skipped['excluded'];
                return;
            }
            $key = implode( '/', $elements );
            $paths[$site->name][$key] = ( isset( $paths[$site->name][$key] ) ? $paths[$site->name][$key] : 0 ) + 1;
        };
        $read = $reader->read( $plans, $onLine );
        unset( $skipped );
        $log['skipped']['too-long'] += $read['too_long'];
        $log['lines'] = $read['lines'] + $read['too_long'];
        $log['bytes'] = $read['bytes'];
        $log['files'] = $read['files'];

        $entries = array();
        foreach ( $job['sites'] as $name => $site )
            $entries[$name] = $this->entry( $job['log'], $current, $read['end'], $state->entry( $name ), $log['lines'], isset( $migrated[$name] ) ? $migrated[$name] : null );
        return array( 'job' => $job, 'state' => $state, 'log' => $log, 'paths' => $paths, 'nodes' => array(), 'entries' => $entries );
    }

    /**
     * A state entry after a read.
     *
     * @return array
     */
    private function entry( $file, array $current, $end, $previous, $lines, $migratedFrom )
    {
        $head = $current['head'] !== '' || $end <= 0 ? $current['head'] : expViewCountLogReader::head( $file );
        $entry = array( 'inode' => $current['inode'], 'device' => $current['device'], 'offset' => (int)$end, 'head' => $head,
                        'lines' => ( is_array( $previous ) && isset( $previous['lines'] ) ? (int)$previous['lines'] : 0 ) + (int)$lines,
                        'updated' => date( 'c' ) );
        if ( $migratedFrom !== null )
            $entry['migrated_from'] = $migratedFrom;
        elseif ( is_array( $previous ) && isset( $previous['migrated_from'] ) )
            $entry['migrated_from'] = $previous['migrated_from'];
        return $entry;
    }

    /**
     * Where the earlier cronjob stopped in this log: its state file in the log directory (updateview-<log name>.log,
     * as a per-log version of it wrote it, else updateview.log) holds the last line it read. The run starts after that
     * line when the log still has it; else at the start, counting only lines newer than that line's time.
     *
     * @param string $logFile
     * @param array $current
     * @return array|null hash file, offset, not_before; null when there is no such state
     */
    private function legacyStart( $logFile, array $current )
    {
        $dir = $this->options['legacy_dir'] !== null ? (string)$this->options['legacy_dir'] : eZSys::logDirectory();
        $base = basename( $logFile );
        $candidates = array( 'updateview-' . str_replace( '.log', '', $base ) . '.log', 'updateview.log' );
        foreach ( $candidates as $candidate )
        {
            $file = rtrim( $dir, '/' ) . '/' . $candidate;
            if ( !is_file( $file ) || !is_readable( $file ) )
                continue;
            $marker = '';
            foreach ( (array)@file( $file ) as $line )
            {
                if ( strpos( $line, '[' ) !== false && $line[0] !== '#' )
                    $marker = rtrim( $line, "\r\n" );
            }
            if ( $marker === '' )
                continue;
            $offset = self::findLine( $logFile, $marker );
            if ( $offset !== null )
                return array( 'file' => $candidate, 'offset' => $offset, 'not_before' => null );
            $record = ( new expViewCountLogParser() )->parse( $marker );
            $time = $record !== null ? expViewCountLogParser::timestamp( $record['time'] ) : null;
            return array( 'file' => $candidate, 'offset' => 0, 'not_before' => $time !== null ? $time : PHP_INT_MAX );
        }
        return null;
    }

    /**
     * The offset after the last line of a file that is the marker (the earlier cronjob kept at most 1023 bytes of a
     * line), null when no line is.
     *
     * @param string $file
     * @param string $marker
     * @return int|null
     */
    public static function findLine( $file, $marker )
    {
        $handle = @fopen( $file, 'rb' );
        if ( !$handle )
            return null;
        $marker = rtrim( $marker );
        $found = null;
        $offset = 0;
        while ( ( $line = fgets( $handle ) ) !== false )
        {
            $offset += strlen( $line );
            if ( substr( $line, -1 ) !== "\n" )
                break;
            if ( rtrim( substr( $line, 0, 1023 ) ) === $marker )
                $found = $offset;
        }
        fclose( $handle );
        return $found;
    }

    /**
     * The offset after the last complete line of a file of this size.
     *
     * @param string $file
     * @param int $size
     * @return int
     */
    public static function completeEnd( $file, $size )
    {
        $size = (int)$size;
        if ( $size <= 0 )
            return 0;
        $handle = @fopen( $file, 'rb' );
        if ( !$handle )
            return 0;
        $position = $size;
        while ( $position > 0 )
        {
            $length = (int)min( 65536, $position );
            fseek( $handle, $position - $length );
            $chunk = fread( $handle, $length );
            $newline = strrpos( (string)$chunk, "\n" );
            if ( $newline !== false )
            {
                fclose( $handle );
                return $position - $length + $newline + 1;
            }
            $position -= $length;
        }
        fclose( $handle );
        return 0;
    }

    /**
     * The siteaccess of a line, as the kernel would match the request (eZSiteAccess::matchAddress()), and how many
     * elements of the path were its part (/bold/...). Results are kept per host, port and first three elements.
     *
     * @param string $host
     * @param int $port
     * @param string[] $elements
     * @return array name, consumed element count
     */
    public function matchSiteAccess( $host, $port, array $elements )
    {
        $key = $host . '|' . $port . '|' . implode( '/', array_slice( $elements, 0, 3 ) ) . ( count( $elements ) > 3 ? '/' : '' );
        if ( isset( $this->matchCache[$key] ) )
            return $this->matchCache[$key];
        $access = eZSiteAccess::matchAddress( implode( '/', $elements ), $host, $port );
        $consumed = 0;
        $part = isset( $access['uri_part'] ) ? array_values( array_filter( array_map( 'strval', (array)$access['uri_part'] ), 'strlen' ) ) : array();
        if ( $part && array_map( 'strtolower', array_slice( $elements, 0, count( $part ) ) ) === array_map( 'strtolower', $part ) )
            $consumed = count( $part );
        if ( count( $this->matchCache ) > 20000 )
            $this->matchCache = array();
        return $this->matchCache[$key] = array( (string)$access['name'], $consumed );
    }

    /**
     * The nodes of a list that exist, in chunks of 500.
     *
     * @param int[] $nodeIDs
     * @return int[]
     */
    public static function existingNodes( array $nodeIDs )
    {
        $existing = array();
        foreach ( array_chunk( array_values( array_map( 'intval', $nodeIDs ) ), 500 ) as $chunk )
        {
            $rows = eZPersistentObject::fetchObjectList( eZContentObjectTreeNode::definition(), array( 'node_id' ),
                                                         array( 'node_id' => array( $chunk ) ), null, null, false );
            foreach ( (array)$rows as $row )
                $existing[] = (int)$row['node_id'];
        }
        return $existing;
    }

    /**
     * @return array the report with its end
     */
    private function finish( array $report, $started, $exit )
    {
        $report['finished'] = date( 'c' );
        $report['seconds'] = round( microtime( true ) - $started, 3 );
        $report['lookups'] = $this->resolver->lookups();
        $report['exit_code'] = $exit;
        return $report;
    }

    /**
     * The report as lines of text, for the terminal and the cronjob's output.
     *
     * @param array $report
     * @return string[]
     */
    public static function lines( array $report )
    {
        $lines = array();
        $lines[] = sprintf( 'View count%s: %d view(s) of %d node(s), mode %s%s', $report['dry_run'] ? ' (dry run, nothing written)' : '',
                            $report['views'], $report['nodes'], $report['mode'],
                            $report['action'] !== 'count' ? ', action ' . $report['action'] : '' );
        foreach ( $report['logs'] as $log )
        {
            $lines[] = '';
            $lines[] = 'Log ' . $log['log'] . ( $log['match'] ? ' (siteaccess matched per line)' : '' );
            $lines[] = '  Siteaccesses: ' . implode( ', ', $log['sites'] );
            $events = array();
            foreach ( $log['events'] as $site => $event )
                $events[$event][] = $site;
            if ( $events )
            {
                $parts = array();
                foreach ( $events as $event => $sites )
                    $parts[] = $event . ' (' . implode( ', ', $sites ) . ')';
                $lines[] = '  Read:         ' . implode( '; ', $parts );
            }
            foreach ( $log['files'] as $file => $bytes )
                $lines[] = sprintf( '  File:         %s, %d byte(s)', $file, $bytes );
            $lines[] = sprintf( '  Lines:        %d read, %d counted as views', $log['lines'], $log['views'] );
            $skipped = array_filter( $log['skipped'] );
            if ( $skipped )
            {
                $parts = array();
                foreach ( $skipped as $reason => $count )
                    $parts[] = "$reason $count";
                $lines[] = '  Not counted:  ' . implode( ', ', $parts );
            }
            $lines[] = '  State:        ' . $log['state'] . ' (' . $log['state_file'] . ')';
            foreach ( $log['warnings'] as $warning )
                $lines[] = '  Warning:      ' . $warning;
        }
        if ( $report['sites'] )
        {
            $lines[] = '';
            $lines[] = '  Siteaccess            Views    Nodes';
            foreach ( $report['sites'] as $site => $counts )
                $lines[] = sprintf( '  %-20s %6d   %6d', $site, $counts['views'], $counts['nodes'] );
        }
        if ( $report['top'] )
        {
            $lines[] = '';
            $lines[] = '  Most viewed:';
            foreach ( $report['top'] as $row )
                $lines[] = sprintf( '  %8d  node %-8d %s', $row['views'], $row['node_id'], $row['name'] );
        }
        if ( !empty( $report['unresolved'] ) )
        {
            $lines[] = '';
            $lines[] = '  Most requested paths that are no node:';
            foreach ( $report['unresolved'] as $row )
                $lines[] = sprintf( '  %8d  %-12s %s', $row['requests'], $row['siteaccess'], $row['path'] );
        }
        foreach ( $report['skipped_siteaccesses'] as $site => $reason )
            $lines[] = "  Not counted: siteaccess $site, $reason";
        foreach ( $report['warnings'] as $warning )
            $lines[] = 'Warning: ' . $warning;
        foreach ( $report['errors'] as $error )
            $lines[] = 'Error: ' . $error;
        return $lines;
    }
}
