<?php
/**
 * The rules for a redirect target that came from outside the code, without the database:
 * eZRedirectManager::unsafeReason(), safeURI(), moduleView(), returnURI(), and the Cancel or Discard target of
 * every edit view that uses them. doc/features/6.0/safe-redirects.md
 *
 *  SAFE-01  Paths of this site are safe: with and without the leading slash, a siteaccess prefix, a query, a
 *           fragment, spaces around them
 *  SAFE-02  Protocol-relative and absolute URLs: only the current host and the allowed hosts, never a user
 *  SAFE-03  Backslash tricks: \\evil, /\evil, \/evil, a backslash after an allowed host
 *  SAFE-04  Schemes other than http and https: javascript:, data:, vbscript:, mixed case, "http:" without "//"
 *  SAFE-05  Percent-encoded variants: %2F%2F, %5C, double encoding, an encoded scheme, encoded CR/LF
 *  SAFE-06  Control characters: CR/LF (header splitting), tab, NUL, DEL
 *  SAFE-07  Not a string, empty
 *  SAFE-08  moduleView(): the module/view of a path, the siteaccess prefix skipped; aliases and URLs are false
 *  RET-01   returnURI(): the first safe page the form names, else the default
 *  RET-02   returnURI(): never the running view (a loop), never a POST-only view, never an unsafe page
 *  RET-03   returnURI(): the page viewed last (LastAccessesURI), only when safe, not the running view and viewable
 *  VIEW-01  The Cancel/Discard target of user/edit, user/password, user/setting, section/edit, state/edit,
 *           state/group_edit, role/edit, class/edit and class/groupedit: the form's page, else their old default
 *  VIEW-02  The same with an unsafe page in the form: their old default
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZRedirectManagerSafeURITest extends PHPUnit\Framework\TestCase
{
    const HOST = 'www.example.com';

    private $post;
    private $session;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->post = $_POST;
        $this->session = isset( $_SESSION ) ? $_SESSION : null;
    }

    protected function tearDown(): void
    {
        $_POST = $this->post;
        $this->setSessionStarted( false );
        if ( $this->session === null )
            unset( $_SESSION );
        else
            $_SESSION = $this->session;
    }

    private function setSessionStarted( $started )
    {
        $property = new ReflectionProperty( 'eZSession', 'hasStarted' );
        if ( PHP_VERSION_ID < 80100 )
            $property->setAccessible( true );
        $property->setValue( null, $started );
    }

    private function reason( $uri )
    {
        return eZRedirectManager::unsafeReason( $uri, array( 'trusted.example.org' ), self::HOST . ':443' );
    }

    /** options for returnURI() and the views' cancelURI(): no session, fixed hosts, no siteaccess prefix */
    private function options( array $more = array() )
    {
        return array_merge( array( 'session' => false,
                                   'allowed_hosts' => array( 'trusted.example.org' ),
                                   'current_host' => self::HOST,
                                   'prefix' => '',
                                   'disallowed_views' => eZRedirectManager::DEFAULT_DISALLOWED_RETURN_VIEWS,
                                   'viewable' => function ( $uri ) { return true; } ),
                            $more );
    }

    /** a module whose running view is $module/$view */
    private function module( $module, $view )
    {
        return new class( $module, $view ) {
            private $m;
            private $v;
            public function __construct( $m, $v ) { $this->m = $m; $this->v = $v; }
            public function currentModule() { return $this->m; }
            public function currentView() { return $this->v; }
        };
    }

    /** SAFE-01 */
    public function testPathsOfThisSiteAreSafe()
    {
        foreach ( array( '/', '/content/view/full/2', 'content/view/full/2', '/admin/content/view/full/2',
                         '/site/Company/About', '/user/edit?x=1', '/content/view/full/2#top', '/a/b?next=//evil.example',
                         '/a/b?q=%2F%2Fevil.example', '/search?x=\\y' ) as $uri )
            $this->assertFalse( $this->reason( $uri ), $uri );
        $this->assertSame( '/user/preferences', eZRedirectManager::safeURI( '  /user/preferences ', array(), self::HOST ) );
    }

    /** SAFE-02 */
    public function testAbsoluteURLsOnlyToTheOwnOrAllowedHosts()
    {
        foreach ( array( 'https://www.example.com/x', 'http://WWW.Example.COM:8080/x', 'https://www.example.com./x',
                         '//www.example.com/x', 'https://trusted.example.org/y' ) as $uri )
            $this->assertFalse( $this->reason( $uri ), $uri );
        foreach ( array( 'https://evil.example/x', '//evil.example', '//evil.example/x', 'http://evil.example',
                         'https://www.example.com.evil.example/', 'https://evilwww.example.com/', 'https://', 'http:///x' ) as $uri )
            $this->assertSame( 'host', $this->reason( $uri ), $uri );
        foreach ( array( 'https://www.example.com@evil.example/', 'https://user:pw@www.example.com/' ) as $uri )
            $this->assertContains( $this->reason( $uri ), array( 'host', 'userinfo' ), $uri );
        $this->assertSame( 'userinfo', $this->reason( 'https://user:pw@www.example.com/' ) );
    }

    /** SAFE-03 */
    public function testBackslashTricks()
    {
        foreach ( array( '\\\\evil.example', '/\\evil.example', '\\/evil.example', '\\evil.example',
                         'https://www.example.com\\@evil.example/', '/\\/evil.example/' ) as $uri )
            $this->assertSame( 'backslash', $this->reason( $uri ), $uri );
    }

    /** SAFE-04 */
    public function testOtherSchemes()
    {
        foreach ( array( 'javascript:alert(1)', 'JavaScript:alert(1)', 'data:text/html,<script>alert(1)</script>',
                         'vbscript:msgbox(1)', 'ftp://www.example.com/', 'mailto:a@example.com', 'http:evil.example',
                         'https:/evil.example', 'jar:file:x' ) as $uri )
            $this->assertSame( 'scheme', $this->reason( $uri ), $uri );
    }

    /** SAFE-05 */
    public function testPercentEncodedVariants()
    {
        foreach ( array( '%2F%2Fevil.example', '/%2F/evil.example', '/%2f%2fevil.example', '%5C%5Cevil.example',
                         '/%5Cevil.example', '%252F%252Fevil.example', 'javascript%3Aalert(1)', '/x%0D%0ALocation:%20//evil',
                         '/%09/evil.example', '%25252F%25252Fevil.example' ) as $uri )
            $this->assertSame( 'encoded', $this->reason( $uri ), $uri );
    }

    /** SAFE-06 */
    public function testControlCharacters()
    {
        foreach ( array( "/x\r\nLocation: https://evil.example", "/x\nSet-Cookie: a=b", "/\t/evil.example",
                         "/x\0", "/x\x7F", "\r\n/x" ) as $uri )
            $this->assertSame( 'control', $this->reason( $uri ), json_encode( $uri ) );
    }

    /** SAFE-07 */
    public function testNotAStringAndEmpty()
    {
        $this->assertSame( 'type', $this->reason( array( '/x' ) ) );
        $this->assertSame( 'type', $this->reason( null ) );
        $this->assertSame( 'type', $this->reason( 42 ) );
        $this->assertSame( 'empty', $this->reason( '' ) );
        $this->assertSame( 'empty', $this->reason( '   ' ) );
        $this->assertFalse( eZRedirectManager::safeURI( '//evil.example', array(), self::HOST ) );
    }

    /** SAFE-08 */
    public function testModuleView()
    {
        $this->assertSame( 'user/edit', eZRedirectManager::moduleView( '/user/edit/14', '' ) );
        $this->assertSame( 'user/edit', eZRedirectManager::moduleView( 'User/Edit?x=1', '' ) );
        $this->assertSame( 'content/action', eZRedirectManager::moduleView( '/admin/content/action', '/admin' ) );
        $this->assertSame( 'admin/content', eZRedirectManager::moduleView( '/admin/content/action', '' ) );
        $this->assertSame( 'content/view', eZRedirectManager::moduleView( '/index.php/site/content/view/full/2', '/index.php/site' ) );
        $this->assertFalse( eZRedirectManager::moduleView( '/Company', '' ) );
        $this->assertFalse( eZRedirectManager::moduleView( 'https://www.example.com/user/edit', '' ) );
        $this->assertFalse( eZRedirectManager::moduleView( '//www.example.com/user/edit', '' ) );
        $this->assertFalse( eZRedirectManager::moduleView( '/Company/Über-uns', '' ) );
    }

    /** RET-01 */
    public function testReturnURIFirstSafePageElseDefault()
    {
        $options = $this->options();
        $this->assertSame( '/content/view/full/42', eZRedirectManager::returnURI( null, '/d', '/content/view/full/42', $options ) );
        $this->assertSame( '/b', eZRedirectManager::returnURI( null, '/d', array( '//evil.example', '', '/b', '/c' ), $options ) );
        $this->assertSame( '/d', eZRedirectManager::returnURI( null, '/d', false, $options ) );
        $this->assertSame( '/d', eZRedirectManager::returnURI( null, '/d', array(), $options ) );
        $this->assertFalse( eZRedirectManager::returnURI( null, false, 'javascript:alert(1)', $options ) );
        $this->assertSame( 'https://trusted.example.org/x', eZRedirectManager::returnURI( null, '/d', 'https://trusted.example.org/x', $options ) );
    }

    /** RET-02 */
    public function testReturnURINeverTheRunningViewNorAPostOnlyView()
    {
        $options = $this->options();
        $module = $this->module( 'user', 'edit' );
        $this->assertSame( '/d', eZRedirectManager::returnURI( $module, '/d', '/user/edit/14', $options ) );
        $this->assertSame( '/d', eZRedirectManager::returnURI( $module, '/d', '/user/edit', $options ) );
        $this->assertSame( '/user/password/14', eZRedirectManager::returnURI( $module, '/d', '/user/password/14', $options ) );
        foreach ( array( '/content/action', '/user/logout', '/content/removeobject', '/shop/add/12', '/content/download/1/2' ) as $uri )
            $this->assertSame( '/d', eZRedirectManager::returnURI( $module, '/d', $uri, $options ), $uri );
        $this->assertSame( '/d', eZRedirectManager::returnURI( $module, '/d', '/admin/user/edit/14', $this->options( array( 'prefix' => '/admin' ) ) ) );
        // a list of its own replaces the default list
        $this->assertSame( '/content/action', eZRedirectManager::returnURI( $module, '/d', '/content/action', $this->options( array( 'disallowed_views' => array() ) ) ) );
        $this->assertSame( '/d', eZRedirectManager::returnURI( $module, '/d', '/section/list', $this->options( array( 'disallowed_views' => array( 'Section/List' ) ) ) ) );
    }

    /** RET-03 */
    public function testReturnURIThePageViewedLast()
    {
        $module = $this->module( 'user', 'edit' );
        $this->setSessionStarted( true );
        $_SESSION = array( 'LastAccessesURI' => '/content/view/full/5' );
        $this->assertSame( '/content/view/full/5', eZRedirectManager::returnURI( $module, '/d', false, $this->options( array( 'session' => true ) ) ) );
        // the form's page comes first
        $this->assertSame( '/x', eZRedirectManager::returnURI( $module, '/d', '/x', $this->options( array( 'session' => true ) ) ) );
        // a page the user can no longer view
        $this->assertSame( '/d', eZRedirectManager::returnURI( $module, '/d', false, $this->options( array( 'session' => true, 'viewable' => function ( $uri ) { return false; } ) ) ) );
        // the running view, a POST-only view, an unsafe page
        foreach ( array( '/user/edit/14', '/content/action', '//evil.example/x', '/\\evil.example', "/x\r\nA: b" ) as $uri )
        {
            $_SESSION = array( 'LastAccessesURI' => $uri );
            $this->assertSame( '/d', eZRedirectManager::returnURI( $module, '/d', false, $this->options( array( 'session' => true ) ) ), json_encode( $uri ) );
        }
        // without a session
        $this->setSessionStarted( false );
        $this->assertSame( '/d', eZRedirectManager::returnURI( $module, '/d', false, $this->options( array( 'session' => true ) ) ) );
    }

    /** the Cancel target of each view for a POST, with no session */
    private function targets( $post )
    {
        $_POST = $post;
        $o = $this->options();
        return array(
            'user/edit' => \Exponential\View\Kernel\User\Edit::cancelURI( null, $o ),
            'user/password' => \Exponential\View\Kernel\User\Password::cancelURI( null, '/default/page', $o ),
            'user/setting' => \Exponential\View\Kernel\User\Setting::cancelURI( null, 14, $o ),
            'section/edit' => \Exponential\View\Kernel\Section\Edit::cancelURI( null, $o ),
            'state/edit' => \Exponential\View\Kernel\State\Edit::cancelURI( null, 'ez_lock', $o ),
            'state/group_edit' => \Exponential\View\Kernel\State\GroupEdit::cancelURI( null, $o ),
            'role/edit' => \Exponential\View\Kernel\Role\Edit::cancelURI( null, $o ),
            'class/edit' => \Exponential\View\Kernel\Class\Edit::cancelURI( null, false, $o ),
            'class/edit from a group' => \Exponential\View\Kernel\Class\Edit::cancelURI( null, '3', $o ),
            'class/groupedit' => \Exponential\View\Kernel\Class\Groupedit::cancelURI( null, $o ),
        );
    }

    private function defaults()
    {
        return array( 'user/edit' => '/content/view/sitemap/5/',
                      'user/password' => '/default/page',
                      'user/setting' => '/content/view/full/14',
                      'section/edit' => '/section/list',
                      'state/edit' => '/state/group/ez_lock',
                      'state/group_edit' => '/state/groups',
                      'role/edit' => '/role/list/',
                      'class/edit' => '/class/grouplist',
                      'class/edit from a group' => '/class/classlist/3/',
                      'class/groupedit' => '/class/grouplist' );
    }

    /** VIEW-01 */
    public function testEveryViewGoesBackToTheFormsPage()
    {
        $this->assertSame( $this->defaults(), $this->targets( array() ) );
        $this->assertSame( array_fill_keys( array_keys( $this->defaults() ), '/content/view/full/42' ),
                           $this->targets( array( 'RedirectIfDiscarded' => ' /content/view/full/42 ' ) ) );
        // the older field names of user/password
        $targets = $this->targets( array( 'RedirectOnCancel' => '/a' ) );
        $this->assertSame( '/a', $targets['user/password'] );
        $this->assertSame( '/content/view/sitemap/5/', $targets['user/edit'] );
        $targets = $this->targets( array( 'RedirectURI' => '/b', 'RedirectOnCancel' => '//evil.example' ) );
        $this->assertSame( '/b', $targets['user/password'] );
    }

    /** VIEW-02 */
    public function testEveryViewFallsBackFromAnUnsafePage()
    {
        foreach ( array( '//evil.example', '/\\evil.example', '\\\\evil.example', 'javascript:alert(1)',
                         'data:text/html,x', '%2F%2Fevil.example', "/x\r\nLocation: //evil.example",
                         'https://evil.example/x', '/content/action', '/user/logout' ) as $uri )
            $this->assertSame( $this->defaults(), $this->targets( array( 'RedirectIfDiscarded' => $uri ) ), json_encode( $uri ) );
        $this->assertSame( $this->defaults(), $this->targets( array( 'RedirectIfDiscarded' => array( '/somewhere' ) ) ) );
    }
}
