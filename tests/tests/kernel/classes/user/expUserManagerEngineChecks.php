<?php
/**
 * The checks of expUserManager (the work behind the exp:user commands) that run on every SQL engine the same way:
 * SQLite in process (expUserManagerSqliteTest) and MySQL/MariaDB, PostgreSQL in the database job
 * (expUserManagerDatabaseTest). Each gives a connection with the kernel schema and the clean data (the administrator
 * 14, the anonymous user 10, the groups Guest accounts, Editors and Administrator users), which it may write to. Mail is never
 * sent: expUserManager::setMailSender() keeps every mail in an array, and the mails the kernel sends itself (the approval
 * mail when an administrator activates an account) go to the file transport in var/tmp, removed at the end.
 *
 *  UE-1 create: a dry run changes nothing; --apply publishes an enabled account in its groups (the first is the main
 *       one), with first and last name, and the generated password signs in; the checks refuse a login that is taken,
 *       an address that is not valid, a weak password, a group that does not exist and more than one password way
 *  UE-2 edit: e-mail address, login, first name, a group added, the main group changed and the old group removed; an
 *       account keeps one group; a taken login is refused
 *  UE-3 disable and enable: the protected accounts (anonymous, administrator, the user who acts) are refused, the
 *       account is disabled and enabled again, and is listed by the state filters
 *  UE-4 reset the password: generated (the old one stops working, the new one works), from standard input (a weak
 *       one refused), the change required at the next sign-in; the link: one key and one mail of category security,
 *       the password unchanged
 *  UE-5 registration: an account created with --send-activation is disabled with a key, its mail sent; it is pending
 *       (state email) and enable refuses it; approve activates it; another is rejected and removed; a second approve
 *       is refused
 *  UE-6 delete: the administrator, the anonymous user and the last administrator are refused; an account is removed
 *       with its locations; find() names the account by id, login and e-mail address
 *  UE-7 list: the filters search, group, enabled and disabled, the paging, and show() never holds a password hash
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

trait expUserManagerEngineChecks
{
    /** @var eZMail[]|null the mails expUserManager handed to the transport; null outside the checks */
    protected $userMails = null;

    /** @var array|null [MailSettings] before the checks: Transport, FileTransportDirectory, DebugSending */
    protected $userMailSettings = null;

    /** @var string|null the directory of the file transport during the checks */
    protected $userMailDir = null;

    /** A unique login of this run. */
    protected function userLogin( $name )
    {
        static $run = null;
        if ( $run === null )
            $run = bin2hex( random_bytes( 3 ) );
        return "ue{$run}{$name}";
    }

    /** @var mixed [UserSessionSettings] Tracking before the checks (null: not set) */
    protected $userTracking = null;

    /**
     * Starts the checks: the administrator acts, mail is kept. The tracking of sessions is off for the checks: the
     * test process keeps one PHP session for every test, whose row another test's database had, and the tracker would
     * end the administrator's "session" (a console command has none).
     */
    protected function userBegin()
    {
        $this->userMails = array();
        $mails = &$this->userMails;
        expUserManager::setMailSender( function ( eZMail $mail ) use ( &$mails ) {
            $mails[] = $mail;
            return true;
        } );
        $ini = eZINI::instance();
        $this->userTracking = $ini->hasVariable( 'UserSessionSettings', 'Tracking' ) ? $ini->variable( 'UserSessionSettings', 'Tracking' ) : null;
        $ini->setVariable( 'UserSessionSettings', 'Tracking', 'disabled' );
        $this->userMailSettings = array();
        foreach ( array( 'Transport', 'FileTransportDirectory', 'DebugSending' ) as $var )
            $this->userMailSettings[$var] = $ini->hasVariable( 'MailSettings', $var ) ? $ini->variable( 'MailSettings', $var ) : '';
        $this->userMailDir = 'var/tmp/expusermanager-mail/run-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        $ini->setVariable( 'MailSettings', 'Transport', 'file' );
        $ini->setVariable( 'MailSettings', 'FileTransportDirectory', $this->userMailDir );
        $ini->setVariable( 'MailSettings', 'DebugSending', 'disabled' );
        expUserSessionTracker::reset();
        $this->assertSame( '', expUserManager::actAs( 'admin' )['error'] );
        $this->assertSame( (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' ), (int)eZUser::currentUserID() );
    }

    protected function userEnd()
    {
        if ( $this->userMails === null )
            return;
        $this->userMails = null;
        expUserManager::setMailSender( null );
        $ini = eZINI::instance();
        if ( $this->userTracking === null )
            $ini->removeSetting( 'UserSessionSettings', 'Tracking' );
        else
            $ini->setVariable( 'UserSessionSettings', 'Tracking', $this->userTracking );
        foreach ( (array)$this->userMailSettings as $var => $value )
            $ini->setVariable( 'MailSettings', $var, $value );
        $this->userMailSettings = null;
        if ( $this->userMailDir !== null )
        {
            foreach ( glob( $this->userMailDir . '/*' ) ?: array() as $file )
            {
                if ( is_file( $file ) )
                    unlink( $file );
            }
            if ( is_dir( $this->userMailDir ) )
                rmdir( $this->userMailDir );
            $this->userMailDir = null;
        }
        expUserSessionTracker::reset();
    }

    /** @return eZContentObjectTreeNode */
    protected function userGroup( $name )
    {
        $group = expUserManager::findGroup( $name );
        $this->assertNotNull( $group['node'], $group['error'] );
        return $group['node'];
    }

    /** A new enabled account in Guest accounts with a generated password, for the checks that need one. */
    protected function userCreate( $name, array $spec = array() )
    {
        $login = $this->userLogin( $name );
        $r = expUserManager::create( $spec + array( 'login' => $login, 'email' => "$login@example.com", 'first_name' => 'Test', 'last_name' => ucfirst( $name ),
                                                    'groups' => array( 'Guest accounts' ), 'generate' => true ), true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertTrue( $r['applied'] );
        $user = eZUser::fetch( $r['user_id'] );
        $this->assertInstanceOf( eZUser::class, $user );
        return array( $user, isset( $r['generated_password'] ) ? $r['generated_password'] : null );
    }

    /** Whether a password signs the account in (the hash compared, no session). */
    protected function userPasswordWorks( eZUser $user, $password )
    {
        $user = eZUser::fetch( (int)$user->attribute( 'contentobject_id' ) );
        return eZUser::authenticateHash( $user->attribute( 'login' ), $password, eZUser::site(), $user->attribute( 'password_hash_type' ),
                                         $user->attribute( 'password_hash' ) );
    }

    protected function userEnabled( $id )
    {
        $setting = eZUserSetting::fetch( (int)$id );
        return $setting instanceof eZUserSetting && (int)$setting->attribute( 'is_enabled' ) === 1;
    }

    /** UE-1 */
    protected function checkCreate()
    {
        $this->userBegin();
        $login = $this->userLogin( 'jane' );
        $spec = array( 'login' => $login, 'email' => "$login@example.com", 'first_name' => 'Jane', 'last_name' => 'Doe',
                       'groups' => array( 'Guest accounts', (string)$this->userGroup( 'Editors' )->attribute( 'node_id' ) ), 'generate' => true );
        $dry = expUserManager::create( $spec, false );
        $this->assertSame( array(), $dry['errors'] );
        $this->assertFalse( $dry['applied'] );
        $this->assertCount( 3, $dry['changes'] );
        $this->assertArrayNotHasKey( 'generated_password', $dry );
        $this->assertNull( eZUser::fetchByName( $login ), 'a dry run creates nothing' );

        $r = expUserManager::create( $spec, true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertTrue( $r['applied'] );
        $user = eZUser::fetch( $r['user_id'] );
        $this->assertSame( $login, $user->attribute( 'login' ) );
        $object = eZContentObject::fetch( $r['user_id'] );
        $this->assertSame( eZContentObject::STATUS_PUBLISHED, (int)$object->attribute( 'status' ) );
        $this->assertSame( 'Jane Doe', $object->attribute( 'name' ) );
        $this->assertTrue( $this->userEnabled( $r['user_id'] ) );
        $this->assertSame( 'active', $r['state'] );
        $parents = array();
        foreach ( $object->assignedNodes() as $node )
            $parents[] = (int)$node->attribute( 'parent_node_id' );
        sort( $parents );
        $expected = array( (int)$this->userGroup( 'Guest accounts' )->attribute( 'node_id' ), (int)$this->userGroup( 'Editors' )->attribute( 'node_id' ) );
        sort( $expected );
        $this->assertSame( $expected, $parents, 'in both groups' );
        $this->assertSame( (int)$this->userGroup( 'Guest accounts' )->attribute( 'node_id' ), (int)$object->mainParentNodeID(), 'the first is the main group' );
        $this->assertTrue( $this->userPasswordWorks( $user, $r['generated_password'] ), 'the generated password signs in' );

        $refused = expUserManager::create( array( 'login' => $login, 'email' => 'not-an-address', 'groups' => array( 'Nowhere' . $login ),
                                                  'password' => 'abc', 'generate' => true ), true );
        $text = implode( "\n", $refused['errors'] );
        foreach ( array( 'give exactly one of', 'already taken', 'not valid', 'no user group is named' ) as $part )
            $this->assertStringContainsString( $part, $text );
        $this->assertFalse( $refused['applied'] );
        $weak = expUserManager::create( array( 'login' => $this->userLogin( 'weak' ), 'email' => $this->userLogin( 'weak' ) . '@example.com',
                                               'groups' => array( 'Guest accounts' ), 'password' => 'abc' ), true );
        $this->assertNotEmpty( $weak['errors'], 'a weak password is refused' );
        $this->assertNull( eZUser::fetchByName( $this->userLogin( 'weak' ) ) );
        $this->userEnd();
    }

    /** UE-2 */
    protected function checkEdit()
    {
        $this->userBegin();
        list( $user ) = $this->userCreate( 'edit' );
        list( $other ) = $this->userCreate( 'other' );
        $id = (int)$user->attribute( 'contentobject_id' );
        $members = (int)$this->userGroup( 'Guest accounts' )->attribute( 'node_id' );
        $editors = (int)$this->userGroup( 'Editors' )->attribute( 'node_id' );
        $newLogin = $this->userLogin( 'edited' );
        $spec = array( 'email' => "$newLogin@example.com", 'login' => $newLogin, 'first_name' => 'Janet', 'add_groups' => array( 'Editors' ),
                       'remove_groups' => array( 'Guest accounts' ), 'main_group' => 'Editors' );
        $dry = expUserManager::edit( $user, $spec, false );
        $this->assertSame( array(), $dry['errors'] );
        $this->assertCount( 6, $dry['changes'], implode( '; ', $dry['changes'] ) );
        $this->assertNull( eZUser::fetchByName( $newLogin ) );

        $r = expUserManager::edit( $user, $spec, true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertTrue( $r['applied'] );
        $user = eZUser::fetch( $id );
        $this->assertSame( $newLogin, $user->attribute( 'login' ) );
        $this->assertSame( "$newLogin@example.com", $user->attribute( 'email' ) );
        eZContentObject::clearCache();
        $object = eZContentObject::fetch( $id );
        $this->assertSame( 'Janet Edit', $object->attribute( 'name' ) );
        $parents = array();
        foreach ( $object->assignedNodes() as $node )
            $parents[] = (int)$node->attribute( 'parent_node_id' );
        $this->assertSame( array( $editors ), $parents, 'moved from Guest accounts to Editors' );
        $this->assertSame( $editors, (int)$object->mainParentNodeID() );

        $last = expUserManager::edit( $user, array( 'remove_groups' => array( 'Editors' ) ), true );
        $this->assertStringContainsString( 'at least one group', implode( ' ', $last['errors'] ) );
        $taken = expUserManager::edit( $user, array( 'login' => $other->attribute( 'login' ) ), true );
        $this->assertStringContainsString( 'already taken', implode( ' ', $taken['errors'] ) );
        $this->assertSame( array(), expUserManager::edit( $user, array( 'login' => $newLogin ), true )['changes'], 'the same login is no change' );
        $this->assertNotEquals( $members, $editors );
        $this->userEnd();
    }

    /** UE-3 */
    protected function checkDisableEnable()
    {
        $this->userBegin();
        list( $user ) = $this->userCreate( 'disable' );
        $id = (int)$user->attribute( 'contentobject_id' );
        $admin = eZUser::fetchByName( 'admin' );
        $adminID = (int)$admin->attribute( 'contentobject_id' );
        foreach ( array( $adminID => 'self', 10 => 'anonymous' ) as $protected => $reason )
        {
            $r = expUserManager::setEnabled( eZUser::fetch( $protected ), false, $adminID, true );
            $this->assertSame( $reason, $r['refusal'] );
            $this->assertNotEmpty( $r['errors'] );
        }
        $this->assertTrue( $this->userEnabled( $adminID ) );
        $this->assertSame( 'creator', expUserManager::protectedReason( $adminID, $id ), 'the administrator account (UserCreatorID) is protected from others too' );

        $dry = expUserManager::setEnabled( $user, false, $adminID, false );
        $this->assertSame( array(), $dry['errors'] );
        $this->assertTrue( $this->userEnabled( $id ), 'a dry run changes nothing' );
        $r = expUserManager::setEnabled( $user, false, $adminID, true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertTrue( $r['applied'] );
        $this->assertFalse( $this->userEnabled( $id ) );
        $this->assertArrayHasKey( 'sessions_ended', $r );
        $again = expUserManager::setEnabled( eZUser::fetch( $id ), false, $adminID, true );
        $this->assertFalse( $again['applied'] );
        $this->assertStringContainsString( 'disabled already', implode( ' ', $again['warnings'] ) );
        $disabled = expUserManager::listAccounts( expUserManager::listFilters( array( 'state' => 'disabled', 'search' => $user->attribute( 'login' ) ) )['filters'] );
        $this->assertSame( array( $id ), array_column( $disabled['accounts'], 'id' ) );

        $r = expUserManager::setEnabled( eZUser::fetch( $id ), true, $adminID, true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertTrue( $this->userEnabled( $id ) );
        $enabled = expUserManager::listAccounts( expUserManager::listFilters( array( 'state' => 'enabled', 'search' => $user->attribute( 'login' ) ) )['filters'] );
        $this->assertSame( array( $id ), array_column( $enabled['accounts'], 'id' ) );
        $this->userEnd();
    }

    /** UE-4 */
    protected function checkResetPassword()
    {
        $this->userBegin();
        list( $user, $old ) = $this->userCreate( 'reset' );
        $id = (int)$user->attribute( 'contentobject_id' );
        $adminID = (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );

        $dry = expUserManager::resetPassword( $user, 'generate', null, true, $adminID, false );
        $this->assertSame( array(), $dry['errors'] );
        $this->assertArrayNotHasKey( 'generated_password', $dry );
        $this->assertTrue( $this->userPasswordWorks( $user, $old ), 'a dry run changes nothing' );

        $r = expUserManager::resetPassword( $user, 'generate', null, true, $adminID, true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertTrue( $r['applied'] );
        $this->assertFalse( $this->userPasswordWorks( $user, $old ), 'the old password stops working' );
        $this->assertTrue( $this->userPasswordWorks( $user, $r['generated_password'] ) );
        $this->assertTrue( expPasswordChangeRequired::isMarked( eZUser::fetch( $id ) ), 'a new password is required' );

        $weak = expUserManager::resetPassword( eZUser::fetch( $id ), 'stdin', 'abc', false, $adminID, true );
        $this->assertNotEmpty( $weak['errors'] );
        $chosen = expUserManager::generatePassword( 20 );
        $r = expUserManager::resetPassword( eZUser::fetch( $id ), 'stdin', $chosen, false, $adminID, true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertTrue( $this->userPasswordWorks( $user, $chosen ) );
        $this->assertArrayNotHasKey( 'generated_password', $r, 'a chosen password is never echoed' );

        $r = expUserManager::resetPassword( eZUser::fetch( $id ), 'send-link', null, false, $adminID, true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertTrue( $r['mail_sent'] );
        $this->assertCount( 1, $this->userMails );
        $this->assertSame( 'security', $this->userMails[0]->category() );
        $this->assertSame( 1, count( eZPersistentObject::fetchObjectList( eZForgotPassword::definition(), null, array( 'user_id' => $id ), null, null, true ) ) );
        $this->assertTrue( $this->userPasswordWorks( $user, $chosen ), 'the link leaves the password as it is' );
        $this->assertNotEmpty( expUserManager::resetPassword( eZUser::fetch( 10 ), 'generate', null, false, $adminID, true )['errors'], 'never the anonymous user' );
        $this->userEnd();
    }

    /** UE-5 */
    protected function checkRegistration()
    {
        $this->userBegin();
        $adminID = (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );
        $login = $this->userLogin( 'member' );
        $r = expUserManager::create( array( 'login' => $login, 'email' => "$login@example.com", 'groups' => array( 'Guest accounts' ), 'send_activation' => true ), true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertArrayNotHasKey( 'generated_password', $r, 'no password is known' );
        $id = (int)$r['user_id'];
        $this->assertFalse( $this->userEnabled( $id ) );
        $this->assertTrue( expUnactivatedUsers::isUnactivated( $id ) );
        $this->assertCount( 1, $this->userMails, 'the activation mail' );
        $this->assertSame( expRegistrationAdminAction::STATE_EMAIL, $r['state'] );
        $this->assertContains( $id, expUserManager::pendingIDs() );
        $pending = expUserManager::listAccounts( expUserManager::listFilters( array( 'state' => 'pending', 'search' => $login ) )['filters'] );
        $this->assertSame( array( $id ), array_column( $pending['accounts'], 'id' ) );
        $this->assertSame( 'email', $pending['accounts'][0]['registration'] );
        $unactivated = expUserManager::listAccounts( expUserManager::listFilters( array( 'state' => 'unactivated', 'search' => $login ) )['filters'] );
        $this->assertSame( array( $id ), array_column( $unactivated['accounts'], 'id' ) );
        $this->assertStringContainsString( 'exp:user:approve', implode( ' ', expUserManager::setEnabled( eZUser::fetch( $id ), true, $adminID, true )['errors'] ) );

        $dry = expUserManager::decideRegistration( eZUser::fetch( $id ), false, $adminID, false );
        $this->assertSame( array(), $dry['errors'] );
        $this->assertFalse( $this->userEnabled( $id ) );
        $r = expUserManager::decideRegistration( eZUser::fetch( $id ), false, $adminID, true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertTrue( $r['applied'] );
        $this->assertTrue( $this->userEnabled( $id ) );
        $this->assertSame( expRegistrationAdminAction::STATE_ACTIVE, $r['state'] );
        $this->assertNotContains( $id, expUserManager::pendingIDs() );
        $this->assertNotEmpty( expUserManager::decideRegistration( eZUser::fetch( $id ), false, $adminID, true )['errors'], 'an active account does not wait' );

        $spam = $this->userLogin( 'spam' );
        $r = expUserManager::create( array( 'login' => $spam, 'email' => "$spam@example.com", 'groups' => array( 'Guest accounts' ), 'send_activation' => true ), true );
        $spamID = (int)$r['user_id'];
        $r = expUserManager::decideRegistration( eZUser::fetch( $spamID ), true, $adminID, true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertSame( expRegistrationAdminAction::STATE_MISSING, $r['state'] );
        $this->assertNull( eZUser::fetch( $spamID ), 'the rejected account is removed' );
        $this->userEnd();
    }

    /** UE-6 */
    protected function checkDelete()
    {
        $this->userBegin();
        list( $user ) = $this->userCreate( 'delete' );
        $id = (int)$user->attribute( 'contentobject_id' );
        $admin = eZUser::fetchByName( 'admin' );
        $adminID = (int)$admin->attribute( 'contentobject_id' );

        foreach ( array( $id, $user->attribute( 'login' ), $user->attribute( 'email' ) ) as $ref )
        {
            $found = expUserManager::find( (string)$ref );
            $this->assertSame( '', $found['error'] );
            $this->assertSame( $id, (int)$found['user']->attribute( 'contentobject_id' ), "found by $ref" );
        }
        $this->assertNotSame( '', expUserManager::find( 'nobody-' . $this->userLogin( 'x' ) )['error'] );

        $this->assertContains( $adminID, expUserManager::administratorIDs() );
        $this->assertNotContains( $id, expUserManager::administratorIDs() );
        $this->assertSame( 'self', expUserManager::delete( $admin, $adminID, true )['refusal'] );
        $this->assertSame( 'anonymous', expUserManager::delete( eZUser::fetch( 10 ), $adminID, true )['refusal'] );
        // an administrator who is not protected otherwise, and the last one: refused
        list( $boss ) = $this->userCreate( 'boss', array( 'groups' => array( 'Administrator users' ) ) );
        $bossID = (int)$boss->attribute( 'contentobject_id' );
        $this->assertContains( $bossID, expUserManager::administratorIDs() );
        $this->assertSame( '', expUserManager::refusal( $bossID, $adminID ), 'another administrator remains' );
        $this->assertTrue( expUserManager::isLastAdministrator( $bossID, array( $bossID ) ) );

        $dry = expUserManager::delete( $user, $adminID, false );
        $this->assertSame( array(), $dry['errors'] );
        $this->assertInstanceOf( eZUser::class, eZUser::fetch( $id ), 'a dry run removes nothing' );
        $r = expUserManager::delete( $user, $adminID, true );
        $this->assertSame( array(), $r['errors'] );
        $this->assertTrue( $r['applied'] );
        eZContentObject::clearCache();
        $this->assertNull( eZUser::fetch( $id ) );
        $this->assertNull( eZUserSetting::fetch( $id ) );
        $this->userEnd();
    }

    /** UE-7 */
    protected function checkList()
    {
        $this->userBegin();
        $made = array();
        foreach ( array( 'lista', 'listb', 'listc' ) as $name )
        {
            list( $user ) = $this->userCreate( $name );
            $made[] = (int)$user->attribute( 'contentobject_id' );
        }
        $search = $this->userLogin( 'list' );
        $all = expUserManager::listAccounts( expUserManager::listFilters( array( 'search' => $search, 'sort' => 'id' ) )['filters'] );
        $this->assertSame( '', $all['error'] );
        $this->assertSame( 3, $all['total'] );
        $this->assertSame( $made, array_column( $all['accounts'], 'id' ) );
        $page = expUserManager::listAccounts( expUserManager::listFilters( array( 'search' => $search, 'sort' => 'id', 'limit' => '2', 'offset' => '2' ) )['filters'] );
        $this->assertSame( 3, $page['total'] );
        $this->assertSame( array( $made[2] ), array_column( $page['accounts'], 'id' ) );
        $members = expUserManager::listAccounts( expUserManager::listFilters( array( 'search' => $search, 'group' => 'Guest accounts' ) )['filters'] );
        $this->assertSame( 3, $members['total'] );
        $editors = expUserManager::listAccounts( expUserManager::listFilters( array( 'search' => $search, 'group' => 'Editors' ) )['filters'] );
        $this->assertSame( 0, $editors['total'] );
        $byMail = expUserManager::listAccounts( expUserManager::listFilters( array( 'search' => '@example.com', 'state' => 'never' ) )['filters'] );
        $this->assertGreaterThanOrEqual( 3, $byMail['total'], 'the search looks into the e-mail address' );
        $this->assertStringContainsString( 'no user group', expUserManager::listAccounts( expUserManager::listFilters( array( 'group' => 'Nowhere' . $search ) )['filters'] )['error'] );

        $admin = eZUser::fetchByName( 'admin' );
        $show = expUserManager::account( eZUser::fetch( $made[0] ), $admin );
        $this->assertSame( $made[0], $show['id'] );
        $this->assertTrue( $show['enabled'] );
        $this->assertSame( 'active', $show['registration'] );
        $this->assertSame( 'Guest accounts', $show['groups'][0]['name'] );
        $this->assertTrue( $show['groups'][0]['main'] );
        $this->assertFalse( $show['administrator'] );
        $encoded = json_encode( $show );
        $hash = (string)eZUser::fetch( $made[0] )->attribute( 'password_hash' );
        $this->assertStringNotContainsString( 'password_hash', $encoded );
        $this->assertStringNotContainsString( $hash, $encoded, 'never the hash' );
        $adminShow = expUserManager::account( $admin, $admin );
        $this->assertTrue( $adminShow['administrator'] );
        $this->assertSame( 'self', $adminShow['protected'] );
        $this->assertNotEmpty( $adminShow['roles'] );
        $this->userEnd();
    }
}
