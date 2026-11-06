<?php
/**
 * @description Test a URL map against a running site: request every old address, follow the redirects and compare where it lands with the new_url
 * @alias exp:url:test
 *
 * File containing the exp:url:test command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expurltest extends ExpurlBase
{
    protected function name()
    {
        return 'exp:url:test';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Test a URL map against a running site\n" .
                             "Requests every old address of the map on --base, several at a time, follows the redirects and\n" .
                             "compares where it lands with the row's new_url: landed, wrong-status (permanent where a temporary\n" .
                             "redirect was expected, or the other way), wrong-target, not-redirected, error; rows without a\n" .
                             "new_url are skipped. A wildcard row is tested with a sample for its *. Ends with exit 1 when a row did\n" .
                             "not land. Node targets (node:<id>) are compared with the node's address on the siteaccess of -s.\n" .
                             "\n" .
                             "./console exp:url:test var/tmp/map.csv --base=https://www.example.com\n" .
                             "./console exp:url:test var/tmp/map.xlsx --base=https://staging.example.com --insecure --report=var/tmp/test.csv\n" .
                             "./console exp:url:test var/tmp/map.csv --base=https://www.example.com --concurrency=16 --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[base:][format:][report:][limit:][concurrency:][timeout:][max-redirects:][insecure]', '[file]', array(
            'base' => 'test: the site to test, https://www.example.com (needed)',
            'format' => 'import: the format of the file (csv, json, xlsx, ods, apache, nginx; default from its name or content); export: the format to write (default from --output, else csv); batches, undo, from-sitemap, from-404, suggest, test: text (default) or json (the machine output exp-url/1)',
            'report' => 'import: also write the plan, row by row, to this file (.json, or .csv); suggest, test: also write every row with its suggestions or its result to this file (.json, or .csv)',
            'limit' => 'batches: how many of the last imports to list (default 20); from-sitemap, from-404, test: the most rows (default all)',
            'concurrency' => 'test: the requests at the same time (default 8)',
            'timeout' => 'from-sitemap, test: the seconds a request may take (default 15)',
            'max-redirects' => 'test: the redirects followed before a row counts as an error (default 5)',
            'insecure' => 'from-sitemap, test: do not verify TLS certificates (for test hosts with a self-signed one)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $printFormat = $this->outputFormat( $options );
        $base = isset( $options['base'] ) && $options['base'] ? trim( (string)$options['base'] ) : '';
        if ( $base === '' || \expUrlMapHttp::problem( $base ) !== null )
            $this->usage( '--base must be the http or https address of the site to test, not "' . $base . '"' );
        $limit = $this->intOption( $options, 'limit', 0, 0, 10000000 );
        $concurrency = $this->intOption( $options, 'concurrency', 8, 1, 64 );
        $timeout = $this->intOption( $options, 'timeout', 15, 1, 600 );
        $maxRedirects = $this->intOption( $options, 'max-redirects', 5, 0, 50 );
        $file = isset( $options['arguments'][0] ) ? (string)$options['arguments'][0] : '';
        $rows = $this->readMap( $file, $printFormat );
        if ( $limit > 0 )
            $rows = array_slice( $rows, 0, $limit );

        // node targets and the PathPrefix need the installation; without a database the paths are still tested
        $resolver = null;
        $prefixes = array();
        try
        {
            $db = \eZDB::instance();
            if ( $db && $db->isConnected() )
            {
                $environment = new \expUrlMapKernelEnvironment();
                $siteaccess = $this->givenSiteaccess() !== '' ? $this->givenSiteaccess() : $environment->defaultSiteaccess();
                $prefix = $environment->pathPrefix( $siteaccess );
                if ( $prefix['prefix'] !== '' )
                    $prefixes[] = '/' . $prefix['prefix'];
                $resolver = function ( $target )
                {
                    $node = \eZContentObjectTreeNode::fetch( (int)substr( (string)$target, 5 ) );
                    if ( !$node instanceof \eZContentObjectTreeNode )
                        return null;
                    $url = (string)$node->urlAlias();
                    \eZURI::transformURI( $url, false, 'relative' );
                    return '/' . ltrim( $url, '/' );
                };
            }
        }
        catch ( \Throwable $e )
        {
            $resolver = null;
        }

        $started = microtime( true );
        $tester = new \expUrlMapTester( $base, array( 'concurrency' => $concurrency, 'max_redirects' => $maxRedirects, 'resolver' => $resolver,
                                                      'equivalent_prefixes' => $prefixes ),
                                        new \expUrlMapHttp( array( 'timeout' => $timeout, 'insecure' => !empty( $options['insecure'] ), 'with_body' => false ) ) );
        $result = $tester->test( $rows );
        $report = array( 'mode' => 'test', 'file' => $file, 'base' => $base, 'counts' => $result['counts'], 'seconds' => round( microtime( true ) - $started, 2 ),
                         'results' => $result['results'], 'errors' => array(), 'warnings' => array() );
        $failed = $result['counts']['rows'] - $result['counts'][\expUrlMapTester::LANDED] - $result['counts'][\expUrlMapTester::SKIPPED];
        if ( $failed > 0 )
            $report['errors'][] = $failed . ' of ' . $result['counts']['rows'] . ' rows did not land where the map says';
        if ( isset( $options['report'] ) && $options['report'] )
        {
            try
            {
                self::writeReport( (string)$options['report'], $result['results'],
                                   array( 'line', 'old_url', 'new_url', 'state', 'status', 'landed', 'expected', 'chain', 'message' ) );
                $report['report'] = (string)$options['report'];
            }
            catch ( \RuntimeException $e )
            {
                $report['warnings'][] = $e->getMessage();
            }
        }
        $this->finish( $report, $printFormat );
    }

    protected function lines( array $report )
    {
        if ( !isset( $report['counts'] ) )
            return array();
        $c = $report['counts'];
        $lines = array( sprintf( 'URL map test of %s on %s: %d rows in %.1f s', $report['file'], $report['base'], $c['rows'], $report['seconds'] ),
                        sprintf( '  %d landed, %d wrong-status, %d wrong-target, %d not-redirected, %d error, %d skipped',
                                 $c['landed'], $c['wrong-status'], $c['wrong-target'], $c['not-redirected'], $c['error'], $c['skipped'] ) );
        $shown = 0;
        foreach ( $report['results'] as $r )
        {
            if ( in_array( $r['state'], array( \expUrlMapTester::LANDED, \expUrlMapTester::SKIPPED ), true ) )
                continue;
            if ( ++$shown > 50 )
            {
                $lines[] = '  ... more (--report writes every row)';
                break;
            }
            $lines[] = sprintf( '  %-14s line %-5d %s -> %s: %s', $r['state'], $r['line'], $r['old_url'], $r['new_url'], $r['message'] );
        }
        if ( isset( $report['report'] ) )
            $lines[] = '  Report: ' . $report['report'];
        return $lines;
    }
}

}
