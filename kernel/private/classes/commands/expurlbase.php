<?php
/**
 * File containing the shared code of the exp:url commands.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

/**
 * What every exp:url command (URL map import and export of the URL translator, wildcards and redirects) shares: the
 * options, the text and JSON output, the exit codes. The work is in the expUrlMap* classes (kernel/classes/urlmap/);
 * these commands only parse, call and print.
 *
 * The names, options, arguments, exit codes and the exp-url/1 output are a permanent contract (contract() and
 * arguments() are the snapshot tests/tests/kernel/classes/urlmap/expUrlCommandContractTest.php compares against):
 * options and keys may be added, never renamed or removed. The exit codes are exp:upgrade's: 0 done, 1 failed,
 * 2 usage, 3 the database unreachable, 4 internal error. The siteaccess is the script's own option -s/--siteaccess.
 */
abstract class ExpurlBase extends \Exponential\Runnable\Command
{
    /** The machine output's schema */
    const REPORT = 'exp-url/1';

    /**
     * Every command with its option configuration (permanent: only additions).
     *
     * @return array command => option config
     */
    public static function contract()
    {
        return array(
            'exp:url:import' => '[format:][language:][on-conflict:][dry-run][report:][host:*][login:][output-format:]',
            'exp:url:export' => '[format:][kind:*][language:][output:][include-node-aliases][output-format:]',
            'exp:url:batches' => '[limit:][format:]',
            'exp:url:undo' => '[force][format:][login:]',
            'exp:url:from-sitemap' => '[output:][output-format:][format:][limit:][only-host:*][any-host][keep-duplicates][full-urls][timeout:][insecure]',
            'exp:url:suggest' => '[output:][output-format:][format:][top:][fill-above:][min-score:][report:]',
            'exp:url:from-404' => '[output:][output-format:][format:][min-hits:][since:][limit:][include-assets][include-bots][log:*]',
            'exp:url:test' => '[base:][format:][report:][limit:][concurrency:][timeout:][max-redirects:][insecure]',
        );
    }

    /**
     * The arguments of the commands that take one (permanent).
     *
     * @return array command => argument config
     */
    public static function arguments()
    {
        return array(
            'exp:url:import' => '[file]',
            'exp:url:undo' => '[batch]',
            'exp:url:from-sitemap' => '[source]',
            'exp:url:suggest' => '[file]',
            'exp:url:test' => '[file]',
        );
    }

