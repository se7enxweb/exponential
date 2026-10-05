<?php
/**
 * bin/php/checkdbfiles.php (exp:checkdbfiles) knows every directory under update/database/<engine>/, and the files
 * there match its upgrade path exactly: no file it does not know, none it expects that is missing. Reads the file
 * system only; needs no database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group updatesql
 */

class CheckDbFilesVersionListTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        if ( !class_exists( '\Exponential\Command\Kernel\Checkdbfiles' ) )
            require_once self::$root . '/kernel/private/classes/commands/checkdbfiles.php';
    }

    public function testEveryUpdateDirectoryIsInTheVersionList()
    {
        $versions = \Exponential\Command\Kernel\Checkdbfiles::versionLists();
        $dbTypes = \Exponential\Command\Kernel\Checkdbfiles::databaseTypes();
        $seen = 0;
        foreach ( glob( self::$root . '/update/database/*', GLOB_ONLYDIR ) as $engineDir )
        {
            $engine = basename( $engineDir );
            $this->assertContains( $engine, $dbTypes, "update/database/$engine is an engine the checker knows" );
            foreach ( glob( $engineDir . '/*', GLOB_ONLYDIR ) as $branchDir )
            {
                $branch = basename( $branchDir );
                $this->assertArrayHasKey( $branch, $versions, "update/database/$engine/$branch has an entry" );
                $databases = isset( $versions[$branch]['databases'] ) ? $versions[$branch]['databases'] : array( 'mysql', 'postgresql' );
                $this->assertContains( $engine, $databases, "the entry of $branch covers $engine" );
                $seen++;
            }
        }
        $this->assertGreaterThanOrEqual( 34, $seen, '4.0 to 7.3 on MySQL and PostgreSQL, and 6.0 on SQLite' );
    }

    public function testTheVersionListNamesNoBranchWithoutADirectory()
    {
        foreach ( \Exponential\Command\Kernel\Checkdbfiles::versionLists() as $branch => $entry )
        {
            $databases = isset( $entry['databases'] ) ? $entry['databases'] : array( 'mysql', 'postgresql' );
            foreach ( $databases as $engine )
                $this->assertDirectoryExists( self::$root . "/update/database/$engine/$branch" );
        }
    }

    public function testTheFilesMatchTheUpgradePath()
    {
        $result = \Exponential\Command\Kernel\Checkdbfiles::check( self::$root . '/' );
        $strip = function ( $file ) { return substr( $file, strlen( self::$root ) + 1 ); };
        $this->assertSame( array(), array_map( $strip, $result['unknown'] ), "'?' files not in the upgrade path" );
        $this->assertSame( array(), array_map( $strip, $result['missing'] ), "'!' files of the upgrade path that do not exist" );
    }

    public function testAnUnknownDirectoryIsReported()
    {
        $tmp = $this->scratchDirectory();
        mkdir( $tmp . '/update/database/mysql/9.9', 0777, true );
        file_put_contents( $tmp . '/update/database/mysql/9.9/dbupdate-9.8.0-to-9.9.0.sql', "-- test\n" );

        $result = \Exponential\Command\Kernel\Checkdbfiles::check( $tmp . '/' );
        $this->assertContains( $tmp . '/update/database/mysql/9.9/dbupdate-9.8.0-to-9.9.0.sql', $result['unknown'] );
        // nothing else is there, so every file of the path is missing, the 6.0 files under their own names
        $this->assertContains( $tmp . '/update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql', $result['missing'] );
        $this->assertContains( $tmp . '/update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql', $result['missing'] );
        $this->assertContains( $tmp . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql', $result['missing'] );
        $this->assertNotContains( $tmp . '/update/database/mysql/7.2/dbupdate-6.13.0-to-7.2.0.sql', $result['missing'], '7.2 is PostgreSQL only' );
    }

    /** @var string[] the directories this test made, removed in tearDown */
    private $scratch = array();

    /**
     * A new directory var/tmp/checkdbfiles-test-<pid>-<n>, removed again after the test.
     */
    private function scratchDirectory()
    {
        $dir = self::$root . '/var/tmp/checkdbfiles-test-' . getmypid() . '-' . count( $this->scratch ) . '-' . bin2hex( random_bytes( 3 ) );
        mkdir( $dir, 0777, true );
        $this->scratch[] = $dir;
        return $dir;
    }

    protected function tearDown(): void
    {
        foreach ( $this->scratch as $dir )
        {
            if ( !is_dir( $dir ) || strpos( basename( $dir ), 'checkdbfiles-test-' ) !== 0 )
                continue;
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ),
                                                       RecursiveIteratorIterator::CHILD_FIRST );
            foreach ( $iterator as $entry )
                $entry->isDir() && !$entry->isLink() ? rmdir( $entry->getPathname() ) : unlink( $entry->getPathname() );
            rmdir( $dir );
        }
        $this->scratch = array();
    }
}
