<?php
/**
 * File containing ezpRestControllerTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

/**
 * Unit test for REST controller
 */
class ezpRestControllerTest extends ezpRestTestCase
{

    /**
     * @group restResponseGroups
     * @group restController
     */
    public function testHasResponseGroup()
    {
        $r = new ezpRestRequest();
        $r->variables['ResponseGroups'] = array( 'foo', 'bar' );
        $r->protocol = 'http-get';
        $controller = new ezpRestTestController( 'test', $r );

        $refObj = new ReflectionObject( $controller );
        $refMethod = $refObj->getMethod( 'hasResponseGroup' );

        self::assertTrue( $refMethod->invoke( $controller, 'foo' ) );
        self::assertTrue( $refMethod->invoke( $controller, 'bar' ) );
        self::assertFalse( $refMethod->invoke( $controller, 'baz' ) );
    }

    /**
     * @group restResponseGroups
     * @group restController
     */
    public function testGetResponseGroups()
    {
        $r = new ezpRestRequest();
        $r->variables['ResponseGroups'] = array( 'foo', 'bar' );
        $r->protocol = 'http-get';
        $controller = new ezpRestTestController( 'test', $r );

        $refObj = new ReflectionObject( $controller );
        $refMethod = $refObj->getMethod( 'getResponseGroups' );

        $res = $refMethod->invoke( $controller );
        self::assertInternalType( PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $res );
        self::assertContains( 'foo', $res );
        self::assertContains( 'bar', $res );
    }

    /**
     * @group restResponseGroups
     * @group restController
     */
    public function testDefaultResponseGroups()
    {
        $r = new ezpRestRequest();
        $r->variables['ResponseGroups'] = array( 'foo', 'bar' );
        $r->protocol = 'http-get';
        $controller = new ezpRestTestController( 'test', $r );

        $refObj = new ReflectionObject( $controller );
        $setDefaultRefMethod = $refObj->getMethod( 'setDefaultResponseGroups' );
        // Add a default response group which is not it provided ones
        // This response group should be registered as a valid response group
        $defaultResponseGroup = 'baz';
        $setDefaultRefMethod->invoke( $controller, array( $defaultResponseGroup ) );

        $getResponseGroupsRefMethod = $refObj->getMethod( 'getResponseGroups' );
        $res = $getResponseGroupsRefMethod->invoke( $controller );
        self::assertInternalType( PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $res );
        self::assertContains( $defaultResponseGroup, $res, 'Default response groups must be considered as valid response groups, even if not provided in URI string' );
    }

    /**
     * @group restContentVariables
     * @group restController
     */
    public function testHasContentVariable()
    {
        $r = new ezpRestRequest();
        $r->protocol = 'http-get';
        $translation = 'eng-GB';
        $r->contentVariables = array( 'Translation' => $translation );
        $controller = new ezpRestTestController( 'test', $r );

        $refObj = new ReflectionObject( $controller );
        $refMethod = $refObj->getMethod( 'hasContentVariable' );
        self::assertTrue( $refMethod->invoke( $controller, 'Translation' ) );
        self::assertFalse( $refMethod->invoke( $controller, 'Foo' ) );
    }

    /**
     * @group restContentVariables
     * @group restController
     */
    public function testGetContentVariable()
    {
        $r = new ezpRestRequest();
        $translation = 'eng-GB';
        $r->contentVariables = array( 'Translation' => $translation );
        $r->protocol = 'http-get';
        $controller = new ezpRestTestController( 'test', $r );

        $refObj = new ReflectionObject( $controller );
        $refMethod = $refObj->getMethod( 'getContentVariable' );
        self::assertEquals( $translation, $refMethod->invoke( $controller, 'Translation' ) );
        self::assertNull( $refMethod->invoke( $controller, 'NonExistentContentVariable' ) );
    }

    /**
     * @group restContentVariables
     * @group restController
     */
    public function testGetAllContentVariables()
    {
        $r = new ezpRestRequest();
        $r->protocol = 'http-get';
        $providedContentVariables = array(
            'Translation' => 'eng-GB',
            'Foo' => 'FooValue',
            'Bar' => 'BarValue'
        );
        $r->contentVariables = $providedContentVariables;
        $controller = new ezpRestTestController( 'test', $r );

        $refObj = new ReflectionObject( $controller );
        $refMethod = $refObj->getMethod( 'getAllContentVariables' );
        self::assertSame( $providedContentVariables, $refMethod->invoke( $controller ) );
    }

}
?>
