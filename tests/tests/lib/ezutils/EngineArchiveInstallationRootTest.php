<?php
/**
 * Read out of the engine archive (EXP_ENGINE_PHAR), a class's __DIR__ names a path inside the
 * archive. The helpers that work out the installation directory from it must use EXP_ROOT_DIR
 * (published by autoload.php, which is always on disk) instead, or what they open or write lands
 * at phar://.../engine.phar/... and silently fails:
 *
 *   - ezpRepairQueue::root()          the missing-libraries page's settings and state
 *   - eZDBInterface::sqlProfilePath() the SQL profile switch and log in var/tmp
 *   - expMongoDB::profilePath()       the MongoDB profile switch and log in var/tmp
 *   - expContentJob::rootDir()        also named kernel/ rather than the installation (one dirname short)
 *
 * The classes are loaded from a small archive built for the test, in a separate PHP process.
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 */

class EngineArchiveInstallationRootTest extends PHPUnit\Framework\TestCase
{
    const FILES = array(
        'lib/ezutils/classes/ezprepairqueue.php',
        'lib/ezdb/classes/ezdbinterface.php',
        'lib/ezdb/classes/expmongodb.php',
        'kernel/classes/contentjob/expcontentjob.php',
    );

    /**
     * Builds an archive of $files and returns what the helpers in it answer.
     *
     * @param string[] $files
     * @return array
     */
    public static function rootsFromArchive( array $files )
    {
        $root = dirname( __DIR__, 4 );
        $dir = $root . '/var/tmp/engine-archive-root-test-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        mkdir( $dir, 0700, true );
        $archive = $dir . '/engine.phar';
        try
        {
            $build = array_merge( array( PHP_BINARY, '-d', 'phar.readonly=0', __DIR__ . '/fixtures/engine_archive_build.php', $archive, $root ), $files );
            exec( implode( ' ', array_map( 'escapeshellarg', $build ) ) . ' 2>&1', $output, $status );
            if ( $status !== 0 )
                throw new RuntimeException( 'The test archive could not be built: ' . implode( "\n", $output ) );
            $output = array();
            $probe = array( PHP_BINARY, __DIR__ . '/fixtures/engine_archive_roots.php', $archive, $root );
            exec( implode( ' ', array_map( 'escapeshellarg', $probe ) ) . ' 2>&1', $output, $status );
            $json = json_decode( (string)end( $output ), true );
            if ( $status !== 0 || !is_array( $json ) )
                throw new RuntimeException( 'The probe failed: ' . implode( "\n", $output ) );
            return $json + array( '_root' => $root );
        }
        finally
        {
            @unlink( $archive );
            @rmdir( $dir );
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        if ( !extension_loaded( 'phar' ) || !function_exists( 'exec' ) )
            $this->markTestSkipped( 'Needs the phar extension and exec()' );
    }

    public function testHelpersNameTheInstallationNotThePathInsideTheArchive()
    {
        $r = self::rootsFromArchive( self::FILES );
        $root = $r['_root'];

        $this->assertSame( $root, $r['repairqueue.root'] );
        $this->assertSame( $root . '/var/tmp/x.log', $r['db.sqlprofile'] );
        $this->assertSame( $root . '/var/tmp/x.log', $r['mongodb.profile'] );
        $this->assertSame( $root, $r['contentjob.root'] );
    }
}
