<?php
/**
 * @description Measure the speed of the GraphQL API against decision 18: cached GET, 304, cached POST, a cached answer through the kernel and an uncached query, N requests each, with median, p95 and max
 * @alias exp:graphql:bench
 *
 * File containing the exp:graphql:bench command (bin/php/expgraphqlbench.php); guide: doc/eng-us/features/6.0/graphql.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expgraphqlbench extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_LIMIT = 1;
    const EXIT_USAGE = 2;
    const EXIT_UNAVAILABLE = 3;

    /** The machine output (--format=json). */
    const SCHEMA = 'exp-graphql-bench/1';

    /** The environment variable that holds an OAuth token or a personal API key (never an option: shell history). */
    const TOKEN_VARIABLE = 'GRAPHQL_BENCH_TOKEN';

    public function run()
    {
        $this->script( array( 'description' => "Measure the speed of the GraphQL API against decision 18\n" .
                                               "Sends the requests of each row one after the other to an end point, --requests times each\n" .
                                               "(after two that warm it up), --runs times, and prints the median, p95 and max of every row:\n" .
                                               "\n" .
                                               "  get       a cached persisted query, GET ?id=          (answered before the kernel starts)\n" .
                                               "  304       the same with If-None-Match\n" .
                                               "  post      a cached persisted query, POST JSON {id}    (answered before the kernel starts)\n" .
                                               "  kernel    a cached persisted query as a form POST, which starts the kernel (what the first\n" .
                                               "            request of new credentials costs)\n" .
                                               "  uncached  the text of --query-file as an ad-hoc POST (never cached)\n" .
                                               "\n" .
                                               "Every cached answer must come within 33 ms (the hard limit, on every request); the targets are\n" .
                                               "20 ms, then 13 ms, for the median and the p95. Uncached answers are measured, without a limit.\n" .
                                               "A time runs from the request being sent to the last byte of the answer (one connection for all,\n" .
                                               "so the TLS handshake is not in it). An OAuth token or a personal API key is read from the\n" .
                                               "environment variable " . self::TOKEN_VARIABLE . " (sent as Authorization: Bearer).\n" .
                                               "\n" .
                                               "./console exp:graphql:bench --url=https://example.com/api/graphql --id=articles\n" .
                                               "./console exp:graphql:bench --url=https://example.com/api/graphql --id=articles \\\n" .
                                               "    --variables='{\"limit\":50}' --query-file=queries/articles.graphql --requests=200 --runs=3\n" .
                                               "./console exp:graphql:bench --url=http://127.0.0.1:8080/api/graphql --id=articles --rows=get,post --format=json\n" .
                                               "\n" .
                                               "Exit code: 0 every cached row within the hard limit; 1 a cached row over it, or not answered from\n" .
                                               "the cache; 3 the end point does not answer; 2 wrong options.",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => false ) );
        $options = $this->startup( '[url:][id:][variables:][query-file:][requests:][runs:][rows:][siteaccess-header:][insecure][format:]', '',
                                   array( 'url' => 'the end point, e.g. https://example.com/api/graphql',
                                          'id' => 'the name or SHA-256 of the persisted query to measure',
                                          'variables' => 'the variables of the query, as JSON',
                                          'query-file' => 'a .graphql file with the text of the query, for the uncached row',
                                          'requests' => 'requests per row and run (default 200)',
                                          'runs' => 'how often every row runs (default 3)',
                                          'rows' => 'the rows to run, comma separated: get,304,post,kernel,uncached (default all)',
                                          'siteaccess-header' => 'send X-Siteaccess with this siteaccess',
                                          'insecure' => 'do not check the TLS certificate (a test server of your own)',
                                          'format' => 'text (the default) or json' ) );
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'text';
        if ( $format !== 'text' && $format !== 'json' )
            $this->usage( "--format must be text or json: $format" );
        $url = isset( $options['url'] ) ? trim( (string)$options['url'] ) : '';
        if ( !preg_match( '#^https?://[^/\s]+#', $url ) )
            $this->usage( '--url must be the http(s) address of the end point' );
        $id = isset( $options['id'] ) ? trim( (string)$options['id'] ) : '';
        if ( $id === '' )
            $this->usage( '--id must name a persisted query (./console exp:graphql:persist --list)' );
        $variables = null;
        if ( isset( $options['variables'] ) && $options['variables'] !== false && $options['variables'] !== '' )
        {
            $variables = json_decode( (string)$options['variables'], true );
            if ( !is_array( $variables ) )
                $this->usage( '--variables must be a JSON object' );
        }
        $query = null;
        if ( isset( $options['query-file'] ) && $options['query-file'] )
        {
            $query = @file_get_contents( (string)$options['query-file'] );
            if ( !is_string( $query ) || trim( $query ) === '' )
                $this->usage( '--query-file cannot be read: ' . $options['query-file'] );
        }
        $requests = self::positive( $options, 'requests', 200 );
        $runs = self::positive( $options, 'runs', 3 );
        if ( $requests === null || $runs === null )
            $this->usage( '--requests and --runs must be whole numbers above 0' );
        $rows = \expGraphQLBench::ROWS;
        if ( isset( $options['rows'] ) && $options['rows'] )
        {
            $rows = array_values( array_filter( array_map( 'trim', explode( ',', (string)$options['rows'] ) ), 'strlen' ) );
            $unknown = array_diff( $rows, \expGraphQLBench::ROWS );
            if ( !$rows || $unknown )
                $this->usage( '--rows takes ' . implode( ',', \expGraphQLBench::ROWS ) . ': ' . implode( ',', $unknown ) );
        }
        if ( !function_exists( 'curl_init' ) )
        {
            $this->cli()->error( 'FAIL the PHP extension curl is missing' );
            $this->shutdown( self::EXIT_UNAVAILABLE );
        }
        $headers = array();
        if ( isset( $options['siteaccess-header'] ) && $options['siteaccess-header'] )
            $headers[] = 'X-Siteaccess: ' . preg_replace( '/[^A-Za-z0-9_-]/', '', (string)$options['siteaccess-header'] );
        $token = getenv( self::TOKEN_VARIABLE );
        if ( is_string( $token ) && trim( $token ) !== '' )
            $headers[] = 'Authorization: Bearer ' . trim( $token );

        $bench = new \expGraphQLBench( $url, $id, array( 'variables' => $variables, 'query' => $query, 'headers' => $headers,
                                                         'insecure' => !empty( $options['insecure'] ) ) );
        $probe = $bench->httpSend( $bench->request( 'get' ) );
        if ( $probe['ms'] === null || $probe['status'] === 0 )
        {
            $this->cli()->error( 'FAIL the end point does not answer: ' . ( isset( $probe['error'] ) ? $probe['error'] : $url ) );
            $this->shutdown( self::EXIT_UNAVAILABLE );
        }

        $report = self::measure( $bench, $rows, $requests, $runs );
        $report = array( 'schema' => self::SCHEMA, 'url' => $url, 'id' => $id, 'requests' => $requests, 'runs' => $runs,
                         'signedIn' => is_string( $token ) && trim( $token ) !== '',
                         'hardLimitMs' => \expGraphQLBench::HARD_LIMIT_MS, 'targetsMs' => \expGraphQLBench::TARGETS_MS ) + $report;
        if ( $format === 'json' )
            $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
        else
            $this->printReport( $report );
        $this->shutdown( $report['pass'] ? self::EXIT_OK : self::EXIT_LIMIT );
    }

    /**
     * Runs the rows: every row once per run, the runs one after the other.
     *
     * @param \expGraphQLBench $bench
     * @param string[] $rows
     * @param int $requests
     * @param int $runs
     * @return array rows (row => runs (each: n, median, p95, max, statuses, cache, verdict), all (the times of every
     *               run together: median, p95, max, verdict)), skipped (rows that cannot run), pass
     */
    public static function measure( \expGraphQLBench $bench, array $rows, $requests, $runs )
    {
        $result = array( 'rows' => array(), 'skipped' => array(), 'pass' => true );
        foreach ( $rows as $row )
        {
            if ( !$bench->canRun( $row ) )
            {
                $result['skipped'][] = $row;
                continue;
            }
            $all = array( 'row' => $row, 'n' => 0, 'times' => array(), 'statuses' => array(), 'cache' => array(), 'failed' => 0 );
            $perRun = array();
            for ( $run = 0; $run < $runs; $run++ )
            {
                $one = $bench->runRow( $row, $requests );
                $all['n'] += $one['n'];
                $all['times'] = array_merge( $all['times'], $one['times'] );
                $all['failed'] += $one['failed'];
                foreach ( array( 'statuses', 'cache' ) as $count )
                    foreach ( $one[$count] as $name => $value )
                        $all[$count][$name] = ( isset( $all[$count][$name] ) ? $all[$count][$name] : 0 ) + $value;
                $verdict = \expGraphQLBench::verdict( $one );
                unset( $one['times'] );
                $perRun[] = $one + $verdict;
            }
            $all = $all + \expGraphQLBench::stats( $all['times'] );
            $verdict = \expGraphQLBench::verdict( $all );
            unset( $all['times'] );
            if ( $verdict['verdict'] === 'FAIL' )
                $result['pass'] = false;
            $result['rows'][$row] = array( 'runs' => $perRun, 'all' => $all + $verdict );
        }
        return $result;
    }

    /**
     * @param array $report
     */
    protected function printReport( array $report )
    {
        $out = $this->cli();
        $out->output( sprintf( '%s  id %s, %d requests x %d runs per row%s', $report['url'], $report['id'], $report['requests'], $report['runs'],
                               $report['signedIn'] ? ', signed in (' . self::TOKEN_VARIABLE . ')' : ', anonymous' ) );
        $out->output( sprintf( '%-9s %4s %9s %9s %9s  %-8s %s', 'row', 'run', 'median', 'p95', 'max', 'verdict', 'targets 20/13 ms' ) );
        foreach ( $report['rows'] as $row => $data )
        {
            foreach ( array_merge( $data['runs'], array( $data['all'] ) ) as $i => $line )
            {
                $targets = $line['targets'] ? implode( '/', array_map( function ( $met ) { return $met ? 'met' : 'not'; }, $line['targets'] ) ) : '-';
                $out->output( sprintf( '%-9s %4s %6.1f ms %6.1f ms %6.1f ms  %-8s %s%s', $row, $i < count( $data['runs'] ) ? (string)( $i + 1 ) : 'all',
                                       (float)$line['median'], (float)$line['p95'], (float)$line['max'], $line['verdict'], $targets,
                                       $line['reason'] !== '' && $line['verdict'] === 'FAIL' ? '  ' . $line['reason'] : '' ) );
            }
        }
        if ( $report['skipped'] )
            $out->output( 'SKIP ' . implode( ', ', $report['skipped'] ) . ' (uncached needs --query-file)' );
        $out->output( $report['pass'] ? 'PASS every cached row within the hard limit of decision 18 (' . \expGraphQLBench::HARD_LIMIT_MS . ' ms)'
                                      : 'FAIL a cached row is over the hard limit of decision 18 (' . \expGraphQLBench::HARD_LIMIT_MS . ' ms), or not answered from the cache' );
    }

    /**
     * @param array $options
     * @param string $name
     * @param int $default
     * @return int|null null: not a whole number above 0
     */
    protected static function positive( array $options, $name, $default )
    {
        if ( !isset( $options[$name] ) || $options[$name] === false || $options[$name] === '' )
            return $default;
        return ctype_digit( (string)$options[$name] ) && (int)$options[$name] > 0 ? (int)$options[$name] : null;
    }

    /**
     * @param string $message
     */
    protected function usage( $message )
    {
        $this->cli()->error( "FAIL $message" );
        $this->shutdown( self::EXIT_USAGE );
    }
}

}
?>
