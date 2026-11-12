<?php
/**
 * File containing the expContentWriterTest class.
 *
 * The parts of expContentWriter (the content writes of the REST API and of scripts) that need no database: which
 * values a class accepts, which addresses a file value may be downloaded from, and how a file value is split.
 *
 *  CW-01 values for unknown attributes, and values that are not text, are refused with the attribute named
 *  CW-02 a file is only fetched from http or https, never a local path or another scheme
 *  CW-03 addresses that resolve to private, loopback, link-local or reserved networks are refused, also when one of
 *        several addresses is, and an IP literal is judged as it is
 *  CW-04 a host that does not resolve, or a URL with credentials, is refused
 *  CW-05 a file value is "<url>" or "<url>|<alternative text or file name>"
 *  CW-06 file attributes are the image, file and media datatypes
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expContentWriterTest extends ezpTestCase
{
    /**
     * @param array $map host => addresses
     * @return callable
     */
    private function resolver( array $map )
    {
        return function( $host ) use ( $map )
        {
            return isset( $map[$host] ) ? $map[$host] : false;
        };
    }

    /** CW-01 */
    public function testInvalidValues()
    {
        $class = array( 'name' => 'ezstring', 'body' => 'ezxmltext', 'image' => 'ezimage' );
        $this->assertNull( expContentWriter::invalidValues( $class, array( 'name' => 'A', 'body' => '<p>x</p>' ), 'article' ) );
        $this->assertNull( expContentWriter::invalidValues( $class, array( 'name' => 12, 'body' => null ), 'article' ) );
        $this->assertSame( 'The class article has no attribute title, intro.', expContentWriter::invalidValues( $class, array( 'title' => 'x', 'name' => 'y', 'intro' => 'z' ), 'article' ) );
        $this->assertSame( 'The value of name must be text.', expContentWriter::invalidValues( $class, array( 'name' => array( 'a' ) ), 'article' ) );
    }

    /** CW-02 */
    public function testOnlyHttpAndHttpsAreFetched()
    {
        $public = $this->resolver( array( 'files.example.org' => array( '93.184.216.34' ) ) );
        $this->assertTrue( expContentWriter::isPublicHttpUrl( 'https://files.example.org/a.jpg', $public ) );
        $this->assertTrue( expContentWriter::isPublicHttpUrl( 'http://files.example.org/a.pdf', $public ) );
        foreach ( array( '/etc/passwd', 'var/storage/a.jpg', 'file:///etc/passwd', 'ftp://files.example.org/a.jpg',
                         'gopher://files.example.org/', 'php://filter/resource=index.php', '', 'https://' ) as $url )
            $this->assertFalse( expContentWriter::isPublicHttpUrl( $url, $public ), $url );
    }

    /** CW-03 */
    public function testInternalAddressesAreRefused()
    {
        $map = array(
            'loop.example' => array( '127.0.0.1' ),
            'lan.example' => array( '192.168.1.10' ),
            'ten.example' => array( '10.0.0.5' ),
            'cloud-meta.example' => array( '169.254.169.254' ),
            'mixed.example' => array( '93.184.216.34', '10.1.2.3' ),
            'v6loop.example' => array( '::1' ),
            'v6local.example' => array( 'fe80::1' ),
            'v6ula.example' => array( 'fd12:3456::1' ),
            'v6mapped.example' => array( '::ffff:10.0.0.1' ),
        );
        foreach ( array_keys( $map ) as $host )
            $this->assertFalse( expContentWriter::isPublicHttpUrl( 'https://' . $host . '/a.jpg', $this->resolver( $map ) ), $host );
        foreach ( array( 'http://127.0.0.1/a', 'http://10.0.0.1/a', 'http://[::1]/a', 'http://0.0.0.0/a', 'http://169.254.169.254/latest/meta-data' ) as $url )
            $this->assertFalse( expContentWriter::isPublicHttpUrl( $url, $this->resolver( array() ) ), $url );
        $this->assertTrue( expContentWriter::isPublicHttpUrl( 'http://93.184.216.34/a.jpg', $this->resolver( array() ) ) );
    }

    /** CW-04 */
    public function testUnresolvedHostsAndCredentialsAreRefused()
    {
        $this->assertFalse( expContentWriter::isPublicHttpUrl( 'https://nowhere.example/a.jpg', $this->resolver( array() ) ) );
        $public = $this->resolver( array( 'files.example.org' => array( '93.184.216.34' ) ) );
        $this->assertFalse( expContentWriter::isPublicHttpUrl( 'https://user:secret@files.example.org/a.jpg', $public ) );
    }

    /** CW-05 */
    public function testSplitFileValue()
    {
        $this->assertSame( array( 'url' => 'https://x.org/a.jpg', 'text' => '' ), expContentWriter::splitFileValue( ' https://x.org/a.jpg ' ) );
        $this->assertSame( array( 'url' => 'https://x.org/a.jpg', 'text' => 'A cat | sitting' ), expContentWriter::splitFileValue( 'https://x.org/a.jpg| A cat | sitting' ) );
    }

    /** CW-06 */
    public function testFileDatatypes()
    {
        $this->assertSame( array( 'ezimage', 'ezbinaryfile', 'ezmedia' ), expContentWriter::FILE_DATATYPES );
    }
}
