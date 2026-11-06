<?php
/**
 * @description Start a URL map from an old site's sitemap (sitemap.xml, a sitemap index, .gz) or a crawl list: every old address as a row to fill in
 * @alias exp:url:from-sitemap
 *
 * File containing the exp:url:from-sitemap command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expurlfromsitemap extends ExpurlBase
{
    protected function name()
    {
        return 'exp:url:from-sitemap';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Start a URL map from an old site's sitemap or a crawl list\n" .
                             "Reads a sitemap (an address or a file): a <urlset>, a sitemap index with the sitemaps it names, either\n" .
                             "gzip-compressed, or a crawl list with one address per line. Writes every old address once as a row\n" .
                             "with an empty new_url, in any format exp:url:import reads: fill the targets in (exp:url:suggest helps),\n" .
                             "then import. Only the addresses of the sitemap's own host are kept unless --only-host or --any-host.\n" .
                             "\n" .
                             "./console exp:url:from-sitemap https://www.old-site.com/sitemap.xml --output=var/tmp/old-site.csv\n" .
                             "./console exp:url:from-sitemap var/tmp/crawl.txt --limit=500 --output=var/tmp/old-site.xlsx\n" .
                             "./console exp:url:from-sitemap https://www.old-site.com/sitemap_index.xml --any-host --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[output:][output-format:][format:][limit:][only-host:*][any-host][keep-duplicates][full-urls][timeout:][insecure]', '[source]', array(
            'output' => 'The file to write (default the standard output; needed for xlsx and ods)',
            'output-format' => 'import, export: what the command prints, text (default) or json (the machine output exp-url/1); --format is the file\'s format there; from-sitemap, from-404, suggest: the format of the file written (csv, json, xlsx, ods, apache, nginx; default from --output, else csv)',
            'format' => 'import: the format of the file (csv, json, xlsx, ods, apache, nginx; default from its name or content); export: the format to write (default from --output, else csv); batches, undo, from-sitemap, from-404, suggest, test: text (default) or json (the machine output exp-url/1)',
            'limit' => 'batches: how many of the last imports to list (default 20); from-sitemap, from-404, test: the most rows (default all)',
            'only-host' => 'from-sitemap: keep only the addresses of this host (a comma list or repeated; default the host of the sitemap address, or of its first address)',
            'any-host' => 'from-sitemap: keep the addresses of every host',
            'keep-duplicates' => 'from-sitemap: keep an address listed more than once (default: once)',
            'full-urls' => 'from-sitemap: write the old addresses with scheme and host (default: the path and query, as an import on this site needs them)',
            'timeout' => 'from-sitemap, test: the seconds a request may take (default 15)',
            'insecure' => 'from-sitemap, test: do not verify TLS certificates (for test hosts with a self-signed one)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $printFormat = $this->outputFormat( $options );
        $mapFormat = $this->mapFormat( $options );
        $source = isset( $options['arguments'][0] ) ? trim( (string)$options['arguments'][0] ) : '';
        if ( $source === '' )
            $this->usage( 'name the sitemap: exp:url:from-sitemap <address or file>' );
        $limit = $this->intOption( $options, 'limit', 0, 0, 10000000 );
        $timeout = $this->intOption( $options, 'timeout', 15, 1, 600 );
        $http = new \expUrlMapHttp( array( 'timeout' => $timeout, 'insecure' => !empty( $options['insecure'] ) ) );
        $sitemap = new \expUrlMapSitemapSource( array( 'limit' => $limit, 'hosts' => self::listOption( isset( $options['only-host'] ) ? $options['only-host'] : array() ),
                                                       'any_host' => !empty( $options['any-host'] ), 'dedupe' => empty( $options['keep-duplicates'] ),
                                                       'full_urls' => !empty( $options['full-urls'] ) ), $http );
        $report = array( 'mode' => 'from-sitemap', 'source' => $source, 'errors' => array(), 'warnings' => array() );
        try
        {
            $rows = $sitemap->rows( $source );
        }
        catch ( \expUrlMapFormatException $e )
        {
            $report['errors'][] = $e->getMessage();
            $this->finish( $report, $printFormat );
            return;
        }
        $report['counts'] = $sitemap->counts();
        $report['hosts'] = $sitemap->hosts();
        $report['warnings'] = $sitemap->warnings();
        if ( !$rows && $report['counts']['other_hosts'] )
            $report['warnings'][] = $report['counts']['other_hosts'] . ' addresses of other hosts left out: --only-host or --any-host keeps them';
        if ( !$rows )
            $report['errors'][] = 'no address found in ' . $source;
        $this->finishRows( $rows, $options, $report, $printFormat, $mapFormat );
    }

    protected function lines( array $report )
    {
        if ( !isset( $report['counts'] ) )
            return array();
        $c = $report['counts'];
        return array( sprintf( 'From sitemap %s: %d rows from %d addresses in %d sitemap(s); %d duplicate, %d of other hosts, %d not usable%s%s',
                               $report['source'], $report['rows'], $c['found'], $c['sitemaps'], $c['duplicates'], $c['other_hosts'], $c['skipped'],
                               $c['limited'] ? ', stopped at --limit' : '', $report['output'] !== '' ? ' -> ' . $report['output'] : '' ) );
    }
}

}
