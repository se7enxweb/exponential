<?php
/**
 * The suggestions and the filtered search page of a site's own design (content/sitesuggest,
 * parts/front_search_suggest.tpl, content/front_search_page.tpl). DB-less.
 *
 *  SSS-01  site.ini [SiteSearchSuggestSettings]: defaults (enabled, user classes excluded) and bounds
 *  SSS-02  an item carries only name, address, class name and picture; user accounts, excluded classes and
 *          addresses of other hosts are dropped
 *  SSS-03  too short a text asks nothing and answers one empty content group
 *  SSS-04  the view: declared with content/read, in the autoload map; the anonymous cache key differs by text,
 *          siteaccess, languages and user
 *  SSS-05  a design's content/search.tpl that includes front_search_page.tpl is the filtered page
 *  SSS-06  the includes: no inline CSS or script, the box wires the form's field, the strings are the admin's
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expSiteSearchSuggestTest extends PHPUnit\Framework\TestCase
{
    private static function ini( array $values )
    {
        return new class( $values ) {
            private $values;
            public function __construct( array $values ) { $this->values = $values; }
            public function hasVariable( $group, $name ) { return $group === 'SiteSearchSuggestSettings' && array_key_exists( $name, $this->values ); }
            public function variable( $group, $name ) { return $this->values[$name]; }
        };
    }

    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    /** SSS-01 */
    public function testTheSiteSettingsHaveSafeDefaults()
    {
        $s = expSearchSuggest::siteSettings( self::ini( array() ) );
        $this->assertTrue( $s['enabled'] );
        $this->assertSame( 3, $s['min_length'] );
        $this->assertSame( 6, $s['limit'] );
        $this->assertSame( array( 'user', 'user_group' ), $s['exclude_classes'] );
        $this->assertSame( 60, $s['cache_ttl'] );
        $this->assertSame( 60, $s['rate_limit'] );

        $s = expSearchSuggest::siteSettings( self::ini( array( 'Enabled' => 'false', 'Limit' => '99', 'PublicCacheTTL' => '99999',
                                                               'ThumbnailAlias' => 'small"><x', 'RateWindow' => '0' ) ) );
        $this->assertFalse( $s['enabled'] );
        $this->assertSame( expSearchSuggest::MAX_LIMIT, $s['limit'] );
        $this->assertSame( 3600, $s['cache_ttl'] );
        $this->assertSame( 'smallx', $s['thumbnail_alias'] );
        $this->assertSame( 1, $s['rate_window'] );
    }

    /** SSS-02 */
    public function testAnItemCarriesNothingOfTheAccountAndNoUser()
    {
        $settings = expSearchSuggest::siteSettings( self::ini( array() ) );
        $item = array( 'type' => 'content', 'title' => 'Easter', 'url' => '/easter', 'meta' => 'Article', 'icon' => '/share/icons/a.png',
                       'path' => array( 'News' ), 'object_id' => 42, 'class_identifier' => 'article', 'is_user' => false, 'thumbnail' => '/var/x/small.jpg' );
        $this->assertSame( array( 'type' => 'content', 'title' => 'Easter', 'url' => '/easter', 'meta' => 'Article', 'icon' => '/var/x/small.jpg' ),
                           expSearchSuggest::siteItem( $item, $settings ) );
        $this->assertSame( '/share/icons/a.png', expSearchSuggest::siteItem( array( 'thumbnail' => '' ) + $item, $settings )['icon'] );
        $this->assertNull( expSearchSuggest::siteItem( array( 'is_user' => true ) + $item, $settings ), 'a user account' );
        $this->assertNull( expSearchSuggest::siteItem( array( 'class_identifier' => 'user_group' ) + $item, $settings ), 'an excluded class' );
        $this->assertNull( expSearchSuggest::siteItem( array( 'url' => '//evil.example/x' ) + $item, $settings ), 'another host' );
        $this->assertSame( '', expSearchSuggest::siteItem( array( 'thumbnail' => 'javascript:x', 'icon' => '' ) + $item, $settings )['icon'] );
    }

    /** SSS-03 */
    public function testTooShortATextAsksNothing()
    {
        $settings = expSearchSuggest::siteSettings( self::ini( array() ) );
        $answer = expSearchSuggest::siteSuggest( 'ab', $settings );
        $this->assertSame( array( 'query' => 'ab', 'total' => 0, 'groups' => array() ), $answer );
    }

    /** SSS-04 */
    public function testTheViewIsDeclaredReadOnlyAndKeyedForCaching()
    {
        $module = (string)file_get_contents( self::root() . '/kernel/content/module.php' );
        $this->assertSame( 1, preg_match( "/\\\$ViewList\\['sitesuggest'\\] = array\\((.*?)\\);\\n/s", $module, $m ) );
        $this->assertStringContainsString( "'functions' => array( 'read' )", $m[1] );
        $this->assertStringContainsString( "'script' => 'sitesuggest.php'", $m[1] );
        $autoload = include self::root() . '/autoload/ezp_kernel.php';
        $this->assertSame( 'kernel/private/classes/views/content/sitesuggest.php', $autoload['Exponential\\View\\Kernel\\Content\\SiteSuggest'] );

        $view = '\Exponential\View\Kernel\Content\SiteSuggest';
        if ( !class_exists( 'eZSys' ) )
            $this->markTestSkipped( 'eZSys' );
        $key = $view::cacheKey( 'easter', 'bold', array( 'eng-GB' ), 10 );
        $this->assertSame( $key, $view::cacheKey( ' easter ', 'bold', array( 'eng-GB' ), 10 ) );
        $this->assertNotSame( $key, $view::cacheKey( 'easter', 'site', array( 'eng-GB' ), 10 ) );
        $this->assertNotSame( $key, $view::cacheKey( 'easter', 'bold', array( 'ger-DE' ), 10 ) );
        $this->assertNotSame( $key, $view::cacheKey( 'easter', 'bold', array( 'eng-GB' ), 14 ) );
        $this->assertNotSame( $key, $view::cacheKey( 'eastern', 'bold', array( 'eng-GB' ), 10 ) );

        $code = (string)file_get_contents( self::root() . '/kernel/private/classes/views/content/sitesuggest.php' );
        $this->assertStringContainsString( "'Cache-Control: private, no-store'", $code );
        $this->assertStringContainsString( "if ( !\$anonymous )", $code, 'a signed-in answer is never kept' );
        $suggest = (string)file_get_contents( self::root() . '/kernel/classes/expsearchsuggest.php' );
        $this->assertStringContainsString( '!$object->canRead()', $suggest, 'every hit is asked again whether the user may read it' );
    }

    /** SSS-05 */
    public function testASiteSearchTemplateIsTheFilteredPage()
    {
        $root = self::root() . '/var/tmp/exp-sss05-' . getmypid();
        @mkdir( "$root/site/templates/content", 0777, true );
        file_put_contents( "$root/site/templates/content/search.tpl", "{include uri='design:content/front_search_page.tpl' result_view='line'}\n" );
        try
        {
            $search = '\Exponential\View\Kernel\Content\Search';
            $this->assertTrue( $search::templateSearchesItself( 'content/search.tpl', array( "$root/site" ) ) );
        }
        finally
        {
            @unlink( "$root/site/templates/content/search.tpl" );
            @rmdir( "$root/site/templates/content" );
            @rmdir( "$root/site/templates" );
            @rmdir( "$root/site" );
            @rmdir( $root );
        }
    }

    /** SSS-06 */
    public function testTheIncludesAreCspSafeAndReuseTheAdminStrings()
    {
        $base = self::root() . '/design/standard/';
        $suggest = (string)file_get_contents( $base . 'templates/parts/front_search_suggest.tpl' );
        $page = (string)file_get_contents( $base . 'templates/content/front_search_page.tpl' );
        foreach ( array( $suggest, $page ) as $tpl )
        {
            $this->assertStringNotContainsString( 'style=', $tpl );
            $this->assertStringNotContainsString( '<style', $tpl );
            $this->assertDoesNotMatchRegularExpression( '#<script(?![^>]*\ssrc=)#', $tpl, 'no inline script' );
        }
        $this->assertStringContainsString( "data-suggest-url={'content/sitesuggest'|ezurl}", $suggest );
        $this->assertStringContainsString( 'data-suggest-prefix="exps-suggest"', $suggest );
        $this->assertStringContainsString( 'method="post"', $page );
        $admin = (string)file_get_contents( $base . 'templates/parts/search_suggest.tpl' );
        preg_match_all( "/'([^']+)'\\|i18n\\( 'design\\/admin\\/search\\/suggest' \\)/", $suggest, $m );
        foreach ( $m[1] as $string )
            $this->assertStringContainsString( "'$string'|i18n( 'design/admin/search/suggest' )", $admin, 'an admin string: ' . $string );

        $js = (string)file_get_contents( $base . 'javascript/page/search_suggest.js' );
        $this->assertStringContainsString( "querySelectorAll('[data-suggest-box][data-suggest-url]').forEach(wire)", $js );
        $this->assertStringContainsString( "box.getAttribute('data-suggest-prefix') || 'a4-suggest'", $js );

        $css = (string)file_get_contents( $base . 'stylesheets/front_search.css' );
        preg_match( '/^:root \{(.*?)\n\}/ms', $css, $light );
        preg_match( '/^:root\[data-theme="dark"\],\n:root\[data-bs-theme="dark"\] \{(.*?)\n\}/ms', $css, $dark );
        $this->assertNotEmpty( $light );
        $this->assertNotEmpty( $dark );
        foreach ( array( '--exps-ink', '--exps-surface', '--exps-line', '--exps-accent', '--exps-muted' ) as $token )
            $this->assertStringContainsString( $token . ':', $dark[1], 'dark value of ' . $token );
    }
}
