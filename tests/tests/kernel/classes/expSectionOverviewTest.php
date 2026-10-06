<?php
/**
 * File containing the expSectionOverviewTest class.
 *
 * What the section pages say about each section (Exponential\View\Kernel\Section\ListView::overview()): objects by
 * status, the roles and policies that name a section, role assignments limited to it, whether it can be removed,
 * what needs attention, the search text and the summary. The pure part needs no database; the last test compares
 * the grouped queries against the kernel's own one-section-at-a-time lookups on a live installation, and is skipped
 * where there is none (CI).
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Section\ListView;

class expSectionOverviewTest extends PHPUnit\Framework\TestCase
{
    private function parts()
    {
        return array( 'ezcontentnavigationpart' => array( 'name' => 'Content structure', 'identifier' => 'ezcontentnavigationpart' ),
                      'ezmedianavigationpart' => array( 'name' => 'Media library', 'identifier' => 'ezmedianavigationpart' ) );
    }

    private function sections()
    {
        return array(
            array( 'id' => 1, 'name' => 'Standard', 'identifier' => 'standard', 'navigation_part_identifier' => 'ezcontentnavigationpart' ),
            array( 'id' => 3, 'name' => 'Media', 'identifier' => 'media', 'navigation_part_identifier' => 'ezmedianavigationpart' ),
            array( 'id' => 6, 'name' => 'Restricted', 'identifier' => '', 'navigation_part_identifier' => 'ezcontentnavigationpart' ),
            array( 'id' => 9, 'name' => 'Empty', 'identifier' => 'empty', 'navigation_part_identifier' => 'ezgonenavigationpart' ),
        );
    }

    private function overview( array $counts = array(), array $policies = array(), array $assignments = array() )
    {
        return ListView::overview( $this->sections(), $counts, $policies, $assignments, $this->parts() );
    }

    public function testObjectsAreCountedByStatus()
    {
        $o = $this->overview( array( array( 'section_id' => 1, 'status' => 1, 'count' => 170 ),
                                     array( 'section_id' => 1, 'status' => 0, 'count' => '4' ),
                                     array( 'section_id' => 1, 'status' => 2, 'count' => 2 ),
                                     array( 'section_id' => 3, 'status' => 0, 'count' => 1 ) ) );
        $s = $o['sections'][1];
        $this->assertSame( array( 170, 4, 2, 176 ), array( $s['published'], $s['drafts'], $s['archived'], $s['objects'] ) );
        $this->assertSame( 0, $o['sections'][3]['published'] );
        $this->assertSame( 1, $o['sections'][3]['objects'], 'a draft counts as an object' );
        $this->assertSame( 0, $o['sections'][9]['objects'] );
    }

    public function testRolesAreGroupedWithTheirPolicyFunctions()
    {
        $o = $this->overview( array(), array(
            array( 'value' => '1', 'role_id' => 4, 'role_name' => 'Editor', 'module_name' => 'content', 'function_name' => 'edit' ),
            array( 'value' => '1', 'role_id' => 4, 'role_name' => 'Editor', 'module_name' => 'content', 'function_name' => 'create' ),
            array( 'value' => '1', 'role_id' => 4, 'role_name' => 'Editor', 'module_name' => 'content', 'function_name' => 'create' ),
            array( 'value' => '1', 'role_id' => 1, 'role_name' => 'Anonymous', 'module_name' => 'content', 'function_name' => 'read' ),
            array( 'value' => '3', 'role_id' => 7, 'role_name' => null, 'module_name' => 'content', 'function_name' => 'read' ),
            array( 'value' => 'not-a-number', 'role_id' => 8, 'role_name' => 'X', 'module_name' => 'content', 'function_name' => 'read' ),
        ) );
        $s = $o['sections'][1];
        $this->assertSame( 4, $s['policy_count'], 'every policy counts, also two of the same function' );
        $this->assertSame( 2, $s['role_count'] );
        $this->assertSame( 'Anonymous', $s['roles'][0]['name'], 'roles by name' );
        $this->assertSame( array( 'content/create', 'content/edit' ), $s['roles'][1]['functions'], 'functions once each, sorted' );
        $this->assertSame( '#7', $o['sections'][3]['roles'][0]['name'], 'a policy of a role that is gone still shows' );
        $this->assertTrue( $s['used_by_roles'] );
        $this->assertFalse( $o['sections'][9]['used_by_roles'] );
    }

    public function testAssignmentsLimitedToASection()
    {
        $o = $this->overview( array(), array(), array(
            array( 'limit_value' => '9', 'role_id' => 2, 'contentobject_id' => 12 ),
            array( 'limit_value' => '9', 'role_id' => 2, 'contentobject_id' => 13 ),
            array( 'limit_value' => '9', 'role_id' => 5, 'contentobject_id' => 12 ),
        ) );
        $s = $o['sections'][9];
        $this->assertSame( 3, $s['assignment_count'] );
        $this->assertSame( 2, $s['assignment_role_count'] );
        $this->assertTrue( $s['used_by_roles'] );
        $this->assertFalse( $s['removable'], 'a role assignment limited to it keeps a section' );
    }

    public function testRemovableOnlyWithoutObjectsPoliciesAndAssignments()
    {
        $o = $this->overview( array( array( 'section_id' => 1, 'status' => 1, 'count' => 1 ),
                                     array( 'section_id' => 6, 'status' => 2, 'count' => 1 ) ),
                              array( array( 'value' => '3', 'role_id' => 1, 'role_name' => 'Anonymous', 'module_name' => 'content', 'function_name' => 'read' ) ) );
        $this->assertFalse( $o['sections'][1]['removable'], 'published objects' );
        $this->assertFalse( $o['sections'][6]['removable'], 'archived objects too, as eZSection::canBeRemoved() counts every status' );
        $this->assertFalse( $o['sections'][3]['removable'], 'a policy' );
        $this->assertTrue( $o['sections'][9]['removable'] );
    }

    public function testAttentionNavigationPartAndSearch()
    {
        $o = $this->overview();
        $this->assertTrue( $o['sections'][6]['missing_identifier'] );
        $this->assertTrue( $o['sections'][6]['attention'] );
        $this->assertFalse( $o['sections'][9]['navigation_part_known'] );
        $this->assertSame( 'ezgonenavigationpart', $o['sections'][9]['navigation_part_name'], 'an unknown part shows its identifier' );
        $this->assertTrue( $o['sections'][9]['attention'] );
        $this->assertSame( 'Media library', $o['sections'][3]['navigation_part_name'] );
        $this->assertFalse( $o['sections'][3]['attention'] );
        $this->assertStringContainsString( 'media library', $o['sections'][3]['search'] );
        $this->assertStringContainsString( 'ezmedianavigationpart', $o['sections'][3]['search'] );
        $this->assertStringContainsString( 'standard', $o['sections'][1]['search'] );
    }

    public function testSearchTextIsLowerCaseAndHoldsRoleNames()
    {
        $sections = array( array( 'id' => 2, 'name' => 'Über Uns', 'identifier' => 'ueber', 'navigation_part_identifier' => 'ezcontentnavigationpart' ) );
        $o = ListView::overview( $sections, array(), array( array( 'value' => 2, 'role_id' => 3, 'role_name' => 'Members Area',
                                 'module_name' => 'content', 'function_name' => 'read' ) ), array(), $this->parts() );
        $this->assertStringContainsString( 'über uns', $o['sections'][2]['search'] );
        $this->assertStringContainsString( 'members area', $o['sections'][2]['search'] );
    }

    public function testSummary()
    {
        $o = $this->overview( array( array( 'section_id' => 1, 'status' => 1, 'count' => 5 ),
                                     array( 'section_id' => 3, 'status' => 1, 'count' => 2 ),
                                     array( 'section_id' => 6, 'status' => 0, 'count' => 1 ) ),
                              array( array( 'value' => '1', 'role_id' => 1, 'role_name' => 'Anonymous', 'module_name' => 'content', 'function_name' => 'read' ) ) );
        $this->assertSame( array( 'sections' => 4, 'published' => 7, 'in_roles' => 1, 'empty' => 2, 'removable' => 1, 'attention' => 2 ), $o['summary'] );
    }

    public function testNoSections()
    {
        $o = ListView::overview( array(), array(), array(), array(), array() );
        $this->assertSame( array(), $o['sections'] );
        $this->assertSame( 0, $o['summary']['sections'] );
    }

    /**
     * On a live installation: the grouped queries give the numbers the kernel's own per-section lookups give, and
     * "removable" agrees with eZSection::canBeRemoved(). Read only.
     */
    public function testGroupedQueriesAgreeWithTheKernelOnALiveInstallation()
    {
        ezpLiveInstallation::requireOrSkip();
        $overview = ListView::overviewFromDatabase();
        $this->assertNotEmpty( $overview['sections'] );
        foreach ( eZSection::fetchList() as $section )
        {
            $id = (int)$section->attribute( 'id' );
            $info = $overview['sections'][$id];
            $published = ( new eZSectionFunctionCollection() )->fetchObjectListCount( $id );
            $this->assertSame( (int)$published['result'], $info['published'], "published objects of section $id" );
            $this->assertCount( $info['policy_count'], eZPolicyLimitation::findByType( 'Section', $id, true, false ), "policies of section $id" );
            $this->assertCount( $info['assignment_count'], eZRole::fetchRolesByLimitation( 'section', $id ), "assignments of section $id" );
            $this->assertSame( (bool)$section->canBeRemoved(), $info['removable'], "removable, section $id" );
        }
    }
}
