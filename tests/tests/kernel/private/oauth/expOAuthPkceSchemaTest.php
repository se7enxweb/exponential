<?php
/**
 * The PKCE columns of ezprest_authcode (code_challenge, code_challenge_method) are the same everywhere the table is
 * written: the persistent object map, share/db_schema.dba (which installs every engine, Oracle and MongoDB
 * included), the kernel schemas of MySQL, PostgreSQL and SQLite and the 6.0.0 to 6.0.15 update files. The SQLite
 * update statements are run on a table made as 6.0.0 made it, in a database in var/tmp. Needs no installation.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/private/oauth/expOAuthPkceSchemaTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group oauth
 */

class expOAuthPkceSchemaTest extends PHPUnit\Framework\TestCase
{
    private static $columns = array( 'client_id', 'code_challenge', 'code_challenge_method', 'expirytime', 'id', 'scope', 'user_id' );

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    public function testPersistentMapAndSchemaDefinitionAgree()
    {
        $source = (string)file_get_contents( self::root() . '/kernel/private/rest/classes/po_maps/ezprestauthcode.php' );
        foreach ( array( 'code_challenge', 'code_challenge_method' ) as $column )
            $this->assertStringContainsString( "->columnName   = '$column';", $source );
        $model = (string)file_get_contents( self::root() . '/kernel/private/rest/classes/models/ezprest_authcode.php' );
        $this->assertStringContainsString( "'code_challenge' => \$this->code_challenge,", $model );
        $this->assertStringContainsString( "'code_challenge_method' => \$this->code_challenge_method,", $model );

        $schema = null;
        include self::root() . '/share/db_schema.dba';
        $this->assertSame( self::$columns, array_keys( $schema['ezprest_authcode']['fields'] ) );
        $this->assertSame( 128, $schema['ezprest_authcode']['fields']['code_challenge']['length'] );
        $this->assertArrayNotHasKey( 'not_null', $schema['ezprest_authcode']['fields']['code_challenge'] );
    }

    public function testKernelSchemas()
    {
        $files = array( 'mysql' => 'kernel/sql/mysql/kernel_schema.sql',
                        'postgresql' => 'kernel/sql/postgresql/kernel_schema.sql',
                        'sqlite' => 'kernel/sql/sqlite/schema.sql' );
        foreach ( $files as $engine => $file )
        {
            $sql = (string)file_get_contents( self::root() . '/' . $file );
            $this->assertSame( 1, preg_match( '/CREATE TABLE `?ezprest_authcode`? \((.*?)\n\)/s', $sql, $m ), $engine );
            preg_match_all( '/^[\s,]*`?([a-z_]+)`? (?:int|integer|bigint|varchar|character)/m', $m[1], $cols );
            $this->assertSame( self::$columns, $cols[1], $engine );
        }
    }

    public function testUpdateFilesAddTheColumns()
    {
        foreach ( array( 'mysql', 'postgresql', 'sqlite' ) as $engine )
        {
            $sql = (string)file_get_contents( self::root() . "/update/database/$engine/6.0/dbupdate-6.0.0-6.0.15.sql" );
            $this->assertMatchesRegularExpression( '/ALTER TABLE ezprest_authcode\s+ADD COLUMN (IF NOT EXISTS )?code_challenge /', $sql, $engine );
            $this->assertMatchesRegularExpression( '/ADD COLUMN (IF NOT EXISTS )?code_challenge_method /', $sql, $engine );
        }
    }

    public function testSqliteUpdateAddsTheColumnsToA600Table()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'no pdo_sqlite' );
        $dir = self::root() . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $file = $dir . '/pkce-authcode-update-test-' . getmypid() . '.db';
        $pdo = new PDO( 'sqlite:' . $file );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $pdo->exec( "CREATE TABLE ezprest_authcode ( client_id varchar(200) NOT NULL DEFAULT '', expirytime integer NOT NULL DEFAULT '0',
                     id varchar(200) NOT NULL DEFAULT '', scope varchar(200) DEFAULT NULL, user_id integer NOT NULL DEFAULT '0', PRIMARY KEY (id) )" );
        $pdo->exec( "INSERT INTO ezprest_authcode ( client_id, expirytime, id, user_id ) VALUES ( 'c', 1, 'old-code', 14 )" );

        $sql = (string)file_get_contents( self::root() . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
        preg_match_all( '/^ALTER TABLE ezprest_authcode [^;]+;/m', $sql, $statements );
        $this->assertCount( 2, $statements[0] );
        foreach ( $statements[0] as $statement )
            $pdo->exec( $statement );

        $cols = array_column( $pdo->query( 'PRAGMA table_info(ezprest_authcode)' )->fetchAll( PDO::FETCH_ASSOC ), 'name' );
        sort( $cols );
        $this->assertSame( self::$columns, $cols );
        $row = $pdo->query( "SELECT code_challenge, code_challenge_method FROM ezprest_authcode WHERE id = 'old-code'" )->fetch( PDO::FETCH_ASSOC );
        $this->assertSame( array( 'code_challenge' => null, 'code_challenge_method' => null ), $row, 'a code issued before the update has no challenge' );
        $pdo = null;
        @unlink( $file );
    }
}
