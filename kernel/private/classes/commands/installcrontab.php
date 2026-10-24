<?php
/**
 * The code of bin/php/installcrontab.php.
 * @description Show, add or remove the cron job of the installation: the crontab lines that run its cronjob parts (--dry-run says what would change)
 * Guide: doc/eng-us/bc/6.0/installer-crontab.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Installcrontab extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $script = $this->script(
            array(
                'description' => "Show, add or remove the cron job of this installation: the crontab lines that run its cronjob\n"
                    . "parts (runcronjobs.php) with the PHP binary and the siteaccess each needs, in the crontab of the\n"
                    . "system user that owns the installation directory, between the lines\n"
                    . "\"# BEGIN Exponential cronjobs: <root>\" and \"# END Exponential cronjobs: <root>\".\n"
                    . "The parts are cronjob.ini [CrontabSettings] Parts[] that the installation defines (an extension's\n"
                    . "when it is active), the schedules [AdminSettings] CrontabSchedule_<part>. --add replaces the block\n"
                    . "or appends it and keeps every other line; a part another line of the crontab runs already is left\n"
                    . "out. Run it as the owner of the installation or as root (crontab -u <owner>); otherwise, or without a\n"
                    . "crontab command, it prints the lines to add by hand. Prints PASS or FAIL at the end.\n\n"
                    . "Example: php bin/php/installcrontab.php --show\n"
                    . "         php bin/php/installcrontab.php --add --dry-run\n"
                    . "         php bin/php/installcrontab.php --add\n"
                    . "         php bin/php/installcrontab.php --remove",
                'use-session' => false,
                'use-modules' => false,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            "[show][add][remove][dry-run][admin-siteaccess:]",
            '',
            array(
                'show' => "The planned lines, the block in the crontab and the last runs (the default)",
                'add' => "Add or update the block",
                'remove' => "Remove the block",
                'dry-run' => "With --add or --remove: show what would change, change nothing",
                'admin-siteaccess' => "The siteaccess of the admin parts (default: [CrontabSettings] AdminSiteAccess, else admin)"
            )
        );

        $actions = array_filter( array( 'add' => $options['add'], 'remove' => $options['remove'], 'show' => $options['show'] ) );
        if ( count( $actions ) > 1 )
        {
            $cli->error( 'FAIL: give one of --show, --add, --remove' );
            $script->shutdown( 1 );
        }
        $action = $actions ? key( $actions ) : 'show';
        $dryRun = (bool)$options['dry-run'];

        $root = \expCrontabSetup::root();
        $description = \expCrontabSetup::describe( $root, $options['admin-siteaccess'] ? (string)$options['admin-siteaccess'] : null );
        $setup = new \expCrontabSetup();

        $cli->output( 'Installation:    ' . $root );
        $cli->output( 'Admin siteaccess: ' . ( $description['admin_siteaccess'] !== '' ? $description['admin_siteaccess'] : '(none: the admin parts run with the default siteaccess)' ) );
        $cli->output( 'Installers write it: ' . ( \expCrontabSetup::enabled( $root ) ? 'yes' : 'no ([CrontabSettings] Install=disabled)' ) );
        $cli->output();

        if ( $action === 'show' )
        {
            $cli->output( 'Planned lines:' );
            foreach ( \expCrontabSetup::block( $description['entries'], $root, $description['php'] ) as $line )
                $cli->output( '  ' . $line );
            $cli->output();
            $state = $setup->show( $description );
            if ( !$state['ok'] )
                $cli->output( 'Crontab of ' . ( $state['user'] !== '' ? $state['user'] : 'the owner' ) . ': cannot be read: ' . $state['why'] );
            else
            {
                $cli->output( 'Crontab of ' . $state['user'] . ': ' . ( $state['block'] ? 'has the block' : 'has no block of this installation' ) );
                foreach ( $state['block'] as $line )
                    $cli->output( '  ' . $line );
                foreach ( $state['outside'] as $part => $line )
                    $cli->output( "  part $part also runs from a line outside the block: $line" );
            }
            $cli->output();
            $status = \expCrontabSetup::runState( $description['entries'], \expCrontabSetup::lastRuns( $root ), time() );
            $cli->output( 'Last runs (var/log/cronjob-lastrun.json):' );
            foreach ( $status['parts'] as $part )
            {
                $cli->output( sprintf( '  %-12s %-16s %-6s %s', $part['part'], $part['schedule'], $part['state'],
                                       $part['started'] ? date( 'Y-m-d H:i:s', $part['started'] ) : 'never' ) );
            }
            $cli->output();
            if ( $status['overall'] !== 'ok' )
                $cli->output( 'WARN: ' . ( $status['overall'] === 'never' ? 'no cronjob part has run yet' : 'a part has not run for longer than its schedule allows' ) );
            $cli->output( 'PASS: shown' );
            $script->shutdown( 0 );
        }

        $result = $action === 'add' ? $setup->add( $description, $dryRun ) : $setup->remove( $description, $dryRun );
        if ( $dryRun && $result['status'] === 'dry-run' )
        {
            $cli->output( 'The crontab of ' . $result['user'] . ' would change:' );
            foreach ( self::difference( $result['before'], $result['after'] ) as $line )
                $cli->output( '  ' . $line );
            foreach ( $result['skipped'] as $part => $line )
                $cli->output( "  part $part left out, an existing line runs it: $line" );
            $cli->output();
            $cli->output( 'PASS: dry run, nothing changed' );
            $script->shutdown( 0 );
        }
        if ( $result['status'] === 'fallback' )
        {
            foreach ( \expCrontabSetup::report( $result ) as $line )
                $cli->output( $line );
            $cli->output();
            $cli->error( 'FAIL: the crontab was not changed: ' . $result['message'] );
            $script->shutdown( 1 );
        }
        if ( $action === 'add' )
        {
            foreach ( \expCrontabSetup::report( $result ) as $line )
                $cli->output( $line );
            foreach ( $result['block'] as $line )
                $cli->output( '  ' . $line );
        }
        else
        {
            foreach ( $result['block'] as $line )
                $cli->output( '  removed: ' . $line );
        }
        $cli->output();
        $cli->output( 'PASS: ' . $result['message'] );
        $script->shutdown();
    }

    /**
     * The lines a change removes (-) and adds (+), in the order of the crontab.
     *
     * @param string[] $before
     * @param string[] $after
     * @return string[]
     */
    public static function difference( array $before, array $after )
    {
        $out = array();
        foreach ( array_diff( $before, $after ) as $line )
            $out[] = '- ' . $line;
        foreach ( array_diff( $after, $before ) as $line )
            $out[] = '+ ' . $line;
        return $out;
    }
}

}
