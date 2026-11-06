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
        );
    }

    /** @return array option => help text for every option of the family */
    public static function optionHelp()
    {
        return array(
            'format' => 'import: the format of the file (csv, json, xlsx, ods, apache, nginx; default from its name or content); export: the format to write (default from --output, else csv); batches, undo: text (default) or json (the machine output exp-url/1)',
            'language' => 'import: the language of rows without one (default the siteaccess\'s); export: only this language',
            'on-conflict' => 'What happens to an old address that has another entry already: skip (default) or overwrite',
            'dry-run' => 'Plan and check only: list what would be imported, write nothing',
            'report' => 'Also write the plan, row by row, to this file (.json, or .csv)',
            'host' => 'An old host and the siteaccess it stands for, old-site.com=site (a comma list or repeated): for full addresses of a host that is not this installation\'s',
            'login' => 'Run as this user (recorded with the import and in the audit)',
            'kind' => 'Only these kinds: alias (the URL translator), wildcard, redirect (a comma list or repeated; default all)',
            'output' => 'The file to write (default the standard output; needed for xlsx and ods)',
            'include-node-aliases' => 'Also the custom URL aliases of nodes (target node:<id>), not only the URL translator\'s',
            'limit' => 'How many of the last imports to list (default 20)',
            'force' => 'Undo also the entries that changed after the import (default: they are kept)',
            'output-format' => 'import, export: what the command prints, text (default) or json (the machine output exp-url/1); --format is the file\'s format there',
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
