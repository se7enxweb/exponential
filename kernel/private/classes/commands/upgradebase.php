<?php
/**
 * File containing the shared code of the exp:upgrade commands.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

/**
 * What every exp:upgrade command shares: the options, the text and JSON output, the exit codes. The work is in
 * expUpgrade (kernel/classes/upgrade/); these commands only parse, call and print.
 *
 * The names, options, exit codes and the exp-upgrade/1 output are a permanent contract (contract() is the snapshot
 * tests/tests/kernel/classes/upgrade/expUpgradeCommandContractTest.php compares against): options and keys may be
 * added, never renamed or removed.
 */
abstract class UpgradeBase extends \Exponential\Runnable\Command
{
    /** Options shared by the commands that run stages. */
    const RUN_OPTIONS = '[apply][dry-run][only:*][skip:*][format:][no-backup][backup-dir:][force-order][fail-on-pending]';

    /**
     * Every command with its option configuration (permanent: only additions).
     *
     * @return array command => option config
     */
    public static function contract()
    {
        return array(
            'exp:upgrade' => '[stages:]' . self::RUN_OPTIONS,
            'exp:upgrade:status' => '[stages:][only:*][format:][fail-on-pending]',
            'exp:upgrade:steps' => '[stage:][engine:][format:]',
            'exp:upgrade:database' => self::RUN_OPTIONS,
            'exp:upgrade:scripts' => self::RUN_OPTIONS,
            'exp:upgrade:verify' => '[stages:][format:]',
            'exp:upgrade:backup' => '[backup-dir:][format:]',
        );
    }

