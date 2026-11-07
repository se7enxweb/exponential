<?php
/**
 * @description Start a URL map from the not-found log: the addresses visitors asked for and did not find, the most asked first, as rows to fill in
 * @alias exp:url:from-404
 *
 * File containing the exp:url:from-404 command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expurlfrom404 extends ExpurlBase
{
    protected function name()
    {
        return 'exp:url:from-404';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Start a URL map from the not-found log\n" .
                             "Reads the log of pages not found (var/log/404.log and its rotated files, written while site.ini\n" .
                             "[RequestLogSettings] NotFoundLog=enabled, the default), or with --log web server access logs, and\n" .
                             "writes every address that answered 404 or 410 once, the most asked first, as a row with an empty\n" .
                             "new_url and the hits in its note. Assets, bots and methods other than GET and HEAD are left out;\n" .
                             "-s keeps the lines of one siteaccess. The log hides query values, so only the path counts there.\n" .
                             "\n" .
                             "./console exp:url:from-404 --min-hits=3 --since=30d --output=var/tmp/not-found.csv\n" .
                             "./console exp:url:from-404 -s site --limit=200 --output=var/tmp/not-found.xlsx\n" .
                             "./console exp:url:from-404 --log=/var/log/nginx/access.log.1.gz --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[output:][output-format:][format:][min-hits:][since:][limit:][include-assets][include-bots][log:*]', '', array(
            'output' => 'The file to write (default the standard output; needed for xlsx and ods)',
            'output-format' => 'import, export: what the command prints, text (default) or json (the machine output exp-url/1); --format is the file\'s format there; from-sitemap, from-404, suggest: the format of the file written (csv, json, xlsx, ods, apache, nginx; default from --output, else csv)',
            'format' => 'import: the format of the file (csv, json, xlsx, ods, apache, nginx; default from its name or content); export: the format to write (default from --output, else csv); batches, undo, from-sitemap, from-404, suggest, test: text (default) or json (the machine output exp-url/1)',
            'min-hits' => 'from-404: only addresses asked for this many times or more (default 1)',
            'since' => 'from-404: only requests since this date (2026-11-01) or this long ago (30d, 12h, 2w)',
            'limit' => 'batches: how many of the last imports to list (default 20); from-sitemap, from-404, test: the most rows (default all)',
            'include-assets' => 'from-404: also images, scripts, style sheets, fonts and files below /var/, /design/ and /extension/',
            'include-bots' => 'from-404: also the requests of bots and crawlers',
            'log' => 'from-404: read these logs instead of this installation\'s not-found log: 404.log files or web server access logs in the common or combined format, .gz too (a comma list or repeated)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $printFormat = $this->outputFormat( $options );
        $mapFormat = $this->mapFormat( $options );
        $since = null;
        if ( isset( $options['since'] ) && $options['since'] )
        {
            $since = self::sinceTime( (string)$options['since'], time() );
            if ( $since === null )
                $this->usage( '--since must be a date (2026-11-01) or a span (30d, 12h, 2w), not ' . $options['since'] );
        }
        $logs = self::listOption( isset( $options['log'] ) ? $options['log'] : array() );
        $files = $logs ?: \expUrlMap404Source::logFiles();
        $report = array( 'mode' => 'from-404', 'logs' => $files, 'since' => $since !== null ? date( 'c', $since ) : null, 'errors' => array(), 'warnings' => array() );
        if ( !$logs && !\expRequestLog::enabled() )
            $report['warnings'][] = 'the not-found log is switched off (site.ini [RequestLogSettings] NotFoundLog=disabled): no new 404s are written';
        if ( !$files )
            $report['warnings'][] = 'there is no not-found log yet (' . \expRequestLog::logFile() . ')';
        foreach ( $logs as $log )
            if ( !is_file( $log ) || !is_readable( $log ) )
                $report['errors'][] = "the log $log cannot be read";
        if ( $report['errors'] )
            $this->finish( $report, $printFormat );
        $source = new \expUrlMap404Source( array( 'min_hits' => $this->intOption( $options, 'min-hits', 1, 1, 100000000 ), 'since' => $since,
                                                  'limit' => $this->intOption( $options, 'limit', 0, 0, 10000000 ),
                                                  'include_assets' => !empty( $options['include-assets'] ), 'include_bots' => !empty( $options['include-bots'] ),
                                                  'siteaccess' => $this->givenSiteaccess() ) );
        $rows = $source->rows( $files );
        $report['counts'] = $source->counts();
        $report['warnings'] = array_merge( $report['warnings'], $source->warnings() );
        $this->finishRows( $rows, $options, $report, $printFormat, $mapFormat );
    }

    protected function lines( array $report )
    {
        if ( !isset( $report['counts'] ) )
            return array();
        $c = $report['counts'];
        return array( sprintf( 'From 404: %d addresses from %d not-found lines in %d log(s) (%d lines); left out: %d assets, %d bots, %d other methods, %d outside --since, %d other siteaccesses%s',
                               $report['rows'], $c['not_found'], count( $report['logs'] ), $c['lines'], $c['assets'], $c['bots'], $c['methods'], $c['outside'], $c['siteaccess'],
                               $report['output'] !== '' ? ' -> ' . $report['output'] : '' ) );
    }
}

}
