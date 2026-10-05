<?php
/**
 * The eZPersistentObject classes of cjw_newsletter against share/db_schema.dba: every table has a class whose
 * definition() has exactly the columns of the table, the classes of the 4.2.0 tables live in the folder of their
 * area, and each of them stores, fetches, lists, counts and removes a row on the installation's database. The test
 * rows carry the marker nltest-phpunit and are removed in tearDown(), whatever happened.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/cjw_newsletter/cjwNewsletterPersistentClassesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\TestCase;

class cjwNewsletterPersistentClassesTest extends TestCase
{
    const MARKER = 'nltest-phpunit';

    /** table => class of the 4.1 tables */
    private static $classes41 = array(
        'cjwnl_blacklist_item' => 'CjwNewsletterBlacklistItem',
        'cjwnl_edition' => 'CjwNewsletterEdition',
        'cjwnl_edition_send' => 'CjwNewsletterEditionSend',
        'cjwnl_edition_send_item' => 'CjwNewsletterEditionSendItem',
        'cjwnl_import' => 'CjwNewsletterImport',
        'cjwnl_list' => 'CjwNewsletterList',
        'cjwnl_mailbox' => 'CjwNewsletterMailbox',
        'cjwnl_mailbox_item' => 'CjwNewsletterMailboxItem',
        'cjwnl_subscription' => 'CjwNewsletterSubscription',
        'cjwnl_user' => 'CjwNewsletterUser',
    );

    /** class => folder of the 4.2.0 tables */
    private static $folders = array(
        'CjwNewsletterThrottleState' => 'deliverability', 'CjwNewsletterSendBatch' => 'deliverability',
        'CjwNewsletterMailinAddress' => 'deliverability', 'CjwNewsletterMailinMessage' => 'deliverability',
        'CjwNewsletterTestGroup' => 'deliverability',
        'CjwNewsletterSchedule' => 'editorial', 'CjwNewsletterScheduleLog' => 'editorial', 'CjwNewsletterArticlePool' => 'editorial',
        'CjwNewsletterEditionArticle' => 'editorial', 'CjwNewsletterApproval' => 'editorial',
        'CjwNewsletterInterest' => 'rendering', 'CjwNewsletterUserInterest' => 'rendering', 'CjwNewsletterEditionSendOutput' => 'rendering',
        'CjwNewsletterLink' => 'statistics', 'CjwNewsletterLinkClick' => 'statistics', 'CjwNewsletterOpen' => 'statistics',
        'CjwNewsletterStatTotal' => 'statistics', 'CjwNewsletterAbTest' => 'statistics', 'CjwNewsletterAbVariant' => 'statistics',
        'CjwNewsletterSmsCode' => 'sms', 'CjwNewsletterSmsMessage' => 'sms', 'CjwNewsletterSmsInbound' => 'sms',
        'CjwNewsletterImportMapping' => 'importexport', 'CjwNewsletterMigrationLog' => 'importexport',
    );

    private static $dba;
    private static $unavailable = null;
    private $stored = array();

    public static function setUpBeforeClass(): void
    {
        $file = dirname( __DIR__, 4 ) . '/extension/cjw_newsletter/share/db_schema.dba';
        if ( !is_file( $file ) )
            self::markTestSkipped( 'cjw_newsletter is not installed in extension/' );
        $schema = null;
        include $file;
        unset( $schema['_info'] );
        self::$dba = $schema;
        // the kernel is started here, outside a test (it installs handlers of its own)
        self::$unavailable = ezpLiveInstallation::unavailableReason();
        if ( self::$unavailable === null && !class_exists( 'CjwNewsletterUser' ) )
            self::$unavailable = 'cjw_newsletter is not active';
    }

    protected function setUp(): void
    {
        if ( self::$unavailable !== null )
            $this->markTestSkipped( self::$unavailable );
    }

    protected function tearDown(): void
    {
        foreach ( array_reverse( $this->stored ) as $row )
        {
            list( $class, $id ) = $row;
            $definition = call_user_func( array( $class, 'definition' ) );
            eZDB::instance()->query( 'DELETE FROM ' . $definition['name'] . ' WHERE id = ' . (int)$id );
        }
        $this->stored = array();
    }

    /** @return array table => class of every table of the .dba */
    private static function classOfTable()
    {
        $map = self::$classes41;
        foreach ( array_keys( self::$folders ) as $class )
        {
            $definition = call_user_func( array( $class, 'definition' ) );
            $map[$definition['name']] = $class;
        }
        return $map;
    }

    public function testEveryTableOfTheDbaHasAClassWithExactlyItsColumns()
    {
        $map = self::classOfTable();
        $this->assertEqualsCanonicalizing( array_keys( self::$dba ), array_keys( $map ), 'one class per table' );
        foreach ( $map as $table => $class )
        {
            $this->assertTrue( class_exists( $class ), "$class is autoloaded" );
            $definition = call_user_func( array( $class, 'definition' ) );
            $this->assertSame( $table, $definition['name'], "$class is the class of $table" );
            $this->assertEqualsCanonicalizing( array_keys( self::$dba[$table]['fields'] ), array_keys( $definition['fields'] ), "the fields of $class" );
        }
    }

    public function testTheNewClassesLiveInTheFolderOfTheirArea()
    {
        foreach ( self::$folders as $class => $folder )
        {
            $file = ( new ReflectionClass( $class ) )->getFileName();
            $this->assertStringContainsString( "/extension/cjw_newsletter/classes/$folder/" . strtolower( $class ) . '.php', $file );
            $this->assertTrue( is_subclass_of( $class, 'eZPersistentObject' ) );
        }
    }

    /** a value for a field, distinct per row so that unique indexes never collide */
    private function value( $field, $def, $seed )
    {
        if ( $def['type'] === 'int' || $def['type'] === 'tinyint' )
            return $def['type'] === 'tinyint' ? 1 : 900000000 + $seed;
        $length = isset( $def['length'] ) ? (int)$def['length'] : 255;
        return substr( self::MARKER . '-' . $seed, 0, max( 1, $length ) );
    }

    public function testEachNewClassStoresFetchesListsCountsAndRemovesARow()
    {
        $seed = getmypid() * 100;
        foreach ( self::$folders as $class => $folder )
        {
            $definition = call_user_func( array( $class, 'definition' ) );
            $table = $definition['name'];
            $row = array();
            foreach ( self::$dba[$table]['fields'] as $field => $def )
                if ( $field !== 'id' )
                    $row[$field] = $this->value( $field, $def, ++$seed );

            $object = call_user_func( array( $class, 'create' ), $row );
            $this->assertInstanceOf( $class, $object );
            $object->store();
            $id = (int)$object->attribute( 'id' );
            $this->assertGreaterThan( 0, $id, "$class got an id" );
            $this->stored[] = array( $class, $id );

            $fetched = call_user_func( array( $class, 'fetch' ), $id );
            $this->assertInstanceOf( $class, $fetched, "$class::fetch" );
            foreach ( $row as $field => $value )
                $this->assertEquals( $value, $fetched->attribute( $field ), "$class.$field read back" );

            $firstField = array_keys( $row )[0];
            $conditions = array( 'id' => $id );
            $this->assertCount( 1, call_user_func( array( $class, 'fetchList' ), $conditions ), "$class::fetchList" );
            $this->assertSame( 1, call_user_func( array( $class, 'fetchListCount' ), $conditions ), "$class::fetchListCount" );

            // the index helpers find the row
            foreach ( self::$dba[$table]['indexes'] as $name => $index )
            {
                if ( $index['type'] === 'primary' )
                    continue;
                $method = ( $index['type'] === 'unique' ? 'fetchBy' : 'fetchListBy' )
                    . implode( 'And', array_map( function ( $f ) { return str_replace( ' ', '', ucwords( str_replace( '_', ' ', $f ) ) ); }, $index['fields'] ) );
                $this->assertTrue( method_exists( $class, $method ), "$class::$method" );
                $args = array();
                foreach ( $index['fields'] as $f )
                    $args[] = $row[$f];
                $found = call_user_func_array( array( $class, $method ), $args );
                if ( $index['type'] === 'unique' )
                    $this->assertSame( $id, (int)$found->attribute( 'id' ), "$class::$method" );
                else
                    $this->assertContains( $id, array_map( function ( $o ) { return (int)$o->attribute( 'id' ); }, $found ), "$class::$method" );
            }

            $fetched->remove();
            $this->assertNull( call_user_func( array( $class, 'fetch' ), $id ), "$class removed" );
            $this->assertNull( call_user_func( array( $class, 'fetch' ), 0 ), "$class::fetch( 0 ) is null" );
        }
    }

    public function testTheNewColumnsOfThe41TablesHaveTheirDefaults()
    {
        $user = CjwNewsletterUser::create( self::MARKER . '-' . getmypid() . '@example.invalid', 0, 'Test', 'Columns', 0, CjwNewsletterUser::STATUS_PENDING, 'default', '', '', '', '' );
        $user->store();
        $this->stored[] = array( 'CjwNewsletterUser', (int)$user->attribute( 'id' ) );
        $fetched = CjwNewsletterUser::fetch( $user->attribute( 'id' ) );
        $this->assertSame( '', (string)$fetched->attribute( 'language' ) );
        $this->assertSame( '', (string)$fetched->attribute( 'phone_number' ) );
        $this->assertSame( 0, (int)$fetched->attribute( 'phone_status' ) );
        $this->assertSame( 0, (int)$fetched->attribute( 'soft_bounce_count' ) );
        $fetched->setAttribute( 'language', 'ger-DE' );
        $fetched->store();
        $this->assertSame( 'ger-DE', CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'language' ) );
    }
}
