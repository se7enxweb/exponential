<?php
/**
 * @description Upgrade rollback: put the database, settings, libraries and code back to a backup of exp:upgrade (dry run unless --apply)
 * @alias exp:upgrade:rollback
 *
 * File containing the exp:upgrade:rollback command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Upgraderollback extends UpgradeBase
{
    protected function name()
    {
        return 'exp:upgrade:rollback';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Upgrade rollback\n" .
                             "Without a backup name: the backups exp:upgrade made (var/backup/upgrade/). With one: what a\n" .
                             "rollback to it restores, and with --apply restores it: the database (SQLite copy, MySQL/MariaDB\n" .
                             "and PostgreSQL dump; tables made later are dropped), settings/override, composer.lock with\n" .
                             "composer install, and the git checkouts moved back to their commits (git reset --keep).\n" .
                             "Never automatic. Afterwards run ./console exp:upgrade:finish --apply.\n" .
                             "\n" .
                             "./console exp:upgrade:rollback\n" .
                             "./console exp:upgrade:rollback 20261029-101500\n" .
                             "./console exp:upgrade:rollback 20261029-101500 --apply --parts=database,settings",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][parts:][backup-dir:][format:]', '[BACKUP]', array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'parts' => 'Only these parts, a comma list of database, settings, libraries, code (default: what the backup has)',
            'backup-dir' => 'Where the backup goes (default var/backup/upgrade); a directory <time> is made in it',
            'format' => 'text (default) or json (the machine output exp-upgrade/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = self::applyRequested( $options );
        if ( $apply === null )
            $this->usage( '--apply and --dry-run exclude each other; without --apply nothing is changed' );
        $arguments = isset( $options['arguments'] ) ? (array)$options['arguments'] : array();
        $dir = !empty( $options['backup-dir'] ) ? (string)$options['backup-dir'] : null;
        if ( !$arguments )
        {
            $list = \expUpgradeBackup::all( $dir );
            $report = \expUpgrade::report( 'backups', array( 'command' => $this->name() ) );
            $report['backups'] = $list;
            if ( $format === 'json' )
                $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            else
            {
                foreach ( $list as $name => $m )
                    $this->output( sprintf( '%-22s %-10s %-14s %s', $name, isset( $m['engine'] ) ? $m['engine'] : '?',
                                            isset( $m['database_version'] ) ? (string)$m['database_version'] : '',
                                            !empty( $m['complete'] ) ? implode( ', ', isset( $m['files'] ) ? $m['files'] : array() ) : 'INCOMPLETE' ) );
                $this->output( count( $list ) . ' backup(s)' );
            }
            $this->shutdown( 0 );
            return;
        }
        $report = \expUpgradeRollback::run( (string)$arguments[0], array(
            'apply' => $apply,
            'parts' => !empty( $options['parts'] ) ? (string)$options['parts'] : array(),
            'backup-dir' => $dir,
            'command' => $this->name(),
        ) );
        if ( $format === 'json' )
        {
            $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            $this->shutdown( $report['exit'] );
            return;
        }
        $this->output( 'Exponential upgrade rollback to ' . $report['rollback']['backup'] . ( $apply ? '' : ': dry run, nothing is changed (--apply restores)' ) );
        foreach ( $report['rollback']['parts'] as $part => $lines )
        {
            $this->output();
            $this->output( "Part $part" );
            foreach ( $lines as $line )
                $this->output( ( $apply ? '  ' : '  would ' ) . $line );
        }
        $this->output();
        foreach ( $report['warnings'] as $w )
            $this->output( 'WARN ' . $w );
        foreach ( $report['errors'] as $e )
            $this->error( 'FAIL: ' . $e );
        $this->output( $report['exit'] === 0 ? 'PASS' : 'FAIL (exit ' . $report['exit'] . ')' );
        $this->shutdown( $report['exit'] );
    }
}

}
