<?php
/**
 * @description Upgrade the installation in one call: every stage in order (dry run unless --apply)
 *
 * File containing the exp:upgrade command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Upgrade extends UpgradeBase
{
    protected function name()
    {
        return 'exp:upgrade';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Upgrade the installation in one call\n" .
                             "Runs the stages in their fixed order: code, libraries, database, scripts, finish. Without --apply\n" .
                             "it changes nothing and lists, step by step, what it would do. With --apply it makes a backup,\n" .
                             "applies the pending steps in order, records each one and stops at the first failure; the finish\n" .
                             "stage then deploys the changes and checks that the site answers.\n" .
                             "\n" .
                             "./console exp:upgrade                                  dry run of every stage\n" .
                             "./console exp:upgrade --apply\n" .
                             "./console exp:upgrade --apply --stages=database,scripts,finish\n" .
                             "./console exp:upgrade --format=json                    the machine output exp-upgrade/1\n" .
                             "\n" .
                             "Exit codes: 0 done or nothing pending, 1 a step failed (or pending with --fail-on-pending),\n" .
                             "2 usage error, 3 database not reachable, 4 internal error.\n" .
                             "Guide: doc/eng-us/guides/upgrading-with-the-console.md",
        ) + self::SCRIPT );
        return (array)$this->startup( '[stages:][apply][dry-run][only:*][skip:*][format:][no-backup][backup-dir:][force-order][fail-on-pending][force-no-backup][no-deploy][url:*][to:][repos:*][update:*][composer:][no-audit][allow-vendor-changes]', '', array(
            'stages' => 'Only these stages, a comma list of code, libraries, database, scripts, finish (in that order)',
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'only' => 'Only these steps (step ids, a comma list or repeated); an optional step runs only when named here',
            'skip' => 'Never these steps (step ids, a comma list or repeated)',
            'format' => 'text (default) or json (the machine output exp-upgrade/1)',
            'no-backup' => 'Write without making a backup first',
            'backup-dir' => 'Where the backup goes (default var/backup/upgrade); a directory <time> is made in it',
            'force-order' => 'Run a stage although an earlier stage has pending steps',
            'fail-on-pending' => 'Exit 1 when steps are still pending (for CI)',
            'force-no-backup' => 'With --no-backup on a production installation: write without a backup all the same',
            'no-deploy' => 'Finish without exp:velocity deploy: extension autoloads and the INI, template and content caches only',
            'url' => 'The addresses the smoke check requests after the deploy (a comma list or repeated); default the front page of SiteURL',
            'to' => 'The code stage moves the checkouts to this tag, branch or commit (default: the upstream of the branch)',
            'repos' => 'More git checkouts the code stage moves, paths inside the installation (a comma list or repeated)',
            'update' => 'composer update these packages, with their dependencies (a comma list or repeated)',
            'composer' => 'The Composer binary or composer.phar (default: composer on the PATH, else composer.phar in the root)',
            'no-audit' => 'Skip the security audit of the libraries (composer audit) before and after',
            'allow-vendor-changes' => 'Install the libraries although Composer reports local changes in vendor/',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $stages = $this->stageOption( $options );
        $run = $this->runOptions( $options );
        $rest = array_values( array_diff( $stages, array( 'code' ) ) );
        if ( !$run['apply'] || !in_array( 'code', $stages, true ) || !$rest )
        {
            $this->finish( \expUpgrade::run( $stages, $run ), $format );
            return;
        }
        // The code stage first. When it moved the code, the stages after it run in a new process: this one has the
        // old classes and the old step registry loaded, the new one reads the new code (and so does Velocity only
        // after the finish stage restarts it).
        $report = \expUpgrade::run( array( 'code' ), $run );
        if ( $report['exit'] !== \expUpgrade::EXIT_OK )
        {
            $this->finish( $report, $format );
            return;
        }
        if ( $report['summary']['applied'] === 0 )
        {
            $this->finish( \expUpgrade::mergeReports( $report, \expUpgrade::run( $rest, $run ) ), $format );
            return;
        }
        $child = $this->continueInNewProcess( $rest, $options, $report['backup'] !== null );
        $report['warnings'][] = 'the code moved: the stages ' . implode( ', ', $rest ) . ' ran in a new process with the new code';
        $this->finish( \expUpgrade::mergeReports( $report, $child ), $format );
    }

    /**
     * Runs the stages after the code stage as ./console exp:upgrade in a new PHP process, with the same options, and
     * returns its exp-upgrade/1 report. The backup was made already when $backedUp, so the new process makes none.
     *
     * @param string[] $stages
     * @param array $options the parsed options of this process
     * @param bool $backedUp
     * @return array
     */
    protected function continueInNewProcess( array $stages, array $options, $backedUp )
    {
        $args = self::childArguments( $stages, $options, $backedUp );
        $access = class_exists( 'eZSiteAccess' ) ? \eZSiteAccess::current() : null;
        if ( is_array( $access ) && !empty( $access['name'] ) )
            $args[] = '--siteaccess=' . $access['name'];
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
            $args[] = '--allow-root-user';
        $root = rtrim( \eZSys::rootDir(), '/' );
        $command = array_merge( array( PHP_BINARY, $root . '/bin/php/upgrade.php' ), $args );
        $process = proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => STDERR ), $pipes, $root );
        $report = \expUpgrade::report( 'apply', array( 'command' => $this->name() ) );
        if ( !is_resource( $process ) )
        {
            $report['errors'][] = 'cannot start the new process for the stages ' . implode( ', ', $stages );
            $report['exit'] = \expUpgrade::EXIT_INTERNAL;
            return $report;
        }
        fclose( $pipes[0] );
        $output = (string)stream_get_contents( $pipes[1] );
        fclose( $pipes[1] );
        $code = proc_close( $process );
        $start = strpos( $output, '{' );
        $child = $start === false ? null : json_decode( substr( $output, $start ), true );
        if ( !is_array( $child ) || !isset( $child['format'] ) )
        {
            $report['errors'][] = 'the new process for the stages ' . implode( ', ', $stages ) . " ended with exit $code without a report";
            $report['exit'] = \expUpgrade::EXIT_INTERNAL;
            return $report;
        }
        // the new process ran with --no-backup because this one made the backup
        $child['warnings'] = array_values( array_filter( $child['warnings'], function ( $w ) { return strpos( $w, 'no backup made' ) !== 0; } ) );
        return $child;
    }

    /**
     * The command line options for the new process. Pure.
     *
     * @param string[] $stages
     * @param array $options
     * @param bool $backedUp
     * @return string[]
     */
    public static function childArguments( array $stages, array $options, $backedUp )
    {
        $args = array( '--stages=' . implode( ',', $stages ), '--apply', '--format=json' );
        foreach ( array( 'force-order', 'fail-on-pending', 'force-no-backup', 'no-deploy', 'no-audit', 'allow-vendor-changes', 'no-backup' ) as $flag )
            if ( !empty( $options[$flag] ) )
                $args[] = "--$flag";
        if ( $backedUp && empty( $options['no-backup'] ) )
            array_push( $args, '--no-backup', '--force-no-backup' );
        foreach ( array( 'backup-dir', 'composer', 'to' ) as $value )
            if ( !empty( $options[$value] ) )
                $args[] = "--$value=" . $options[$value];
        foreach ( array( 'only', 'skip', 'url', 'update', 'repos' ) as $list )
            if ( !empty( $options[$list] ) )
                $args[] = "--$list=" . implode( ',', \expUpgrade::listOption( $options[$list] ) );
        return array_values( array_unique( $args ) );
    }
}

}
