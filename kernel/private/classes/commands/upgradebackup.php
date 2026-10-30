<?php
/**
 * @description Upgrade backup: the backup exp:upgrade makes before it writes, on its own (database dump and settings overrides)
 * @alias exp:upgrade:backup
 *
 * File containing the exp:upgrade:backup command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Upgradebackup extends UpgradeBase
{
    protected function name()
    {
        return 'exp:upgrade:backup';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Upgrade backup\n" .
                             "Makes the backup exp:upgrade --apply makes before its first write, into\n" .
                             "var/backup/upgrade/<time>/: SQLite a consistent copy (VACUUM INTO), MySQL/MariaDB mysqldump,\n" .
                             "PostgreSQL pg_dump, MongoDB mongodump, and a copy of settings/override. Oracle: make one with\n" .
                             "expdp. Passwords never reach a command line.\n" .
                             "\n" .
                             "./console exp:upgrade:backup\n" .
                             "./console exp:upgrade:backup --backup-dir=/srv/backup/site --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[backup-dir:][format:]', '', array(
            'backup-dir' => 'Where the backup goes (default var/backup/upgrade); a directory <time> is made in it',
            'format' => 'text (default) or json (the machine output exp-upgrade/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $db = \eZDB::instance();
        $report = \expUpgrade::report( 'backup', array( 'command' => $this->name() ) );
        if ( !$db->isConnected() )
        {
            $report['errors'][] = 'the database is not reachable';
            $report['exit'] = \expUpgrade::EXIT_UNREACHABLE;
            $this->finish( $report, $format );
            return;
        }
        $report['installation']['engine'] = \expKernelTableSchema::type( $db );
        $backup = \expUpgradeBackup::create( $db, !empty( $options['backup-dir'] ) ? (string)$options['backup-dir'] : null );
        $report['backup'] = array( 'dir' => $backup['dir'], 'ok' => $backup['ok'], 'files' => $backup['files'], 'messages' => $backup['messages'] );
        if ( !$backup['ok'] )
        {
            $report['errors'] = $backup['messages'];
            $report['exit'] = \expUpgrade::EXIT_FAILED;
        }
        if ( $format === 'json' )
        {
            $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        }
        else
        {
            $this->output( 'Backup: ' . $backup['dir'] );
            foreach ( $backup['files'] as $f )
                $this->output( '  ' . $f );
            foreach ( $backup['messages'] as $m )
                $this->output( '  ' . $m );
            $this->output( $backup['ok'] ? 'PASS' : 'FAIL (exit 1)' );
        }
        $this->shutdown( $report['exit'] );
    }
}

}
