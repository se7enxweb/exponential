<?php
/**
 * The MySQL update files set the default engine with SET default_storage_engine, never with SET storage_engine:
 * MySQL removed that variable in 5.7.5 and MariaDB in 12.0, so a file that still says it stops on its first line.
 * Reads the files only; needs no database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group updatesql
 */

class UpdateSqlStorageEngineTest extends PHPUnit\Framework\TestCase
{
    /**
     * Every .sql file below $dir, recursively.
     */
    private static function sqlFiles( $dir )
    {
        $files = array();
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $iterator as $file )
        {
            if ( $file->isFile() && substr( $file->getFilename(), -4 ) === '.sql' )
                $files[] = $file->getPathname();
        }
        sort( $files );
        return $files;
    }

    public function testNoMysqlUpdateFileSetsTheRemovedStorageEngineVariable()
    {
        $root = dirname( __DIR__, 5 );
        $files = self::sqlFiles( $root . '/update/database/mysql' );
        $this->assertNotEmpty( $files, 'the MySQL update files are found' );

        $offenders = array();
        foreach ( $files as $file )
        {
            // a statement, not a comment: SET storage_engine, SET SESSION storage_engine, SET @@storage_engine ...
            if ( preg_match( '/^\s*SET\s+(?:(?:SESSION|GLOBAL|LOCAL)\s+|@@(?:session\.|global\.)?)?storage_engine\s*=/mi', file_get_contents( $file ) ) )
                $offenders[] = substr( $file, strlen( $root ) + 1 );
        }
        $this->assertSame( array(), $offenders, 'use SET default_storage_engine=InnoDB; instead' );
    }

    public function testTheMysqlFilesThatSetAnEngineUseDefaultStorageEngine()
    {
        $root = dirname( __DIR__, 5 );
        $count = 0;
        foreach ( self::sqlFiles( $root . '/update/database/mysql' ) as $file )
        {
            if ( preg_match( '/^SET default_storage_engine=InnoDB;$/mi', file_get_contents( $file ) ) )
                $count++;
        }
        // 4.0 to 7.3: every file except the 6.0.0 to 6.0.15 one, whose tables name their engine themselves
        $this->assertGreaterThanOrEqual( 54, $count );
    }
}
