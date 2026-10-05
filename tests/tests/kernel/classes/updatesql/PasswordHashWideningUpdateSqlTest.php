<?php
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The 6.0 update path widens ezuser.password_hash to the width of the kernel schema on every engine that can have a
 * 5.x database, both in the 5.4 to 6.0.0 file and, for a site that reached 6.0 with an older copy of it, in the
 * 6.0.0 to 6.0.15 file. A 5.x database has varchar(50); the bcrypt hash every user gets at the first sign-in is 60
 * characters. Reads the files only, plus an SQLite database in memory; needs no installation.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group updatesql
 */

class PasswordHashWideningUpdateSqlTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    /**
     * The width of ezuser.password_hash in the kernel schema, from share/db_schema.dba.
     */
    private static function schemaWidth()
    {
        // the file assigns $schema
        $schema = null;
        include self::$root . '/share/db_schema.dba';
        return (int) $schema['ezuser']['fields']['password_hash']['length'];
    }

    /**
     * The SQL of a file without its comment lines.
     */
    private static function statements( $file )
    {
        return preg_replace( '/^\s*--.*$/m', '', file_get_contents( self::$root . '/' . $file ) );
    }

    public static function widenings()
    {
        $mysql = '/^\s*ALTER\s+TABLE\s+ezuser\s+(?:CHANGE\s+password_hash|MODIFY(?:\s+COLUMN)?)\s+password_hash\s+VARCHAR\((\d+)\)\s+default\s+NULL\s*;/mi';
        $postgresql = '/^\s*ALTER\s+TABLE\s+ezuser\s+ALTER\s+COLUMN\s+password_hash\s+TYPE\s+VARCHAR\((\d+)\)\s*;/mi';
        return array(
            'mysql 5.4.0 to 6.0.0' => array( 'update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql', $mysql ),
            'mysql 6.0.0 to 6.0.15' => array( 'update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql', $mysql ),
            'postgresql 5.4 to 6.0' => array( 'update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql', $postgresql ),
            'postgresql 6.0.0 to 6.0.15' => array( 'update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql', $postgresql ),
        );
    }

    #[DataProvider( 'widenings' )]
    public function testTheFileWidensPasswordHashToTheSchemaWidth( $file, $pattern )
    {
        $this->assertSame( 1, preg_match_all( $pattern, self::statements( $file ), $matches ), "$file widens ezuser.password_hash once" );
        $this->assertSame( self::schemaWidth(), (int) $matches[1][0], 'to the width of the kernel schema' );
    }

    public function testTheKernelSchemaIsWideEnoughForBcrypt()
    {
        $this->assertGreaterThanOrEqual( 60, self::schemaWidth() );
        $this->assertMatchesRegularExpression( '/password_hash varchar\(' . self::schemaWidth() . '\) default NULL/', file_get_contents( self::$root . '/kernel/sql/mysql/kernel_schema.sql' ) );
        $this->assertMatchesRegularExpression( '/password_hash character varying\(' . self::schemaWidth() . '\)/', file_get_contents( self::$root . '/kernel/sql/postgresql/kernel_schema.sql' ) );
        $this->assertMatchesRegularExpression( '/`password_hash` varchar\(' . self::schemaWidth() . '\)/', file_get_contents( self::$root . '/kernel/sql/sqlite/schema.sql' ) );
    }

    /**
     * Every engine with a 6.0 directory is covered: MySQL and PostgreSQL above, SQLite here, and nothing else.
     */
    public function testEveryEngineOfTheSixZeroPathIsCovered()
    {
        $engines = array();
        foreach ( glob( self::$root . '/update/database/*/6.0', GLOB_ONLYDIR ) as $dir )
            $engines[] = basename( dirname( $dir ) );
        sort( $engines );
        $this->assertSame( array( 'mysql', 'postgresql', 'sqlite' ), $engines, 'a new engine needs its own widening, or a reason why not' );
    }

    /**
     * SQLite needs no statement: it does not enforce a VARCHAR length, so a bcrypt hash fits even a varchar(50)
     * column. The SQLite file says so.
     */
    public function testSqliteNeedsNoWidening()
    {
        $this->assertStringContainsString( 'SQLite does not enforce the length of a VARCHAR', file_get_contents( self::$root . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' ) );
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'no pdo_sqlite' );
        $pdo = new PDO( 'sqlite::memory:' );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $pdo->exec( 'CREATE TABLE ezuser (contentobject_id integer NOT NULL, password_hash varchar(50) DEFAULT NULL)' );
        $hash = password_hash( 'a password', PASSWORD_BCRYPT );
        $this->assertSame( 60, strlen( $hash ) );
        $insert = $pdo->prepare( 'INSERT INTO ezuser (contentobject_id, password_hash) VALUES (14, ?)' );
        $insert->execute( array( $hash ) );
        $this->assertSame( $hash, $pdo->query( 'SELECT password_hash FROM ezuser WHERE contentobject_id = 14' )->fetchColumn() );
    }
}
