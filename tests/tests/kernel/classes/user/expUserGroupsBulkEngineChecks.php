<?php
/**
 * The checks of the second part of the exp:user commands (expUserGroupManager, expUserBulk and the account tools of
 * expUserManager) that run on every SQL engine the same way, next to expUserManagerEngineChecks (whose helpers they
 * use): SQLite in process and MySQL/MariaDB, PostgreSQL in the database job. Mail is never sent.
 *
 *  UE-8  groups: the tree below the users root, a group created, renamed, moved below another and removed to the
 *        trash once empty; a group with members and the users root are refused; a group cannot go below itself
 *  UE-9  roles: a role assigned to an account limited to a subtree, listed with its limitation, assigned again is no
 *        change, unassigned; a group's own role listed; a wrong section refused
 *  UE-10 unlock and the password change mark: failed sign-ins back to 0; the mark set and cleared
 *  UE-11 check-login: the right password signs in, a wrong one does not and counts no failed sign-in, a disabled
 *        account does not
 *  UE-12 import and export: a CSV file imported (created, an existing login skipped, a login twice refused), a
 *        password column refused as a whole, the export holds the accounts and never a hash
 *  UE-13 bulk: the dry run counts, --apply without or with a wrong --confirm refused, with the right one every account
 *        disabled; a protected account in the selection is refused, never changed
 *  UE-14 the read-only tools: hygiene counts, sessions (untracked here), two-step sign-in state, the audit rows
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

trait expUserGroupsBulkEngineChecks
{
    /** UE-8 */
    protected function checkGroups()
    {
        $this->userBegin();
        $tree = expUserGroupManager::tree();
        $this->assertSame( '', $tree['error'] );
        $names = array_column( $tree['groups'], 'name' );
        foreach ( array( 'Guest accounts', 'Administrator users', 'Editors' ) as $name )
            $this->assertContains( $name, $names );
        $this->assertSame( expUserGroupManager::rootNodeID(), $tree['groups'][0]['node_id'], 'the tree starts at the users root' );

        $name = $this->userLogin( 'Partners' );
        $dry = expUserGroupManager::create( $name, 'Guest accounts', false );
        $this->assertSame( array(), $dry['errors'] );
        $this->assertNotSame( '', expUserManager::findGroup( $name )['error'], 'a dry run creates nothing' );
        $r = expUserGroupManager::create( $name, 'Guest accounts', true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertTrue( $r['applied'] );
        $nodeID = $r['node_id'];
        $this->assertSame( $nodeID, (int)expUserManager::findGroup( $name )['node']->attribute( 'node_id' ) );

        $renamed = $this->userLogin( 'Agencies' );
        $r = expUserGroupManager::rename( (string)$nodeID, $renamed, true );
        $this->assertSame( array(), $r['errors'] );
        eZContentObject::clearCache();
        $this->assertSame( $renamed, (string)eZContentObjectTreeNode::fetch( $nodeID )->attribute( 'name' ) );

        $editors = (int)$this->userGroup( 'Editors' )->attribute( 'node_id' );
        $r = expUserGroupManager::move( (string)$nodeID, 'Editors', true );
        $this->assertSame( array(), $r['errors'] );
        eZContentObject::clearCache();
        $this->assertSame( $editors, (int)eZContentObjectTreeNode::fetch( $nodeID )->attribute( 'parent_node_id' ) );
        $this->assertStringContainsString( 'below itself', implode( ' ', expUserGroupManager::move( 'Editors', (string)$nodeID, false )['errors'] ) );

        list( $member ) = $this->userCreate( 'grpmember', array( 'groups' => array( (string)$nodeID ) ) );
        $this->assertStringContainsString( 'still holds 1', implode( ' ', expUserGroupManager::delete( (string)$nodeID, true )['errors'] ) );
        $this->assertNotEmpty( expUserGroupManager::delete( (string)expUserGroupManager::rootNodeID(), true )['errors'], 'never the users root' );
        $this->assertSame( array(), expUserManager::edit( $member, array( 'add_groups' => array( 'Guest accounts' ), 'remove_groups' => array( (string)$nodeID ) ), true )['errors'] );
        $r = expUserGroupManager::delete( (string)$nodeID, true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertTrue( $r['applied'] );
        eZContentObject::clearCache();
        $this->assertNull( eZContentObjectTreeNode::fetch( $nodeID ), 'moved to the trash' );
        $this->assertInstanceOf( eZUser::class, eZUser::fetch( (int)$member->attribute( 'contentobject_id' ) ), 'the member is kept' );
        $this->userEnd();
    }

    /** UE-9 */
    protected function checkRoles()
    {
        $this->userBegin();
        list( $user ) = $this->userCreate( 'roles' );
        $role = expUserGroupManager::findRole( 'Editor' );
        $this->assertSame( '', $role['error'] );
        $this->assertSame( $role['role']->attribute( 'id' ), expUserGroupManager::findRole( (string)$role['role']->attribute( 'id' ) )['role']->attribute( 'id' ) );
        $this->assertNotSame( '', expUserGroupManager::findRole( 'No such role ' . $this->userLogin( 'x' ) )['error'] );
        $target = expUserGroupManager::findTarget( $user->attribute( 'login' ) );
        $this->assertSame( 'user', $target['type'] );
        $limitation = expUserGroupManager::limitation( '2', '' );
        $this->assertSame( '', $limitation['error'] );
        $this->assertStringStartsWith( 'Subtree:/1/2/', $limitation['stored'] );
        $this->assertNotSame( '', expUserGroupManager::limitation( '2', '1' )['error'], 'one limitation at a time' );
        $this->assertNotSame( '', expUserGroupManager::limitation( '', 'no-such-section-' . $this->userLogin( 'x' ) )['error'] );

        $dry = expUserGroupManager::assign( $role['role'], $target, $limitation, false );
        $this->assertSame( array(), $dry['errors'] );
        $this->assertSame( array(), array_filter( expUserGroupManager::assignments( $target ), function ( $a ) { return $a['role'] === 'Editor' && $a['via'] > 0 && strpos( $a['limitation'], 'Subtree' ) === 0; } ) );
        $r = expUserGroupManager::assign( $role['role'], $target, $limitation, true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertTrue( $r['applied'] );
        $mine = array_values( array_filter( expUserGroupManager::assignments( $target ), function ( $a ) use ( $target ) { return $a['via'] === $target['object_id']; } ) );
        $this->assertCount( 1, $mine );
        $this->assertSame( 'Editor', $mine[0]['role'] );
        $this->assertSame( $limitation['stored'], $mine[0]['limitation'] );
        $this->assertStringContainsString( 'already', implode( ' ', expUserGroupManager::assign( $role['role'], $target, $limitation, true )['warnings'] ) );

        $r = expUserGroupManager::unassign( $role['role'], $target, array( 'identifier' => '', 'value' => '', 'stored' => '', 'error' => '' ), true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertCount( 1, $r['changes'] );
        $this->assertSame( array(), array_values( array_filter( expUserGroupManager::assignments( $target ), function ( $a ) use ( $target ) { return $a['via'] === $target['object_id']; } ) ) );

        $group = expUserGroupManager::findTarget( 'group:Editors' );
        $this->assertSame( 'group', $group['type'] );
        $this->assertContains( 'Editor', array_column( expUserGroupManager::assignments( $group ), 'role' ) );
        $this->assertNotSame( '', expUserGroupManager::findTarget( 'group:Nowhere' . $this->userLogin( 'x' ) )['error'] );
        $this->userEnd();
    }

    /** UE-10 */
    protected function checkUnlockAndMark()
    {
        $this->userBegin();
        list( $user ) = $this->userCreate( 'unlock' );
        $id = (int)$user->attribute( 'contentobject_id' );
        eZUser::setFailedLoginAttempts( $id, 7, true );
        $this->assertSame( 7, (int)eZUser::failedLoginAttemptsByUserID( $id ) );
        $dry = expUserManager::unlock( eZUser::fetch( $id ), false );
        $this->assertCount( 1, $dry['changes'] );
        $this->assertSame( 7, (int)eZUser::failedLoginAttemptsByUserID( $id ) );
        $r = expUserManager::unlock( eZUser::fetch( $id ), true );
        $this->assertTrue( $r['applied'] );
        $this->assertSame( 0, (int)eZUser::failedLoginAttemptsByUserID( $id ) );
        $this->assertStringContainsString( 'no failed sign-ins', implode( ' ', expUserManager::unlock( eZUser::fetch( $id ), true )['warnings'] ) );

        $r = expUserManager::markPasswordChange( eZUser::fetch( $id ), true, true );
        $this->assertTrue( $r['applied'] );
        $this->assertTrue( expPasswordChangeRequired::isMarked( eZUser::fetch( $id ) ) );
        $r = expUserManager::markPasswordChange( eZUser::fetch( $id ), false, true );
        $this->assertTrue( $r['applied'] );
        $this->assertFalse( expPasswordChangeRequired::isMarked( eZUser::fetch( $id ) ) );
        $this->userEnd();
    }

    /** UE-11 */
    protected function checkCheckLogin()
    {
        $this->userBegin();
        list( $user, $password ) = $this->userCreate( 'check' );
        $id = (int)$user->attribute( 'contentobject_id' );
        $this->assertSame( array( 'valid' => true, 'reason' => 'ok' ), expUserManager::checkLogin( $user, $password ) );
        $wrong = expUserManager::checkLogin( $user, $password . 'x' );
        $this->assertFalse( $wrong['valid'] );
        $this->assertSame( 'wrong_password', $wrong['reason'] );
        $this->assertSame( 0, (int)eZUser::failedLoginAttemptsByUserID( $id ), 'a check never counts as a failed sign-in' );
        $adminID = (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );
        expUserManager::setEnabled( $user, false, $adminID, true );
        $this->assertSame( 'disabled', expUserManager::checkLogin( eZUser::fetch( $id ), $password )['reason'] );
        $this->userEnd();
    }

    /** UE-12 */
    protected function checkImportExport()
    {
        $this->userBegin();
        list( $existing ) = $this->userCreate( 'exists' );
        $a = $this->userLogin( 'impa' );
        $b = $this->userLogin( 'impb' );
        $csv = "login,email,first_name,last_name,groups\n"
             . "$a,$a@example.com,Ann,Import,Guest accounts\n"
             . "$b,$b@example.com,Bob,Import,Guest accounts|Editors\n"
             . "$a,other-$a@example.com,Ann,Again,\n"
             . $existing->attribute( 'login' ) . ",x-" . $existing->attribute( 'email' ) . ",X,Y,\n";
        $parsed = expUserBulk::parse( $csv, 'csv' );
        $this->assertSame( '', $parsed['error'] );
        $this->assertCount( 4, $parsed['rows'] );
        $this->assertSame( 2, $parsed['rows'][0]['line'] );
        $this->assertNotSame( '', expUserBulk::parse( "login,email,password\nx,x@example.com,secret\n", 'csv' )['error'], 'a password column refuses the file' );
        $this->assertNotSame( '', expUserBulk::parse( "login,email,colour\nx,x@example.com,red\n", 'csv' )['error'], 'an unknown column refuses the file' );
        $this->assertSame( '', expUserBulk::parse( json_encode( array( array( 'login' => 'j', 'email' => 'j@example.com' ) ) ), 'json' )['error'] );

        $dry = expUserBulk::import( $parsed['rows'], 'disabled', array( 'Guest accounts' ), false );
        $this->assertSame( array( 'would_create', 'would_create', 'refused', 'skipped' ), array_column( $dry['rows'], 'result' ) );
        $this->assertNull( eZUser::fetchByName( $a ), 'a dry run creates nothing' );
        $r = expUserBulk::import( $parsed['rows'], 'send-activation', array( 'Guest accounts' ), true );
        $this->assertSame( array( 2, 1, 1 ), array( $r['created'], $r['skipped'], $r['refused'] ) );
        $this->assertSame( array( 'created', 'created', 'refused', 'skipped' ), array_column( $r['rows'], 'result' ) );
        $this->assertCount( 2, $this->userMails, 'one activation mail each' );
        $this->assertTrue( expUnactivatedUsers::isUnactivated( (int)eZUser::fetchByName( $b )->attribute( 'contentobject_id' ) ) );
        $groups = array();
        foreach ( eZContentObject::fetch( (int)eZUser::fetchByName( $b )->attribute( 'contentobject_id' ) )->assignedNodes() as $node )
            $groups[] = (int)$node->attribute( 'parent_node_id' );
        $this->assertCount( 2, $groups, 'both groups of the row' );

        $stream = fopen( 'php://memory', 'w+' );
        $filters = expUserManager::listFilters( array( 'search' => $this->userLogin( 'imp' ) ) )['filters'];
        $result = expUserBulk::export( $stream, $filters, 'csv' );
        $this->assertSame( array( 'count' => 2, 'error' => '' ), $result );
        rewind( $stream );
        $text = stream_get_contents( $stream );
        fclose( $stream );
        $this->assertStringStartsWith( implode( ',', expUserBulk::EXPORT_COLUMNS ) . "\n", $text );
        $this->assertStringContainsString( "$a@example.com", $text );
        $this->assertStringContainsString( 'Ann', $text );
        $this->assertStringNotContainsString( (string)eZUser::fetchByName( $a )->attribute( 'password_hash' ), $text, 'never a hash' );
        $back = expUserBulk::parse( $text, 'csv' );
        $this->assertNotSame( '', $back['error'], 'an export is not an import file as it is (it has more columns)' );
        $this->userEnd();
    }

    /** UE-13 */
    protected function checkBulk()
    {
        $this->userBegin();
        $adminID = (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );
        $ids = array();
        foreach ( array( 'bulka', 'bulkb', 'bulkc' ) as $name )
        {
            list( $user ) = $this->userCreate( $name );
            $ids[] = (int)$user->attribute( 'contentobject_id' );
        }
        $filters = expUserManager::listFilters( array( 'search' => $this->userLogin( 'bulk' ) ) )['filters'];
        $dry = expUserBulk::run( 'disable', $filters, 500, $adminID, false );
        $this->assertSame( array(), $dry['errors'] );
        $this->assertSame( 3, $dry['matched'] );
        $this->assertSame( array( 'would', 'would', 'would' ), array_column( $dry['accounts'], 'result' ) );
        $this->assertStringContainsString( '--confirm=3', implode( ' ', expUserBulk::run( 'disable', $filters, 500, $adminID, true )['errors'] ) );
        $this->assertStringContainsString( 'does not match', implode( ' ', expUserBulk::run( 'disable', $filters, 500, $adminID, true, '2' )['errors'] ) );
        $this->assertStringContainsString( 'more than --limit=2', implode( ' ', expUserBulk::run( 'disable', $filters, 2, $adminID, false )['errors'] ) );
        foreach ( $ids as $id )
            $this->assertTrue( $this->userEnabled( $id ), 'nothing changed yet' );
        $r = expUserBulk::run( 'disable', $filters, 500, $adminID, true, '3' );
        $this->assertSame( array(), $r['errors'] );
        $this->assertSame( 3, $r['done'] );
        foreach ( $ids as $id )
            $this->assertFalse( $this->userEnabled( $id ) );
        $again = expUserBulk::run( 'disable', $filters, 500, $adminID, true, '3' );
        $this->assertSame( array( 'unchanged', 'unchanged', 'unchanged' ), array_column( $again['accounts'], 'result' ) );

        $admin = expUserBulk::run( 'disable', expUserManager::listFilters( array( 'search' => 'admin', 'state' => 'enabled' ) )['filters'], 500, $adminID, false );
        $this->assertContains( 'refused', array_column( $admin['accounts'], 'result' ), 'the administrator account is refused' );
        $this->assertSame( '', expUserBulk::confirmProblem( 3, '3' ) );
        $this->assertNotSame( '', expUserBulk::confirmProblem( 3, null ) );
        $this->userEnd();
    }

    /** UE-14 */
    protected function checkReadOnlyTools()
    {
        $this->userBegin();
        list( $user ) = $this->userCreate( 'tools' );
        $counts = expUserManager::hygieneCounts( 180 );
        $this->assertSame( '', $counts['error'] );
        foreach ( expUserManager::STATES as $state )
            $this->assertArrayHasKey( $state, $counts['counts'] );
        $this->assertGreaterThanOrEqual( 2, $counts['counts']['all'] );
        $this->assertGreaterThanOrEqual( 1, $counts['counts']['never'] );
        $sessions = expUserManager::sessions( $user );
        $this->assertFalse( $sessions['tracking'], 'the checks run without tracking' );
        $this->assertSame( array(), $sessions['sessions'] );
        $twoFactor = expUserManager::twoFactor( $user );
        foreach ( array( 'available', 'active', 'method', 'passkeys' ) as $key )
            $this->assertArrayHasKey( $key, $twoFactor );
        $this->assertFalse( $twoFactor['active'] );
        $reset = expUserManager::resetTwoFactor( $user, false, true, true );
        $this->assertSame( array(), $reset['errors'] );
        $audit = expUserManager::auditRows( $user, expUserManager::auditFilters( array() )['filters'] );
        $this->assertArrayHasKey( 'rows', $audit );
        $this->assertNotEmpty( expUserManager::auditFilters( array( 'from' => '2026-13-01', 'result' => 'maybe' ) )['errors'] );
        $this->userEnd();
    }
}
