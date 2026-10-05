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
        $tmp = self::$root . '/var/tmp/checkdbfiles-test-' . getmypid();
        if ( !is_dir( $tmp . '/update/database/mysql/9.9' ) )
            mkdir( $tmp . '/update/database/mysql/9.9', 0777, true );
        file_put_contents( $tmp . '/update/database/mysql/9.9/dbupdate-9.8.0-to-9.9.0.sql', "-- test\n" );

        $result = \Exponential\Command\Kernel\Checkdbfiles::check( $tmp . '/' );
        $this->assertContains( $tmp . '/update/database/mysql/9.9/dbupdate-9.8.0-to-9.9.0.sql', $result['unknown'] );
        // nothing else is there, so every file of the path is missing, the 6.0 files under their own names
        $this->assertContains( $tmp . '/update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql', $result['missing'] );
        $this->assertContains( $tmp . '/update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql', $result['missing'] );
        $this->assertContains( $tmp . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql', $result['missing'] );
        $this->assertNotContains( $tmp . '/update/database/mysql/7.2/dbupdate-6.13.0-to-7.2.0.sql', $result['missing'], '7.2 is PostgreSQL only' );
        // the copy is left in var/tmp for the owner to remove
    }
}
