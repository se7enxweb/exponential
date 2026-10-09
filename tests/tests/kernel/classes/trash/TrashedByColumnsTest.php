<?php
/**
 * Who moved an object to the trash is kept in the trash row: ezcontentobject_trash.trashed_by and trashed_via.
 *
 *  - The kernel schema of every engine and share/db_schema.dba have both columns.
 *  - The 6.0.0 to 6.0.15 update file of each engine adds them; MySQL and PostgreSQL only when missing.
 *  - eZContentObjectTrashNode knows them and createFromNode() fills them with the current user and where the
 *    removal came from.
 *  - currentVia() names the command line script, or the script ezexec.php runs, and stays within 100 characters.
 *
 * Reads files and builds objects only; no database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** A tree node as far as createFromNode() reads it */
class expTestTrashedByNode
{
    public function attribute( $name )
    {
        $values = array( 'node_id' => 501, 'parent_node_id' => 2, 'main_node_id' => 501, 'contentobject_id' => 301,
                         'path_string' => '/1/2/501/', 'path_identification_string' => 'a/b', 'remote_id' => 'r' );
        return isset( $values[$name] ) ? $values[$name] : 0;
    }
}

class TrashedByColumnsTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    /** @var array the $_SERVER['argv'] and current user of before the test */
    private $saved = array();

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    protected function setUp(): void
    {
        $this->saved = array( 'argv' => isset( $_SERVER['argv'] ) ? $_SERVER['argv'] : null,
                              'user' => isset( $GLOBALS['eZUserGlobalInstance_'] ) ? $GLOBALS['eZUserGlobalInstance_'] : null );
    }

    protected function tearDown(): void
    {
        if ( $this->saved['argv'] === null )
            unset( $_SERVER['argv'] );
        else
            $_SERVER['argv'] = $this->saved['argv'];
        if ( $this->saved['user'] === null )
            unset( $GLOBALS['eZUserGlobalInstance_'] );
        else
            $GLOBALS['eZUserGlobalInstance_'] = $this->saved['user'];
    }

    private static function read( $file )
    {
        return file_get_contents( self::$root . '/' . $file );
    }

    /** The SQL of a file without its comment lines */
    private static function statements( $file )
    {
        return preg_replace( '/^\s*--.*$/m', '', self::read( $file ) );
    }

    public function testKernelSchemasHaveTheColumns()
    {
        $this->assertMatchesRegularExpression( '/trashed_by int\(11\) NOT NULL default \'0\',\s*trashed_via varchar\(100\) NOT NULL default \'\'/',
                                               self::read( 'kernel/sql/mysql/kernel_schema.sql' ) );
        $this->assertMatchesRegularExpression( '/trashed_by integer DEFAULT 0 NOT NULL,\s*trashed_via character varying\(100\)/',
                                               self::read( 'kernel/sql/postgresql/kernel_schema.sql' ) );
        $this->assertMatchesRegularExpression( '/`trashed_by` integer NOT NULL DEFAULT \'0\'\s*,\s*`trashed_via` varchar\(100\)/',
                                               self::read( 'kernel/sql/sqlite/schema.sql' ) );

        $schema = eval( '?>' . self::read( 'share/db_schema.dba' ) . '<?php return $schema;' );
        $this->assertIsArray( $schema );
        $fields = $schema['ezcontentobject_trash']['fields'];
        $this->assertSame( array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ), $fields['trashed_by'] );
        $this->assertSame( array( 'length' => 100, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ), $fields['trashed_via'] );
    }

    public function testUpdateFilesAddTheColumns()
    {
        $mysql = self::statements( 'update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        foreach ( array( 'trashed_by' => 'int\(11\) NOT NULL DEFAULT \'\'0\'\'', 'trashed_via' => 'varchar\(100\) NOT NULL DEFAULT \'\'\'\'' ) as $column => $type )
        {
            $this->assertMatchesRegularExpression( "/COLUMN_NAME = '$column' \) = 0,\s*'ALTER TABLE ezcontentobject_trash ADD $column $type'/", $mysql, "MySQL $column, guarded" );
        }

        $postgresql = self::statements( 'update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        foreach ( array( 'trashed_by', 'trashed_via' ) as $column )
        {
            $this->assertMatchesRegularExpression( "/IF NOT EXISTS \( SELECT 1 FROM information_schema.columns[^;]*column_name = '$column' \) THEN\s*ALTER TABLE ezcontentobject_trash ADD $column /",
                                                   $postgresql, "PostgreSQL $column, guarded" );
        }

        $sqlite = self::statements( 'update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertStringContainsString( 'ALTER TABLE ezcontentobject_trash ADD COLUMN trashed_by integer NOT NULL DEFAULT 0;', $sqlite );
        $this->assertStringContainsString( "ALTER TABLE ezcontentobject_trash ADD COLUMN trashed_via varchar(100) NOT NULL DEFAULT '';", $sqlite );
    }

    public function testCreateFromNodeFillsTheColumns()
    {
        $definition = eZContentObjectTrashNode::definition();
        $this->assertArrayHasKey( 'trashed_by', $definition['fields'] );
        $this->assertSame( 100, $definition['fields']['trashed_via']['max_length'] );

        // the anonymous user is the current user without a database read
        $GLOBALS['eZUserGlobalInstance_'] = new eZUser( array( 'contentobject_id' => eZUser::anonymousId(), 'login' => 'anonymous' ) );
        $_SERVER['argv'] = array( 'bin/php/mytool.php' );
        $trashNode = eZContentObjectTrashNode::createFromNode( new expTestTrashedByNode() );
        $this->assertSame( (int)eZUser::anonymousId(), $trashNode->attribute( 'trashed_by' ) );
        $this->assertSame( 'cli mytool.php', $trashNode->attribute( 'trashed_via' ) );
        $this->assertGreaterThan( 0, $trashNode->attribute( 'trashed' ) );
    }

    public function testCurrentVia()
    {
        $_SERVER['argv'] = array( 'bin/php/ezexec.php', 'extension/x/bin/cleanup.php' );
        $this->assertSame( 'cli cleanup.php', eZContentObjectTrashNode::currentVia() );
        $_SERVER['argv'] = array( str_repeat( 'x', 300 ) . '.php' );
        $this->assertSame( 100, strlen( eZContentObjectTrashNode::currentVia() ) );
    }
}
