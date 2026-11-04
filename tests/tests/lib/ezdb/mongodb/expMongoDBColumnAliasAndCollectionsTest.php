<?php
/**
 * Two things the SQL engines answer and the MongoDB driver did not, both seen in a site package install on MongoDB
 * (the installer's checks reported "159 of 159 declared tables missing" and "257 class or attribute names stored
 * without a language", while the site was fine):
 *
 *  MA-01 - "SELECT column AS name": the column is projected and the row answers to the name; the column itself is
 *          left out unless it is selected under its own name as well
 *  MA-02 - ORDER BY an alias sorts by its column
 *  MA-03 - A schema reader made from a connection (expMongoSchema without a .dba schema) lists the database's
 *          collections as tables, instead of none
 *
 * No server needed: an in-process collection records the find() options and applies the projection.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group mongodb
 */

require_once __DIR__ . '/stubs.php';
foreach ( array( 'lib/ezdbschema/classes/ezdbschemainterface.php', 'lib/ezdbschema/classes/expmongoschema.php' ) as $file )
    require_once dirname( __DIR__, 5 ) . '/' . $file;

/** A collection that keeps the options of the last find() and applies its projection */
class expMongoDBColumnAliasCollection
{
    public static $lastOptions = array();
    public $rows;

    public function __construct( array &$rows )
    {
        $this->rows = &$rows;
    }

    public function find( array $filter = array(), array $options = array() )
    {
        self::$lastOptions = $options;
        $result = array();
        foreach ( $this->rows as $row )
        {
            if ( !empty( $options['projection'] ) )
            {
                $projected = array();
                foreach ( $options['projection'] as $field => $on )
                {
                    if ( $on && array_key_exists( $field, $row ) )
                        $projected[$field] = $row[$field];
                }
                $row = $projected;
            }
            $result[] = new StubMongoDocument( $row );
        }
        return $result;
    }
}

class expMongoDBColumnAliasClient
{
    private $collections;

    public function __construct( array &$collections )
    {
        $this->collections = &$collections;
    }

    public function selectCollection( $dbName, $collName )
    {
        if ( !isset( $this->collections[$collName] ) )
            $this->collections[$collName] = array();
        return new expMongoDBColumnAliasCollection( $this->collections[$collName] );
    }

    public function selectDatabase( $dbName )
    {
        return $this;
    }

    public function listCollectionNames()
    {
        return array_merge( array_keys( $this->collections ), array( 'system.views' ) );
    }
}

/** The driver with the recording client (getClient() of the testable driver is typed to its own stub) */
class expMongoDBColumnAliasRecordingDriver extends expMongoDB
{
    public $stubCollections = array();
    public $DB;

    public function __construct()
    {
        $this->TransactionCounter = 0;
        $this->IsConnected = true;
        $this->OutputSQL = false;
        $this->DB = 'mongo';
    }

    public function getClient()
    {
        return new expMongoDBColumnAliasClient( $this->stubCollections );
    }
}

class expMongoDBColumnAliasAndCollectionsTest extends PHPUnit\Framework\TestCase
{
    /** @var array the driver's schema statics before the test (it reads the .dba files of an installation) */
    private $saved = array();

    protected function setUp(): void
    {
        foreach ( array( 'DeclaredColumns', 'NumericColumns' ) as $name )
        {
            if ( !property_exists( 'expMongoDB', $name ) )
                continue;
            $property = new ReflectionProperty( 'expMongoDB', $name );
            if ( PHP_VERSION_ID < 80100 )
                $property->setAccessible( true );
            $this->saved[$name] = $property->getValue();
            $property->setValue( null, array() );
        }
    }

    protected function tearDown(): void
    {
        foreach ( $this->saved as $name => $value )
        {
            $property = new ReflectionProperty( 'expMongoDB', $name );
            if ( PHP_VERSION_ID < 80100 )
                $property->setAccessible( true );
            $property->setValue( null, $value );
        }
    }

    private function driver()
    {
        $db = new expMongoDBColumnAliasRecordingDriver();
        $db->stubCollections['ezcontentclass'] = array(
            array( 'id' => 2, 'version' => 0, 'identifier' => 'folder', 'serialized_name_list' => 'a:1:{s:6:"eng-US";s:6:"Folder";}' ),
            array( 'id' => 1, 'version' => 0, 'identifier' => 'article', 'serialized_name_list' => 'a:1:{s:6:"eng-US";s:7:"Article";}' ),
        );
        $db->stubCollections['ezcontentclass_attribute'] = array();
        return $db;
    }

    public function testColumnAlias()
    {
        $rows = $this->driver()->arrayQuery( 'SELECT serialized_name_list AS s FROM ezcontentclass WHERE version = 0' );
        $this->assertSame( array( '_id' => 0, 'serialized_name_list' => 1 ), expMongoDBColumnAliasCollection::$lastOptions['projection'] );
        $this->assertCount( 2, $rows );
        $this->assertSame( array( 's' ), array_keys( $rows[0] ) );
        $this->assertSame( 'a:1:{s:6:"eng-US";s:6:"Folder";}', $rows[0]['s'] );

        $rows = $this->driver()->arrayQuery( 'SELECT identifier, identifier AS i, ezcontentclass.id AS class_id FROM ezcontentclass' );
        $this->assertSame( array( 'identifier' => 'folder', 'i' => 'folder', 'class_id' => 2 ), $rows[0] );
    }

    public function testOrderByAlias()
    {
        $this->driver()->arrayQuery( 'SELECT identifier AS name FROM ezcontentclass ORDER BY name DESC' );
        $this->assertSame( array( 'identifier' => -1 ), expMongoDBColumnAliasCollection::$lastOptions['sort'] );
    }

    public function testSchemaReaderListsCollections()
    {
        $schema = new expMongoSchema( array( 'instance' => $this->driver() ) );
        $tables = $schema->schema( array( 'format' => 'local' ) );
        $this->assertSame( array( 'ezcontentclass', 'ezcontentclass_attribute' ), array_keys( $tables ) );
        $this->assertSame( array(), $tables['ezcontentclass']['fields'] );

        $given = new expMongoSchema( array( 'instance' => $this->driver(), 'schema' => array( 'eztags' => array( 'fields' => array() ) ) ) );
        $this->assertSame( array( 'eztags' ), array_keys( $given->schema() ), 'a reader given a .dba schema returns that schema' );
    }
}