    /** @return array option => help text for every option of the family */
    public static function optionHelp()
    {
        return array(
            'format' => 'import: the format of the file (csv, json, xlsx, ods, apache, nginx; default from its name or content); export: the format to write (default from --output, else csv); batches, undo, from-sitemap, from-404, suggest, test: text (default) or json (the machine output exp-url/1)',
            'language' => 'import: the language of rows without one (default the siteaccess\'s); export: only this language',
            'on-conflict' => 'What happens to an old address that has another entry already: skip (default) or overwrite',
            'dry-run' => 'Plan and check only: list what would be imported, write nothing',
            'report' => 'import: also write the plan, row by row, to this file (.json, or .csv); suggest, test: also write every row with its suggestions or its result to this file (.json, or .csv)',
            'host' => 'An old host and the siteaccess it stands for, old-site.com=site (a comma list or repeated): for full addresses of a host that is not this installation\'s',
            'login' => 'Run as this user (recorded with the import and in the audit)',
            'kind' => 'Only these kinds: alias (the URL translator), wildcard, redirect (a comma list or repeated; default all)',
            'output' => 'The file to write (default the standard output; needed for xlsx and ods)',
            'include-node-aliases' => 'Also the custom URL aliases of nodes (target node:<id>), not only the URL translator\'s',
            'limit' => 'batches: how many of the last imports to list (default 20); from-sitemap, from-404, test: the most rows (default all)',
            'force' => 'Undo also the entries that changed after the import (default: they are kept)',
            'output-format' => 'import, export: what the command prints, text (default) or json (the machine output exp-url/1); --format is the file\'s format there; from-sitemap, from-404, suggest: the format of the file written (csv, json, xlsx, ods, apache, nginx; default from --output, else csv)',
            'only-host' => 'from-sitemap: keep only the addresses of this host (a comma list or repeated; default the host of the sitemap address, or of its first address)',
            'any-host' => 'from-sitemap: keep the addresses of every host',
            'keep-duplicates' => 'from-sitemap: keep an address listed more than once (default: once)',
            'full-urls' => 'from-sitemap: write the old addresses with scheme and host (default: the path and query, as an import on this site needs them)',
            'timeout' => 'from-sitemap, test: the seconds a request may take (default 15)',
            'insecure' => 'from-sitemap, test: do not verify TLS certificates (for test hosts with a self-signed one)',
            'top' => 'suggest: the suggestions per row (default 3)',
            'fill-above' => 'suggest: write the best suggestion into new_url when it scores this much or more (0 to 100; never a tie; default: never)',
            'min-score' => 'suggest: the lowest score suggested (0 to 100, default 30)',
            'min-hits' => 'from-404: only addresses asked for this many times or more (default 1)',
            'since' => 'from-404: only requests since this date (2026-11-01) or this long ago (30d, 12h, 2w)',
            'include-assets' => 'from-404: also images, scripts, style sheets, fonts and files below /var/, /design/ and /extension/',
            'include-bots' => 'from-404: also the requests of bots and crawlers',
            'log' => 'from-404: read these logs instead of this installation\'s not-found log: 404.log files or web server access logs in the common or combined format, .gz too (a comma list or repeated)',
            'base' => 'test: the site to test, https://www.example.com (needed)',
            'concurrency' => 'test: the requests at the same time (default 8)',
            'max-redirects' => 'test: the redirects followed before a row counts as an error (default 5)',
        );
    }

    /** @return string the command's name, e.g. exp:url:import */
    abstract protected function name();

    /**
     * Starts the script: $this->script( array( 'description' => "...", ... ) ) and $this->startup( '<config>',
     * '<arguments>', <help> ), written out literally in each command, so ./console exp:help:examples reads the help
     * text and the options from the code.
     *
     * @return array the options
     */
    abstract protected function begin();

    /**
     * The command's work: parse with begin(), call the expUrlMap* classes, print with finish(). Every command
     * overrides it; this one only starts the script and ends with a usage error.
     */
    public function run()
    {
        $this->begin();
        $this->usage( $this->name() . ' has no work of its own' );
    }

