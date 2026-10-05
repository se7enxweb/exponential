<?php
/**
 * Tests of expAdminPagination: the page size of an administration list from admininterface.ini (per view, the
 * caller's default, DefaultItemsPerPage, the built-in fallback), one page of an in-memory list, the offset from the
 * module parameters or user parameters, and the sizes a list with a selector offers.
 *
 * The settings are set by the test and put back. No database; chosen(), which reads eZPreferences, is not used.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class expAdminPaginationTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( expAdminPagination::INI_FILE );
        foreach ( array_reverse( $this->saved ) as $name => $value )
        {
            if ( $value === null )
                $ini->removeSetting( expAdminPagination::INI_BLOCK, $name );
            else
                $ini->setVariable( expAdminPagination::INI_BLOCK, $name, $value[0] );
        }
        $this->saved = array();
    }

    private function setting( $name, $value )
    {
        $ini = eZINI::instance( expAdminPagination::INI_FILE );
        if ( !array_key_exists( $name, $this->saved ) )
            $this->saved[$name] = $ini->hasVariable( expAdminPagination::INI_BLOCK, $name ) ? array( $ini->variable( expAdminPagination::INI_BLOCK, $name ) ) : null;
        if ( $value === null )
            $ini->removeSetting( expAdminPagination::INI_BLOCK, $name );
        else
            $ini->setVariable( expAdminPagination::INI_BLOCK, $name, $value );
    }

    public static function limitProvider()
    {
        return array(
            'configured view'         => array( array( 't1/list' => '40' ), '15', false, 40 ),
            'caller default'          => array( array(), '15', 30, 30 ),
            'default items per page'  => array( array(), '15', false, 15 ),
            'fallback'                => array( array(), null, false, expAdminPagination::FALLBACK ),
            'zero is not a size'      => array( array( 't1/list' => '0' ), '0', 0, expAdminPagination::FALLBACK ),
            'text is not a size'      => array( array( 't1/list' => 'many' ), '15', false, 15 ),
            'negative default'        => array( array(), '15', -5, 15 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('limitProvider')]
    public function testLimit( $perView, $defaultSetting, $callerDefault, $expected )
    {
        $this->setting( 'ItemsPerPage', $perView );
        $this->setting( 'DefaultItemsPerPage', $defaultSetting );
        $this->assertSame( $expected, expAdminPagination::limit( 't1/list', $callerDefault ) );
    }

    public static function pageProvider()
    {
        $items = array( 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5 );
        return array(
            'first page'      => array( $items, 0, 2, array( 'a' => 1, 'b' => 2 ) ),
            'second page'     => array( $items, 2, 2, array( 'c' => 3, 'd' => 4 ) ),
            'last part'       => array( $items, 4, 2, array( 'e' => 5 ) ),
            'past the end'    => array( $items, 9, 2, array() ),
            'negative offset' => array( $items, -3, 1, array( 'a' => 1 ) ),
            'no limit'        => array( $items, 1, 0, $items ),
            'not a list'      => array( 'text', 0, 2, array() ),
            'numeric keys'    => array( array( 10 => 'x', 20 => 'y' ), 1, 1, array( 20 => 'y' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('pageProvider')]
    public function testPage( $items, $offset, $limit, $expected )
    {
        $this->assertSame( $expected, expAdminPagination::page( $items, $offset, $limit ) );
    }

    public static function offsetProvider()
    {
        return array(
            'view parameter'      => array( array( 'Offset' => '50' ), 'offset', 50 ),
            'negative'            => array( array( 'Offset' => '-5' ), 'offset', 0 ),
            'false parameter'     => array( array( 'Offset' => false, 'UserParameters' => array( 'offset' => '25' ) ), 'offset', 25 ),
            'user parameter'      => array( array( 'UserParameters' => array( 'offset' => '75' ) ), 'offset', 75 ),
            'other name'          => array( array( 'Offset' => '50', 'UserParameters' => array( 'page_offset' => '10' ) ), 'page_offset', 10 ),
            'nothing'             => array( array(), 'offset', 0 ),
            'text'                => array( array( 'UserParameters' => array( 'offset' => 'abc' ) ), 'offset', 0 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('offsetProvider')]
    public function testOffset( $params, $name, $expected )
    {
        $this->assertSame( $expected, expAdminPagination::offset( $params, $name ) );
    }

    public static function sizesProvider()
    {
        return array(
            'configured'      => array( array( '10', '20', '100' ), array( 10, 20, 100 ) ),
            'duplicates'      => array( array( '10', '10', '20' ), array( 10, 20 ) ),
            'nonsense only'   => array( array( 'x', '0', '-3' ), array( 10, 25, 50 ) ),
            'not configured'  => array( null, array( 10, 25, 50 ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('sizesProvider')]
    public function testSizes( $setting, $expected )
    {
        $this->setting( 'ItemsPerPageList_t1_list', $setting );
        $this->assertSame( $expected, expAdminPagination::sizes( 't1/list' ) );
    }

    public function testSizesWithTheCallersDefault()
    {
        $this->setting( 'ItemsPerPageList_t1_list', null );
        $this->assertSame( array( 5, 15 ), expAdminPagination::sizes( 't1/list', array( 5, 15 ) ) );
    }
}
