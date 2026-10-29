<?php
/**
 * The section based approval of new staff accounts (expUserApprovalSection), the optional alternative to the event
 * "Approve user accounts": the default groups, the approval event's fields (section Staff, approvers, the group without
 * approval, new objects only), the policies the staff accounts leave, the setup mode, the command's options, and the
 * collaboration mails that link to the item in the administration.
 *
 * No database: plain values, the kernel's own files.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expUserApprovalSectionTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expUserApprovalSectionTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    public function testTheStaffSectionHoldsAdministratorUsersAndEditors()
    {
        $this->assertSame( array( expUserApproval::ADMINISTRATORS, expUserApproval::EDITORS ), expUserApprovalSection::staffGroupSpecs() );
        $this->assertSame( 'Staff', expUserApprovalSection::SECTION_NAME );
        $this->assertSame( 'staff', expUserApprovalSection::SECTION_IDENTIFIER );
    }

    public function testTheApprovalEventAsksForNewObjectsInTheSection()
    {
        $fields = expUserApprovalSection::eventFields( 9, array( 12, '12', 0 ), array( 12 ) );
        $this->assertSame( '9', $fields['data_text1'], 'section Staff' );
        $this->assertSame( '12', $fields['data_text4'], 'approving groups, once' );
        $this->assertSame( '12', $fields['data_text2'], 'the group without approval' );
        $this->assertSame( '', $fields['data_text3'], 'no single approving user' );
        $this->assertSame( 0, $fields['data_int2'], 'every language' );
        $this->assertSame( eZApproveType::VERSION_OPTION_FIRST_ONLY, $fields['data_int3'], 'new objects only' );
    }

    public function testTheEventFieldsAreWhatTheApprovalEventReads()
    {
        $fields = expUserApprovalSection::eventFields( 9, array( 12 ), array( 12 ) );
        $event = new eZWorkflowEvent( $fields + array( 'id' => 7, 'version' => 0, 'workflow_id' => 3, 'workflow_type_string' => 'event_ezapprove',
                                                       'placement' => 1, 'data_int1' => 0, 'data_int4' => 0, 'data_text5' => '' ) );
        $type = new eZApproveType();
        $this->assertSame( array( '9' ), array_map( 'strval', $type->attributeDecoder( $event, 'selected_sections' ) ) );
        $this->assertSame( array( '12' ), array_map( 'strval', $type->attributeDecoder( $event, 'approve_groups' ) ) );
        $this->assertSame( array( '12' ), array_map( 'strval', $type->attributeDecoder( $event, 'selected_usergroups' ) ) );
        $this->assertSame( eZApproveType::VERSION_OPTION_FIRST_ONLY, (int)$type->attributeDecoder( $event, 'version_option' ) );
    }

    public function testPoliciesOfTheSectionTheAccountsLeaveAreListed()
    {
        $rows = array( array( 'role' => 'Anonymous', 'module' => 'content', 'function' => 'read', 'section' => 2 ),
                       array( 'role' => 'Anonymous', 'module' => 'content', 'function' => 'read', 'section' => 2 ),
                       array( 'role' => 'Editor', 'module' => 'content', 'function' => 'edit', 'section' => 1 ) );
        $this->assertSame( array( 'role "Anonymous": content/read limited to section 2' ),
                           expUserApprovalSection::sectionPolicyWarnings( $rows, array( 2 ) ) );
        $this->assertSame( array(), expUserApprovalSection::sectionPolicyWarnings( $rows, array() ) );
    }

    public function testTheSetupChoosesOneMode()
    {
        $this->assertSame( 'group', expUserApprovalSection::mode( 'group' ) );
        $this->assertSame( 'section', expUserApprovalSection::mode( ' Section ' ) );
        $this->assertSame( 'none', expUserApprovalSection::mode( 'none' ) );
        // anything else: the default
        $this->assertSame( 'group', expUserApprovalSection::mode( 'both' ) );
        $this->assertSame( 'group', expUserApprovalSection::mode( '' ) );
        $ini = file_get_contents( self::root() . '/settings/site.ini' );
        $this->assertMatchesRegularExpression( '/^AccountApprovalMode=group$/m', $ini, 'the shipped default is the group based approval' );
        $step = file_get_contents( self::root() . '/kernel/setup/steps/ezstep_create_sites.php' );
        $this->assertStringContainsString( 'expUserApprovalSection::ensure()', $step );
    }

    public function testTheCommandIsADryRunInSectionModeUnlessApplied()
    {
        $code = file_get_contents( self::root() . '/kernel/private/classes/commands/adduserapproval.php' );
        // every mode: a dry run unless --apply (Exponential\Runnable\Command::applyRequested())
        $this->assertStringContainsString( '"[apply][dry-run][mode:]', $code );
        $this->assertStringContainsString( '$apply = self::applyRequested( $options );', $code );
        $this->assertStringContainsString( '$this->runSection( $cli, $script, $options, $dryRun );', $code );
        // the group mode warns when the section based event is on the trigger too
        $this->assertStringContainsString( 'expUserApprovalSection::eventOnTrigger()', $code );
    }

    public function testTheCollaborationMailsLinkToTheAdministration()
    {
        $definition = eZCollaborationItem::definition();
        $this->assertSame( 'adminURL', $definition['function_attributes']['admin_url'] );
        foreach ( array( 'standard', 'base' ) as $design )
        {
            foreach ( array( 'plain.tpl', 'ezapprove/approve.tpl', 'ezapprove/author.tpl' ) as $file )
            {
                $tpl = file_get_contents( self::root() . "/design/$design/templates/notification/handler/ezcollaboration/view/$file" );
                $this->assertStringContainsString( '{$collaboration_item.admin_url}', $tpl, "$design $file" );
                $this->assertStringNotContainsString( 'concat( "collaboration/item/full/"', $tpl, "$design $file" );
            }
        }
    }

    public function testThePlainApprovalEventMailsAndAuditsAccounts()
    {
        $code = file_get_contents( self::root() . '/kernel/classes/workflowtypes/event/ezapprove/ezapprovetype.php' );
        $this->assertStringContainsString( 'expUserApproval::sectionRequested(', $code );
        $this->assertTrue( method_exists( 'expUserApproval', 'modeOfItem' ) );
        $this->assertTrue( method_exists( 'expUserApproval', 'sectionProcessOfItem' ) );
        $reauth = file_get_contents( self::root() . '/kernel/classes/reauth/expreauth.php' );
        $this->assertStringContainsString( 'expUserApproval::modeOfItem( $itemID )', $reauth );
    }
}
