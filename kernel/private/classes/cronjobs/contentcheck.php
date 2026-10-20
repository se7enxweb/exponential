<?php
/**
 * The code of cronjobs/contentcheck.php: builds the reference index of the content checks and keeps it in the cache
 * (expContentCheckReferences). Run it as the cronjob part contentcheck (php runcronjobs.php contentcheck) more often
 * than contentcheck.ini [UnreferencedSettings] IndexTTL when BuildOnRequest is false. With [MissingFileSettings]
 * CronjobReport=enabled it also counts the missing files and alias problems of the whole tree and writes the count
 * to the cronjob output (a warning when there are any); it never resolves anything.
 * Guide: doc/en/us/features/6.0/content-checks.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Contentcheck extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        $index = \expContentCheckReferences::build();
        \expContentCheckReferences::writeCache( $index );
        $cli->output( sprintf( 'Content checks: reference index of %d objects, %d nodes, %d remote ids, %d paths, %d collections built in %.2f s',
                               count( $index['objects'] ), count( $index['nodes'] ), count( $index['remote'] ), count( $index['aliases'] ),
                               count( $index['queries'] ), $index['seconds'] ) );
        if ( !empty( $index['failed'] ) )
            $cli->error( 'Content checks: these sources could not be read: ' . implode( ', ', $index['failed'] ) );

        if ( class_exists( 'expContentCheckFiles' ) && \expContentCheck::setting( 'MissingFileSettings', 'CronjobReport', 'disabled' ) === 'enabled' )
            self::reportMissingFiles( $cli );
    }

    /**
     * Counts the missing files and alias problems of the whole tree (as the cronjob's user may read it).
     *
     * @param \eZCLI $cli
     */
    protected static function reportMissingFiles( $cli )
    {
        $params = \expContentCheckFiles::parameters( array( 'NodeID' => 1, 'Limit' => 200 ), 1 );
        $counts = array_fill_keys( \expContentCheckFiles::PROBLEMS, 0 );
        do
        {
            $result = \expContentCheckFiles::scan( $params );
            if ( $result['error'] !== '' )
            {
                $cli->error( 'Content checks: missing files not checked: ' . $result['error'] );
                return;
            }
            foreach ( $result['rows'] as $row )
                $counts[$row['problem']]++;
            $params['after'] = $result['last'];
        }
        while ( !$result['done'] );
        $line = sprintf( 'Content checks: %d files missing, %d images with missing aliases, %d with aliases out of date',
                         $counts['missing'], $counts['alias_missing'], $counts['alias_stale'] );
        if ( $counts['missing'] + $counts['alias_missing'] + $counts['alias_stale'] > 0 )
            $cli->warning( $line . ' (contentcheck/missingfiles)' );
        else
            $cli->output( $line );
    }
}

}

?>
