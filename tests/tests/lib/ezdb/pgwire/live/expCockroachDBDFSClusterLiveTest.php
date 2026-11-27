<?php
/**
 * The DFS cluster on CockroachDB, live: the parity checks of every backend through the PostgreSQL backend, on the tables
 * exp:cluster:enable makes (expDFSPostgreSQLWireLiveSetup). Server: EXP_TEST_PGWIRE_COCKROACHDB_HOST ...
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDFSPostgreSQLWireLiveSetup.php';

#[\PHPUnit\Framework\Attributes\Group('cluster-live')]
#[\PHPUnit\Framework\Attributes\Group('pgwire-live')]
class expCockroachDBDFSClusterLiveTest extends ezpTestCase
{
    use expDFSBackendParityChecks;
    use expDFSPostgreSQLWireLiveSetup;

    protected function supportsBoundedLockRetry()
    {
        return true;
    }

    protected function supportsTimesAfter2038()
    {
        return true;
    }

    const PROFILE = 'cockroachdb';

    /**
     * The parity check, except that the purge may need a second run: FOR UPDATE SKIP LOCKED on CockroachDB also skips
     * a row whose writing transaction committed a moment ago while its write intent is not resolved yet, so a purge
     * right after the write can find nothing (measured: 0 rows at once, 1 after 0.5 s). Such a row is purged by the
     * next run, which is what the cluster's purge does anyway.
     */
    public function testDeleteCacheFilesMovesNoStorage()
    {
        $cacheRow = eZSys::cacheDirectory() . '/parity/' . getmypid() . '.cache';
        $storageRow = $this->path( 'storage/kept.txt' );
        $this->rawExec( "INSERT INTO ezdfsfile (name, name_trunk, name_hash, datatype, scope, size, mtime, expired) VALUES " .
                        "('$cacheRow', '$cacheRow', '" . md5( $cacheRow ) . "', 'text/plain', 'viewcache', 1, 1700000000, 0)" );
        $this->backend->_storeContents( $storageRow, 'k', 'binaryfile', 'text/plain', 1700000000 );

        $deleted = 0;
        for ( $run = 0; $run < 20 && $deleted === 0; $run++ )
        {
            if ( $run > 0 )
                usleep( 250000 );
            $deleted = (int)$this->backend->deleteCacheFiles( 10 );
        }
        $this->assertSame( 1, $deleted );
        $this->assertCount( 0, $this->rawRows( "SELECT name FROM ezdfsfile WHERE name_hash = '" . md5( $cacheRow ) . "'" ) );
        $this->assertTrue( $this->backend->_exists( $storageRow ) );
    }
}
