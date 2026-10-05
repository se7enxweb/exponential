<?php
/**
 * The code of bin/php/benchmark.php (exp:benchmark).
 * @description Measure this installation: page timings over HTTP, A/B between servers, kernel probes, regression check
 * Guide: doc/features/6.0/benchmark.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Benchmark extends \Exponential\Runnable\Command
{
    /** Above these, a host that is not this machine's loopback is only benchmarked with --force. */
    const CAP_REQUESTS = 1000;
    const CAP_CONCURRENCY = 16;
    const CAP_WARMUP = 100;
    const CAP_TOTAL = 20000;

    /** Exit codes. */
    const EXIT_OK = 0;
    const EXIT_REGRESSION = 1;
    const EXIT_USAGE = 2;

    /** @var bool machine output (--json, --csv) on stdout: progress goes to stderr */
    protected $machine = false;

    public function run()
    {
        $this->script( array(
            'description' => (
                "Exponential benchmark\n\n" .
                "Measures this installation and compares it with a saved run.\n\n" .
                "Modes:\n" .
                "  http      (the default) request pages and time them: the front page, the first\n" .
                "            pages of the site's menu and the admin login page, or --url / --urls-file\n" .
                "  kernel    time the parts of a page in-process: boot, full view render cold and warm,\n" .
                "            INI load, content fetch, node list, database round trip, cache write and\n" .
                "            read, image alias lookup\n" .
                "  compare <baseline.json> <run.json>   compare two saved runs\n" .
                "  show <run.json>                      print a saved run as a table\n\n" .
                "Examples:\n" .
                "  ./console exp:benchmark\n" .
                "  ./console exp:benchmark --compare=https://example.com,https://example.com:8080\n" .
                "  ./console exp:benchmark --cold --requests=50\n" .
                "  ./console exp:benchmark kernel --repeat=30\n" .
                "  ./console exp:benchmark --save=var/benchmark/base.json\n" .
                "  ./console exp:benchmark --baseline=var/benchmark/base.json --threshold=15\n\n" .
                "Polite by default: 100 requests per URL, 4 at a time. Against a host other than\n" .
                "this machine's loopback, more than " . self::CAP_REQUESTS . " requests per URL or " . self::CAP_CONCURRENCY .
                " at a time needs --force,\n" .
                "and a host the site does not serve is only measured when its URLs are named with\n" .
                "--url or --urls-file. Nothing is written but cache entries.\n\n" .
                "Exit code: 0 ok, 1 a metric regressed against --baseline, 2 usage error or refused.\n" .
                "Guide: doc/features/6.0/benchmark.md" ),
            'use-session'    => false,
            'use-modules'    => true,
            'use-extensions' => true,
        ) );

        $options = $this->startup(
            '[base:][compare:][url:*][urls-file:][pages:][no-admin][requests:][concurrency:][warmup:][rounds:]' .
            '[cold][cold-clear][insecure][auth:][header:*][cookie:][resolve:*][http:][encoding:][timeout:][no-keepalive]' .
            '[tool:][duration:][repeat:][boot-repeat:][node:][probe:*][json][csv][save:][baseline:][threshold:]' .
            '[min-delta:][all-checks][force]',
            '[MODE*]',
            array(
                'base'         => 'Server to measure (default: https:// and the SiteURL of the siteaccess)',
                'compare'      => 'Two or more servers, comma separated, measured back to back (A/B)',
                'url'          => 'A URL or path to measure; repeat it for more',
                'urls-file'    => 'A file with one URL or path per line (# starts a comment)',
                'pages'        => 'How many pages of the menu the default list takes (default 3)',
                'no-admin'     => 'Leave the admin login page out of the default list',
                'requests'     => 'Requests per URL and server (default 100)',
                'concurrency'  => 'Requests at the same time (default 4)',
                'warmup'       => 'Unmeasured requests per URL and server first (default 5)',
                'rounds'       => 'Split the requests into rounds, alternating the server order (default 2)',
                'cold'         => 'A unique query string on every request, so no response cache answers',
                'cold-clear'   => 'Clear the content view cache once before the run (this installation only)',
                'insecure'     => 'Do not verify TLS certificates (self-signed servers)',
                'auth'         => 'HTTP basic auth, user:password (or the EXP_BENCHMARK_AUTH variable)',
                'header'       => 'An extra request header, "Name: value"; repeat it for more',
                'cookie'       => 'A Cookie header to send, e.g. to measure a signed-in page',
                'resolve'      => 'host:port:address, as curl --resolve, to reach a host at another address',
                'http'         => 'HTTP version: 1.1 or 2 (default: what curl negotiates)',
                'encoding'     => 'The Accept-Encoding to send (default gzip; "none" for none)',
                'timeout'      => 'Seconds per request (default 30)',
                'no-keepalive' => 'A new connection for every request',
                'tool'         => 'curl (default, built in), ab, wrk or oha when installed',
                'duration'     => 'Seconds per URL for wrk (default 10)',
                'repeat'       => 'kernel: runs of every probe (default 20)',
                'boot-repeat'  => 'kernel: new processes for boot and the cold render (default 5)',
                'node'         => 'kernel: the node to render and fetch (default: the front page)',
                'probe'        => 'kernel: only these probes; repeat it for more',
                'json'         => 'Print the run as JSON',
                'csv'          => 'Print the rows as CSV',
                'save'         => 'Write the run as JSON to this file',
                'baseline'     => 'Compare with this saved run; exit 1 when a metric regressed',
                'threshold'    => 'Percent a metric may get worse before it counts as a regression (default 15)',
                'min-delta'    => 'Milliseconds a latency must grow by, too, to count (default 1)',
                'all-checks'   => 'Show every comparison, not only the regressions',
                'force'        => 'Allow more than the polite limits against a host that is not loopback',
            ) );

        $this->machine = !empty( $options['json'] ) || !empty( $options['csv'] );
        $arguments = isset( $options['arguments'] ) ? array_values( (array)$options['arguments'] ) : array();
        $mode = isset( $arguments[0] ) ? strtolower( (string)$arguments[0] ) : 'http';

        try
        {
            switch ( $mode )
            {
                case 'http':
                    $code = $this->runHttp( $options );
                    break;
                case 'kernel':
                    $code = $this->runKernel( $options );
                    break;
                case 'compare':
                    $code = $this->runCompare( $options, $arguments );
                    break;
                case 'show':
                    $code = $this->runShow( $options, $arguments );
                    break;
                case 'probe':
                    // Internal: the child process of the kernel boot probe.
                    $nodeID = isset( $options['node'] ) ? (int)$options['node'] : 0;
                    fwrite( STDOUT, "\n" . json_encode( \expBenchmarkKernel::childProbe( $nodeID ) ) . "\n" );
                    $code = self::EXIT_OK;
                    break;
                default:
                    $this->error( "Unknown mode \"$mode\": http, kernel, compare or show. See --help." );
                    $code = self::EXIT_USAGE;
            }
        }
        catch ( \Exception $e )
        {
            $this->error( 'ERROR: ' . $e->getMessage() );
            $code = self::EXIT_USAGE;
        }

        $this->shutdown( $code );
        return $code;
    }

    /**
     * http mode.
     */
    protected function runHttp( $options )
    {
        $settings = \expBenchmarkHttp::defaults();
        foreach ( array( 'requests', 'concurrency', 'warmup', 'rounds', 'timeout', 'duration' ) as $name )
        {
            if ( isset( $options[$name] ) and $options[$name] !== false and $options[$name] !== null )
            {
                if ( !preg_match( '/^\d+$/', (string)$options[$name] ) )
                    throw new \InvalidArgumentException( "--$name must be a whole number" );
                $settings[$name] = (int)$options[$name];
            }
        }
        if ( $settings['requests'] < 1 or $settings['concurrency'] < 1 )
            throw new \InvalidArgumentException( '--requests and --concurrency must be at least 1' );

        $settings['cold'] = !empty( $options['cold'] );
        $settings['insecure'] = !empty( $options['insecure'] );
        $settings['keepalive'] = empty( $options['no-keepalive'] );
        if ( !empty( $options['http'] ) )
        {
            if ( !in_array( (string)$options['http'], array( '1.1', '2' ), true ) )
                throw new \InvalidArgumentException( '--http must be 1.1 or 2' );
            $settings['http'] = (string)$options['http'];
        }
        if ( isset( $options['encoding'] ) and $options['encoding'] !== false and $options['encoding'] !== null )
            $settings['encoding'] = strtolower( (string)$options['encoding'] ) === 'none' ? '' : (string)$options['encoding'];
        $settings['headers'] = $this->listOption( $options, 'header' );
        if ( !empty( $options['cookie'] ) )
            $settings['headers'][] = 'Cookie: ' . $options['cookie'];
        $settings['resolve'] = $this->listOption( $options, 'resolve' );
        $settings['auth'] = !empty( $options['auth'] ) ? (string)$options['auth'] : (string)getenv( 'EXP_BENCHMARK_AUTH' );

        $tool = !empty( $options['tool'] ) ? strtolower( (string)$options['tool'] ) : 'curl';
        $tools = \expBenchmarkHttp::availableTools();
        if ( $tool !== 'curl' )
        {
            if ( !in_array( $tool, array( 'ab', 'wrk', 'oha' ), true ) )
                throw new \InvalidArgumentException( "--tool must be curl, ab, wrk or oha" );
            if ( !isset( $tools[$tool] ) )
                throw new \InvalidArgumentException( "--tool=$tool: $tool is not installed (found: " . ( $tools ? implode( ', ', array_keys( $tools ) ) : 'none' ) . ')' );
        }
        $settings['tool'] = $tool;

        // What to request
        $explicit = $this->listOption( $options, 'url' );
        if ( !empty( $options['urls-file'] ) )
            $explicit = array_merge( $explicit, $this->readUrlsFile( (string)$options['urls-file'] ) );
        $hasExplicit = !empty( $explicit );

        // Where to send it
        $targets = array();
        if ( !empty( $options['compare'] ) )
        {
            foreach ( explode( ',', (string)$options['compare'] ) as $base )
            {
                if ( trim( $base ) !== '' )
                    $targets[] = $this->normalizeBase( $base );
            }
            if ( count( $targets ) < 2 )
                throw new \InvalidArgumentException( '--compare needs at least two servers, comma separated' );
        }
        else if ( !empty( $options['base'] ) )
        {
            $targets[] = $this->normalizeBase( (string)$options['base'] );
        }

        $groups = array();   // target => list of paths
        if ( $hasExplicit and empty( $targets ) )
        {
            // Absolute URLs name their own server; paths go to the site's own.
            foreach ( $explicit as $url )
            {
                $origin = \expBenchmarkHttp::origin( $url );
                $target = $origin !== '' ? $origin : $this->siteBase();
                $groups[$target][] = $origin !== '' ? \expBenchmarkHttp::pathOf( $url ) : '/' . ltrim( $url, '/' );
            }
        }
        else
        {
            if ( empty( $targets ) )
                $targets[] = $this->siteBase();
            if ( $hasExplicit )
            {
                $paths = array();
                foreach ( $explicit as $url )
                    $paths[] = \expBenchmarkHttp::origin( $url ) !== '' ? \expBenchmarkHttp::pathOf( $url ) : '/' . ltrim( $url, '/' );
            }
            else
            {
                $pages = isset( $options['pages'] ) && $options['pages'] !== false && $options['pages'] !== null ? max( 0, (int)$options['pages'] ) : 3;
                $paths = $this->defaultPaths( $pages, empty( $options['no-admin'] ) );
            }
            $groups[implode( ',', $targets )] = array_values( array_unique( $paths ) );
        }

        // Safe by default
        $owned = $this->ownedHosts();
        $allTargets = array();
        $total = 0;
        foreach ( $groups as $groupKey => $paths )
        {
            foreach ( explode( ',', $groupKey ) as $target )
            {
                $allTargets[] = $target;
                $total += count( $paths ) * ( $settings['requests'] + $settings['warmup'] );
            }
        }
        $refusal = $this->refusal( $allTargets, $owned, $hasExplicit, $settings, $total, !empty( $options['force'] ) );
        if ( $refusal !== '' )
        {
            $this->error( 'REFUSED: ' . $refusal );
            return self::EXIT_USAGE;
        }

        if ( !empty( $options['cold-clear'] ) )
        {
            foreach ( $allTargets as $target )
            {
                if ( !$this->isOwned( parse_url( $target, PHP_URL_HOST ), $owned ) )
                {
                    $this->error( "REFUSED: --cold-clear clears this installation's cache; $target is not served by it" );
                    return self::EXIT_USAGE;
                }
            }
            $this->progress( '  clearing the content view cache (--cold-clear); the first request of every page renders' );
            \eZCache::clearByID( array( 'content' ) );
            $settings['warmup'] = 0;
        }

        $this->progress( sprintf( 'exp:benchmark http  %d URL(s) x %d server(s), %d requests each, concurrency %d, warm-up %d, %s, tool %s%s',
            array_sum( array_map( 'count', $groups ) ), count( $allTargets ) / max( 1, count( $groups ) ), $settings['requests'],
            $settings['concurrency'], $settings['warmup'], $settings['cold'] ? 'cold (unique query string)' : 'warm',
            $tool, $tools ? '  (installed: ' . implode( ', ', array_keys( $tools ) ) . ')' : '' ) );

        $rows = array();
        $progress = array( $this, 'progress' );
        foreach ( $groups as $groupKey => $paths )
            $rows = array_merge( $rows, \expBenchmarkHttp::run( explode( ',', $groupKey ), $paths, $settings, $progress ) );

        $recorded = $settings;
        $recorded['auth'] = $settings['auth'] !== '';
        $recorded['headers'] = array_map( function ( $line )
        {
            // Values of Cookie and Authorization headers are secrets and never saved.
            return preg_match( '/^(cookie|authorization)\s*:/i', $line ) ? preg_replace( '/:.*/', ': (hidden)', $line ) : $line;
        }, $settings['headers'] );
        $recorded['targets'] = $allTargets;
        $recorded['cold_clear'] = !empty( $options['cold-clear'] );

        $extra = array( 'tools' => $tools );
        $pairs = !empty( $options['compare'] ) ? \expBenchmark::pairs( $rows, $targets ) : array();
        if ( $pairs )
            $extra['pairs'] = $pairs;

        $document = \expBenchmark::document( 'http', $rows, $recorded, $extra );
        return $this->finish( $document, $options );
    }

    /**
     * kernel mode.
     */
    protected function runKernel( $options )
    {
        $settings = \expBenchmarkKernel::defaults();
        foreach ( array( 'repeat' => 'repeat', 'boot-repeat' => 'boot_repeat', 'node' => 'node' ) as $option => $key )
        {
            if ( isset( $options[$option] ) and $options[$option] !== false and $options[$option] !== null )
            {
                if ( !preg_match( '/^\d+$/', (string)$options[$option] ) )
                    throw new \InvalidArgumentException( "--$option must be a whole number" );
                $settings[$key] = (int)$options[$option];
            }
        }
        $settings['probes'] = $this->listOption( $options, 'probe' );
        foreach ( $settings['probes'] as $probe )
        {
            if ( !in_array( $probe, \expBenchmarkKernel::probeNames(), true ) )
                throw new \InvalidArgumentException( "Unknown probe \"$probe\": " . implode( ', ', \expBenchmarkKernel::probeNames() ) );
        }
        $settings['siteaccess'] = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '';
        $settings['script'] = 'bin/php/benchmark.php';

        $this->progress( sprintf( 'exp:benchmark kernel  siteaccess %s, %d runs per probe, %d new processes for boot',
            $settings['siteaccess'] !== '' ? $settings['siteaccess'] : '(default)', $settings['repeat'], $settings['boot_repeat'] ) );

        $rows = \expBenchmarkKernel::run( $settings, array( $this, 'progress' ) );

        $recorded = $settings;
        $recorded['php'] = PHP_BINARY;
        $recorded['opcache'] = function_exists( 'opcache_get_status' ) && (bool)ini_get( 'opcache.enable_cli' );
        $document = \expBenchmark::document( 'kernel', $rows, $recorded );
        return $this->finish( $document, $options );
    }

    /**
     * compare mode: two saved runs.
     */
    protected function runCompare( $options, $arguments )
    {
        if ( count( $arguments ) < 3 )
            throw new \InvalidArgumentException( 'compare needs two files: compare <baseline.json> <run.json>' );
        $baseline = \expBenchmark::load( $arguments[1] );
        $current = \expBenchmark::load( $arguments[2] );
        list( $threshold, $minDelta ) = $this->thresholds( $options );
        $comparison = \expBenchmark::compare( $baseline['rows'], $current['rows'], $threshold, $minDelta );
        if ( !empty( $options['json'] ) )
            fwrite( STDOUT, \expBenchmark::json( $comparison ) . "\n" );
        else
            $this->printComparison( $comparison, $arguments[1], !empty( $options['all-checks'] ) );
        return $comparison['regressions'] > 0 ? self::EXIT_REGRESSION : self::EXIT_OK;
    }

    /**
     * show mode: a saved run as a table.
     */
    protected function runShow( $options, $arguments )
    {
        if ( count( $arguments ) < 2 )
            throw new \InvalidArgumentException( 'show needs a file: show <run.json>' );
        $document = \expBenchmark::load( $arguments[1] );
        $this->writeResult( $document, $options );
        return self::EXIT_OK;
    }

    /**
     * Prints the run, saves it and compares it with the baseline.
     */
    protected function finish( $document, $options )
    {
        $code = self::EXIT_OK;
        $comparison = null;
        if ( !empty( $options['baseline'] ) )
        {
            $baseline = \expBenchmark::load( (string)$options['baseline'] );
            list( $threshold, $minDelta ) = $this->thresholds( $options );
            $comparison = \expBenchmark::compare( $baseline['rows'], $document['rows'], $threshold, $minDelta );
            $document['comparison'] = $comparison + array( 'baseline' => (string)$options['baseline'],
                                                           'baseline_started' => isset( $baseline['started'] ) ? $baseline['started'] : '' );
            if ( $comparison['regressions'] > 0 )
                $code = self::EXIT_REGRESSION;
        }

        if ( !empty( $options['save'] ) )
        {
            $file = (string)$options['save'];
            if ( !is_dir( dirname( $file ) ) )
                \eZDir::mkdir( dirname( $file ), false, true );
            if ( !\expBenchmark::save( $file, $document ) )
                throw new \RuntimeException( "Could not write $file" );
            $this->progress( "  saved: $file" );
        }

        $this->writeResult( $document, $options );
        if ( $comparison !== null and !$this->machine )
            $this->printComparison( $comparison, (string)$options['baseline'], !empty( $options['all-checks'] ) );
        return $code;
    }

    protected function writeResult( $document, $options )
    {
        if ( !empty( $options['json'] ) )
        {
            fwrite( STDOUT, \expBenchmark::json( $document ) . "\n" );
            return;
        }
        if ( !empty( $options['csv'] ) )
        {
            fwrite( STDOUT, \expBenchmark::csv( $document['rows'] ) );
            return;
        }

        $this->say( '' );
        $this->say( sprintf( '  %s mode, %s, PHP %s, %s', $document['mode'], $document['machine'], $document['php'], $document['started'] ) );
        $this->say( '  times in milliseconds; req/s counts successful requests' );
        $this->say( '' );
        $this->say( rtrim( \expBenchmark::table( $document['rows'] ), "\n" ) );
        foreach ( $document['rows'] as $row )
        {
            foreach ( isset( $row['error_messages'] ) ? $row['error_messages'] : array() as $message => $count )
                $this->say( sprintf( '  %s %s: %d x %s', \expBenchmarkHttp::origin( $row['target'] ) ?: $row['target'], $row['name'], $count, $message ) );
        }
        if ( !empty( $document['pairs'] ) )
        {
            $this->say( '' );
            $this->say( '  A/B, measured back to back (B/A below 1 means B is faster for the median, above 1 more req/s):' );
            $this->say( rtrim( \expBenchmark::pairsTable( $document['pairs'] ), "\n" ) );
        }
    }

    protected function printComparison( $comparison, $baselineFile, $all )
    {
        $this->say( '' );
        $this->say( sprintf( '  against %s: %d regression(s), threshold %.0f %%, noise floor %.1f ms',
            $baselineFile, $comparison['regressions'], $comparison['threshold'], $comparison['min_delta'] ) );
        $text = \expBenchmark::comparisonTable( $comparison, $all );
        if ( $text !== '' )
            $this->say( rtrim( $text, "\n" ) );
        $this->say( $comparison['regressions'] > 0 ? '  FAIL: slower than the baseline' : '  PASS: no regression' );
    }

    /**
     * A line of the result: always on stdout, with -q too (-q silences the progress lines only).
     */
    protected function say( $line )
    {
        fwrite( STDOUT, $line . "\n" );
    }

    /**
     * A line of progress: stdout for the table output, stderr when stdout carries JSON or CSV.
     */
    public function progress( $line )
    {
        if ( $this->isQuiet() )
            return;
        if ( $this->machine )
            fwrite( STDERR, $line . "\n" );
        else
            $this->say( $line );
    }

    /**
     * Why a run must not start, or an empty string.
     */
    protected function refusal( $targets, $owned, $hasExplicit, $settings, $total, $force )
    {
        $remote = false;
        foreach ( $targets as $target )
        {
            $host = (string)parse_url( $target, PHP_URL_HOST );
            if ( $host === '' )
                return "\"$target\" is not an http:// or https:// URL";
            if ( !$hasExplicit and !$this->isOwned( $host, $owned ) )
                return "$host is not served by this installation (its siteaccesses name " . implode( ', ', array_slice( $owned, 0, 8 ) ) .
                       "). To measure another server, name its URLs with --url or --urls-file.";
            if ( !\expBenchmarkHttp::isLoopback( $host ) )
                $remote = true;
        }
        if ( $remote and !$force )
        {
            if ( $settings['requests'] > self::CAP_REQUESTS )
                return '--requests above ' . self::CAP_REQUESTS . ' against a host that is not loopback needs --force';
            if ( $settings['concurrency'] > self::CAP_CONCURRENCY )
                return '--concurrency above ' . self::CAP_CONCURRENCY . ' against a host that is not loopback needs --force';
            if ( $settings['warmup'] > self::CAP_WARMUP )
                return '--warmup above ' . self::CAP_WARMUP . ' against a host that is not loopback needs --force';
            if ( $total > self::CAP_TOTAL )
                return "$total requests in all (above " . self::CAP_TOTAL . ') against a host that is not loopback needs --force';
        }
        return '';
    }

    protected function isOwned( $host, $owned )
    {
        $host = strtolower( (string)$host );
        return \expBenchmarkHttp::isLoopback( $host ) || in_array( $host, $owned, true );
    }

    /**
     * The host names this installation answers to: the SiteURL of every siteaccess and the hosts of the
     * host matching settings.
     *
     * @return array lower-case host names
     */
    protected function ownedHosts()
    {
        $ini = \eZINI::instance();
        $hosts = array();
        $add = function ( $value ) use ( &$hosts )
        {
            $value = trim( (string)$value );
            if ( $value === '' )
                return;
            $host = parse_url( ( preg_match( '#^https?://#i', $value ) ? '' : 'http://' ) . $value, PHP_URL_HOST );
            if ( $host )
                $hosts[strtolower( $host )] = true;
        };

        $add( $ini->variable( 'SiteSettings', 'SiteURL' ) );
        foreach ( array( 'HostMatchMapItems', 'HostUriMatchMapItems' ) as $setting )
        {
            if ( $ini->hasVariable( 'SiteAccessSettings', $setting ) )
            {
                foreach ( (array)$ini->variable( 'SiteAccessSettings', $setting ) as $item )
                {
                    $parts = explode( ';', (string)$item );
                    $add( $parts[0] );
                }
            }
        }
        $list = $ini->hasVariable( 'SiteAccessSettings', 'AvailableSiteAccessList' )
              ? (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) : array();
        foreach ( $list as $siteaccess )
        {
            $saIni = \eZSiteAccess::getIni( $siteaccess, 'site.ini' );
            if ( $saIni and $saIni->hasVariable( 'SiteSettings', 'SiteURL' ) )
                $add( $saIni->variable( 'SiteSettings', 'SiteURL' ) );
        }
        return array_keys( $hosts );
    }

    /**
     * https:// and the SiteURL of the current siteaccess.
     */
    protected function siteBase()
    {
        $url = trim( (string)\eZINI::instance()->variable( 'SiteSettings', 'SiteURL' ) );
        if ( $url === '' )
            throw new \RuntimeException( '[SiteSettings] SiteURL is empty; name the server with --base' );
        return $this->normalizeBase( $url );
    }

    protected function normalizeBase( $base )
    {
        $base = trim( (string)$base );
        if ( !preg_match( '#^https?://#i', $base ) )
            $base = 'https://' . $base;
        return rtrim( $base, '/' );
    }

    /**
     * The default URL list: the front page, the first pages of the menu (the children of the front page node
     * with an URL alias, by priority) and the login page of the admin siteaccess.
     */
    protected function defaultPaths( $pages, $withAdmin )
    {
        $paths = array( '/' );
        if ( $pages > 0 )
        {
            $children = \eZContentObjectTreeNode::subTreeByNodeID(
                array( 'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => $pages * 3, 'Limitation' => array(),
                       'SortBy' => array( 'priority', true ) ),
                \expBenchmarkKernel::frontPageNodeID() );
            foreach ( (array)$children as $child )
            {
                $alias = trim( (string)$child->urlAlias(), '/' );
                if ( $alias === '' )
                    continue;
                $paths[] = '/' . $alias;
                if ( count( $paths ) > $pages )
                    break;
            }
        }

        if ( $withAdmin )
        {
            $admin = $this->adminSiteaccess();
            $ini = \eZINI::instance();
            $order = $ini->hasVariable( 'SiteAccessSettings', 'MatchOrder' ) ? $ini->variable( 'SiteAccessSettings', 'MatchOrder' ) : '';
            if ( is_array( $order ) )
                $order = implode( ';', $order );
            // Reachable as /<admin>/user/login only where the siteaccess is matched by URI.
            if ( $admin !== '' and strpos( (string)$order, 'uri' ) !== false )
                $paths[] = '/' . $admin . '/user/login';
        }
        return $paths;
    }

    /**
     * The admin siteaccess: "admin" when it exists, else the first one with an admin design.
     */
    protected function adminSiteaccess()
    {
        $ini = \eZINI::instance();
        $list = $ini->hasVariable( 'SiteAccessSettings', 'AvailableSiteAccessList' )
              ? (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) : array();
        if ( in_array( 'admin', $list, true ) )
            return 'admin';
        foreach ( $list as $siteaccess )
        {
            $saIni = \eZSiteAccess::getIni( $siteaccess, 'site.ini' );
            if ( $saIni and $saIni->hasVariable( 'DesignSettings', 'SiteDesign' )
                 and preg_match( '/^admin\d*$/', (string)$saIni->variable( 'DesignSettings', 'SiteDesign' ) ) )
                return $siteaccess;
        }
        return '';
    }

    protected function readUrlsFile( $file )
    {
        if ( !is_file( $file ) or !is_readable( $file ) )
            throw new \InvalidArgumentException( "--urls-file: cannot read $file" );
        $urls = array();
        foreach ( file( $file, FILE_IGNORE_NEW_LINES ) as $line )
        {
            $line = trim( preg_replace( '/(^|\s)#.*$/', '', $line ) );
            if ( $line !== '' )
                $urls[] = $line;
        }
        return $urls;
    }

    protected function listOption( $options, $name )
    {
        if ( !isset( $options[$name] ) or $options[$name] === false or $options[$name] === null )
            return array();
        return array_values( array_filter( array_map( 'trim', (array)$options[$name] ), 'strlen' ) );
    }

    protected function thresholds( $options )
    {
        $threshold = isset( $options['threshold'] ) && $options['threshold'] !== false && $options['threshold'] !== null
                   ? (float)$options['threshold'] : \expBenchmark::DEFAULT_THRESHOLD;
        $minDelta = isset( $options['min-delta'] ) && $options['min-delta'] !== false && $options['min-delta'] !== null
                  ? (float)$options['min-delta'] : \expBenchmark::DEFAULT_MIN_DELTA_MS;
        if ( $threshold <= 0 )
            throw new \InvalidArgumentException( '--threshold must be above 0' );
        return array( $threshold, max( 0.0, $minDelta ) );
    }
}

}