    /** @return array option => help text for every option of the family */
    public static function optionHelp()
    {
        return array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'stages' => 'Only these stages, a comma list of code, libraries, database, scripts, finish (in that order)',
            'stage' => 'Only the steps of this stage',
            'only' => 'Only these steps (step ids, a comma list or repeated); an optional step runs only when named here',
            'skip' => 'Never these steps (step ids, a comma list or repeated)',
            'format' => 'text (default) or json (the machine output exp-upgrade/1)',
            'no-backup' => 'Write without making a backup first',
            'backup-dir' => 'Where the backup goes (default var/backup/upgrade); a directory <time> is made in it',
            'force-order' => 'Run a stage although an earlier stage has pending steps',
            'fail-on-pending' => 'Exit 1 when steps are still pending (for CI)',
            'engine' => 'Only the steps for this engine: mysql, postgresql, sqlite, oracle, mongodb',
        );
    }

    /** @return string the command's name, e.g. exp:upgrade:status */
    abstract protected function name();

    /** @return string the description shown by --help */
    abstract protected function description();

    /**
     * Starts the script with the command's options from contract().
     *
     * @return array the options
     */
    protected function begin()
    {
        $this->script( array(
            'description' => $this->description(),
            'use-session' => false,
            'use-modules' => true,
            'use-extensions' => true,
        ) );
        $contract = self::contract();
        $config = $contract[$this->name()];
        $help = array();
        preg_match_all( '/\[([a-z-]+)/', $config, $m );
        foreach ( $m[1] as $option )
            $help[$option] = self::optionHelp()[$option];
        return (array)$this->startup( $config, '', $help );
    }

    /**
     * The output format, or null (after an error, exit 2) when it is not text or json.
     *
     * @param array $options
     * @return string|null
     */
    protected function format( array $options )
    {
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'text';
        if ( !in_array( $format, array( 'text', 'json' ), true ) )
        {
            $this->usage( "--format must be text or json, not $format" );
            return null;
        }
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
     * The options of a run, from the command line.
     *
     * @param array $options
     * @return array|null null after a usage error
     */
    protected function runOptions( array $options )
    {
        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $this->usage( '--apply and --dry-run exclude each other; without --apply nothing is changed' );
            return null;
        }
        $cli = $this->cli();
        $quiet = $this->isQuiet();
        return array(
            'apply' => $apply,
            'only' => isset( $options['only'] ) ? \expUpgrade::listOption( $options['only'] ) : array(),
            'skip' => isset( $options['skip'] ) ? \expUpgrade::listOption( $options['skip'] ) : array(),
            'no-backup' => !empty( $options['no-backup'] ),
            'backup-dir' => !empty( $options['backup-dir'] ) ? (string)$options['backup-dir'] : null,
            'force-order' => !empty( $options['force-order'] ),
            'fail-on-pending' => !empty( $options['fail-on-pending'] ),
            'command' => $this->name(),
            'progress' => function ( $line ) use ( $cli, $quiet ) {
                if ( !$quiet )
                    fwrite( STDERR, '  ' . $line . "\n" );
            },
        );
    }

    /**
     * The stages of a --stages option, or null after a usage error.
     *
     * @param array $options
     * @return string[]|null
     */
    protected function stageOption( array $options )
    {
        $errors = array();
        $stages = \expUpgrade::stages( isset( $options['stages'] ) ? $options['stages'] : null, $errors );
        if ( $errors )
        {
            $this->usage( implode( '; ', $errors ) );
            return null;
        }
        return $stages;
    }

    /**
     * Prints a report and ends with its exit code.
     *
     * @param array $report
     * @param string $format
     */
    protected function finish( array $report, $format )
    {
        if ( $format === 'json' )
            $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        else
            $this->printText( $report );
        $this->shutdown( \expUpgrade::exitCode( $report ) );
    }

    /**
     * The report as text.
     *
     * @param array $report
     */
    protected function printText( array $report )
    {
        $cli = $this->cli();
        $modes = array(
            'status' => 'status',
            'dry-run' => 'dry run, nothing is changed (--apply makes the changes)',
            'apply' => 'apply',
            'verify' => 'verify',
        );
        $cli->output( 'Exponential upgrade: ' . ( isset( $modes[$report['mode']] ) ? $modes[$report['mode']] : $report['mode'] ) );
        $i = $report['installation'];
        if ( $i['engine'] !== '' )
            $cli->output( sprintf( 'Code %s, database %s%s', $i['kernel_version'], $i['engine'],
                                   $i['database_version'] !== null && $i['database_version'] !== '' ? ', version ' . $i['database_version'] : '' ) );
        if ( $report['backup'] )
            $cli->output( 'Backup: ' . $report['backup']['dir'] . ( $report['backup']['ok'] ? '' : ' (FAILED)' ) );
        foreach ( $report['stages'] as $stage )
        {
            $cli->output();
            $cli->output( sprintf( 'Stage %-12s %s%s', $stage['stage'], $stage['steps'] ? $stage['state'] : 'no steps',
                                   $stage['pending'] ? ' (' . $stage['pending'] . ' pending)' : '' ) );
            foreach ( $stage['steps'] as $step )
            {
                $label = $step['action'] === 'applied' ? 'applied' : ( $step['action'] === 'blocked' ? 'blocked' : $step['state'] );
                $cli->output( sprintf( '  %-11s %-36s %s', $label, $step['id'], $step['detail'] ) );
                foreach ( $step['plan'] as $line )
                    $cli->output( '              would ' . $line );
                foreach ( $step['messages'] as $line )
                    $cli->output( '              ' . $line );
                if ( $step['drift'] )
                    $cli->output( '              DRIFT: recorded as done, but missing again' );
            }
        }
        $cli->output();
        foreach ( $report['warnings'] as $w )
            $cli->output( 'WARN ' . $w );
        foreach ( $report['errors'] as $e )
            $cli->error( 'FAIL: ' . $e );
        $s = $report['summary'];
        $cli->output( sprintf( 'Summary: %d pending, %d done, %d not needed, %d failed, %d unknown%s', $s['pending'], $s['done'], $s['not-needed'],
                               $s['failed'], $s['unknown'], $s['applied'] ? ", {$s['applied']} applied now" : '' ) );
        if ( $report['exit'] === \expUpgrade::EXIT_OK )
            $cli->output( 'PASS' );
        else
            $cli->output( 'FAIL (exit ' . $report['exit'] . ')' );
    }

    /**
     * Runs stages from the command line: options, the run, the output.
     *
     * @param string[]|null $stages fixed stages, or null to take --stages
     */
    protected function runStages( $stages = null )
    {
        $options = $this->begin();
        $format = $this->format( $options );
        if ( $format === null )
            return;
        if ( $stages === null )
            $stages = $this->stageOption( $options );
        if ( $stages === null )
            return;
        $run = $this->runOptions( $options );
        if ( $run === null )
            return;
        $this->finish( \expUpgrade::run( $stages, $run ), $format );
    }
}

}
