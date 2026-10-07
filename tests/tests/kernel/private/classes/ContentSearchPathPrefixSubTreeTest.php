<?php
/**
 * content/search without SubTreeArray is limited to the siteaccess's PathPrefix node:
 * \Exponential\View\Kernel\Content\Search::pathPrefixSubTreeArray(). No database: the URL alias lookup is a stub.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ContentSearchPathPrefixSubTreeTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        if ( !class_exists( '\Exponential\View\Kernel\Content\Search', false ) )
            require_once 'kernel/private/classes/views/content/search.php';
    }

    private function resolver( array $map, array &$asked = array() )
    {
        return function ( $path ) use ( $map, &$asked )
        {
            $asked[] = $path;
            return isset( $map[$path] ) ? $map[$path] : 0;
        };
    }

    public function testNoPathPrefixMeansNoSubtree()
    {
        $asked = array();
        $resolver = $this->resolver( array( '' => 2 ), $asked );
        $this->assertSame( array(), \Exponential\View\Kernel\Content\Search::pathPrefixSubTreeArray( '', $resolver ) );
        $this->assertSame( array(), \Exponential\View\Kernel\Content\Search::pathPrefixSubTreeArray( null, $resolver ) );
        $this->assertSame( array(), \Exponential\View\Kernel\Content\Search::pathPrefixSubTreeArray( '/', $resolver ) );
        $this->assertSame( array(), $asked, 'no lookup without a prefix' );
    }

    public function testPathPrefixNodeIsTheSubtree()
    {
        $asked = array();
        $resolver = $this->resolver( array( 'bold-agency' => 73, 'fit-healthy' => 89 ), $asked );
        $this->assertSame( array( 73 ), \Exponential\View\Kernel\Content\Search::pathPrefixSubTreeArray( 'bold-agency', $resolver ) );
        $this->assertSame( array( 89 ), \Exponential\View\Kernel\Content\Search::pathPrefixSubTreeArray( '/fit-healthy/', $resolver ) );
        $this->assertSame( array( 'bold-agency', 'fit-healthy' ), $asked );
    }

    public function testUnknownPathPrefixMeansNoSubtree()
    {
        $resolver = $this->resolver( array() );
        $this->assertSame( array(), \Exponential\View\Kernel\Content\Search::pathPrefixSubTreeArray( 'no-such-prefix', $resolver ) );
    }
}
