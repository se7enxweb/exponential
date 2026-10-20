<?php
/**
 * The context help (doc/en/us/features/6.0/help-system.md, "Context help"): which help page the "?" control of an admin page
 * opens, from a given map, without settings or a database.
 *
 *  HC-01  module/view hit: level view, the url is the viewer's with the anchor
 *  HC-02  fallback: module, then the dashboard
 *  HC-03  a design's own entry wins over the generic one, only for that design
 *  HC-04  entries naming a refused page (.., absolute, settings, doxygen, other suffixes, unknown) are skipped
 *  HC-05  hostile module/view/design names and anchors never reach the url
 *  HC-06  providers (extensions) come first; a provider's bad answer falls back to the map
 *  HC-07  the shipped [ContextHelp] map: every entry is syntactically valid and names a tracked page below doc/
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpContextRegistryTest extends PHPUnit\Framework\TestCase
{
    const PAGES = array( 'help/setup-cache.md' => 'Clearing caches', 'help/setup.md' => 'Setup', 'help/admin4-dashboard.md' => 'Dashboard (admin4)',
                         'help/content.md' => 'Content', 'help/layouts.md' => 'Layouts', 'guides/editing.txt' => 'Editing' );

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private function registry( array $map, array $providers = array() )
    {
        $pages = self::PAGES;
        return new expHelpContextRegistry( $map, $providers, static function ( $id ) use ( $pages ) {
            return isset( $pages[$id] ) ? array( 'title' => $pages[$id] ) : null;
        } );
    }

    protected function tearDown(): void
    {
        if ( method_exists( 'expHelpContextRegistry', 'resetInstance' ) )
            expHelpContextRegistry::resetInstance();
    }

    /** HC-01 */
    public function testViewHit()
    {
        $r = $this->registry( array( 'setup/cache' => 'help/setup-cache.md#clear-all', 'setup' => 'help/setup.md' ) );
        $hit = $r->lookup( 'setup', 'cache', 'admin4' );
        $this->assertSame( 'view', $hit['level'] );
        $this->assertSame( 'help/setup-cache.md', $hit['id'] );
        $this->assertSame( 'clear-all', $hit['anchor'] );
        $this->assertSame( expHelp::url( 'viewer', 'help/setup-cache.md', 'clear-all' ), $hit['url'] );
        $this->assertSame( '/exphelp/viewer/help/setup-cache.md#clear-all', $hit['url'] );
        $this->assertSame( 'Clearing caches', $hit['title'] );
    }

    /** HC-02 */
    public function testFallbacks()
    {
        $r = $this->registry( array( 'setup/cache' => 'help/setup-cache.md', 'setup' => 'help/setup.md' ) );
        $hit = $r->lookup( 'setup', 'info', '' );
        $this->assertSame( 'module', $hit['level'] );
        $this->assertSame( 'help/setup.md', $hit['id'] );
        $hit = $r->lookup( 'role', 'list', '' );
        $this->assertSame( 'dashboard', $hit['level'] );
        $this->assertSame( '/exphelp/dashboard', $hit['url'] );
        $this->assertSame( '', $hit['id'] );
    }

    /** HC-03 */
    public function testDesignEntryWins()
    {
        $r = $this->registry( array( 'admin4:content/dashboard' => 'help/admin4-dashboard.md', 'content/dashboard' => 'help/content.md',
                                     'editor:content' => 'guides/editing.txt' ) );
        $this->assertSame( 'help/admin4-dashboard.md', $r->lookup( 'content', 'dashboard', 'admin4' )['id'] );
        $this->assertSame( 'help/content.md', $r->lookup( 'content', 'dashboard', 'admin' )['id'] );
        $this->assertSame( 'help/content.md', $r->lookup( 'content', 'dashboard', '' )['id'] );
        // design:module beats nothing, but module/view (generic, more specific) is tried before design:module
        $this->assertSame( 'guides/editing.txt', $r->lookup( 'content', 'edit', 'editor' )['id'] );
        $this->assertSame( 'module', $r->lookup( 'content', 'edit', 'editor' )['level'] );
    }

    public static function refusedTargets()
    {
        return array(
            'parent'        => array( '../settings/site.ini' ),
            'parent inside' => array( 'help/../../settings/site.ini.md' ),
            'absolute'      => array( '/etc/passwd.md' ),
            'settings'      => array( 'settings/site.ini' ),
            'php'           => array( 'help/setup.php' ),
            'doxygen'       => array( 'doxygen/html/index.md' ),
            'hidden'        => array( 'help/.secret.md' ),
            'scheme'        => array( 'javascript:alert(1)' ),
            'url'           => array( 'https://example.com/x.md' ),
            'unknown'       => array( 'help/not-there.md' ),
            'encoded'       => array( '%2e%2e/settings/site.ini.md' ),
            'nul'           => array( "help/setup.md\0.md" ),
        );
    }

    /**
     * HC-04
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'refusedTargets' )]
    public function testRefusedTargetFallsBack( $target )
    {
        $r = $this->registry( array( 'setup/cache' => $target, 'setup' => 'help/setup.md' ) );
        $hit = $r->lookup( 'setup', 'cache', '' );
        $this->assertSame( 'module', $hit['level'], 'refused: ' . $target );
        $this->assertSame( 'help/setup.md', $hit['id'] );
    }

    public function testDocPrefixStripped()
    {
        $hit = $this->registry( array( 'setup' => 'doc/en/us/help/setup.md' ) )->lookup( 'setup', 'x', '' );
        $this->assertSame( 'help/setup.md', $hit['id'] );
    }

    /** HC-05 */
    public function testHostileNames()
    {
        $r = $this->registry( array( 'setup' => 'help/setup.md#"><script>alert(1)</script>' ) );
        $hit = $r->lookup( 'setup', '', '' );
        $this->assertSame( 'help/setup.md', $hit['id'], 'a bad anchor is dropped, the page kept' );
        $this->assertSame( '', $hit['anchor'] );
        $this->assertStringNotContainsString( '<', $hit['url'] );
        foreach ( array( array( '<script>', 'x', '' ), array( '../setup', '', '' ), array( 'setup', '"><img>', '' ),
                         array( 'setup', 'cache', '<svg onload=1>' ) ) as $args )
        {
            $hit = $r->lookup( $args[0], $args[1], $args[2] );
            $this->assertDoesNotMatchRegularExpression( '/[<>"\']/', $hit['url'] . $hit['anchor'] . $hit['id'], implode( ' ', $args ) );
        }
        $this->assertSame( 'dashboard', $r->lookup( '<script>', 'x', '' )['level'] );
    }

    /** HC-06 */
    public function testProvidersFirst()
    {
        if ( !interface_exists( 'expHelpContextProviderInterface' ) )
            $this->markTestSkipped( 'expHelpContextProviderInterface is not there yet' );
        $map = array( 'layouts' => 'help/content.md' );
        $provider = static function ( $answer ) {
            // eval-free: an anonymous class implementing the interface by name
            return new class( $answer ) implements expHelpContextProviderInterface {
                private $answer;
                public function __construct( $answer ) { $this->answer = $answer; }
                public function helpContext( $module, $view, $design ) { return $module === 'layouts' ? $this->answer : null; }
            };
        };
        $hit = $this->registry( $map, array( $provider( 'help/layouts.md#editor' ) ) )->lookup( 'layouts', 'edit', '' );
        $this->assertSame( 'help/layouts.md', $hit['id'] );
        $this->assertSame( 'editor', $hit['anchor'] );
        $this->assertSame( 'view', $hit['level'] );
        $hit = $this->registry( $map, array( $provider( '../../settings/site.ini' ) ) )->lookup( 'layouts', 'edit', '' );
        $this->assertSame( 'help/content.md', $hit['id'], 'a refused provider answer falls back to the map' );
        $hit = $this->registry( $map, array( $provider( null ) ) )->lookup( 'other', 'x', '' );
        $this->assertSame( 'dashboard', $hit['level'] );
    }

    /** HC-07 */
    public function testShippedMap()
    {
        // the shipped file itself, not the INI of this installation (settings/override may add entries)
        $ini = eZINI::fetchFromFile( self::root() . '/settings/exphelp.ini' );
        $map = $ini->hasVariable( 'ContextHelp', 'Map' ) ? (array)$ini->variable( 'ContextHelp', 'Map' ) : array();
        $this->assertIsArray( $map );
        $tracked = self::tracked();
        foreach ( $map as $key => $target )
        {
            $parsed = expHelpContextRegistry::parseTarget( $target );
            $this->assertNotEmpty( $parsed, 'Map[' . $key . ']=' . $target . ' is valid' );
            $id = preg_replace( '/#.*$/', '', $parsed['id'] );
            // a page id is the path below doc/en/us/ (or below doc/ for a file in no language directory)
            if ( $tracked !== null && strpos( $id, 'ext/' ) !== 0 )
                $this->assertTrue( in_array( 'doc/en/us/' . $id, $tracked, true ) || in_array( 'doc/' . $id, $tracked, true ),
                                   'Map[' . $key . '] names a tracked page' );
        }
    }

    /** @return string[]|null the tracked files below doc/, null without git */
    private static function tracked()
    {
        if ( !is_dir( self::root() . '/.git' ) && !is_file( self::root() . '/.git' ) )
            return null;
        $out = array();
        $rc = 1;
        exec( 'git -C ' . escapeshellarg( self::root() ) . ' ls-files doc/ 2>/dev/null', $out, $rc );
        return $rc === 0 && $out ? $out : null;
    }
}
