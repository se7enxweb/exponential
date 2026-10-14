<?php
/**
 * The Locations window of the edit form (content/edit_locations.tpl of design/admin and design/admin4): the
 * visibility a location gets on publishing is not forced to "Visible" (EZP-26501).
 *
 *  EL-01 - an existing location preselects "Unchanged", whether it is visible or hidden, so publishing keeps it
 *  EL-02 - a new location that is to be hidden preselects "Hidden"; a new visible one "Unchanged"
 *  EL-03 - "Visible" is never preselected; exactly one option is selected for every location
 *  EL-04 - the select is disabled when the location interface is off; its name carries the parent node id
 *  EL-05 - design/admin and design/admin4 render the same select
 *
 * The <select> of the template is taken from the file as it is and rendered inside the template's own section
 * over the assignments (eZTemplate::factory(), the kernel's operators, no database), with assignments as arrays.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit f1b7f056b4), Copyright (C) Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/content/eZEditLocationsVisibilityTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class eZEditLocationsVisibilityTest extends PHPUnit\Framework\TestCase
{
    /** @var string private directory of the extracted templates */
    private static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-edit-locations-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( glob( self::$dir . '/*' ) as $file )
            unlink( $file );
        rmdir( self::$dir );
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
    }

    public static function designProvider()
    {
        return array( 'admin' => array( 'admin' ), 'admin4' => array( 'admin4' ) );
    }

    /** The visibility <select> of the design's edit_locations.tpl, as written there */
    private static function selectBlock( $design )
    {
        $source = file_get_contents( "design/$design/templates/content/edit_locations.tpl" );
        // the select may carry other attributes (admin4 names it by its column, aria-labelledby) before these
        $name = strpos( $source, '{if $location_ui_enabled|not}disabled="disabled" {/if}name="FutureNodeHiddenState_' );
        self::assertNotFalse( $name, "$design: the visibility select is in the template" );
        $start = strrpos( substr( $source, 0, $name ), '<select ' );
        self::assertNotFalse( $start, "$design: the visibility select is in the template" );
        $end = strpos( $source, '</select>', $start );
        return substr( $source, $start, $end + strlen( '</select>' ) - $start );
    }

    /**
     * The select rendered per assignment, in the template's own namespace and section; array( parent node id =>
     * array( 'selected' => option values, 'disabled' => bool ) ).
     */
    private function render( $design, array $assignments, $uiEnabled = true )
    {
        $file = self::$dir . "/$design.tpl";
        file_put_contents( $file, '{let name=Node t1=1}{section loop=$assigned_node_array}{let parent_node=$Node:item.parent_node_obj}'
                                  . self::selectBlock( $design ) . "\n" . '{/let}{/section}{/let}' );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'assigned_node_array', $assignments );
        $tpl->setVariable( 'location_ui_enabled', $uiEnabled );
        $html = $tpl->fetch( $file );
        $tpl->unsetVariable( 'assigned_node_array' );
        $tpl->unsetVariable( 'location_ui_enabled' );

        $this->assertSame( count( $assignments ), preg_match_all( '#<select([^>]*)name="FutureNodeHiddenState_(\d+)">(.*?)</select>#s', $html, $selects, PREG_SET_ORDER ), $html );
        $result = array();
        foreach ( $selects as $select )
        {
            preg_match_all( '#<option value="([a-z]+)"( selected="selected")?>#', $select[3], $options, PREG_SET_ORDER );
            $this->assertSame( array( 'unchanged', 'visible', 'hidden' ), array_column( $options, 1 ), 'the three choices' );
            $selected = array();
            foreach ( $options as $option )
            {
                if ( isset( $option[2] ) && $option[2] !== '' )
                    $selected[] = $option[1];
            }
            $result[(int)$select[2]] = array( 'selected' => $selected, 'disabled' => strpos( $select[1], 'disabled="disabled"' ) !== false );
        }
        return $result;
    }

    private static function assignment( $parentNodeID, $existingNode, $hidden )
    {
        return array( 'parent_node' => $parentNodeID, 'node' => $existingNode ? array( 'node_id' => $parentNodeID + 1000 ) : false,
                      'is_hidden' => $hidden, 'parent_node_obj' => array( 'node_id' => $parentNodeID ) );
    }

    /** EL-01 to EL-04 */
    #[\PHPUnit\Framework\Attributes\DataProvider('designProvider')]
    public function testVisibleIsNeverForced( $design )
    {
        $result = $this->render( $design, array(
            self::assignment( 11, true, false ),
            self::assignment( 12, true, true ),
            self::assignment( 13, false, true ),
            self::assignment( 14, false, false ),
        ) );
        $this->assertSame( array( 'unchanged' ), $result[11]['selected'], 'EL-01 existing visible location' );
        $this->assertSame( array( 'unchanged' ), $result[12]['selected'], 'EL-01 existing hidden location stays hidden' );
        $this->assertSame( array( 'hidden' ), $result[13]['selected'], 'EL-02 new hidden location' );
        $this->assertSame( array( 'unchanged' ), $result[14]['selected'], 'EL-02 new visible location' );
        foreach ( $result as $id => $select )
        {
            $this->assertNotContains( 'visible', $select['selected'], "EL-03 $id" );
            $this->assertCount( 1, $select['selected'], "EL-03 $id" );
            $this->assertFalse( $select['disabled'], "EL-04 $id" );
        }
    }

    /** EL-04 */
    #[\PHPUnit\Framework\Attributes\DataProvider('designProvider')]
    public function testDisabledWithoutTheLocationInterface( $design )
    {
        $result = $this->render( $design, array( self::assignment( 21, true, true ) ), false );
        $this->assertTrue( $result[21]['disabled'] );
        $this->assertSame( array( 'unchanged' ), $result[21]['selected'] );
    }

    /** EL-05: the same select in both designs; admin4 also names it by its column header (aria-labelledby) */
    public function testBothDesignsHaveTheSameSelect()
    {
        $admin4 = self::selectBlock( 'admin4' );
        $this->assertStringContainsString( '<select aria-labelledby="edit-locations-future-visibility" ', $admin4 );
        $this->assertSame( self::selectBlock( 'admin' ), str_replace( 'aria-labelledby="edit-locations-future-visibility" ', '', $admin4 ) );
    }
}
