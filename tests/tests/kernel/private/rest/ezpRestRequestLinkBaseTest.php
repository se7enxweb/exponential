<?php
/**
 * The links a REST answer writes (ezpRestRequest::getHostURI(), getBaseURI() builds on it), without a database: the
 * scheme of the request (https when it came encrypted, also behind a proxy), and the path the installation is served
 * below (an Alias such as /doc, or index_rest.php in non-vhost mode), kept from the request parser's prefix.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/private/rest/ezpRestRequestLinkBaseTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezpRestRequestLinkBaseTest extends PHPUnit\Framework\TestCase
{
    protected function request( $encrypted, $basePath )
    {
        $request = new ezpRestRequest( null, 'http-get', 'doc.example.com', '/api/content/node/7' );
        $request->isEncrypted = $encrypted;
        $request->basePath = $basePath;
        return $request;
    }

    public function testAnEncryptedRequestBelowAnAliasLinksWithHttpsAndThePath()
    {
        $this->assertSame( 'https://doc.example.com/doc', $this->request( true, '/doc' )->getHostURI() );
        $this->assertSame( 'https://doc.example.com/doc', $this->request( true, '/doc/' )->getHostURI() );
    }

    public function testThePlainRequestAtTheRootIsUnchanged()
    {
        $this->assertSame( 'http://doc.example.com', $this->request( false, '' )->getHostURI() );
    }

    public function testTheNonVhostAddressKeepsItsIndexFile()
    {
        $this->assertSame( 'http://doc.example.com/index_rest.php', $this->request( false, '/index_rest.php' )->getHostURI() );
    }

    public function testTheBasePathSurvivesAnExport()
    {
        $request = $this->request( true, '/doc' );
        $copy = eval( 'return ' . var_export( $request, true ) . ';' );
        $this->assertSame( '/doc', $copy->basePath );
        $this->assertSame( 'https://doc.example.com/doc', $copy->getHostURI() );
    }

    public function testTheParserHandsItsPrefixToTheRequest()
    {
        $source = (string)file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/rest/classes/request/http_parser.php' );
        $this->assertStringContainsString( '$request->basePath = (string)$this->prefix;', $source );
    }
}
