<?php
/**
 * Who moved an object to the trash is kept in the trash row: ezcontentobject_trash.trashed_by and trashed_via.
 *
 *  - The kernel schema of every engine and share/db_schema.dba have both columns.
 *  - The 6.0.0 to 6.0.15 update file of each engine adds them; MySQL and PostgreSQL only when missing.
 *  - eZContentObjectTrashNode knows them and createFromNode() fills them with the current user and where the
 *    removal came from.
 *  - currentVia() names the command line script, or the script ezexec.php runs, or the siteaccess of the request,
 *    in printable ASCII within 100 bytes.
 *  - Before the database update (hasTrashedByColumns() false) definition() leaves the two columns out.
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

    /**
     * Printable ASCII only: a script name in Latin-1 is no valid UTF-8 (PostgreSQL refuses the INSERT, which stops
     * the transaction of the trash move), and 100 multibyte characters are 200 bytes, more than a VARCHAR2(100)
     * that counts bytes (Oracle) holds.
     */
    public function testCurrentViaIsPrintableAsciiWithinOneHundredBytes()
    {
        $_SERVER['argv'] = array( "bin/php/caf\xE9.php" );
        $this->assertSame( 'cli caf?.php', eZContentObjectTrashNode::currentVia() );
        $_SERVER['argv'] = array( str_repeat( "\xC3\xA4", 150 ) . '.php' );
        $via = eZContentObjectTrashNode::currentVia();
        $this->assertLessThanOrEqual( 100, strlen( $via ), 'bytes' );
        $this->assertMatchesRegularExpression( '/^[\x20-\x7E]*$/', $via );
        $this->assertSame( "cli a?b", eZContentObjectTrashNode::cleanVia( "cli a\nb" ), 'no line break' );
    }

    /** Web requests: the siteaccess of each request, also in a Velocity worker that serves one after the other. */
    public function testCurrentViaNamesTheSiteaccessOfEachRequest()
    {
        $savedUri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : null;
        $savedAccess = isset( $GLOBALS['eZCurrentAccess'] ) ? $GLOBALS['eZCurrentAccess'] : null;
        try
        {
            $_SERVER['REQUEST_URI'] = '/content/remove';
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'admin' );
            $this->assertSame( 'web admin', eZContentObjectTrashNode::currentVia() );
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'site' );
            $this->assertSame( 'web site', eZContentObjectTrashNode::currentVia() );
            unset( $GLOBALS['eZCurrentAccess'] );
            $this->assertSame( 'web', eZContentObjectTrashNode::currentVia() );
        }
        finally
        {
            if ( $savedUri === null )
                unset( $_SERVER['REQUEST_URI'] );
            else
                $_SERVER['REQUEST_URI'] = $savedUri;
            if ( $savedAccess === null )
                unset( $GLOBALS['eZCurrentAccess'] );
            else
                $GLOBALS['eZCurrentAccess'] = $savedAccess;
        }
    }

    /**
     * movetrashrecords.php may neither copy from nor remove a trashed.json that holds no JSON object (all() reads it
     * as empty, so --remove-file would have deleted every entry); an entry without node_id or trashed matches no row
     * and raises no warning.
     */
    public function testAnUnreadableFileIsRecognisedAndABrokenEntryMatchesNothing()
    {
        if ( !class_exists( 'Exponential\\Service\\TrashRecord' ) )
            require_once self::$root . '/kernel/private/classes/services/trashrecord.php';
        $file = tempnam( sys_get_temp_dir(), 'trashed' );
        try
        {
            foreach ( array( '{"5":{"node_id":7,"trashed":1}}' => false, '{}' => false, '[]' => false, '' => false,
                             '{"5":{"node_id":7,' => true, 'not json' => true, '"a string"' => true ) as $content => $unreadable )
            {
                file_put_contents( $file, (string)$content );
                $this->assertSame( $unreadable, Exponential\Service\TrashRecord::isUnreadable( $file ), var_export( (string)$content, true ) );
            }
        }
        finally
        {
            unlink( $file );
        }
        $this->assertFalse( Exponential\Service\TrashRecord::isUnreadable( $file ), 'no file: nothing to read, nothing wrong' );

        $warnings = array();
        set_error_handler( function ( $level, $message ) use ( &$warnings ) { $warnings[] = $message; return true; } );
        try
        {
            $entry = Exponential\Service\TrashRecord::entryFor( array( '5' => array( 'user_id' => 14 ) ), 5, 7, 1 );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertNull( $entry );
        $this->assertSame( array(), $warnings );
    }

    /**
     * Before the database update the table has no trashed_by and trashed_via: once hasTrashedByColumns() has found
     * that, definition() leaves them out, so the INSERT of a trash move and the SELECT of a trash row name only
     * columns that exist. With the columns (or not asked yet) they are in.
     */
    public function testDefinitionLeavesTheColumnsOutWhileTheyAreMissing()
    {
        $flag = new ReflectionProperty( 'eZContentObjectTrashNode', 'trashedByColumns' );
        if ( PHP_VERSION_ID < 80100 )
            $flag->setAccessible( true );
        $saved = $flag->getValue();
        try
        {
            $flag->setValue( null, false );
            $definition = eZContentObjectTrashNode::definition();
            $this->assertArrayNotHasKey( 'trashed_by', $definition['fields'] );
            $this->assertArrayNotHasKey( 'trashed_via', $definition['fields'] );
            $this->assertArrayHasKey( 'trashed', $definition['fields'] );

            // the row built for a trash move keeps working: the fields the table lacks are simply not stored
            $GLOBALS['eZUserGlobalInstance_'] = new eZUser( array( 'contentobject_id' => eZUser::anonymousId(), 'login' => 'anonymous' ) );
            $trashNode = eZContentObjectTrashNode::createFromNode( new expTestTrashedByNode() );
            $this->assertSame( 301, (int)$trashNode->attribute( 'contentobject_id' ) );

            foreach ( array( null, true ) as $value )
            {
                $flag->setValue( null, $value );
                $definition = eZContentObjectTrashNode::definition();
                $this->assertArrayHasKey( 'trashed_by', $definition['fields'], var_export( $value, true ) );
                $this->assertArrayHasKey( 'trashed_via', $definition['fields'], var_export( $value, true ) );
            }
            // found once, kept: no database is asked again
            $flag->setValue( null, true );
            $this->assertTrue( eZContentObjectTrashNode::hasTrashedByColumns() );
        }
        finally
        {
            $flag->setValue( null, $saved );
        }
    }
}
