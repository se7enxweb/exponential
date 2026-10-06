<?php
/**
 * Small kernel helpers without the database: the URL alias filter that appends the node ID for the classes of
 * site.ini [AppendNodeIDFilterSettings] ApplyOnClass, and ezpMultivariateTest with the handler of content.ini
 * [TestingSettings] (switched on and off, an unknown or wrong handler class, a handler that picks another node).
 *
 * No database: the node is built in memory with its class identifier. Settings are set and put back.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ezpSmallKernelHelpersTestNode extends eZContentObjectTreeNode
{
    public $classIdentifier;

    function attribute( $attr, $noFunction = false )
    {
        return $attr === 'class_identifier' ? $this->classIdentifier : parent::attribute( $attr, $noFunction );
    }
}

class ezpSmallKernelHelpersTestHandler implements ezpMultivariateTestHandlerInterface
{
    public function isEnabled()
    {
        return true;
    }

    public function execute( $nodeID )
    {
        return $nodeID + 1000;
    }
}

class ezpSmallKernelHelpersTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();
    private $separator;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->separator = array_key_exists( 'eZCharTransform_wordSeparator', $GLOBALS ) ? array( $GLOBALS['eZCharTransform_wordSeparator'] ) : null;
    }

    protected function tearDown(): void
    {
        foreach ( array_reverse( $this->saved ) as $entry )
        {
            list( $file, $group, $name, $value ) = $entry;
            $ini = eZINI::instance( $file );
            if ( $value === null )
                $ini->removeSetting( $group, $name );
            else
                $ini->setVariable( $group, $name, $value[0] );
        }
        $this->saved = array();
        if ( $this->separator === null )
            unset( $GLOBALS['eZCharTransform_wordSeparator'] );
        else
            $GLOBALS['eZCharTransform_wordSeparator'] = $this->separator[0];
    }

    private function setting( $file, $group, $name, $value )
    {
        $ini = eZINI::instance( $file );
        $this->saved[] = array( $file, $group, $name, $ini->hasVariable( $group, $name ) ? array( $ini->variable( $group, $name ) ) : null );
        $ini->setVariable( $group, $name, $value );
    }

    private static function node( $nodeID, $class )
    {
        $node = new ezpSmallKernelHelpersTestNode( array( 'node_id' => $nodeID ) );
        $node->classIdentifier = $class;
        return $node;
    }

    // ---------------------------------------------------------------- eZURLAliasFilterAppendNodeID

    public function testNodeIdIsAppendedForTheListedClasses()
    {
        $this->setting( 'site.ini', 'AppendNodeIDFilterSettings', 'ApplyOnClass', array( 'article', 'blog_post' ) );
        $GLOBALS['eZCharTransform_wordSeparator'] = '-';
        $filter = new eZURLAliasFilterAppendNodeID();
        $language = null;
        $node = self::node( 42, 'article' );
        $this->assertSame( 'my-title-42', $filter->process( 'my-title', $language, $node ) );
        $node = self::node( 43, 'folder' );
        $this->assertSame( 'my-folder', $filter->process( 'my-folder', $language, $node ) );
        $GLOBALS['eZCharTransform_wordSeparator'] = '_';
        $node = self::node( 7, 'blog_post' );
        $this->assertSame( 'post_7', $filter->process( 'post', $language, $node ) );
    }

    public function testFilterLeavesTheTextAloneWithoutANode()
    {
        $filter = new eZURLAliasFilterAppendNodeID();
        $language = null;
        $caller = 'not a node';
        $this->assertSame( 'text', $filter->process( 'text', $language, $caller ) );
    }

    // ---------------------------------------------------------------- ezpMultivariateTest

    public function testDefaultHandlerFollowsTheSetting()
    {
        $this->setting( 'content.ini', 'TestingSettings', 'MultivariateTestingHandlerClass', 'ezpMultivariateTestHandler' );
        $handler = ezpMultivariateTest::getHandler();
        $this->assertInstanceOf( 'ezpMultivariateTestHandler', $handler );
        $test = new ezpMultivariateTest( $handler );
        $this->setting( 'content.ini', 'TestingSettings', 'MultivariateTesting', 'disabled' );
        $this->assertFalse( $test->isEnabled() );
        $this->setting( 'content.ini', 'TestingSettings', 'MultivariateTesting', 'enabled' );
        $this->assertTrue( $test->isEnabled() );
    }

    public function testUnknownOrWrongHandlerClassGivesNoHandler()
    {
        $this->setting( 'content.ini', 'TestingSettings', 'MultivariateTestingHandlerClass', 'k1NoSuchHandler' );
        $this->assertNull( ezpMultivariateTest::getHandler() );
        $this->setting( 'content.ini', 'TestingSettings', 'MultivariateTestingHandlerClass', 'stdClass' );
        $this->assertNull( ezpMultivariateTest::getHandler() );
    }

    public function testOwnHandlerPicksTheNode()
    {
        $this->setting( 'content.ini', 'TestingSettings', 'MultivariateTestingHandlerClass', 'ezpSmallKernelHelpersTestHandler' );
        $test = new ezpMultivariateTest( ezpMultivariateTest::getHandler() );
        $this->assertTrue( $test->isEnabled() );
        $this->assertSame( 1002, $test->execute( 2 ) );
    }
}
