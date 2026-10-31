<?php
/**
 * Switching the GraphQL API on and off without a database (doc/eng-us/features/6.0/graphql.md, "Switching it on"):
 * the plans of exp:graphql:enable and exp:graphql:disable (and of the buttons of Setup > GraphQL) on a fixture
 * installation under var/tmp/ini-tests/, their dry run (a diff, nothing written), their writing and that a second run
 * changes nothing, the state of each siteaccess read back from the files, the origins of CORS, the scopes of a key,
 * and the statistics of the response cache.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

require_once dirname( __DIR__ ) . '/ini/fixtures/expinienginetestfixtures.php';

class expGraphQLSetupTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = expIniEngineTestFixtures::makeRoot( $this->name() );
        copy( dirname( __DIR__, 5 ) . '/settings/graphql.ini', $this->root . 'settings/graphql.ini' );
        copy( dirname( __DIR__, 5 ) . '/settings/rest.ini', $this->root . 'settings/rest.ini' );
    }

    protected function tearDown(): void
    {
        expIniEditor::setRoot( null );
        parent::tearDown();
    }

    protected function file( $relative )
    {
        return is_file( $this->root . $relative ) ? (string)file_get_contents( $this->root . $relative ) : null;
    }

    public function testEnableForSiteAccessesWithMutationsAndAnOrigin()
    {
        $changes = expGraphQLSetup::enableChanges( array( 'eng' ), array( 'https://shop.example.com/' ), true );
        $this->assertSame( array(
            array( 'global', 'graphql', 'set', 'Enabled', 'enabled' ),
            array( 'global', 'graphql', 'add', 'SiteAccessList', 'eng' ),
            array( 'siteaccess:eng', 'graphql', 'set', 'MutationsEnabled', 'enabled' ),
            array( 'global', 'rest', 'set', 'Cors', 'enabled' ),
            array( 'global', 'rest', 'add', 'AllowedOrigins', 'https://shop.example.com' ),
        ), array_map( function ( $c ) { return array( $c['scope'], $c['file'], $c['op'], $c['variable'], $c['value'] ); }, $changes ) );

        // the dry run: a diff of each file, nothing written
        $dry = expGraphQLSetup::apply( $changes, true );
        $this->assertTrue( $dry['ok'] );
        $this->assertSame( 0, $dry['written'] );
        $this->assertSame( array( 'settings/override/graphql.ini.append.php', 'settings/siteaccess/eng/graphql.ini.append.php', 'settings/override/rest.ini.append.php' ),
                           array_column( $dry['files'], 'path' ) );
        $this->assertStringContainsString( '+Enabled=enabled', $dry['files'][0]['diff'] );
        $this->assertStringContainsString( '+SiteAccessList[]=eng', $dry['files'][0]['diff'] );
        $this->assertNull( $this->file( 'settings/override/graphql.ini.append.php' ) );

        $done = expGraphQLSetup::apply( $changes, false );
        $this->assertTrue( $done['ok'] );
        $this->assertSame( 3, $done['written'] );
        $this->assertStringContainsString( "Enabled=enabled\nSiteAccessList[]=eng", $this->file( 'settings/override/graphql.ini.append.php' ) );
        $this->assertStringContainsString( 'MutationsEnabled=enabled', $this->file( 'settings/siteaccess/eng/graphql.ini.append.php' ) );
        $this->assertStringContainsString( "Cors=enabled\nAllowedOrigins[]=https://shop.example.com", $this->file( 'settings/override/rest.ini.append.php' ) );

        $states = array();
        foreach ( expGraphQLSetup::siteAccessStates() as $state )
            $states[$state['name']] = array( $state['enabled'], $state['mutations'] );
        $this->assertSame( array( 'eng' => array( true, true ), 'admin' => array( false, false ), 'plain' => array( false, false ) ), $states );

        // a second run changes nothing
        $again = expGraphQLSetup::apply( expGraphQLSetup::enableChanges( array( 'eng' ), array( 'https://shop.example.com' ), true ), false );
        $this->assertSame( 0, $again['written'] );
        $this->assertSame( array( false, false, false ), array_column( $again['files'], 'changed' ) );
    }

    public function testEnableEverywhereAndDisable()
    {
        $done = expGraphQLSetup::apply( expGraphQLSetup::enableChanges( array(), array(), false ), false );
        $this->assertSame( 1, $done['written'] );
        foreach ( expGraphQLSetup::siteAccessStates() as $state )
            $this->assertSame( array( true, false ), array( $state['enabled'], $state['mutations'] ), $state['name'] );

        // answering everywhere already: a siteaccess named is not added to SiteAccessList[] (that would switch the others off)
        $changes = expGraphQLSetup::enableChanges( array( 'admin' ), array(), false );
        $this->assertSame( array( 'Enabled' ), array_column( $changes, 'variable' ) );

        // one siteaccess off: settings/override wins over a siteaccess's file, so the others go into SiteAccessList[]
        expGraphQLSetup::apply( expGraphQLSetup::disableChanges( array( 'eng' ), false ), false );
        $this->assertStringContainsString( "SiteAccessList[]\nSiteAccessList[]=admin\nSiteAccessList[]=plain", $this->file( 'settings/override/graphql.ini.append.php' ) );
        $this->assertSame( array( 'eng' => false, 'admin' => true, 'plain' => true ), array_column( expGraphQLSetup::siteAccessStates(), 'enabled', 'name' ) );

        // on again: back into the list; the last one out switches the API off (an empty list means every siteaccess)
        expGraphQLSetup::apply( expGraphQLSetup::enableChanges( array( 'eng' ), array(), false ), false );
        $this->assertSame( array( 'eng' => true, 'admin' => true, 'plain' => true ), array_column( expGraphQLSetup::siteAccessStates(), 'enabled', 'name' ) );
        expGraphQLSetup::apply( expGraphQLSetup::disableChanges( array( 'eng', 'admin', 'plain' ), false ), false );
        $this->assertSame( array( false, false, false ), array_column( expGraphQLSetup::siteAccessStates(), 'enabled' ) );
        // on everywhere: the list is emptied
        expGraphQLSetup::apply( expGraphQLSetup::enableChanges( array(), array(), false ), false );
        $this->assertSame( array( true, true, true ), array_column( expGraphQLSetup::siteAccessStates(), 'enabled' ) );

        // the mutations: everywhere, then off in one siteaccess (the global value moves into the others' files)
        expGraphQLSetup::apply( expGraphQLSetup::mutationChanges( array(), true ), false );
        $this->assertSame( array( true, true, true ), array_column( expGraphQLSetup::siteAccessStates(), 'mutations' ) );
        expGraphQLSetup::apply( expGraphQLSetup::disableChanges( array( 'eng' ), true ), false );
        $this->assertSame( array( 'eng' => false, 'admin' => true, 'plain' => true ), array_column( expGraphQLSetup::siteAccessStates(), 'mutations', 'name' ) );
        $this->assertStringNotContainsString( 'MutationsEnabled', $this->file( 'settings/override/graphql.ini.append.php' ) );
        expGraphQLSetup::apply( expGraphQLSetup::disableChanges( array(), true ), false );
        $this->assertSame( array( false, false, false ), array_column( expGraphQLSetup::siteAccessStates(), 'mutations' ) );
        $this->assertSame( array( true, true, true ), array_column( expGraphQLSetup::siteAccessStates(), 'enabled' ) );
        expGraphQLSetup::apply( expGraphQLSetup::disableChanges( array(), false ), false );
        $this->assertSame( array( false, false, false ), array_column( expGraphQLSetup::siteAccessStates(), 'enabled' ) );
    }

    public function testAnUnknownSiteAccessIsAnError()
    {
        $result = expGraphQLSetup::apply( expGraphQLSetup::mutationChanges( array( 'nosuch' ), true ), false );
        $this->assertFalse( $result['ok'] );
        $this->assertNotNull( $result['files'][0]['error'] );
        $this->assertSame( 0, $result['written'] );
    }

    public function testOrigins()
    {
        foreach ( array( 'https://shop.example.com', 'https://shop.example.com/', 'http://localhost:3000', 'https://a.b-c.example:8443' ) as $origin )
            $this->assertTrue( expGraphQLSetup::isOrigin( $origin ), $origin );
        foreach ( array( '', 'shop.example.com', 'https://shop.example.com/path', 'ftp://x', '*', 'https://', 'https://x.com?a=1' ) as $origin )
            $this->assertFalse( expGraphQLSetup::isOrigin( $origin ), $origin );
        $this->expectException( 'expGraphQLError' );
        expGraphQLSetup::enableChanges( array(), array( 'https://shop.example.com/path' ), false );
    }

    public function testKeyScopes()
    {
        $this->assertSame( array( 'graphql:read' ), expGraphQLSetup::keyScopes( array(), false ) );
        $this->assertSame( array( 'graphql:read', 'graphql:write' ), expGraphQLSetup::keyScopes( array(), true ) );
        $this->assertSame( array( 'graphql:read', 'graphql:write' ), expGraphQLSetup::keyScopes( array( 'graphql:write' ), false ), 'a key that writes also reads' );
        $this->assertTrue( expGraphQLSetup::isGraphQLKey( array( 'read', 'graphql:read' ) ) );
        $this->assertFalse( expGraphQLSetup::isGraphQLKey( array( 'read', 'edit' ) ) );
        $this->expectException( 'expGraphQLError' );
        expGraphQLSetup::keyScopes( array( 'graphql:read', 'edit' ), false );
    }

    public function testResponseCacheStats()
    {
        $dir = $this->root . 'var/graphql-cache';
        $cache = new expGraphQLResponseCache( $dir );
        $this->assertSame( array( 'entries' => 0, 'bytes' => 0, 'expiredAt' => 0 ), $cache->stats() );
        $cache->write( str_repeat( 'a', 40 ), '{"data":{}}', array( 'etag' => '"x"', 'schemaHash' => 'h', 'ttl' => 60 ) );
        $cache->write( str_repeat( 'b', 40 ), '{"data":{"a":1}}', array( 'etag' => '"y"', 'schemaHash' => 'h', 'ttl' => 60 ) );
        $stats = $cache->stats();
        $this->assertSame( 2, $stats['entries'] );
        $this->assertGreaterThan( 0, $stats['bytes'] );
        $cache->clear();
        $stats = $cache->stats();
        $this->assertSame( 0, $stats['entries'] );
        $this->assertGreaterThan( 0, $stats['expiredAt'] );
    }
}

?>
