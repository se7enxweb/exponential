<?php
/**
 * The sub items "Create new" menu (children_detailed.tpl) lists the class group of the tree the node is in first:
 * Users under the users tree, Media under the media tree, Content elsewhere. Under Users, the group came last,
 * after some sixty classes of the other groups, so User and User group were out of sight.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminCreateMenuOwnGroupFirstTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminCreateMenuOwnGroupFirstTest extends PHPUnit\Framework\TestCase
{
    public static function designs()
    {
        return array( 'admin' => array( 'admin' ), 'admin4' => array( 'admin4' ) );
    }

    /** @dataProvider designs */
    #[PHPUnit\Framework\Attributes\DataProvider( 'designs' )]
    public function testTheTreesOwnGroupComesFirst( $design )
    {
        $tpl = file_get_contents( dirname( __DIR__, 3 ) . "/design/$design/templates/children_detailed.tpl" );
        $fetch = strpos( $tpl, "fetch( 'content', 'can_instantiate_class_list'" );
        $this->assertNotFalse( $fetch );
        $lead = strpos( $tpl, '{def $create_lead_group = cond(', $fetch );
        $this->assertNotFalse( $lead, 'the lead group is chosen after the fetch' );
        $block = substr( $tpl, $lead, strpos( $tpl, '{undef $create_lead_group $create_ordered_groups}', $lead ) - $lead );
        // Users under the users tree, Media under the media tree, Content elsewhere
        $this->assertMatchesRegularExpression( "/UserRootNode.*?ClassGroupIDs', 'Users'.*?MediaRootNode.*?ClassGroupIDs', 'Media'.*?ClassGroupIDs', 'Content'/s", $block );
        $this->assertStringContainsString( '{set $create_ordered_groups = $create_ordered_groups|prepend( $create_group )}', $block );
        $this->assertStringContainsString( '{set $create_ordered_groups = $create_ordered_groups|append( $create_group )}', $block );
        $this->assertStringContainsString( '{set $can_create_classes = $create_ordered_groups}', $block );
        // every use of the list comes after the reordering
        $this->assertFalse( strpos( substr( $tpl, $fetch, $lead - $fetch ), '{foreach $can_create_classes as $group}' ),
                            'no menu is drawn from the list before it is reordered' );
    }
}
