<?php
/**
 * expSearchSuggest, the suggestions of the administration's header search (content/suggest): the decisions that need
 * no database. DB-less: only the static, pure methods are called.
 *
 *  SS-01  the settings: defaults, unknown groups left out, limits held between 0 and 20, an unknown wildcard mode is auto
 *  SS-02  the fallbacks: eZ Find answers only as the search engine with the extension active; PDF pages only with
 *         eZ Find, ezfindpdfdeepsearch active and its fetch function; else the kernel's search engine, also when the
 *         configured engine could not be loaded
 *  SS-03  the search text gets a "*" after its last word only with wildcards on and a long enough word
 *  SS-04  matching: every typed word, without case, best first
 *  SS-05  permissions: readable() asks one item after the other and stops at the limit; classes only for a user who
 *         may open the class module
 *  SS-06  the admin pages: links of a rendered menu (site addresses only, once each, with the tab before them), the
 *         left menu template of a navigation part as the page layout includes it
 *  SS-07  the answer: groups in the configured order, cut to their limits, empty and switched off ones left out
 *  SS-08  the view: content/suggest is declared with the policy function read and its options as view parameters,
 *         both classes are in the kernel autoload
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expSearchSuggestTest.php
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expSearchSuggestTest extends PHPUnit\Framework\TestCase
{
    /** An INI stand-in for admininterface.ini */
    private static function ini( array $values )
    {
        return new class( $values ) {
            private $values;
            public function __construct( array $values ) { $this->values = $values; }
            public function hasVariable( $group, $name ) { return $group === 'SearchSuggestSettings' && array_key_exists( $name, $this->values ); }
            public function variable( $group, $name ) { return $this->values[$name]; }
        };
    }

    /** SS-01 */
    public function testTheSettingsHaveDefaultsAndBounds()
    {
        $s = expSearchSuggest::settings( self::ini( array() ) );
        $this->assertTrue( $s['enabled'] );
        $this->assertSame( 3, $s['min_length'] );
        $this->assertSame( expSearchSuggest::GROUPS, $s['groups'] );
        $this->assertSame( 'auto', $s['wildcard'] );
        $this->assertSame( array( 'file' ), $s['page_hit_classes'] );

        $s = expSearchSuggest::settings( self::ini( array(
            'Enabled' => 'false', 'MinLength' => '0', 'Groups' => array( 'admin', 'nonsense', 'content', 'admin' ),
            'Limits' => array( 'content' => '25', 'users' => '-1', 'admin' => '2' ), 'Wildcard' => 'sometimes' ) ) );
        $this->assertFalse( $s['enabled'] );
        $this->assertSame( 1, $s['min_length'] );
        $this->assertSame( array( 'admin', 'content' ), $s['groups'] );
        $this->assertSame( 20, $s['limits']['content'] );
        $this->assertSame( 0, $s['limits']['users'] );
        $this->assertSame( 2, $s['limits']['admin'] );
        $this->assertSame( expSearchSuggest::DEFAULT_LIMIT, $s['limits']['classes'] );
        $this->assertSame( 'auto', $s['wildcard'] );
    }

    /** SS-02 */
    public static function backendProvider()
    {
        $all = array( 'ezjscore', 'ezfind', 'ezfindpdfdeepsearch' );
        return array(
            'eZ Find with deep search'          => array( 'eZSolr', $all, true, 'ezfind', true ),
            'eZ Find, deep search inactive'     => array( 'eZSolr', array( 'ezfind' ), true, 'ezfind', false ),
            'eZ Find, no page_hits function'    => array( 'eZSolr', $all, false, 'ezfind', false ),
            'eZSolr class, ezfind not active'   => array( 'eZSolr', array( 'ezfindpdfdeepsearch' ), true, 'kernel', false ),
            'no engine could be loaded'         => array( '', $all, true, 'kernel', false ),
            'kernel engine, extensions active'  => array( 'eZSearchEngine', $all, true, 'kernel', false ),
            'another engine'                    => array( 'mySearchEngine', $all, true, 'other', false ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'backendProvider' )]
    public function testTheFallbacksFollowTheExtensionsAndTheEngine( $engine, array $extensions, $pageHits, $expected, $pages )
    {
        $this->assertSame( array( 'engine' => $expected, 'pages' => $pages ), expSearchSuggest::backend( $engine, $extensions, $pageHits ) );
    }

    /** SS-03 */
    public function testTheWildcardFollowsTheSiteSetting()
    {
        $this->assertSame( 'oste', expSearchSuggest::searchText( 'oste', 'auto', false, 3 ) );
        $this->assertSame( 'oste*', expSearchSuggest::searchText( 'oste', 'auto', true, 3 ) );
        $this->assertSame( 'oste*', expSearchSuggest::searchText( ' oste ', 'enabled', false, 3 ) );
        $this->assertSame( 'oste', expSearchSuggest::searchText( 'oste', 'disabled', true, 3 ) );
        $this->assertSame( 'ostern ab', expSearchSuggest::searchText( 'ostern ab', 'enabled', true, 3 ) );
        $this->assertSame( 'oste*', expSearchSuggest::searchText( 'oste*', 'enabled', true, 3 ) );
        $this->assertSame( '"frohe oste', expSearchSuggest::searchText( '"frohe oste', 'enabled', true, 3 ) );
        $this->assertSame( 'größe*', expSearchSuggest::searchText( 'größe', 'enabled', true, 5 ) );
    }

    /** SS-04 */
    public function testEveryTypedWordMustMatchBestFirst()
    {
        $this->assertSame( 0, expSearchSuggest::score( 'Setup › Cache', 'cache foo' ) );
        $this->assertSame( 3, expSearchSuggest::score( 'Cache management', 'CACHE' ) );
        $this->assertSame( 2, expSearchSuggest::score( 'Setup › Cache', 'cache setup' ) );
        $this->assertSame( 1, expSearchSuggest::score( 'Setup › Solr-Admin', 'dmin' ) );
        $this->assertSame( 3, expSearchSuggest::score( 'Übersicht', 'übers' ) );

        $items = array( array( 'title' => 'Setup › Template-Cache' ), array( 'title' => 'Nothing' ),
                        array( 'title' => 'Cache-Verwaltung' ), array( 'title' => 'Setup › Cache' ) );
        $this->assertSame( array( 'Cache-Verwaltung', 'Setup › Template-Cache', 'Setup › Cache' ),
                           array_column( expSearchSuggest::matchItems( $items, 'cache' ), 'title' ) );
    }

    /** SS-05 */
    public function testOnlyReadableItemsUpToTheLimit()
    {
        $asked = array();
        $result = expSearchSuggest::readable( array( 1, 2, 3, 4, 5, 6 ), function ( $id ) use ( &$asked ) {
            $asked[] = $id;
            return $id % 2 === 0;
        }, 2 );
        $this->assertSame( array( 2, 4 ), $result );
        $this->assertSame( array( 1, 2, 3, 4 ), $asked, 'nobody is asked about once the limit is reached' );

        $classes = array( array( 'id' => 1, 'identifier' => 'folder', 'name' => 'Folder' ),
                          array( 'id' => 2, 'identifier' => 'article', 'name' => 'Artikel' ),
                          array( 'id' => 4, 'identifier' => 'user', 'name' => 'Benutzer' ) );
        $this->assertSame( array(), expSearchSuggest::matchClasses( $classes, 'folder', false ) );
        $this->assertSame( array( array( 'id' => 2, 'identifier' => 'article', 'title' => 'Artikel' ) ),
                           expSearchSuggest::matchClasses( $classes, 'artic', true ), 'by identifier too' );
    }

    /** SS-06 */
    public function testTheLinksOfARenderedMenu()
    {
        $html = '<h4>Setup</h4><ul><li><a href="/admin/setup/cache">Cache   <b>management</b></a></li>'
              . '<li><a href="#">Toggle</a></li><li><a href="javascript:void(0)">Script</a></li>'
              . '<li><a href="//evil.example/x">Other host</a></li><li><a href="https://evil.example/x">Absolute</a></li>'
              . '<li><a href="/admin/setup/info"></a></li><li><a href="/admin/setup/cache">Again</a></li>'
              . '<li><a href="/admin/class/grouplist">Klassen &amp; Gruppen</a></li></ul>';
        $this->assertSame( array( array( 'title' => 'Setup › Cache management', 'url' => '/admin/setup/cache' ),
                                  array( 'title' => 'Setup › Klassen & Gruppen', 'url' => '/admin/class/grouplist' ) ),
                           expSearchSuggest::menuLinks( $html, 'Setup' ) );
        $this->assertSame( array(), expSearchSuggest::menuLinks( '' ) );

        $this->assertSame( 'design:parts/setup/menu.tpl', expSearchSuggest::menuTemplate( 'ezsetupnavigationpart' ) );
        $this->assertSame( 'design:parts/visual/menu.tpl', expSearchSuggest::menuTemplate( 'ezvisualnavigationpart' ) );
        $this->assertSame( 'design:parts/audit/menu.tpl', expSearchSuggest::menuTemplate( 'expauditnavigationpart' ) );
        $this->assertSame( 'design:parts/experiment/menu.tpl', expSearchSuggest::menuTemplate( 'experimentnavigationpart' ) );
        $this->assertNull( expSearchSuggest::menuTemplate( '../x' ) );
        $this->assertNull( expSearchSuggest::menuTemplate( 'eznavigationpart' ) );
    }

    /** SS-07 */
    public function testTheAnswerKeepsOrderAndLimits()
    {
        $settings = expSearchSuggest::settings( self::ini( array(
            'Groups' => array( 'admin', 'content', 'users', 'classes' ),
            'Limits' => array( 'admin' => 2, 'content' => 3, 'users' => 0, 'classes' => 3 ) ) ) );
        $item = function ( $title ) { return array( 'title' => $title, 'url' => '/' . $title ); };
        $found = array(
            'content' => array( 'items' => array( $item( 'a' ), $item( 'b' ), $item( 'c' ), $item( 'd' ) ), 'count' => 40 ),
            'users' => array( 'items' => array( $item( 'u' ) ) ),
            'classes' => array( 'items' => array() ),
            'admin' => array( 'items' => array( $item( 'x' ), $item( 'y' ), $item( 'z' ) ) ),
            'pages' => array( 'items' => array( $item( 'p' ) ) ),
        );
        $answer = expSearchSuggest::assemble( $found, $settings, array( 'content' => 'Content', 'admin' => 'Administration' ), 'abc', 40 );
        $this->assertSame( 'abc', $answer['query'] );
        $this->assertSame( 40, $answer['total'] );
        $this->assertSame( array( 'admin', 'content' ), array_column( $answer['groups'], 'id' ), 'users off (0), classes empty, pages not configured' );
        $this->assertSame( array( 'x', 'y' ), array_column( $answer['groups'][0]['items'], 'title' ) );
        $this->assertSame( 3, $answer['groups'][0]['count'] );
        $this->assertSame( 'Administration', $answer['groups'][0]['title'] );
        $this->assertSame( array( 'a', 'b', 'c' ), array_column( $answer['groups'][1]['items'], 'title' ) );
        $this->assertSame( 40, $answer['groups'][1]['count'] );

        $this->assertSame( array( 'A', 'B' ), expSearchSuggest::shortenPath( array( 'A', 'B' ) ) );
        $this->assertSame( array( 'A', '…', 'D', 'E' ), expSearchSuggest::shortenPath( array( 'A', 'B', 'C', 'D', 'E' ) ) );
        $this->assertSame( 'abcd…', expSearchSuggest::cut( 'abcdefgh', 5 ) );
        $this->assertTrue( expSearchSuggest::isSiteAddress( '/admin/x' ) );
        $this->assertFalse( expSearchSuggest::isSiteAddress( '//host/x' ) );
        $this->assertFalse( expSearchSuggest::isSiteAddress( '/\\host/x' ) );
    }

    /** SS-08 */
    public function testTheViewIsRegistered()
    {
        $root = dirname( __DIR__, 4 );
        // read, not included: module.php asks the database for the state groups
        $module = (string)file_get_contents( $root . '/kernel/content/module.php' );
        $this->assertSame( 1, preg_match( "/\\\$ViewList\\['suggest'\\] = array\\((.*?)\\);\\n/s", $module, $m ), 'the view is declared' );
        $this->assertStringContainsString( "'functions' => array( 'read' )", $m[1] );
        $this->assertStringContainsString( "'unordered_params' => array( 'text' => 'Text' )", $m[1] );
        $this->assertStringContainsString( "'ui_context' => 'ajax'", $m[1] );
        $this->assertStringContainsString( "'script' => 'suggest.php'", $m[1] );
        $this->assertFileExists( $root . '/kernel/content/suggest.php' );

        $autoload = include $root . '/autoload/ezp_kernel.php';
        $this->assertSame( 'kernel/classes/expsearchsuggest.php', $autoload['expSearchSuggest'] );
        $this->assertSame( 'kernel/private/classes/views/content/suggest.php', $autoload['Exponential\\View\\Kernel\\Content\\Suggest'] );

        $view = (string)file_get_contents( $root . '/kernel/private/classes/views/content/suggest.php' );
        $this->assertStringContainsString( "expViewParameters::text( \$Params, 'text', 'Text' )", $view );
        $this->assertStringNotContainsString( '$_GET', $view );
        $this->assertStringContainsString( "Content-Type: application/json", $view );
        $this->assertStringContainsString( 'Cache-Control: private, no-store', $view );
    }
}
