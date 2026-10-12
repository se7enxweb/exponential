<?php
/**
 * The class filter type of the fetch functions content/list, tree, list_count, tree_count and calendar when a
 * fetch gives class_filter_array without class_filter_type: content.ini [FetchSettings] DefaultClassFilterType.
 * Empty (the default): the type stays unset and the array is ignored, as before; include or exclude: that type.
 * A type the fetch gives always wins.
 *
 * What the type does to the query is shown by the SQL condition eZContentObjectTreeNode builds from it. No
 * database (class ids, not identifiers).
 *
 * The default was first written for LoveStack by Mugo Web, with thanks:
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 9c0ff40a78), Copyright (C) Mugo Web, GPL v2
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZContentFunctionCollectionClassFilterTypeTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $ini = eZINI::instance( 'content.ini' );
        $this->saved = $ini->hasVariable( 'FetchSettings', 'DefaultClassFilterType' ) ? array( $ini->variable( 'FetchSettings', 'DefaultClassFilterType' ) ) : null;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'content.ini' );
        if ( $this->saved !== null )
            $ini->setVariable( 'FetchSettings', 'DefaultClassFilterType', $this->saved[0] );
        else if ( $ini->hasVariable( 'FetchSettings', 'DefaultClassFilterType' ) )
            $ini->removeSetting( 'FetchSettings', 'DefaultClassFilterType' );
    }

    private function setDefault( $value )
    {
        eZINI::instance( 'content.ini' )->setVariable( 'FetchSettings', 'DefaultClassFilterType', $value );
    }

    private static function condition( $type, array $classes )
    {
        return trim( (string)eZContentObjectTreeNode::createClassFilteringSQLString( $type, $classes ) );
    }

    public function testShippedDefaultIsEmpty()
    {
        $ini = new eZINI( 'content.ini', 'settings', null, false, false, true );
        $this->assertSame( '', $ini->variable( 'FetchSettings', 'DefaultClassFilterType' ) );
    }

    public function testEmptySettingKeepsTodaysBehaviour()
    {
        $this->setDefault( '' );
        $type = eZContentFunctionCollection::classFilterType( false, array( 2, 16 ) );
        $this->assertFalse( $type );
        $this->assertSame( '', self::condition( $type, array( 2, 16 ) ), 'the array is ignored: every class is listed' );
    }

    public function testIncludeFiltersByTheArray()
    {
        $this->setDefault( 'include' );
        $type = eZContentFunctionCollection::classFilterType( false, array( 2, 16 ) );
        $this->assertSame( 'include', $type );
        $this->assertSame( 'ezcontentobject.contentclass_id  IN  ( 2, 16 ) AND', self::condition( $type, array( 2, 16 ) ) );
    }

    public function testExcludeIsAllowedToo()
    {
        $this->setDefault( 'exclude' );
        $this->assertSame( 'exclude', eZContentFunctionCollection::classFilterType( false, array( 2 ) ) );
    }

    public function testGivenTypeWins()
    {
        $this->setDefault( 'include' );
        $this->assertSame( 'exclude', eZContentFunctionCollection::classFilterType( 'exclude', array( 2 ) ) );
    }

    public function testNoArrayNoType()
    {
        $this->setDefault( 'include' );
        $this->assertFalse( eZContentFunctionCollection::classFilterType( false, false ) );
        $this->assertFalse( eZContentFunctionCollection::classFilterType( false, array() ) );
    }

    public function testUnknownSettingValueIsIgnored()
    {
        $this->setDefault( 'everything' );
        $this->assertFalse( eZContentFunctionCollection::classFilterType( false, array( 2 ) ) );
    }
}
