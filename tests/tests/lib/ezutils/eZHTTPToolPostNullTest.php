<?php
/**
 * eZHTTPTool with $_POST set to null, the case of LoveStack (mugoweb/ezpublish-legacy) commit 75cbb1daa2:
 * array_keys( null ) is a TypeError from PHP 8, so the first eZHTTPTool::instance() of a request ended it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 * @group php8
 */

class eZHTTPToolPostNullTest extends PHPUnit\Framework\TestCase
{
    private $savedPost;
    private $savedInstance;

    protected function setUp(): void
    {
        $this->savedPost = $_POST;
        $this->savedInstance = array_key_exists( 'eZHTTPToolInstance', $GLOBALS ) ? array( $GLOBALS['eZHTTPToolInstance'] ) : null;
    }

    protected function tearDown(): void
    {
        $_POST = $this->savedPost;
        if ( $this->savedInstance === null )
            unset( $GLOBALS['eZHTTPToolInstance'] );
        else
            $GLOBALS['eZHTTPToolInstance'] = $this->savedInstance[0];
    }

    public function testImageButtonsWithPostNull()
    {
        $_POST = null;
        $http = new eZHTTPTool();
        $http->createPostVarsFromImageButtons();
        $this->assertFalse( $http->hasPostVariable( 'Anything' ) );
    }

    public function testImageButtonsStillBecomePostVariables()
    {
        $_POST = array( 'RemoveButton_x' => '3', 'RemoveButton_y' => '4', 'SelectButton_12_x' => '1', 'SelectButton_12_y' => '1' );
        $http = new eZHTTPTool();
        $http->createPostVarsFromImageButtons();
        $this->assertTrue( $http->postVariable( 'RemoveButton' ) );
        $this->assertSame( '12', $http->postVariable( 'SelectButton' ) );
    }
}
