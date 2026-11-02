<?php
/**
 * File containing the shared code of the exp:migration:record commands.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

/**
 * What the exp:migration:record:* commands share: the user whose changes a session recording keeps (--user, else
 * [RunSettings] RunAsUser), the recording an argument names, and the lines of a recording. The work is in
 * expMigrationRecording; these commands parse, call and print.
 */
abstract class ExpmigrationrecordBase extends ExpmigrationBase
{
    /**
     * The user of --user (default [RunSettings] RunAsUser of migration.ini), by login; a usage error when there is
     * none of that login.
     *
     * @param array $options
     * @return \eZUser
     */
    protected function recordingUser( array $options )
    {
        $login = isset( $options['user'] ) && $options['user'] !== false && $options['user'] !== '' ? trim( (string)$options['user'] ) : '';
        if ( $login === '' )
        {
            $ini = \eZINI::instance( 'migration.ini' );
            $login = $ini->hasVariable( 'RunSettings', 'RunAsUser' ) ? trim( (string)$ini->variable( 'RunSettings', 'RunAsUser' ) ) : 'admin';
        }
        $user = \eZUser::fetchByName( $login );
        if ( !$user instanceof \eZUser )
            $this->usage( "no user with the login $login (--user)" );
        return $user;
    }

    /**
     * The recording an argument names: its id, checked; '' when none is given.
     *
     * @param array $options
     * @return string
     */
    protected function recordingArgument( array $options )
    {
        $id = $this->argument( $options );
        if ( $id !== '' && !\expMigrationRecording::validId( $id ) )
            $this->usage( "a recording id is <date>-<time>-<6 hex digits> (./console exp:migration:record:list lists them), not $id" );
        return $id;
    }

    /**
     * Ends with exit 1 when the recording tables do not exist yet.
     *
     * @param \eZDBInterface $db
     * @param string $format
     */
    protected function tables( $db, $format )
    {
        if ( \expMigrationRecording::tablesExist( $db ) !== true )
            $this->finish( array( 'mode' => 'record', 'errors' => array( 'the recording tables do not exist yet: ./console exp:upgrade --apply makes them (upgrade step 6.0.15-migration-recording-tables)' ),
                                  'warnings' => array(), 'exit' => \expUpgrade::EXIT_FAILED ), $format );
    }

    /**
     * One line for a recording. Pure.
     *
     * @param array $r a row of expMigrationRecording
     * @return string
     */
    public static function recordingLine( array $r )
    {
        return sprintf( '  %-22s %-9s %-7s %5d event(s)  %s%s%s', $r['recording_id'], $r['state'], $r['kind'], $r['event_count'],
                        $r['started_at'] ? date( 'Y-m-d H:i', $r['started_at'] ) : '-',
                        $r['kind'] === \expMigrationRecording::MODE_SESSION ? '  user #' . $r['user_id'] : '',
                        $r['label'] !== '' ? '  "' . $r['label'] . '"' : '' );
    }
}

}
