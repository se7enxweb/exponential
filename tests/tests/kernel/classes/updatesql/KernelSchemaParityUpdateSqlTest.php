<?php
/**
 * The kernel schema is written four times: share/db_schema.dba and the MySQL, PostgreSQL and SQLite files under
 * kernel/sql. Every column the .dba gives a table is in the CREATE TABLE of each engine's file, so a new installation
 * gets the same tables whichever file it is built from. Two 6.0.15 additions are checked by name as well: the footer
 * columns of ezpdf_export, and the index ezcontentobject_modified, which the 6.0.0 to 6.0.15 update file of each
 * engine creates only when it is missing. Reads the files only; needs no database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group updatesql
 */

use PHPUnit\Framework\Attributes\DataProvider;

#[\PHPUnit\Framework\Attributes\Group('updatesql')]
class KernelSchemaParityUpdateSqlTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    private static function read( $file )
    {
        return file_get_contents( self::$root . '/' . $file );
    }

    /**
     * The SQL of a file without its comment lines.
     */
    private static function statements( $file )
    {
        return preg_replace( '/^\s*--.*$/m', '', self::read( $file ) );
    }

    private static function dba()
    {
        // the file assigns $schema
        $schema = null;
        include self::$root . '/share/db_schema.dba';
        return $schema;
    }

    /**
     * table => column names of the CREATE TABLE statements of an engine's kernel schema file.
     */
    private static function engineColumns( $file )
    {
        preg_match_all( '/^CREATE TABLE\s+[`"]?([a-z_0-9]+)[`"]?\s*\((.*?)^\)/ms', self::read( $file ), $tables, PREG_SET_ORDER );
        $result = array();
        foreach ( $tables as $table )
        {
            preg_match_all( '/^\s*,?\s*[`"]?([a-z_0-9]+)[`"]?\s+[a-z]/mi', $table[2], $columns );
            $result[$table[1]] = array_map( 'strtolower', $columns[1] );
        }
        return $result;
    }

    public static function engines()
    {
        return array(
            'mysql' => array( 'kernel/sql/mysql/kernel_schema.sql' ),
            'postgresql' => array( 'kernel/sql/postgresql/kernel_schema.sql' ),
            'sqlite' => array( 'kernel/sql/sqlite/schema.sql' ),
        );
    }

    #[DataProvider( 'engines' )]
    public function testEveryColumnOfTheDbaIsInTheEngineSchema( $file )
    {
        $engine = self::engineColumns( $file );
        $missing = array();
        foreach ( self::dba() as $table => $definition )
        {
            if ( !is_array( $definition ) || !isset( $definition['fields'] ) || !isset( $engine[$table] ) )
                continue;
            foreach ( array_keys( $definition['fields'] ) as $field )
            {
                if ( !in_array( strtolower( $field ), $engine[$table], true ) )
                    $missing[] = "$table.$field";
            }
        }
        $this->assertSame( array(), $missing, "columns of share/db_schema.dba missing from $file" );
    }

    public function testThePdfExportFooterColumns()
    {
        $fields = self::dba()['ezpdf_export']['fields'];
        $this->assertSame( '1', (string) $fields['show_footer']['default'] );
        $this->assertSame( '', (string) $fields['footer_text']['default'] );
        $this->assertMatchesRegularExpression( "/^\s*show_footer int\(11\) NOT NULL default '1',/m", self::read( 'kernel/sql/mysql/kernel_schema.sql' ) );
        $this->assertMatchesRegularExpression( "/^\s*footer_text varchar\(255\) NOT NULL default '',/m", self::read( 'kernel/sql/mysql/kernel_schema.sql' ) );
        $this->assertMatchesRegularExpression( '/^\s*show_footer integer DEFAULT 1 NOT NULL,/m', self::read( 'kernel/sql/postgresql/kernel_schema.sql' ) );
        $this->assertMatchesRegularExpression( "/^\s*footer_text character varying\(255\) DEFAULT ''::character varying NOT NULL,/m", self::read( 'kernel/sql/postgresql/kernel_schema.sql' ) );
    }

    public function testTheContentObjectModifiedIndex()
    {
        $this->assertSame( array( 'modified' ), self::dba()['ezcontentobject']['indexes']['ezcontentobject_modified']['fields'] );
        $this->assertMatchesRegularExpression( '/^\s*KEY ezcontentobject_modified \(modified\)/m', self::read( 'kernel/sql/mysql/kernel_schema.sql' ) );
        $this->assertMatchesRegularExpression( '/^CREATE INDEX ezcontentobject_modified ON ezcontentobject USING btree \(modified\);/m', self::read( 'kernel/sql/postgresql/kernel_schema.sql' ) );
        $this->assertMatchesRegularExpression( '/^CREATE INDEX "idx_ezcontentobject_ezcontentobject_modified" ON "ezcontentobject" \(`modified`\);/m', self::read( 'kernel/sql/sqlite/schema.sql' ) );

        $mysql = self::statements( 'update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertMatchesRegularExpression( "/TABLE_NAME = 'ezcontentobject'\s+AND INDEX_NAME = 'ezcontentobject_modified' \) = 0,\s+'CREATE INDEX ezcontentobject_modified ON ezcontentobject \( modified \)'/", $mysql );
        $this->assertMatchesRegularExpression( '/^PREPARE exp_contentobject_modified_index_stmt FROM @exp_contentobject_modified_index_sql;\s*^EXECUTE exp_contentobject_modified_index_stmt;/m', $mysql );
        $this->assertMatchesRegularExpression( '/^CREATE INDEX IF NOT EXISTS ezcontentobject_modified ON ezcontentobject USING btree \( modified \);/m', self::statements( 'update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql' ) );
        $this->assertMatchesRegularExpression( '/^CREATE INDEX IF NOT EXISTS idx_ezcontentobject_ezcontentobject_modified ON ezcontentobject \( modified \);/m', self::statements( 'update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' ) );
    }

    /**
     * The update files create the same index the upgrade step adds when it is missing.
     */
    public function testTheUpgradeStepNamesTheSameIndex()
    {
        $this->assertStringContainsString( "'ezcontentobject' => array( 'ezcontentobject_modified' )", self::read( 'kernel/classes/upgrade/steps/expupgradestepcontentobjectmodifiedindex.php' ) );
    }
}
