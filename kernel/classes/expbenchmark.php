<?php
/**
 * File containing the expBenchmark class.
 *
 * The statistics, the report formats and the regression check of exp:benchmark.
 *
 * Nothing in this class measures anything or touches the network, the database
 * or the disk: it takes the samples the HTTP and kernel runners collected and
 * turns them into rows, and compares rows against a saved run. That keeps the
 * numbers testable on their own (tests/tests/kernel/classes/benchmark/).
 *
 * A row is one thing that was measured: one URL against one server, or one
 * kernel probe. Its "key" is what a later run is matched by, so it must not
 * depend on anything that changes between runs (no time, no counter):
 *
 *   array( 'key' => 'https://example.com /news', 'kind' => 'http', 'target' => 'https://example.com',
 *          'name' => '/news', 'tool' => 'curl', 'n' => 100, 'errors' => 0,
 *          'min' => .., 'mean' => .., 'median' => .., 'p95' => .., 'p99' => .., 'max' => .., 'rps' => ..,
 *          'status' => array( '200' => 100 ), 'bytes' => 51234, 'cache' => array( 'HIT' => 100 ) )
 *
 * Times are milliseconds, bytes are the mean body size of one response.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expBenchmark
{
    /** Version of the saved-run format (--save, --json); a baseline of another version is refused. */
    const FORMAT = 1;

    /** Default regression threshold in percent (--threshold). */
    const DEFAULT_THRESHOLD = 15.0;

    /**
     * Default noise floor in milliseconds (--min-delta): a latency that grew by less than this is never a
     * regression, however large the percentage. A 0.2 ms database round trip that becomes 0.3 ms is 50 %
     * slower and means nothing.
     */
    const DEFAULT_MIN_DELTA_MS = 1.0;

    /**
     * The p-th percentile of a sorted list, by linear interpolation between the two nearest ranks
     * (the method of numpy's default, Excel's PERCENTILE.INC and R's type 7).
     *
     * @param array $sorted numbers, ascending
     * @param float $p 0 to 100
     * @return float|null null for an empty list
     */
    public static function percentile( $sorted, $p )
    {
        $sorted = array_values( (array)$sorted );
        $n = count( $sorted );
        if ( $n === 0 )
            return null;
        if ( $n === 1 )
            return (float)$sorted[0];

        $p = max( 0.0, min( 100.0, (float)$p ) );
        $rank = ( $p / 100.0 ) * ( $n - 1 );
        $low = (int)floor( $rank );
        $high = (int)ceil( $rank );
        $fraction = $rank - $low;
        return (float)$sorted[$low] + ( (float)$sorted[$high] - (float)$sorted[$low] ) * $fraction;
    }

    /**
     * Summary statistics of a list of durations.
     *
     * @param array $samples milliseconds, in any order
     * @return array n, min, mean, median, p95, p99, max, stddev (all null when there are no samples)
     */
    public static function summarize( $samples )
    {
        $samples = array_map( 'floatval', array_values( (array)$samples ) );
        sort( $samples, SORT_NUMERIC );
        $n = count( $samples );
        if ( $n === 0 )
        {
            return array( 'n' => 0, 'min' => null, 'mean' => null, 'median' => null, 'p95' => null,
                          'p99' => null, 'max' => null, 'stddev' => null );
        }

        $mean = array_sum( $samples ) / $n;
        $variance = 0.0;
        foreach ( $samples as $value )
            $variance += ( $value - $mean ) * ( $value - $mean );
        $stddev = $n > 1 ? sqrt( $variance / ( $n - 1 ) ) : 0.0;

        return array(
            'n'      => $n,
            'min'    => $samples[0],
            'mean'   => $mean,
            'median' => self::percentile( $samples, 50 ),
            'p95'    => self::percentile( $samples, 95 ),
            'p99'    => self::percentile( $samples, 99 ),
            'max'    => $samples[$n - 1],
            'stddev' => $stddev,
        );
    }

    /**
     * One row from the raw results of HTTP requests.
     *
     * @param string $target the base URL the request went to ("https://example.com:8080")
     * @param string $name the path that was asked for
     * @param array $requests list of array( 'ms' => float, 'ttfb' => float, 'status' => int, 'bytes' => int,
     *                        'cache' => string, 'error' => string ) -- one per request
     * @param float $wallSeconds how long the requests took together (for requests per second)
     * @param string $tool the client that made them
     * @return array a row
     */
    public static function httpRow( $target, $name, $requests, $wallSeconds, $tool = 'curl' )
    {
        $times = array();
        $ttfb = array();
        $status = array();
        $cache = array();
        $bytes = 0;
        $errors = 0;
        $ok = 0;
        $errorMessages = array();
        foreach ( (array)$requests as $request )
        {
            $code = isset( $request['status'] ) ? (int)$request['status'] : 0;
            $label = $code > 0 ? (string)$code : 'failed';
            $status[$label] = isset( $status[$label] ) ? $status[$label] + 1 : 1;

            $error = isset( $request['error'] ) ? (string)$request['error'] : '';
            if ( $error === '' and ( $code === 0 or $code >= 400 ) )
                $error = 'HTTP ' . $code;
            if ( $error !== '' )
            {
                $errors++;
                $errorMessages[$error] = isset( $errorMessages[$error] ) ? $errorMessages[$error] + 1 : 1;
                // A refused connection takes no time; counting it would make a broken server look fast.
                if ( $code === 0 )
                    continue;
            }
            else
            {
                $ok++;
            }

            $times[] = (float)$request['ms'];
            if ( isset( $request['ttfb'] ) )
                $ttfb[] = (float)$request['ttfb'];
            $bytes += isset( $request['bytes'] ) ? (int)$request['bytes'] : 0;
            if ( isset( $request['cache'] ) and $request['cache'] !== '' )
            {
                // "BYPASS (query string)" and "BYPASS (cookie)" are the same answer for a summary.
                $word = strtoupper( strtok( (string)$request['cache'], " (;," ) );
                $cache[$word] = isset( $cache[$word] ) ? $cache[$word] + 1 : 1;
            }
        }

        $row = array(
            'key'    => self::key( $target, $name ),
            'kind'   => 'http',
            'target' => (string)$target,
            'name'   => (string)$name,
            'tool'   => (string)$tool,
        ) + self::summarize( $times );

        $ttfbSummary = self::summarize( $ttfb );
        // the status codes in numeric order, "failed" after them: ksort() orders a mix of
        // number and text keys differently before PHP 8.2 ("failed" first there)
        uksort( $status, function ( $a, $b )
        {
            if ( is_int( $a ) != is_int( $b ) )
                return is_int( $a ) ? -1 : 1;
            return $a < $b ? -1 : ( $a > $b ? 1 : 0 );
        } );
        arsort( $cache );
        $row['requests'] = count( (array)$requests );
        $row['errors'] = $errors;
        $row['error_messages'] = $errorMessages;
        $row['rps'] = $wallSeconds > 0 ? $ok / $wallSeconds : null;
        $row['ttfb_median'] = $ttfbSummary['median'];
        $row['status'] = $status;
        $row['bytes'] = count( $times ) > 0 ? (int)round( $bytes / count( $times ) ) : 0;
        $row['cache'] = $cache;
        return $row;
    }

    /**
     * One row from the summary an external tool (ab, wrk, oha) printed. The tool reports aggregates, not
     * single requests, so what it did not report stays null.
     *
     * @param string $target
     * @param string $name
     * @param array $parsed what parseAb(), parseWrk() or parseOha() returned
     * @param string $tool
     * @return array a row
     */
    public static function toolRow( $target, $name, $parsed, $tool )
    {
        $fields = array( 'n', 'min', 'mean', 'median', 'p95', 'p99', 'max', 'stddev', 'rps', 'errors', 'bytes' );
        $row = array(
            'key'    => self::key( $target, $name ),
            'kind'   => 'http',
            'target' => (string)$target,
            'name'   => (string)$name,
            'tool'   => (string)$tool,
        );
        foreach ( $fields as $field )
            $row[$field] = isset( $parsed[$field] ) ? $parsed[$field] : null;
        $row['requests'] = $row['n'];
        $row['errors'] = (int)$row['errors'];
        $row['error_messages'] = array();
        $row['ttfb_median'] = null;
        $row['status'] = isset( $parsed['status'] ) ? $parsed['status'] : array();
        $row['cache'] = array();
        $row['bytes'] = (int)$row['bytes'];
        return $row;
    }

    /**
     * One row of a kernel probe.
     *
     * @param string $name the probe ("db_query", "render_warm", ...)
     * @param array $samples milliseconds
     * @param int $errors how many runs failed
     * @param int|null $memoryPeak bytes, the highest memory the probe needed
     * @param string $note what the probe used (node id, query, ...)
     * @return array a row
     */
    public static function kernelRow( $name, $samples, $errors = 0, $memoryPeak = null, $note = '' )
    {
        $row = array(
            'key'    => self::key( 'kernel', $name ),
            'kind'   => 'kernel',
            'target' => 'kernel',
            'name'   => (string)$name,
            'tool'   => 'hrtime',
        ) + self::summarize( $samples );
        $row['requests'] = $row['n'] + (int)$errors;
        $row['errors'] = (int)$errors;
        $row['error_messages'] = array();
        $row['rps'] = ( $row['mean'] !== null and $row['mean'] > 0 ) ? 1000.0 / $row['mean'] : null;
        $row['mem_peak'] = $memoryPeak;
        $row['note'] = (string)$note;
        return $row;
    }

    /**
     * The key a row is matched by in a later run.
     *
     * @param string $target
     * @param string $name
     * @return string
     */
    public static function key( $target, $name )
    {
        return rtrim( (string)$target, '/' ) . ' ' . (string)$name;
    }

    /**
     * A whole run: what was measured, where, with what, and the rows. This is what --save and --json write
     * and what --baseline reads.
     *
     * @param string $mode "http" or "kernel"
     * @param array $rows
     * @param array $settings the options the run was made with (no secrets)
     * @param array $extra more top-level fields (pairs, tools, ...)
     * @return array
     */
    public static function document( $mode, $rows, $settings = array(), $extra = array() )
    {
        $version = class_exists( 'eZPublishSDK' )
                 ? eZPublishSDK::version() : '';
        return array(
            'tool'        => 'exp:benchmark',
            'format'      => self::FORMAT,
            'mode'        => (string)$mode,
            'started'     => date( 'c' ),
            'machine'     => php_uname( 'n' ),
            'php'         => PHP_VERSION,
            'exponential' => $version,
            'settings'    => $settings,
            'rows'        => array_values( $rows ),
        ) + $extra;
    }

    /**
     * The back-to-back comparison of two or more servers: for every path, each later target against the first.
     *
     * @param array $rows http rows
     * @param array $targets the targets in the order given; the first is the reference
     * @return array list of array( name, base, other, base_median, other_median, median_ratio, base_rps, other_rps, rps_ratio )
     */
    public static function pairs( $rows, $targets )
    {
        $targets = array_values( (array)$targets );
        if ( count( $targets ) < 2 )
            return array();

        $byKey = array();
        foreach ( (array)$rows as $row )
            $byKey[$row['key']] = $row;

        $names = array();
        foreach ( (array)$rows as $row )
            $names[$row['name']] = true;

        $pairs = array();
        foreach ( array_keys( $names ) as $name )
        {
            $baseKey = self::key( $targets[0], $name );
            if ( !isset( $byKey[$baseKey] ) )
                continue;
            $base = $byKey[$baseKey];
            for ( $i = 1; $i < count( $targets ); $i++ )
            {
                $otherKey = self::key( $targets[$i], $name );
                if ( !isset( $byKey[$otherKey] ) )
                    continue;
                $other = $byKey[$otherKey];
                $pairs[] = array(
                    'name'         => (string)$name,
                    'base'         => rtrim( $targets[0], '/' ),
                    'other'        => rtrim( $targets[$i], '/' ),
                    'base_median'  => $base['median'],
                    'other_median' => $other['median'],
                    'median_ratio' => self::ratio( $other['median'], $base['median'] ),
                    'base_rps'     => $base['rps'],
                    'other_rps'    => $other['rps'],
                    'rps_ratio'    => self::ratio( $other['rps'], $base['rps'] ),
                );
            }
        }
        return $pairs;
    }

    /**
     * Compares a run against a saved one.
     *
     * Latency (median, p95) regresses when it grew by more than $threshold percent AND by more than
     * $minDeltaMs; requests per second (HTTP rows only) regresses when it fell by more than $threshold
     * percent while the median grew by more than $minDeltaMs; the error rate regresses when it rose by more than one percentage point. A row of the
     * baseline missing from the run is reported, not counted as a regression: the URL list may have changed.
     *
     * @param array $baselineRows
     * @param array $rows
     * @param float $threshold percent
     * @param float $minDeltaMs
     * @return array( 'regressions' => int, 'checks' => list of array( key, metric, baseline, current,
     *                change_pct, regressed ), 'missing' => list of keys, 'new' => list of keys )
     */
    public static function compare( $baselineRows, $rows, $threshold = self::DEFAULT_THRESHOLD, $minDeltaMs = self::DEFAULT_MIN_DELTA_MS )
    {
        $threshold = (float)$threshold;
        $minDeltaMs = (float)$minDeltaMs;

        $old = array();
        foreach ( (array)$baselineRows as $row )
            $old[$row['key']] = $row;
        $new = array();
        foreach ( (array)$rows as $row )
            $new[$row['key']] = $row;

        $checks = array();
        $regressions = 0;
        foreach ( $new as $key => $row )
        {
            if ( !isset( $old[$key] ) )
                continue;
            $base = $old[$key];

            foreach ( array( 'median', 'p95' ) as $metric )
            {
                if ( !isset( $base[$metric], $row[$metric] ) )
                    continue;
                $change = self::changePercent( $base[$metric], $row[$metric] );
                $regressed = $change !== null && $change > $threshold
                          && ( $row[$metric] - $base[$metric] ) > $minDeltaMs;
                $checks[] = self::check( $key, $metric, $base[$metric], $row[$metric], $change, $regressed );
                $regressions += $regressed ? 1 : 0;
            }

            if ( isset( $row['kind'] ) and $row['kind'] === 'http' and isset( $base['rps'], $row['rps'] ) )
            {
                $change = self::changePercent( $base['rps'], $row['rps'] );
                // Requests per second follow the latency: a drop whose median grew by less than the noise
                // floor is the client and the machine, not the server.
                $latencyGrew = !isset( $base['median'], $row['median'] ) || ( $row['median'] - $base['median'] ) > $minDeltaMs;
                $regressed = $change !== null && -$change > $threshold && $latencyGrew;
                $checks[] = self::check( $key, 'rps', $base['rps'], $row['rps'], $change, $regressed );
                $regressions += $regressed ? 1 : 0;
            }

            $baseRate = self::errorRate( $base );
            $rate = self::errorRate( $row );
            if ( $baseRate !== null and $rate !== null )
            {
                $regressed = ( $rate - $baseRate ) > 1.0;
                $checks[] = self::check( $key, 'error_pct', $baseRate, $rate, $rate - $baseRate, $regressed );
                $regressions += $regressed ? 1 : 0;
            }
        }

        return array(
            'threshold'   => $threshold,
            'min_delta'   => $minDeltaMs,
            'regressions' => $regressions,
            'checks'      => $checks,
            'missing'     => array_values( array_diff( array_keys( $old ), array_keys( $new ) ) ),
            'new'         => array_values( array_diff( array_keys( $new ), array_keys( $old ) ) ),
        );
    }

    /**
     * Reads a saved run (--baseline).
     *
     * @param string $file
     * @return array the document
     * @throws RuntimeException when the file is missing, not JSON, or of another format
     */
    public static function load( $file )
    {
        if ( !is_file( $file ) or !is_readable( $file ) )
            throw new RuntimeException( "Baseline not found or not readable: $file" );
        $document = json_decode( (string)file_get_contents( $file ), true );
        if ( !is_array( $document ) or !isset( $document['rows'] ) or !is_array( $document['rows'] ) )
            throw new RuntimeException( "Not a saved exp:benchmark run: $file" );
        if ( !isset( $document['format'] ) or (int)$document['format'] !== self::FORMAT )
            throw new RuntimeException( "Saved with another format version than " . self::FORMAT . ": $file" );
        return $document;
    }

    /**
     * Writes a run (--save). The directory must exist.
     *
     * @param string $file
     * @param array $document
     * @return bool
     */
    public static function save( $file, $document )
    {
        return file_put_contents( $file, self::json( $document ) . "\n" ) !== false;
    }

    /**
     * @param array $document
     * @return string pretty JSON
     */
    public static function json( $document )
    {
        return json_encode( $document, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION );
    }

    /**
     * The rows as CSV, one line per row, with a header line. Status codes and cache answers are folded into
     * one column each ("200:98 502:2").
     *
     * @param array $rows
     * @return string
     */
    public static function csv( $rows )
    {
        $columns = array( 'key', 'kind', 'target', 'name', 'tool', 'n', 'errors', 'min', 'mean', 'median',
                          'p95', 'p99', 'max', 'stddev', 'rps', 'ttfb_median', 'bytes', 'status', 'cache', 'mem_peak' );
        $handle = fopen( 'php://temp', 'r+' );
        fputcsv( $handle, $columns, ',', '"', '' );
        foreach ( (array)$rows as $row )
        {
            $line = array();
            foreach ( $columns as $column )
            {
                $value = isset( $row[$column] ) ? $row[$column] : '';
                if ( is_array( $value ) )
                    $value = self::counts( $value );
                else if ( is_float( $value ) )
                    $value = round( $value, 3 );
                $line[] = $value;
            }
            fputcsv( $handle, $line, ',', '"', '' );
        }
        rewind( $handle );
        $csv = stream_get_contents( $handle );
        fclose( $handle );
        return $csv;
    }

    /**
     * The rows as a text table for a terminal.
     *
     * @param array $rows
     * @return string
     */
    public static function table( $rows )
    {
        $rows = array_values( (array)$rows );
        if ( empty( $rows ) )
            return "  (nothing measured)\n";

        $kernel = isset( $rows[0]['kind'] ) && $rows[0]['kind'] === 'kernel';
        $header = $kernel
            ? array( 'probe', 'n', 'min', 'median', 'p95', 'p99', 'max', 'ops/s', 'err', 'mem peak', 'note' )
            : array( 'target', 'url', 'n', 'min', 'median', 'p95', 'p99', 'max', 'req/s', 'err', 'status', 'bytes', 'cache' );

        $lines = array( $header );
        foreach ( $rows as $row )
        {
            if ( $kernel )
            {
                $lines[] = array(
                    $row['name'], (string)$row['n'], self::ms( $row['min'] ), self::ms( $row['median'] ),
                    self::ms( $row['p95'] ), self::ms( $row['p99'] ), self::ms( $row['max'] ),
                    self::number( $row['rps'] ), (string)$row['errors'],
                    isset( $row['mem_peak'] ) ? self::bytes( $row['mem_peak'] ) : '-',
                    isset( $row['note'] ) ? $row['note'] : '',
                );
            }
            else
            {
                $lines[] = array(
                    self::shortTarget( $row['target'] ), $row['name'], (string)$row['n'], self::ms( $row['min'] ),
                    self::ms( $row['median'] ), self::ms( $row['p95'] ), self::ms( $row['p99'] ),
                    self::ms( $row['max'] ), self::number( $row['rps'] ), (string)$row['errors'],
                    self::counts( $row['status'] ), self::bytes( $row['bytes'] ),
                    $row['tool'] !== 'curl' ? '(' . $row['tool'] . ')' : self::counts( $row['cache'] ),
                );
            }
        }
        return self::render( $lines, $kernel ? array( 0, 10 ) : array( 0, 1, 10, 12 ) );
    }

    /**
     * The A/B pairs as a text table.
     *
     * @param array $pairs from pairs()
     * @return string
     */
    public static function pairsTable( $pairs )
    {
        if ( empty( $pairs ) )
            return '';
        $lines = array( array( 'url', 'A', 'B', 'A median', 'B median', 'B/A', 'A req/s', 'B req/s', 'B/A' ) );
        foreach ( $pairs as $pair )
        {
            $lines[] = array(
                $pair['name'], self::shortTarget( $pair['base'] ), self::shortTarget( $pair['other'] ),
                self::ms( $pair['base_median'] ), self::ms( $pair['other_median'] ), self::times( $pair['median_ratio'] ),
                self::number( $pair['base_rps'] ), self::number( $pair['other_rps'] ), self::times( $pair['rps_ratio'] ),
            );
        }
        return self::render( $lines, array( 0, 1, 2 ) );
    }

    /**
     * The result of compare() as text.
     *
     * @param array $comparison
     * @param bool $all every check, not only the regressions
     * @return string
     */
    public static function comparisonTable( $comparison, $all = false )
    {
        $lines = array( array( 'row', 'metric', 'baseline', 'now', 'change', '' ) );
        foreach ( $comparison['checks'] as $check )
        {
            if ( !$all and !$check['regressed'] )
                continue;
            $isPct = $check['metric'] === 'error_pct';
            $lines[] = array(
                $check['key'], $check['metric'],
                $isPct ? self::number( $check['baseline'] ) . '%' : self::number( $check['baseline'] ),
                $isPct ? self::number( $check['current'] ) . '%' : self::number( $check['current'] ),
                $check['change_pct'] === null ? '-' : sprintf( '%+.1f%s', $check['change_pct'], $isPct ? 'pp' : '%' ),
                $check['regressed'] ? 'REGRESSED' : 'ok',
            );
        }
        $text = count( $lines ) > 1 ? self::render( $lines, array( 0, 1, 5 ) ) : '';
        foreach ( $comparison['missing'] as $key )
            $text .= "  not measured this time: $key\n";
        return $text;
    }

    /**
     * @param array $counts label => count
     * @return string "200:98 502:2"
     */
    public static function counts( $counts )
    {
        $parts = array();
        foreach ( (array)$counts as $label => $count )
            $parts[] = $label . ':' . $count;
        return empty( $parts ) ? '-' : implode( ' ', $parts );
    }

    /**
     * Change from $old to $new in percent of $old.
     *
     * @return float|null null when $old is zero or missing
     */
    public static function changePercent( $old, $new )
    {
        if ( $old === null or $new === null or (float)$old == 0.0 )
            return null;
        return ( (float)$new - (float)$old ) / (float)$old * 100.0;
    }

    protected static function ratio( $a, $b )
    {
        if ( $a === null or $b === null or (float)$b == 0.0 )
            return null;
        return (float)$a / (float)$b;
    }

    protected static function errorRate( $row )
    {
        $requests = isset( $row['requests'] ) ? (int)$row['requests'] : ( isset( $row['n'] ) ? (int)$row['n'] : 0 );
        if ( $requests <= 0 or !isset( $row['errors'] ) )
            return null;
        return (int)$row['errors'] / $requests * 100.0;
    }

    protected static function check( $key, $metric, $baseline, $current, $change, $regressed )
    {
        return array( 'key' => $key, 'metric' => $metric, 'baseline' => $baseline, 'current' => $current,
                      'change_pct' => $change, 'regressed' => (bool)$regressed );
    }

    protected static function ms( $value )
    {
        if ( $value === null )
            return '-';
        if ( $value < 10 )
            return sprintf( '%.2f', $value );
        if ( $value < 100 )
            return sprintf( '%.1f', $value );
        return sprintf( '%.0f', $value );
    }

    protected static function number( $value )
    {
        if ( $value === null )
            return '-';
        if ( abs( $value ) < 10 )
            return sprintf( '%.2f', $value );
        if ( abs( $value ) < 100 )
            return sprintf( '%.1f', $value );
        return sprintf( '%.0f', $value );
    }

    protected static function times( $ratio )
    {
        return $ratio === null ? '-' : sprintf( 'x%.2f', $ratio );
    }

    protected static function bytes( $bytes )
    {
        if ( $bytes === null )
            return '-';
        if ( $bytes >= 1048576 )
            return sprintf( '%.1fM', $bytes / 1048576 );
        if ( $bytes >= 1024 )
            return sprintf( '%.1fk', $bytes / 1024 );
        return (string)(int)$bytes;
    }

    protected static function shortTarget( $target )
    {
        return preg_replace( '#^https?://#', '', rtrim( (string)$target, '/' ) );
    }

    /**
     * Lines as aligned columns; columns listed in $leftAligned are padded on the right, the rest on the left.
     */
    protected static function render( $lines, $leftAligned )
    {
        $widths = array();
        foreach ( $lines as $line )
            foreach ( array_values( $line ) as $i => $cell )
                $widths[$i] = max( isset( $widths[$i] ) ? $widths[$i] : 0, strlen( (string)$cell ) );

        $text = '';
        foreach ( $lines as $index => $line )
        {
            $cells = array();
            foreach ( array_values( $line ) as $i => $cell )
                $cells[] = in_array( $i, $leftAligned, true )
                         ? str_pad( (string)$cell, $widths[$i] )
                         : str_pad( (string)$cell, $widths[$i], ' ', STR_PAD_LEFT );
            $text .= '  ' . rtrim( implode( '  ', $cells ) ) . "\n";
            if ( $index === 0 )
            {
                $rule = array();
                foreach ( $widths as $width )
                    $rule[] = str_repeat( '-', $width );
                $text .= '  ' . implode( '  ', $rule ) . "\n";
            }
        }
        return $text;
    }
}
