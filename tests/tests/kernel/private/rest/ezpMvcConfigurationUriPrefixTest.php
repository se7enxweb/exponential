<?php
/**
 * The REST request parser's address prefix (ezpMvcConfiguration::uriPrefix()), without a database: an installation
 * served below a path (an Alias such as /doc) answers /doc/api/... as /api/..., the non-vhost index_rest.php address
 * keeps working, and an installation at the root strips nothing.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/private/rest/ezpMvcConfigurationUriPrefixTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezpMvcConfigurationUriPrefixTest extends PHPUnit\Framework\TestCase
{
    public function testAnInstallationBelowAPathStripsThatPath()
    {
        $this->assertSame( '/doc', ezpMvcConfiguration::uriPrefix( '/doc/index_rest.php', '/doc/api/ezp/v2/content/node/2?x=1', '/doc/index_rest.php', '/doc' ) );
        $this->assertSame( '/doc', ezpMvcConfiguration::uriPrefix( '/doc/index.php', '/doc/api/graphql', '/doc/index.php', '/doc/' ) );
    }

    public function testTheScriptAddressDecidesWhenEZSysKnowsNoDirectory()
    {
        // inside the REST kernel below an Alias, eZSys reports no index and no www directory
        $this->assertSame( '/doc', ezpMvcConfiguration::uriPrefix( '/doc/index_rest.php', '/doc/api/ezp/v2/content/node/2', '', '' ) );
        $this->assertSame( '/doc/index_rest.php', ezpMvcConfiguration::uriPrefix( '/doc/index_rest.php', '/doc/index_rest.php/api/graphql', '', '' ) );
        $this->assertSame( '', ezpMvcConfiguration::uriPrefix( '/index_rest.php', '/api/ezp/v2/content/node/2', '', '' ) );
    }

    public function testTheNonVhostAddressKeepsItsIndexFile()
    {
        $this->assertSame( '/index_rest.php', ezpMvcConfiguration::uriPrefix( '/index_rest.php', '/index_rest.php/api/ezp/v2/content/node/2', '/index_rest.php', '' ) );
        $this->assertSame( '/site/index_rest.php', ezpMvcConfiguration::uriPrefix( '/site/index_rest.php', '/site/index_rest.php/api/x', '/site/index_rest.php', '/site' ) );
    }

    public function testAnInstallationAtTheRootStripsNothing()
    {
        $this->assertSame( '', ezpMvcConfiguration::uriPrefix( '/index_rest.php', '/api/ezp/v2/content/node/2', '/index_rest.php', '' ) );
        $this->assertSame( '', ezpMvcConfiguration::uriPrefix( '/index.php', '/api/graphql', '/index.php', '/' ) );
        // a path that only begins like the installation's is not it
        $this->assertSame( '', ezpMvcConfiguration::uriPrefix( '/doc/index_rest.php', '/docs/api/x', '/doc/index_rest.php', '/doc' ) );
    }
}
