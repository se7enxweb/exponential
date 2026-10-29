<?php
/**
 * The help shows a local copy of an external image (README.md's screenshots on github.com/user-attachments), stored
 * by exp:help index and served by exphelp/image/(id)/<sha256>, so the admin's img-src 'self' allows it. No database,
 * no network: the fetcher is a fake.
 *
 *  HRI-01  fetch() follows redirects (https only), stores the bytes as <sha256 of the address>.<suffix>, readable by
 *          the other server user, and does not fetch a stored image again
 *  HRI-02  refused: http, a redirect to http, a user name, another port, too many redirects, a status other than 200,
 *          a body over MAX_BYTES, SVG and other content types, bytes that are not the type the header says
 *  HRI-03  references() finds ![](https://), <img src=https://> and reference definitions, as the renderer resolves them; image syntax inside code blocks and code spans is skipped
 *  HRI-04  the renderer shows the copy as an <img> with the local address, keeps width, height and alt of an <img>
 *          tag as attributes, and keeps the link fallback for an image without a copy
 *  HRI-05  the view exphelp/image names a file only for the 64 hex id of a stored copy: unknown ids, upper case,
 *          path tricks, a further path and a symbolic link are refused
 *  HRI-06  the view is registered with the read function, the id as an unordered (view) parameter
 *  HRI-07  isPublicAddress() refuses private, loopback and link-local addresses
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpRemoteImagesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'expHelpMarkdownRenderer' => 'exphelpmarkdownrenderer.php', 'expHelpRemoteImages' => 'exphelpremoteimages.php' ) as $helpClass => $helpFile )
    if ( !class_exists( $helpClass ) )
        require_once dirname( __DIR__, 5 ) . '/kernel/classes/help/' . $helpFile;
unset( $helpClass, $helpFile );
if ( !class_exists( 'Exponential\\View\\Kernel\\Exphelp\\Image' ) )
    require_once dirname( __DIR__, 5 ) . '/kernel/private/classes/views/exphelp/image.php';

class expHelpRemoteImagesTest extends PHPUnit\Framework\TestCase
{
    /** a 1 x 1 PNG */
    const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

    const SELF_ONLY = array( "'self'", 'data:', 'blob:' );

    private $dir = '';

    /** @var array the addresses the fake fetcher was asked for */
    private $requests = array();

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-exphelp-images-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        mkdir( $this->dir, 0777, true );
        $this->requests = array();
    }

    protected function tearDown(): void
    {
        if ( $this->dir !== '' && is_dir( $this->dir ) )
        {
            foreach ( scandir( $this->dir ) as $name )
                if ( $name !== '.' && $name !== '..' )
                    unlink( $this->dir . '/' . $name );
            rmdir( $this->dir );
        }
    }

    /**
     * A store whose fetcher answers from $responses (url => response), recording each request.
     */
    private function store( array $responses )
    {
        $requests = &$this->requests;
        return new expHelpRemoteImages( $this->dir, static function ( $url ) use ( $responses, &$requests ) {
            $requests[] = $url;
            return isset( $responses[$url] ) ? $responses[$url] : array( 'status' => 404, 'headers' => array(), 'body' => '' );
        } );
    }

    private static function image( $body = null, $type = 'image/png' )
    {
        return array( 'status' => 200, 'headers' => array( 'Content-Type' => $type ), 'body' => $body === null ? base64_decode( self::PNG ) : $body );
    }

    private static function redirect( $location )
    {
        return array( 'status' => 302, 'headers' => array( 'Location' => $location ), 'body' => '' );
    }

    /** HRI-01 */
    public function testFetchFollowsRedirectsAndStores()
    {
        $url = 'https://github.com/user-attachments/assets/b16a5e96-7483-4e83-b658-ac4fe92e84b8';
        $store = $this->store( array(
            $url => self::redirect( 'https://objects.example.com/signed/x.png?X-Amz-Signature=abc' ),
            'https://objects.example.com/signed/x.png?X-Amz-Signature=abc' => self::image( null, 'image/png; charset=binary' ),
        ) );
        $result = $store->fetch( $url );
        $this->assertSame( 'stored', $result['status'], $result['reason'] );
        $this->assertSame( hash( 'sha256', $url ), $result['id'] );
        $file = $this->dir . '/' . $result['id'] . '.png';
        $this->assertFileExists( $file );
        $this->assertSame( base64_decode( self::PNG ), file_get_contents( $file ) );
        $this->assertSame( 0044, fileperms( $file ) & 0044, 'group and others can read it (Velocity runs as root, PHP-FPM as the site user)' );
        $this->assertSame( array(), glob( $this->dir . '/.*.tmp' ), 'no temporary file left' );
        $this->assertCount( 2, $this->requests );

        $again = $store->fetch( $url );
        $this->assertSame( 'cached', $again['status'] );
        $this->assertCount( 2, $this->requests, 'a stored image is not fetched again' );

        $stats = $store->fetchAll( array( $url, 'http://example.com/a.png' ) );
        $this->assertSame( 2, $stats['images'] );
        $this->assertSame( 1, $stats['cached'] );
        $this->assertArrayHasKey( 'http://example.com/a.png', $stats['failed'] );

        $this->assertNotSame( '', $store->signature() );
        $resolved = $store->resolve( $url );
        $this->assertSame( 1, $resolved['width'] );
        $this->assertSame( 1, $resolved['height'] );
        $this->assertStringEndsWith( '/exphelp/image/(id)/' . $result['id'], $resolved['url'] );
        $this->assertNull( $store->resolve( 'https://example.com/never-fetched.png' ) );
    }

    /** HRI-02 */
    public function testFetchRefuses()
    {
        $big = base64_decode( self::PNG ) . str_repeat( "\0", expHelpRemoteImages::MAX_BYTES );
        $loop = array();
        for ( $i = 0; $i <= expHelpRemoteImages::MAX_REDIRECTS + 1; $i++ )
            $loop['https://loop.example.com/' . $i] = self::redirect( '/' . ( $i + 1 ) );
        $store = $this->store( $loop + array(
            'https://a.example.com/to-http' => self::redirect( 'http://a.example.com/x.png' ),
            'https://a.example.com/to-file' => self::redirect( 'file:///etc/passwd' ),
            'https://a.example.com/gone' => array( 'status' => 404, 'headers' => array(), 'body' => '' ),
            'https://a.example.com/big.png' => self::image( $big ),
            'https://a.example.com/x.svg' => self::image( '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>', 'image/svg+xml' ),
            'https://a.example.com/page' => self::image( '<html>', 'text/html' ),
            'https://a.example.com/lie.png' => self::image( '<svg/>', 'image/png' ),
            'https://a.example.com/gif-as-png' => self::image( "GIF89a\x01\x00\x01\x00", 'image/png' ),
        ) );
        $refused = array(
            'http://a.example.com/x.png' => 'plain http',
            'https://user:pw@a.example.com/x.png' => 'a user name',
            'https://a.example.com:8443/x.png' => 'another port',
            'https://a.example.com/to-http' => 'a redirect to http',
            'https://a.example.com/to-file' => 'a redirect to file:',
            'https://loop.example.com/0' => 'too many redirects',
            'https://a.example.com/gone' => 'a 404',
            'https://a.example.com/big.png' => 'over MAX_BYTES',
            'https://a.example.com/x.svg' => 'SVG',
            'https://a.example.com/page' => 'HTML',
            'https://a.example.com/lie.png' => 'bytes that are no PNG',
            'https://a.example.com/gif-as-png' => 'a GIF sent as PNG',
        );
        foreach ( $refused as $url => $what )
        {
            $result = $store->fetch( $url );
            $this->assertSame( 'failed', $result['status'], $what );
            $this->assertNotSame( '', $result['reason'], $what );
        }
        $this->assertSame( array(), array_values( array_diff( scandir( $this->dir ), array( '.', '..' ) ) ), 'nothing was stored' );
        $this->assertNotContains( 'http://a.example.com/x.png', $this->requests, 'plain http is never requested' );
        $this->assertNotContains( 'file:///etc/passwd', $this->requests );
        $this->assertLessThanOrEqual( expHelpRemoteImages::MAX_REDIRECTS + 1, count( array_filter( $this->requests, static function ( $u ) {
            return strpos( $u, 'https://loop.example.com/' ) === 0; } ) ) );
    }

    /** HRI-03 */
    public function testReferences()
    {
        $md = "![Logo](https://github.com/user-attachments/assets/b16a)\n"
            . "<img width=\"1200\" height=\"693\" alt=\"MongoDB\" src=\"https://github.com/user-attachments/assets/ef51\" />\n"
            . "![local](help/img/x.png) ![plain](http://example.com/p.png) ![js](javascript:alert(1))\n"
            . "![ref][shot] and [link]: https://example.com/not-an-image\n\n[shot]: https://example.com/shot.webp\n"
            . "![dup](https://github.com/user-attachments/assets/b16a)\n";
        $this->assertSame( array( 'https://github.com/user-attachments/assets/b16a', 'https://github.com/user-attachments/assets/ef51',
                                  'https://example.com/shot.webp' ),
                           expHelpRemoteImages::references( $md ) );
        $code = "Write `![alt](https://example.com/span.png)` for an image.\n\n"
            . "```markdown\n![alt](https://example.com/fence.png)\n<img src=\"https://example.com/fence2.png\">\n```\n\n"
            . "~~~\n![alt](https://example.com/tilde.png)\n~~~\n"
            . "![real](https://example.com/real.png)\n";
        $this->assertSame( array( 'https://example.com/real.png' ), expHelpRemoteImages::references( $code ), 'examples in code are not images' );
        $this->assertSame( 'https://example.com/a.png', expHelpRemoteImages::normalize( '//example.com/a.png' ), 'as the renderer sees it' );
        $this->assertNull( expHelpRemoteImages::normalize( 'img/a.png' ) );
    }

    /** HRI-04 */
    public function testRendererUsesTheCopy()
    {
        $stored = 'https://github.com/user-attachments/assets/ef51';
        $store = $this->store( array( $stored => self::image() ) );
        $this->assertSame( 'stored', $store->fetch( $stored )['status'] );
        $id = hash( 'sha256', $stored );
        $renderer = new expHelpMarkdownRenderer( array(
            'link_resolver' => static function ( $href ) { return $href; },
            'image_resolver' => static function () { return null; },
            'translate' => static function ( $text ) { return $text; },
            'image_sources' => self::SELF_ONLY,
            'remote_images' => static function ( $href ) use ( $store ) {
                $local = $store->resolve( $href );
                if ( $local !== null )
                    $local['url'] = '/admin' . preg_replace( '#^.*(/exphelp/image/)#', '$1', $local['url'] );
                return $local;
            } ) );
        $md = "<img width=\"1200\" height=\"693\" alt=\"MongoDB-exp\" src=\"$stored\" />\n\n"
            . "![Logo]($stored)\n\n![Missing](https://github.com/user-attachments/assets/none)\n\n"
            . "<img width=\"40%\" style=\"x\" alt=\"Odd\" src=\"$stored\">\n";
        $html = $renderer->render( $md, 'README.md' )['html'];
        $dom = new DOMDocument();
        $prev = libxml_use_internal_errors( true );
        $dom->loadHTML( '<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>', LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( $prev );
        $imgs = iterator_to_array( $dom->getElementsByTagName( 'img' ) );
        $this->assertCount( 3, $imgs, $html );
        foreach ( $imgs as $img )
            $this->assertSame( '/admin/exphelp/image/(id)/' . $id, $img->getAttribute( 'src' ), 'the local copy, allowed by img-src \'self\'' );
        $this->assertSame( array( '1200', '693', 'MongoDB-exp' ), array( $imgs[0]->getAttribute( 'width' ), $imgs[0]->getAttribute( 'height' ), $imgs[0]->getAttribute( 'alt' ) ),
                           'an <img> tag keeps its size and alt' );
        $this->assertSame( array( '1', '1', 'Logo' ), array( $imgs[1]->getAttribute( 'width' ), $imgs[1]->getAttribute( 'height' ), $imgs[1]->getAttribute( 'alt' ) ),
                           'Markdown: the size of the copy' );
        $this->assertSame( array( '1', '1' ), array( $imgs[2]->getAttribute( 'width' ), $imgs[2]->getAttribute( 'height' ) ), 'a width that is no number is not taken' );
        $this->assertStringNotContainsString( 'style=', $html, 'never an inline style' );
        $this->assertStringNotContainsString( '<img class="a4-help-img" src="https://', $html );
        $this->assertStringContainsString( 'class="a4-help-ext a4-help-imglink" href="https://github.com/user-attachments/assets/none"', $html,
                                           'no copy: the link fallback' );
    }

    /** HRI-05 */
    public function testViewRefusesUnknownIdsAndPathTricks()
    {
        $url = 'https://example.com/a.png';
        $store = $this->store( array( $url => self::image() ) );
        $id = $store->fetch( $url )['id'];
        $view = 'Exponential\\View\\Kernel\\Exphelp\\Image';
        $this->assertSame( $this->dir . '/' . $id . '.png', $view::file( array( 'Id' => $id, 'Parameters' => array() ), $store ) );
        $this->assertSame( 'image/png', expHelpRemoteImages::contentType( $view::file( array( 'Id' => $id ), $store ) ) );

        symlink( $this->dir . '/' . $id . '.png', $this->dir . '/' . str_repeat( 'b', 64 ) . '.png' );
        file_put_contents( $this->dir . '/' . str_repeat( 'c', 64 ) . '.svg', '<svg/>' );
        $refused = array(
            'unknown' => array( 'Id' => str_repeat( 'a', 64 ) ),
            'upper case' => array( 'Id' => strtoupper( $id ) ),
            'short' => array( 'Id' => substr( $id, 0, 63 ) ),
            'with suffix' => array( 'Id' => $id . '.png' ),
            'traversal' => array( 'Id' => '../' . $id ),
            'traversal 2' => array( 'Id' => '..%2F..%2Fsettings%2Fsite.ini' ),
            'absolute' => array( 'Id' => '/etc/passwd' ),
            'nul' => array( 'Id' => $id . "\0" ),
            'newline' => array( 'Id' => $id . "\n" ),
            'array' => array( 'Id' => array( $id ) ),
            'false (no view parameter)' => array( 'Id' => false ),
            'missing' => array(),
            'a further path' => array( 'Id' => $id, 'Parameters' => array( '..', 'x' ) ),
            'symbolic link' => array( 'Id' => str_repeat( 'b', 64 ) ),
            'svg' => array( 'Id' => str_repeat( 'c', 64 ) ),
        );
        foreach ( $refused as $what => $params )
            $this->assertNull( $view::file( $params, $store ), $what );
        $this->assertNull( ( new expHelpRemoteImages( null ) )->file( $id ), 'no directory' );
    }

    /** HRI-06 */
    public function testViewIsRegistered()
    {
        $ViewList = array();
        include dirname( __DIR__, 5 ) . '/kernel/exphelp/module.php';
        $this->assertArrayHasKey( 'image', $ViewList );
        $this->assertSame( array( 'read' ), $ViewList['image']['functions'] );
        $this->assertSame( array(), $ViewList['image']['params'] );
        $this->assertSame( array( 'id' => 'Id' ), $ViewList['image']['unordered_params'] );
        $source = (string)file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/classes/views/exphelp/image.php' );
        $this->assertStringNotContainsString( '$_GET', $source, 'view parameters only' );
        $this->assertStringNotContainsString( '$_REQUEST', $source );
    }

    /** HRI-07 */
    public function testPublicAddresses()
    {
        foreach ( array( '127.0.0.1', '10.0.0.1', '192.168.1.1', '172.16.0.1', '169.254.169.254', '0.0.0.0', '::1', 'fd00::1', 'fe80::1' ) as $address )
            $this->assertFalse( expHelpRemoteImages::isPublicAddress( $address ), $address );
        foreach ( array( '140.82.121.4', '2606:50c0:8000::153' ) as $address )
            $this->assertTrue( expHelpRemoteImages::isPublicAddress( $address ), $address );
        $this->assertNull( expHelpRemoteImages::publicAddress( '127.0.0.1' ) );
        $this->assertSame( 'https://h.example.com/a/c.png', expHelpRemoteImages::absoluteLocation( 'https://h.example.com/a/b.png', 'c.png' ) );
        $this->assertSame( 'https://h.example.com/c.png', expHelpRemoteImages::absoluteLocation( 'https://h.example.com/a/b.png', '/c.png' ) );
        $this->assertSame( 'https://o.example.com/c.png', expHelpRemoteImages::absoluteLocation( 'https://h.example.com/a', '//o.example.com/c.png' ) );
    }
}
