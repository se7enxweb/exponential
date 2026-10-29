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
        return (array)$this->startup( '[stages:][apply][dry-run][only:*][skip:*][format:][no-backup][backup-dir:][force-order][fail-on-pending][force-no-backup][no-caption][force-no-caption][no-deploy][url:*][to:][repos:*][update:*][composer:][no-audit][allow-vendor-changes][no-verify][rollback-on-fail][post-caption][no-tests]', '', array(
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
            'no-caption' => 'Write without taking a caption first (the same as --no-backup); refused on production without --force-no-caption',
            'force-no-caption' => 'With --no-caption on a production installation: write without a caption all the same',
            'no-deploy' => 'Finish without exp:velocity deploy: extension autoloads and the INI, template and content caches only',
            'url' => 'The addresses the smoke check requests after the deploy (a comma list or repeated); default the front page of SiteURL',
            'to' => 'The code stage moves the checkouts to this tag, branch or commit (default: the upstream of the branch)',
            'repos' => 'More git checkouts the code stage moves, paths inside the installation (a comma list or repeated)',
            'update' => 'composer update these packages, with their dependencies (a comma list or repeated)',
            'composer' => 'The Composer binary or composer.phar (default: composer on the PATH, else composer.phar in the root)',
            'no-audit' => 'Skip the security audit of the libraries (composer audit) before and after',
            'allow-vendor-changes' => 'Install the libraries although Composer reports local changes in vendor/',
            'no-verify' => 'Skip the verification after the upgrade (exp:upgrade:verify --after)',
            'rollback-on-fail' => 'When the upgrade or its verification fails, roll back to the pre-upgrade caption at once',
            'post-caption' => 'After a passed verification, take a post caption: the last known good state',
            'no-tests' => 'Leave the test suite upgrade-smoke out of the verification',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $stages = $this->stageOption( $options );
        $run = $this->runOptions( $options );
        $started = time();
        $this->finish( $this->complete( $this->runAll( $stages, $run, $options ), $options, $started ), $format );
    }

    /**
     * The stages. The code stage first: when it moved the code, the stages after it run in a new process; this one
     * has the old classes and the old step registry loaded, the new one reads the new code (and so does Velocity only
     * after the finish stage restarts it).
     *
     * @return array the report
     */
    protected function runAll( array $stages, array $run, array $options )
    {
        $rest = array_values( array_diff( $stages, array( 'code' ) ) );
        if ( !$run['apply'] || !in_array( 'code', $stages, true ) || !$rest )
            return \expUpgrade::run( $stages, $run );
        $report = \expUpgrade::run( array( 'code' ), $run );
        if ( $report['exit'] !== \expUpgrade::EXIT_OK )
            return $report;
        if ( $report['summary']['applied'] === 0 )
            return \expUpgrade::mergeReports( $report, \expUpgrade::run( $rest, $run ) );
        $child = $this->continueInNewProcess( $rest, $options, $report['backup'] !== null );
        $report['warnings'][] = 'the code moved: the stages ' . implode( ', ', $rest ) . ' ran in a new process with the new code';
        return \expUpgrade::mergeReports( $report, $child );
    }

    /**
     * After an applied run: the verification (exp:upgrade:verify --after, in a new process so it uses the new code);
     * on a failure exit 1, the captions with their rollback commands, and with --rollback-on-fail the rollback to the
     * pre-upgrade caption; after a passed verification with --post-caption a post caption.
     *
     * @param array $report
     * @param array $options
     * @param int $started
     * @return array
     */
    protected function complete( array $report, array $options, $started )
    {
        if ( !self::applyRequested( $options ) )
            return $report;
        $changed = $report['summary']['applied'] > 0 || $report['summary']['failed'] > 0;
        if ( $changed && empty( $options['no-verify'] ) && $report['exit'] !== \expUpgrade::EXIT_USAGE )
        {
            $args = array( '--after', '--format=json', '--since=' . $started );
            if ( !empty( $options['url'] ) )
                $args[] = '--url=' . implode( ',', \expUpgrade::listOption( $options['url'] ) );
            if ( !empty( $options['no-tests'] ) )
                $args[] = '--no-tests';
            $verify = $this->subCommand( 'upgradeverify', $args );
            $report['verify'] = $verify && isset( $verify['after'] ) ? $verify['after']
                              : array( 'ok' => false, 'checks' => array( 'run' => array( 'ok' => false, 'detail' => 'the verification did not run', 'items' => array() ) ) );
            if ( !$report['verify']['ok'] )
            {
                $report['errors'][] = 'the verification after the upgrade failed: '
                                    . implode( ', ', array_keys( array_filter( $report['verify']['checks'], function ( $c ) { return $c['ok'] === false; } ) ) );
                $report['exit'] = max( $report['exit'], \expUpgrade::EXIT_FAILED );
            }
        }
        if ( $report['exit'] !== \expUpgrade::EXIT_OK && $report['exit'] !== \expUpgrade::EXIT_USAGE )
        {
            $report['captions'] = array_slice( \expUpgradeCaption::all( !empty( $options['backup-dir'] ) ? $options['backup-dir'] : null ), 0, 10 );
            $pre = !empty( $report['caption']['ok'] ) ? $report['caption']['id'] : '';
            if ( !empty( $options['rollback-on-fail'] ) && $pre !== '' )
            {
                $args = array( $pre, '--apply', '--format=json' );
                if ( !empty( $options['no-tests'] ) )
                    $args[] = '--no-tests';
                if ( !empty( $options['url'] ) )
                    $args[] = '--url=' . implode( ',', \expUpgrade::listOption( $options['url'] ) );
                $rollback = $this->subCommand( 'upgraderollback', $args );
                $report['rollback'] = array( 'caption' => $pre, 'errors' => $rollback ? $rollback['errors'] : array( 'the rollback did not run' ),
                                             'parts' => $rollback ? $rollback['rollback']['parts'] : array(),
                                             'safety_caption' => $rollback ? $rollback['rollback']['safety_caption'] : null );
            }
            else if ( !empty( $options['rollback-on-fail'] ) )
                $report['warnings'][] = '--rollback-on-fail: no pre-upgrade caption to roll back to';
        }
        else if ( !empty( $options['post-caption'] ) && $changed )
        {
            $context = new \expUpgradeContext( \eZDB::instance(), true, array( 'root' => rtrim( \eZSys::rootDir(), '/' ), 'mode' => 'apply',
                                                                              'repos' => isset( $options['repos'] ) ? \expUpgrade::listOption( $options['repos'] ) : array(),
                                                                              'backup-dir' => !empty( $options['backup-dir'] ) ? $options['backup-dir'] : null ) );
            $to = !empty( $options['to'] ) ? (string)$options['to'] : \expUpgrade::kernelVersion();
            $context->setOption( 'caption-to', $to );
            $post = \expUpgradeCaption::take( 'post', 'upgrade ' . ( isset( $report['caption']['label'] ) ? preg_replace( '/ \S+ pre$/', '', $report['caption']['label'] ) : "→$to" )
                                                      . ' ' . date( 'Ymd-His' ) . ' post', $context );
            $report['post_caption'] = array( 'id' => $post['id'], 'ok' => $post['ok'], 'source' => $post['source'], 'messages' => $post['messages'] );
        }
        return $report;
    }

    /**
     * Runs another exp:upgrade command in a new PHP process and returns its JSON report (null when it gave none).
     *
     * @param string $script bin/php/<script>.php
     * @param string[] $args
     * @return array|null
     */
    protected function subCommand( $script, array $args )
    {
        $access = class_exists( 'eZSiteAccess' ) ? \eZSiteAccess::current() : null;
        if ( is_array( $access ) && !empty( $access['name'] ) )
            $args[] = '--siteaccess=' . $access['name'];
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
            $args[] = '--allow-root-user';
        $root = rtrim( \eZSys::rootDir(), '/' );
        $process = proc_open( array_merge( array( PHP_BINARY, "$root/bin/php/$script.php" ), array_values( $args ) ),
                              array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => STDERR ), $pipes, $root );
        if ( !is_resource( $process ) )
            return null;
        fclose( $pipes[0] );
        $output = (string)stream_get_contents( $pipes[1] );
        fclose( $pipes[1] );
        proc_close( $process );
        $start = strpos( $output, '{' );
        $data = $start === false ? null : json_decode( substr( $output, $start ), true );
        return is_array( $data ) ? $data : null;
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
        // the first process verifies, after the merged run; the new one only runs the stages
        $args = array( '--stages=' . implode( ',', $stages ), '--apply', '--format=json', '--no-verify' );
        foreach ( array( 'force-order', 'fail-on-pending', 'force-no-backup', 'no-deploy', 'no-audit', 'allow-vendor-changes', 'no-backup', 'no-caption', 'force-no-caption', 'no-tests' ) as $flag )
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
