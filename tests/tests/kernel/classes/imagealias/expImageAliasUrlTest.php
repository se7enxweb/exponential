<?php
/**
 * The URLs of on-demand image aliases (expImageAliasUrl), without a database:
 *
 *  IAU-01 - every URL form is built and read back to the same values (token, ids-token, ids, node, storage)
 *  IAU-02 - the token is signed: a changed payload or signature is refused, the payload stays readable for the
 *           placeholder size only
 *  IAU-03 - key rotation: the previous key is accepted until its grace time, not after
 *  IAU-04 - drafts and previews (images-versioned) get tokens that expire; published images do not
 *  IAU-05 - the access path of the siteaccess goes in front of content/image
 *  IAU-06 - storage paths of aliases are read (published and versioned directories, aliases with "_" in the
 *           basename or the alias name); anything outside the storage, or with "..", is refused
 *  IAU-07 - malformed tokens, file names and parameters are refused
 *  IAU-08 - the signing key: a read that failed never makes a new key (only a key the store says is missing); no key
 *           is asked again within seconds, a key after KEY_TTL; every read goes to the store itself, so a
 *           persistent worker sees a key another process changed
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/imagealias/expImageAliasUrlTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expImageAliasUrlTest extends PHPUnit\Framework\TestCase
{
    const ORIGINAL = 'var/site/storage/images/media/images/sea/123-4-eng-GB/Sea.jpg';

    protected function setUp(): void
    {
        parent::setUp();
        expImageAliasUrl::setKeys( str_repeat( 'a', 64 ) );
    }

    protected function tearDown(): void
    {
        expImageAliasUrl::resetKeys();
        parent::tearDown();
    }

    private static function payload( array $extra = array() )
    {
        return $extra + array( 'a' => 123, 'v' => 4, 'l' => 'eng-GB', 'n' => 'small', 'o' => self::ORIGINAL, 'u' => 'Sea_small.jpg',
                               'w' => 100, 'h' => 67, 'm' => 'image/jpeg' );
    }

    /** The view's parameters of a built URL (what eZModule hands the view) */
    private static function params( $url )
    {
        $rest = substr( $url, strpos( $url, 'content/image/' ) + strlen( 'content/image/' ) );
        return explode( '/', $rest );
    }

    /** IAU-01 */
    public function testEveryFormRoundTrips()
    {
        $payload = self::payload();

        $token = expImageAliasUrl::build( $payload, 'token', array( 'prefix' => '' ) );
        $this->assertStringStartsWith( 'content/image/', $token );
        $this->assertStringEndsWith( '/Sea_small.jpg', $token );
        $parsed = expImageAliasUrl::parse( self::params( $token ) );
        $this->assertSame( 'token', $parsed['form'] );
        $this->assertSame( 'Sea_small.jpg', $parsed['file'] );
        $check = expImageAliasUrl::verify( $parsed['token'] );
        $this->assertTrue( $check['valid'] );
        $this->assertSame( $payload, $check['payload'] );
        $this->assertSame( 'var/site/storage/images/media/images/sea/123-4-eng-GB/Sea_small.jpg', expImageAliasUrl::targetPath( $check['payload'] ) );

        $idsToken = expImageAliasUrl::build( $payload, 'ids-token', array( 'prefix' => '' ) );
        $parsed = expImageAliasUrl::parse( self::params( $idsToken ) );
        $this->assertSame( array( 'ids-token', 123, 4, 'eng-GB', 'small', 'Sea_small.jpg' ),
                           array( $parsed['form'], $parsed['attribute'], $parsed['version'], $parsed['language'], $parsed['alias'], $parsed['file'] ) );
        $this->assertTrue( expImageAliasUrl::verify( $parsed['token'] )['valid'] );

        $ids = expImageAliasUrl::build( $payload, 'ids', array( 'prefix' => '' ) );
        $this->assertSame( 'content/image/123/4/eng-GB/small/Sea_small.jpg', $ids );
        $parsed = expImageAliasUrl::parse( self::params( $ids ) );
        $this->assertSame( array( 'ids', 123, 4, 'eng-GB', 'small', 'Sea_small.jpg' ),
                           array( $parsed['form'], $parsed['attribute'], $parsed['version'], $parsed['language'], $parsed['alias'], $parsed['file'] ) );
        $this->assertArrayNotHasKey( 'token', $parsed );

        $node = expImageAliasUrl::build( $payload, 'node', array( 'prefix' => '', 'node' => 58, 'identifier' => 'image' ) );
        $this->assertSame( 'content/image/58/image/small', $node );
        $parsed = expImageAliasUrl::parse( self::params( $node ) );
        $this->assertSame( array( 'node', 58, 'image', 'small' ), array( $parsed['form'], $parsed['node'], $parsed['identifier'], $parsed['alias'] ) );
        $this->assertNull( expImageAliasUrl::build( $payload, 'node', array( 'prefix' => '' ) ), 'no node: no node form' );

        $this->assertSame( 'var/site/storage/images/media/images/sea/123-4-eng-GB/Sea_small.jpg', expImageAliasUrl::build( $payload, 'storage' ) );

        // A file name with spaces and non-ASCII letters is one encoded path segment
        $special = self::payload( array( 'u' => 'Meer und Strand ä_small.jpg' ) );
        $url = expImageAliasUrl::build( $special, 'token', array( 'prefix' => '' ) );
        $this->assertStringEndsWith( '/' . rawurlencode( 'Meer und Strand ä_small.jpg' ), $url );
        $this->assertSame( 'Meer und Strand ä_small.jpg', expImageAliasUrl::parse( self::params( $url ) )['file'] );
    }

    /** IAU-02 */
    public function testTheTokenIsSigned()
    {
        $token = expImageAliasUrl::token( self::payload() );
        list( $part, $signature ) = explode( '.', $token );

        // Another payload under the same signature
        $forged = expImageAliasUrl::base64UrlEncode( json_encode( self::payload( array( 'o' => 'var/site/storage/images/other/9-1-eng-GB/x.jpg' ) ), JSON_UNESCAPED_SLASHES ) );
        $check = expImageAliasUrl::verify( $forged . '.' . $signature );
        $this->assertFalse( $check['valid'] );
        $this->assertSame( 'signature', $check['reason'] );
        $this->assertSame( 'var/site/storage/images/other/9-1-eng-GB/x.jpg', $check['payload']['o'], 'readable, for the placeholder size' );

        // A changed signature
        $other = $signature;
        $other[0] = $other[0] === 'A' ? 'B' : 'A';
        $this->assertFalse( expImageAliasUrl::verify( $part . '.' . $other )['valid'] );

        // Another key
        expImageAliasUrl::setKeys( str_repeat( 'b', 64 ) );
        $this->assertFalse( expImageAliasUrl::verify( $token )['valid'] );

        // No key at all: nothing is valid, and no signed URL is built
        expImageAliasUrl::setKeys( '' );
        $this->assertFalse( expImageAliasUrl::verify( $token )['valid'] );
        $this->assertNull( expImageAliasUrl::token( self::payload() ) );
        $this->assertNull( expImageAliasUrl::build( self::payload(), 'token', array( 'prefix' => '' ) ) );
    }

    /** IAU-03 */
    public function testThePreviousKeyIsAcceptedDuringTheGracePeriod()
    {
        $old = str_repeat( 'a', 64 );
        $new = str_repeat( 'c', 64 );
        $token = expImageAliasUrl::token( self::payload() );

        expImageAliasUrl::setKeys( $new, $old, time() + 3600 );
        $this->assertTrue( expImageAliasUrl::verify( $token )['valid'], 'old URLs work during the grace period' );
        $this->assertTrue( expImageAliasUrl::verify( expImageAliasUrl::token( self::payload() ) )['valid'], 'new URLs are signed with the new key' );
        $this->assertNotSame( $token, expImageAliasUrl::token( self::payload() ) );

        expImageAliasUrl::setKeys( $new, $old, time() - 1 );
        $this->assertFalse( expImageAliasUrl::verify( $token )['valid'], 'not after it' );
    }

    /** IAU-04 */
    public function testDraftTokensExpire()
    {
        $now = 1800000000;
        $draft = self::payload( array( 'o' => 'var/site/storage/images-versioned/123/5-eng-GB/Sea.jpg', 'x' => $now + 60 ) );
        $token = expImageAliasUrl::token( $draft );
        $this->assertTrue( expImageAliasUrl::verify( $token, $now )['valid'] );
        $this->assertTrue( expImageAliasUrl::verify( $token, $now + 60 )['valid'] );
        $late = expImageAliasUrl::verify( $token, $now + 61 );
        $this->assertFalse( $late['valid'] );
        $this->assertSame( 'expired', $late['reason'] );

        $this->assertTrue( expImageAliasUrl::isVersionedPath( 'var/site/storage/images-versioned/123/5-eng-GB/Sea_small.jpg' ) );
        $this->assertFalse( expImageAliasUrl::isVersionedPath( self::ORIGINAL ) );

        // A published image's token has no expiry
        $this->assertTrue( expImageAliasUrl::verify( expImageAliasUrl::token( self::payload() ), PHP_INT_MAX )['valid'] );
    }

    /** IAU-05 */
    public function testTheAccessPathGoesInFront()
    {
        $this->assertStringStartsWith( 'eng/content/image/', expImageAliasUrl::build( self::payload(), 'token', array( 'prefix' => 'eng/' ) ) );
        $this->assertStringStartsWith( 'index.php/eng/content/image/123/', expImageAliasUrl::build( self::payload(), 'ids', array( 'prefix' => 'index.php/eng/' ) ) );
        $this->assertSame( 'var/site/storage/images/media/images/sea/123-4-eng-GB/Sea_small.jpg',
                           expImageAliasUrl::build( self::payload(), 'storage', array( 'prefix' => 'eng/' ) ), 'never in front of a storage path' );
    }

    /** IAU-06 */
    public function testStoragePathsAreRead()
    {
        $aliases = array( 'original', 'small', 'large', 'i320', 'gallery_thumb', 'thumb' );
        $parsed = expImageAliasUrl::parseStoragePath( '/var/site/storage/images/media/images/sea/123-4-eng-GB/My_beach_gallery_thumb.jpg', $aliases );
        $this->assertSame( array( false, 123, 4, 'eng-GB', 'var/site/storage/images/media/images/sea/123-4-eng-GB', 'My_beach_gallery_thumb.jpg' ),
                           array( $parsed['versioned'], $parsed['attribute'], $parsed['version'], $parsed['language'], $parsed['directory'], $parsed['file'] ) );
        // Longest alias name first; the basename keeps its own underscores
        $this->assertSame( array( array( 'alias' => 'gallery_thumb', 'basename' => 'My_beach', 'suffix' => 'jpg' ),
                                  array( 'alias' => 'thumb', 'basename' => 'My_beach_gallery', 'suffix' => 'jpg' ) ), $parsed['candidates'] );

        $versioned = expImageAliasUrl::parseStoragePath( 'var/storage/images-versioned/77/3-ger-DE/Bild_i320.png', $aliases );
        $this->assertSame( array( true, 77, 3, 'ger-DE', 'var/storage/images-versioned/77/3-ger-DE' ),
                           array( $versioned['versioned'], $versioned['attribute'], $versioned['version'], $versioned['language'], $versioned['directory'] ) );
        $this->assertSame( 'i320', $versioned['candidates'][0]['alias'] );

        // An original (no alias in the name) has no candidate
        $this->assertSame( array(), expImageAliasUrl::parseStoragePath( self::ORIGINAL, $aliases )['candidates'] );

        foreach ( array( 'settings/site.ini', 'var/site/storage/../../settings/site.ini', 'var/site/storage/images/a/../123-4-eng-GB/x_small.jpg',
                         '/etc/passwd', 'var/site/storage/images/123-4-eng-GB', 'var/site/cache/images/1-1-eng-GB/x_small.jpg',
                         "var/site/storage/images/a/1-1-eng-GB/x\0_small.jpg", 'var/site/storage/images/a/1-1-eng/x_small.jpg' ) as $path )
            $this->assertNull( expImageAliasUrl::parseStoragePath( $path, $aliases ), $path );
    }

    /** IAU-07 */
    public function testMalformedInputIsRefused()
    {
        foreach ( array( '', 'abc', 'abc.def', '!!!!!!!!!.AAAAAAAAAAAAAAAAAAAAAA', str_repeat( 'A', 20 ) . '.' . str_repeat( 'B', 22 ) ) as $token )
            $this->assertFalse( expImageAliasUrl::verify( $token )['valid'], $token );
        // A signed payload without the fields a token needs
        $part = expImageAliasUrl::base64UrlEncode( json_encode( array( 'a' => 1 ) ) );
        $this->assertNull( expImageAliasUrl::decodePayload( $part ) );
        // A payload that names a path outside the storage or a file name with a slash
        $this->assertNull( expImageAliasUrl::decodePayload( expImageAliasUrl::base64UrlEncode( json_encode( self::payload( array( 'o' => '../settings/site.ini' ) ) ) ) ) );
        $this->assertNull( expImageAliasUrl::decodePayload( expImageAliasUrl::base64UrlEncode( json_encode( self::payload( array( 'u' => '../x.jpg' ) ) ) ) ) );
        $this->assertNull( expImageAliasUrl::decodePayload( expImageAliasUrl::base64UrlEncode( json_encode( self::payload( array( 'n' => 'small/../x' ) ) ) ) ) );

        foreach ( array( array(), array( 'x' ), array( 'notatoken', 'a.jpg' ), array( 'a.b', '..' ), array( 'x', 'y', 'z', 'w' ),
                         array( '1', '2', 'english', 'small', 'a.jpg' ), array( '1', 'b', 'eng-GB', 'small', 'a.jpg' ),
                         array( '1', '2', 'eng-GB', 'small', 'notatoken', 'a.jpg' ), array( 'abc', 'image', 'small' ),
                         array( '1', '2', '3', '4', '5', '6', '7' ) ) as $params )
            $this->assertNull( expImageAliasUrl::parse( $params ), implode( '/', $params ) );

        $this->assertFalse( expImageAliasUrl::validFileName( 'a/b.jpg' ) );
        $this->assertFalse( expImageAliasUrl::validFileName( "a\nb.jpg" ) );
        $this->assertTrue( expImageAliasUrl::validFileName( 'Sea_small.jpg' ) );
    }

    /** IAU-08 */
    public function testTheSigningKeyIsNeverReplacedByAFailedRead()
    {
        // A new key only when wanted, none was read and the store says for sure that there is none
        $this->assertTrue( expImageAliasUrl::mayCreateKey( null, true, true ), 'the first URL makes the key' );
        $this->assertTrue( expImageAliasUrl::mayCreateKey( '', true, true ) );
        $this->assertFalse( expImageAliasUrl::mayCreateKey( null, true, null ), 'the database did not answer: no new key' );
        $this->assertFalse( expImageAliasUrl::mayCreateKey( null, true, false ), 'the row is there, the read failed: no new key' );
        $this->assertFalse( expImageAliasUrl::mayCreateKey( null, false, true ), 'verifying never makes a key' );
        $this->assertFalse( expImageAliasUrl::mayCreateKey( 'k', true, true ), 'a key that was read stays' );

        // A key is kept KEY_TTL seconds; no key is asked again after KEY_RETRY seconds
        $now = 1800000000;
        $entry = expImageAliasUrl::keysEntry( str_repeat( 'k', 64 ), null, 0, $now );
        $this->assertSame( $now, $entry['read'] );
        $this->assertNull( $entry['previous'] );
        $entry = expImageAliasUrl::keysEntry( null, 'old', 5, $now );
        $this->assertNull( $entry['current'] );
        $this->assertSame( 'old', $entry['previous'] );
        $this->assertSame( expImageAliasUrl::KEY_TTL - expImageAliasUrl::KEY_RETRY, $now - $entry['read'], 'read again after KEY_RETRY seconds' );
        $this->assertLessThan( 60, expImageAliasUrl::KEY_RETRY );

        // Every read goes to the store itself (expSecrets keeps values for the life of a persistent worker), and
        // a new key is made only through mayCreateKey() with the store's own answer
        $source = (string)file_get_contents( dirname( __DIR__, 5 ) . '/kernel/classes/imagealias/expimagealiasurl.php' );
        $keys = substr( $source, strpos( $source, 'public static function keys(' ) );
        $keys = substr( $keys, 0, strpos( $keys, 'public static function keysEntry(' ) );
        $this->assertGreaterThan( 0, strpos( $keys, 'expSecrets::reset()' ) );
        $this->assertLessThan( strpos( $keys, 'expSecrets::get( self::SECRET )' ), strpos( $keys, 'expSecrets::reset()' ), 'reset before the read' );
        $this->assertStringContainsString( 'self::mayCreateKey(', $keys );
        $this->assertStringContainsString( 'self::secretMissing()', $keys );
        $this->assertSame( 1, substr_count( $keys, 'expSecrets::set( self::SECRET,' ), 'the key is made in one place only' );
    }
}