    /** The script settings every command shares besides its description. */
    const SCRIPT = array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true );

    /**
     * The output format text or json; after an error the command ends with exit 2.
     *
     * @param array $options
     * @return string
     */
    protected function outputFormat( array $options, $name = 'format' )
    {
        $format = isset( $options[$name] ) && $options[$name] ? (string)$options[$name] : 'text';
        if ( !in_array( $format, array( 'text', 'json' ), true ) )
            $this->usage( "--$name must be text or json, not $format" );
        return $format;
    }

    /**
     * A usage error: the message and exit 2.
     *
     * @param string $message
     */
    protected function usage( $message )
    {
        $this->error( 'FAIL: ' . $message );
        $this->shutdown( \expUpgrade::EXIT_USAGE );
    }

    /**
     * A list option (a comma list or repeated) as its values. Pure.
     *
     * @param mixed $value
     * @return string[]
     */
    public static function listOption( $value )
    {
        $out = array();
        foreach ( (array)$value as $item )
            foreach ( explode( ',', (string)$item ) as $part )
                if ( trim( $part ) !== '' )
                    $out[] = trim( $part );
        return array_values( array_unique( $out ) );
    }

    /**
     * A whole-number option within limits; outside them a usage error.
     *
     * @param array $options
     * @param string $name
     * @param int|null $default
     * @param int $min
     * @param int $max
     * @return int|null
     */
    protected function intOption( array $options, $name, $default, $min, $max )
    {
        if ( !isset( $options[$name] ) || $options[$name] === false || $options[$name] === null || $options[$name] === '' )
            return $default;
        $value = (string)$options[$name];
        if ( !preg_match( '/^\d+$/', $value ) || (int)$value < $min || (int)$value > $max )
            $this->usage( "--$name must be a whole number from $min to $max, not $value" );
        return (int)$value;
    }

    /**
     * The time a --since value stands for: a date (2026-11-01, 2026-11-01 08:00) or a span back from now (30d, 12h,
     * 2w, 45m); null when it is neither. Pure.
     *
     * @param string $value
     * @param int $now
     * @return int|null
     */
    public static function sinceTime( $value, $now )
    {
        $value = trim( (string)$value );
        if ( preg_match( '/^(\d+)\s*([mhdw])$/i', $value, $m ) )
        {
            $unit = array( 'm' => 60, 'h' => 3600, 'd' => 86400, 'w' => 604800 );
            return (int)$now - (int)$m[1] * $unit[strtolower( $m[2] )];
        }
        if ( preg_match( '/^\d{4}-\d{2}-\d{2}(?:[ T]\d{2}:\d{2}(?::\d{2})?)?$/', $value ) )
        {
            $time = strtotime( $value );
            return $time === false ? null : $time;
        }
        return null;
    }

    /**
     * The format of the map a helper writes: --output-format, else from --output's name, else csv; a usage error for
     * one that cannot be written, or xlsx and ods without a file.
     *
     * @param array $options
     * @return string
     */
    protected function mapFormat( array $options )
    {
        $output = isset( $options['output'] ) && $options['output'] ? (string)$options['output'] : '';
        $format = isset( $options['output-format'] ) && $options['output-format'] ? strtolower( (string)$options['output-format'] )
                : ( $output !== '' ? (string)\expUrlMapFormats::detect( $output ) : 'csv' );
        if ( $format === '' )
            $format = 'csv';
        if ( !in_array( $format, \expUrlMapFormats::writable(), true ) )
            $this->usage( '--output-format must be one of ' . implode( ', ', \expUrlMapFormats::writable() ) . ", not $format" );
        if ( $output === '' && in_array( $format, array( 'xlsx', 'ods' ), true ) )
            $this->usage( "--output-format=$format needs --output=<file>" );
        return $format;
    }

    /**
     * Ends a helper that made rows: writes them to --output and prints the report, or, without --output, prints the
     * map itself (text) or the report with the rows (json).
     *
     * @param \expUrlMapRow[] $rows
     * @param array $options
     * @param array $report
     * @param string $printFormat text or json
     * @param string $mapFormat
     */
    protected function finishRows( array $rows, array $options, array $report, $printFormat, $mapFormat )
    {
        $output = isset( $options['output'] ) && $options['output'] ? (string)$options['output'] : '';
        $report += array( 'errors' => array(), 'warnings' => array() );
        $report['output'] = $output;
        $report['output_format'] = $mapFormat;
        $report['rows'] = count( $rows );
        try
        {
            if ( $report['errors'] )
                $report['map'] = array();
            elseif ( $output !== '' )
                \expUrlMapFormats::writeFile( $output, $rows, $mapFormat );
            elseif ( $printFormat === 'json' )
                $report['map'] = array_map( function ( $row ) { return $row->toArray(); }, $rows );
            else
            {
                // the map itself is the output; the summary and the warnings go to the error output
                $this->cli()->output( \expUrlMapFormats::writer( $mapFormat )->writeString( $rows ), false );
                foreach ( $this->lines( $report ) as $line )
                    $this->error( $line );
                foreach ( $report['warnings'] as $w )
                    $this->error( 'WARN ' . $w );
                $this->shutdown( \expUpgrade::EXIT_OK );
                return;
            }
        }
        catch ( \expUrlMapFormatException $e )
        {
            $report['errors'][] = $e->getMessage();
        }
        $this->finish( $report, $printFormat );
    }

    /**
     * Writes a helper's report: .json as a list of objects ({"schema": "exp-url-report/1", "rows": [...]}), any other
     * name as CSV with the given columns (a list of suggestions as "path (score); ...", a redirect chain as
     * "301 url > 200 url").
     *
     * @param string $path
     * @param array[] $entries
     * @param string[] $columns
     * @throws \RuntimeException when it cannot be written
     */
    public static function writeReport( $path, array $entries, array $columns )
    {
        if ( strtolower( pathinfo( (string)$path, PATHINFO_EXTENSION ) ) === 'json' )
            $data = json_encode( array( 'schema' => 'exp-url-report/1', 'rows' => array_values( $entries ) ),
                                 JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR ) . "\n";
        else
        {
            $data = implode( ',', $columns ) . "\n";
            foreach ( $entries as $entry )
            {
                $fields = array();
                foreach ( $columns as $column )
                    $fields[] = \expUrlMapCsvWriter::field( self::reportValue( isset( $entry[$column] ) ? $entry[$column] : '' ), ',' );
                $data .= implode( ',', $fields ) . "\n";
            }
        }
        if ( @file_put_contents( (string)$path, $data ) === false )
            throw new \RuntimeException( "The report $path cannot be written" );
    }

    /**
     * A report value as text. Pure.
     *
     * @param mixed $value
     * @return string
     */
    public static function reportValue( $value )
    {
        if ( is_bool( $value ) )
            return $value ? 'yes' : 'no';
        if ( !is_array( $value ) )
            return (string)$value;
        $parts = array();
        foreach ( $value as $item )
        {
            if ( is_array( $item ) && isset( $item['path'], $item['score'] ) )
                $parts[] = $item['path'] . ' (' . $item['score'] . ')';
            elseif ( is_array( $item ) && isset( $item['status'], $item['url'] ) )
                $parts[] = $item['status'] . ' ' . $item['url'];
            elseif ( is_array( $item ) && isset( $item['status'] ) )
                $parts[] = (string)$item['status'];
            elseif ( is_scalar( $item ) )
                $parts[] = (string)$item;
        }
        return implode( isset( $value[0]['url'] ) ? ' > ' : '; ', $parts );
    }

    /**
     * Reads the map a helper works on (any format exp:url:import reads, found from its name or content); a failure
     * ends the command with exit 1.
     *
     * @param string $file
     * @param string $printFormat
     * @return \expUrlMapRow[]
     */
    protected function readMap( $file, $printFormat )
    {
        if ( $file === '' )
            $this->usage( 'name the file: ' . $this->name() . ' <file>' );
        if ( !is_file( $file ) || !is_readable( $file ) )
            $this->finish( array( 'mode' => 'read', 'file' => $file, 'errors' => array( "the file $file cannot be read" ) ), $printFormat );
        try
        {
            return \expUrlMapFormats::readFile( $file, \expUrlMapFormats::detect( $file, (string)file_get_contents( $file, false, null, 0, 65536 ) ) );
        }
        catch ( \expUrlMapFormatException $e )
        {
            $this->finish( array( 'mode' => 'read', 'file' => $file, 'errors' => array( $e->getMessage() ) ), $printFormat );
        }
        return array();
    }

    /**
     * The siteaccess given with -s/--siteaccess, '' without one.
     *
     * @return string
     */
    protected function givenSiteaccess()
    {
        $given = $this->script()->usedSiteAccess();
        return is_string( $given ) ? $given : '';
    }

    /**
     * Signs in the user of --login (a usage error when there is none of that name).
     *
     * @param array $options
     * @return int the user id, 0 without --login
     */
    protected function login( array $options )
    {
        $login = isset( $options['login'] ) && $options['login'] !== false ? trim( (string)$options['login'] ) : '';
        if ( $login === '' )
            return 0;
        $user = \eZUser::fetchByName( $login );
        if ( !$user instanceof \eZUser )
            $this->usage( "--login names no user: $login" );
        \eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ), \eZUser::NO_SESSION_REGENERATE );
        return (int)$user->attribute( 'contentobject_id' );
    }

    /**
     * Ends with exit 3 when the database is not reachable.
     *
     * @param string $format
     * @return \eZDBInterface
     */
    protected function database( $format )
    {
        $problem = '';
        try
        {
            $db = \eZDB::instance();
        }
        catch ( \Throwable $e )
        {
            $db = null;
            $problem = ': ' . $e->getMessage();
        }
        if ( !$db || !$db->isConnected() )
            $this->finish( array( 'mode' => 'unreachable', 'errors' => array( 'the database is not reachable' . $problem ), 'warnings' => array(),
                                  'exit' => \expUpgrade::EXIT_UNREACHABLE ), $format );
        return $db;
    }

    /**
     * Prints a report and ends with its exit code.
     *
     * @param array $report
     * @param string $format
     */
    protected function finish( array $report, $format )
    {
        $report = array( 'schema' => self::REPORT, 'command' => $this->name() ) + $report + array( 'errors' => array(), 'warnings' => array() );
        if ( !isset( $report['exit'] ) )
            $report['exit'] = !empty( $report['errors'] ) ? \expUpgrade::EXIT_FAILED : \expUpgrade::EXIT_OK;
        if ( $format === 'json' )
            $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR ) );
        else
        {
            foreach ( $this->lines( $report ) as $line )
                $this->output( $line );
            foreach ( $report['warnings'] as $w )
                $this->output( 'WARN ' . $w );
            foreach ( $report['errors'] as $e )
                $this->error( 'FAIL: ' . $e );
            $this->output( (int)$report['exit'] === \expUpgrade::EXIT_OK ? 'PASS' : 'FAIL (exit ' . (int)$report['exit'] . ')' );
        }
        $this->shutdown( (int)$report['exit'] );
    }

    /**
     * The command's lines of a report.
     *
     * @param array $report
     * @return string[]
     */
    protected function lines( array $report )
    {
        return array();
    }

    /**
     * The lines of a plan and an import's result. Pure.
     *
     * @param array $report counts (the plan's), result (counts, batch_id, items), rows (the items not new or
     *                      unchanged), file, dry_run
     * @return string[]
     */
    public static function planLines( array $report )
    {
        if ( !isset( $report['counts'] ) )
            return array();
        $c = $report['counts'];
        $lines = array( sprintf( 'URL map %s: %s (%d rows)', !empty( $report['dry_run'] ) ? 'dry run' : 'import', isset( $report['file'] ) ? $report['file'] : '', $c['rows'] ) );
        $lines[] = sprintf( '  Plan:   %d new, %d changed, %d unchanged, %d duplicate, %d conflict, %d broken target, %d unknown host, %d not importable',
                            $c['new'], $c['changed'], $c['unchanged'], $c['duplicate'], $c['conflict'], $c['broken-target'], $c['unknown-host'], $c['not-importable'] );
        $lines[] = sprintf( '  Action: %d to create, %d to update, %d skipped', $c['create'], $c['update'], $c['skip'] );
        if ( !empty( $report['result'] ) )
        {
            $r = $report['result']['counts'];
            $lines[] = sprintf( '  Done:   %d created, %d updated, %d skipped, %d failed%s', $r['created'], $r['updated'], $r['skipped'], $r['failed'],
                                !empty( $report['result']['batch_id'] ) ? '; batch ' . $report['result']['batch_id'] . ' (exp:url:undo ' . $report['result']['batch_id'] . ' undoes it)' : '' );
        }
        foreach ( array_slice( isset( $report['rows'] ) ? $report['rows'] : array(), 0, 50 ) as $item )
            $lines[] = sprintf( '  %-14s line %-5d %s -> %s%s', $item['state'], $item['line'], $item['old_url'], $item['new_url'],
                                $item['messages'] ? ': ' . implode( '; ', $item['messages'] ) : '' );
        if ( isset( $report['rows'] ) && count( $report['rows'] ) > 50 )
            $lines[] = '  ... ' . ( count( $report['rows'] ) - 50 ) . ' more (--report writes every row)';
        return $lines;
    }
}

}
