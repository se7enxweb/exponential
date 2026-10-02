<?php
/**
 * File containing the eZContentCachePathInfoTest class.
 *
 * content/pdf passes a layout of false and view parameters; the cache path
 * must still be built (array_merge() refuses non-arrays on PHP 8).
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZContentCachePathInfoTest extends ezpTestCase
{
    public function testPathInfoAcceptsFalseLayoutAndViewParameters()
    {
        $GLOBALS['eZCurrentAccess']['name'] = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : 'admin';
        $info = eZContentCache::cachePathInfo( 'site', 2, 'pdf', false, 0, array( 1, 2 ), array(), false, true,
                                               array( 'view_parameters' => array( 'offset' => 5 ) ) );
        $this->assertArrayHasKey( 'path', $info );
        $this->assertStringEndsWith( '.cache', $info['path'] );
    }

    public function testContentObjectCacheInfoIsAnInstanceMethod()
    {
        $method = new ReflectionMethod( 'eZContentObject', 'cacheInfo' );
        $this->assertFalse( $method->isStatic() );
    }
}
