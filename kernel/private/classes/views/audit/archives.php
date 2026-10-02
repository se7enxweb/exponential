<?php
/**
 * The audit/archives view (doc/bc/6.0/audit.md, "Views", "Rotation, archives and retention"): per channel its live
 * files (count, size, oldest), its archives and manifests, the verification state of every live file (as the index
 * stores it: intact, repaired, broken with the line, unchecked) and the signing key's id and fingerprint.
 * Read-only: archiving, verifying and restoring are done by exp:audit and the audit cronjob part.
 * Policy audit/manage. Recorded as system.audit.read.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Audit
{

class Archives extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !class_exists( 'expAuditConsole' ) || !\expAuditConsole::available() )
            return $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

        $config = \expAuditConfig::get();
        $only = isset( $Params['Channel'] ) && preg_match( '/^[a-z][a-z0-9_]{0,31}$/', (string)$Params['Channel'] ) ? (string)$Params['Channel'] : null;
        $reader = new \expAuditReader( $config['logDir'] );
        $live = $reader->channels();
        $chainStates = array_column( \expAuditConsole::chainStates( null ), null, 'channel' );
        $states = \expAuditConsole::indexUsable() ? ( new \expAuditIndexer() )->fileStates() : array();

        $archiveStatus = array();
        $manifests = array();
        if ( class_exists( 'expAuditArchiver' ) && method_exists( 'expAuditArchiver', 'status' ) )
        {
            try
            {
                $archiver = new \expAuditArchiver( $config );
                $archiveStatus = $archiver->status();
                foreach ( $config['channels'] as $c )
                    $manifests[$c] = array_keys( $archiver->manifests( $c ) );
            }
            catch ( \Throwable $e )
            {
                $archiveStatus = array();
            }
        }

        $channels = array();
        foreach ( array_unique( array_merge( $config['channels'], array_keys( $live ) ) ) as $c )
        {
            if ( $only !== null && $c !== $only )
                continue;
            $files = array();
            $worst = 'intact';
            $unchecked = 0;
            foreach ( isset( $live[$c]['files'] ) ? $live[$c]['files'] : array() as $file => $bytes )
            {
                $s = isset( $states[$c . "\n" . $file] ) ? $states[$c . "\n" . $file] : null;
                $verified = $s ? $s['verified'] : 'unchecked';
                if ( $verified === 'broken' )
                    $worst = 'broken';
                elseif ( $verified === 'repaired' && $worst === 'intact' )
                    $worst = 'repaired';
                elseif ( $verified === 'unchecked' )
                    $unchecked++;
                $files[] = array( 'name' => $file, 'bytes' => $bytes, 'records' => $s ? (int)$s['records'] : 0,
                                  'verified' => $verified, 'verified_at' => $s && (int)$s['verified_ms'] ? date( 'Y-m-d H:i', (int)( $s['verified_ms'] / 1000 ) ) : '',
                                  'break_line' => $s ? (int)$s['break_line'] : 0,
                                  'console_url' => \expAuditConsole::url( 'audit/console', array( 'channel' => $c ) ) );
            }
            if ( !$files )
                $worst = 'empty';
            elseif ( $unchecked === count( $files ) && $worst === 'intact' )
                $worst = 'unchecked';
            $a = isset( $archiveStatus[$c] ) ? $archiveStatus[$c] : array();
            $channels[] = array(
                'channel' => $c,
                'files' => array_reverse( $files ),
                'live_count' => count( $files ),
                'live_bytes' => isset( $live[$c]['bytes'] ) ? (int)$live[$c]['bytes'] : 0,
                'oldest_live' => $files ? \expAuditWriter::parseFileName( $files[0]['name'] )['date'] : '',
                'archives' => isset( $a['archives'] ) ? (int)$a['archives'] : 0,
                'archive_bytes' => isset( $a['archive_bytes'] ) ? (int)$a['archive_bytes'] : 0,
                'oldest_archive' => isset( $a['oldest_archive'] ) ? (string)$a['oldest_archive'] : '',
                'manifests' => isset( $manifests[$c] ) ? array_reverse( array_slice( $manifests[$c], -10 ) ) : array(),
                'format' => isset( $a['format'] ) ? (string)$a['format'] : '',
                'live_days' => isset( $a['live_days'] ) ? (int)$a['live_days'] : 0,
                'archive_days' => isset( $a['archive_days'] ) ? (int)$a['archive_days'] : 0,
                'state' => isset( $chainStates[$c] ) && $worst !== 'broken' && $worst !== 'empty' ? $chainStates[$c]['result'] : $worst,
                'verified_at' => isset( $chainStates[$c] ) ? $chainStates[$c]['verified_at'] : '',
            );
        }

        $keys = new \expAuditKeys( $config );
        $active = $keys->activeKeyId();
        $signing = $active ? $keys->signingKey( $active ) : null;

        \expAuditConsole::recordRead( 'audit/archives', $only ? array( 'channel' => $only ) : array(), count( $channels ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'channels', $channels );
        $tpl->setVariable( 'log_dir', \expAudit::relativePath( $config['logDir'] ) );
        $tpl->setVariable( 'archive_dir', class_exists( 'expAuditArchiver' ) && isset( $archiver ) ? \expAudit::relativePath( $archiver->archiveDir() ) : '' );
        $tpl->setVariable( 'archiver', !empty( $archiveStatus ) );
        $tpl->setVariable( 'key_id', (string)$active );
        $tpl->setVariable( 'key_fingerprint', $signing ? \expAuditKeys::fingerprint( $signing ) : '' );
        return \expAuditConsole::result( $tpl->fetch( 'design:audit/archives.tpl' ), \ezpI18n::tr( 'design/admin/audit', 'Archives' ) );
    }
}

}
