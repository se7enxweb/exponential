<?php
/**
 * File containing the shared code of the exp:upgrade commands.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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
    const RUN_OPTIONS = '[apply][dry-run][only:*][skip:*][format:][no-backup][backup-dir:][force-order][fail-on-pending][force-no-backup][no-caption][force-no-caption]';

    /** Options of the code stage. */
    const CODE_OPTIONS = '[to:][repos:*]';

    /** Options of the libraries stage. */
    const LIBRARIES_OPTIONS = '[update:*][composer:][no-audit][allow-vendor-changes]';

    /** Options of the finish stage. */
    const FINISH_OPTIONS = '[no-deploy][url:*]';

    /**
     * Every command with its option configuration (permanent: only additions).
     *
     * @return array command => option config
     */
    public static function contract()
    {
        return array(
            'exp:upgrade' => '[stages:]' . self::RUN_OPTIONS . self::FINISH_OPTIONS . self::CODE_OPTIONS . self::LIBRARIES_OPTIONS . '[no-verify][rollback-on-fail][post-caption][no-tests]',
            'exp:upgrade:status' => '[stages:][only:*][format:][fail-on-pending]',
            'exp:upgrade:steps' => '[stage:][engine:][format:]',
            'exp:upgrade:code' => self::RUN_OPTIONS . self::CODE_OPTIONS,
            'exp:upgrade:libraries' => self::RUN_OPTIONS . self::LIBRARIES_OPTIONS,
            'exp:upgrade:database' => self::RUN_OPTIONS,
            'exp:upgrade:scripts' => self::RUN_OPTIONS,
            'exp:upgrade:finish' => self::RUN_OPTIONS . self::FINISH_OPTIONS,
            'exp:upgrade:verify' => '[stages:][format:][after][url:*][no-tests][since:]',
            'exp:upgrade:captions' => '[format:][verify:][prune][apply][dry-run][backup-dir:]',
            'exp:upgrade:backup' => '[backup-dir:][format:]',
            'exp:upgrade:rollback' => '[apply][dry-run][parts:][backup-dir:][format:][no-caption][force-no-caption][no-verify][url:*][no-tests][allow-vendor-changes]',
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
            'no-deploy' => 'Finish without exp:velocity deploy: extension autoloads and the INI, template and content caches only',
            'url' => 'The addresses the smoke check requests after the deploy (a comma list or repeated); default the front page of SiteURL',
            'force-no-backup' => 'With --no-backup on a production installation: write without a backup all the same',
            'to' => 'The code stage moves the checkouts to this tag, branch or commit (default: the upstream of the branch)',
            'repos' => 'More git checkouts the code stage moves, paths inside the installation (a comma list or repeated)',
            'update' => 'composer update these packages, with their dependencies (a comma list or repeated)',
            'composer' => 'The Composer binary or composer.phar (default: composer on the PATH, else composer.phar in the root)',
            'no-audit' => 'Skip the security audit of the libraries (composer audit) before and after',
            'allow-vendor-changes' => 'Install the libraries although Composer reports local changes in vendor/',
            'parts' => 'Only these parts, a comma list of database, storage, settings, libraries, code (default: what the caption has)',
            'no-caption' => 'Write without taking a caption first (the same as --no-backup); refused on production without --force-no-caption',
            'force-no-caption' => 'With --no-caption on a production installation: write without a caption all the same',
            'no-verify' => 'Skip the verification after the upgrade (exp:upgrade:verify --after)',
            'rollback-on-fail' => 'When the upgrade or its verification fails, roll back to the pre-upgrade caption at once',
            'post-caption' => 'After a passed verification, take a post caption: the last known good state',
            'no-tests' => 'Leave the test suite upgrade-smoke out of the verification',
            'after' => 'The verification after an upgrade: steps, smoke addresses, tests, error log, autoloads',
            'since' => 'The error log is read from this moment on: a Unix time (default an hour ago)',
            'verify' => 'Check this caption: its checksums and a restore of its dump into a throwaway database',
            'prune' => 'Remove the captions of exp:upgrade beyond [UpgradeSettings] CaptionKeep, CaptionKeepDays, CaptionKeepUpgrades',
        );
    }

    /** @return string the command's name, e.g. exp:upgrade:status */
    abstract protected function name();

    /**
     * The command's work: by default the stages of --stages, as exp:upgrade runs them; the commands of one stage and
     * the reading commands override it.
     */
    public function run()
    {
        $this->runStages();
    }

    /**
     * Starts the script: $this->script( array( 'description' => "...", ... ) ) and $this->startup( '<config>', '',
     * <help> ), written out literally in each command, so ./console exp:help:examples reads the help text and the
     * options from the code. The option configuration must equal contract() and every help text optionHelp()
     * (expUpgradeCommandContractTest checks both).
     *
     * @return array the options
     */
    abstract protected function begin();

    /** The script settings every command shares besides its description. */
    const SCRIPT = array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true );

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
            'force-no-backup' => !empty( $options['force-no-backup'] ),
            'no-caption' => !empty( $options['no-caption'] ),
            'force-no-caption' => !empty( $options['force-no-caption'] ),
            'no-deploy' => !empty( $options['no-deploy'] ),
            'url' => isset( $options['url'] ) ? \expUpgrade::listOption( $options['url'] ) : array(),
            'to' => isset( $options['to'] ) && $options['to'] ? (string)$options['to'] : '',
            'repos' => isset( $options['repos'] ) ? \expUpgrade::listOption( $options['repos'] ) : array(),
            'update' => isset( $options['update'] ) ? \expUpgrade::listOption( $options['update'] ) : array(),
            'composer' => isset( $options['composer'] ) && $options['composer'] ? (string)$options['composer'] : '',
            'no-audit' => !empty( $options['no-audit'] ),
            'allow-vendor-changes' => !empty( $options['allow-vendor-changes'] ),
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
        if ( !empty( $report['caption'] ) )
            $cli->output( 'Caption: ' . $report['caption']['id'] . ' (' . $report['caption']['source'] . ') ' . $report['caption']['label'] . ( $report['caption']['ok'] ? '' : ' (FAILED)' ) );
        else if ( $report['backup'] )
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
        if ( !empty( $report['verify'] ) )
        {
            $cli->output( 'Verification after the upgrade: ' . ( $report['verify']['ok'] ? 'PASS' : 'FAIL' ) );
            $this->printVerify( $report['verify'] );
            $cli->output();
        }
        if ( !empty( $report['post_caption'] ) )
            $cli->output( 'Post caption: ' . $report['post_caption']['id'] . ( $report['post_caption']['ok'] ? '' : ' (FAILED: ' . implode( '; ', $report['post_caption']['messages'] ) . ')' ) );
        if ( !empty( $report['rollback'] ) && isset( $report['rollback']['caption'] ) )
            $cli->output( 'Rolled back to ' . $report['rollback']['caption'] . ': ' . ( empty( $report['rollback']['errors'] ) ? 'done' : 'FAILED: ' . implode( '; ', $report['rollback']['errors'] ) ) );
        if ( !empty( $report['captions'] ) )
        {
            $cli->output( 'The captions to roll back to, newest first:' );
            $this->printCaptions( $report['captions'] );
            $cli->output();
        }
        $s = $report['summary'];
        $cli->output( sprintf( 'Summary: %d pending, %d done, %d not needed, %d failed, %d unknown%s', $s['pending'], $s['done'], $s['not-needed'],
                               $s['failed'], $s['unknown'], $s['applied'] ? ", {$s['applied']} applied now" : '' ) );
        if ( $report['exit'] === \expUpgrade::EXIT_OK )
            $cli->output( 'PASS' );
        else
            $cli->output( 'FAIL (exit ' . $report['exit'] . ')' );
    }

    /**
     * The result of expUpgradeVerifyAfter as text.
     *
     * @param array $after
     */
    protected function printVerify( array $after )
    {
        $words = array( 'PASS', 'FAIL', 'SKIP' );
        foreach ( $after['checks'] as $name => $check )
        {
            $word = $check['ok'] === true ? $words[0] : ( $check['ok'] === false ? $words[1] : $words[2] );
            $this->output( sprintf( '  %-5s %-9s %s', $word, $name, $check['detail'] ) );
            foreach ( array_slice( $check['items'], 0, 10 ) as $item )
                $this->output( '              ' . $item );
        }
    }

    /**
     * Captions as text, newest first, with the rollback command of each.
     *
     * @param array[] $captions expUpgradeCaption::all()
     * @param int $max
     */
    protected function printCaptions( array $captions, $max = 0 )
    {
        if ( !$captions )
        {
            $this->output( '  no captions' );
            return;
        }
        foreach ( $max ? array_slice( $captions, 0, $max ) : $captions as $c )
        {
            $this->output( sprintf( '  %-24s %-11s %s  %9s  %-10s %s', $c['id'], $c['kind'], date( 'Y-m-d H:i', $c['created'] ),
                                    class_exists( 'BackupManager' ) ? \BackupManager::formatBytes( $c['size'] ) : $c['size'] . ' B',
                                    $c['engine'] ?: '-', $c['label'] ) );
            $this->output( '      ' . $c['rollback'] );
        }
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
