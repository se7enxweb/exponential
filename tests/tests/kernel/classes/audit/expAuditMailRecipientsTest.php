<?php
/**
 * Who gets the audit's alert mail (owner decision 2026-10-02; expAuditMailRecipients).
 *
 *  RC-01 — address:, a bare address, admin (AdminEmail); a malformed address or one with a line break is refused
 *  RC-02 — group:<name> ([AlertRecipients_<name>] Addresses[] and Recipients[], nested, a loop cut)
 *  RC-03 — user:<id> and login:<login>, with the current address; a disabled user is left out
 *  RC-04 — usergroup:<node id>, usergroup:<object remote id>, usergroup:<node remote id>: every enabled user
 *          below, sub-groups included
 *  RC-05 — role:<name> and role:<id>: users the role is assigned to directly and through a user group
 *  RC-06 — deduplication (case-insensitive) across kinds, with every source kept
 *  RC-07 — which list applies: the rule's Recipients[], else [AuditAlertSettings] Recipients[], else
 *          [AuditSink_mail] Receivers[], else admin; an unknown rule name or addresses in the event never count
 *  RC-08 — mail through a test transport (no real mail): one mail per recipient, throttled per rule and recipient
 *
 * Live database, as the kernel runs: the test creates its own user groups, users and role in setUpBeforeClass()
 * and removes them in tearDownAfterClass(); no existing user, group or role is changed. Its audit events go to
 * var/tmp/audit-tests/.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';

/** The mail transport of this test: writes each mail into the test directory. */
class expAuditRecipientsTestTransport extends eZMailTransport
{
    public static $dir = null;

    function sendMail( eZMail $mail )
    {
        $n = count( glob( self::$dir . '/mail-*.txt' ) ) + 1;
        $to = array_map( function ( $r ) { return $r['email']; }, $mail->receiverElements() );
        return file_put_contents( self::$dir . sprintf( '/mail-%02d.txt', $n ), 'To: ' . implode( ', ', $to ) . "\n\n" . $mail->body() ) !== false;
    }
}

class expAuditMailRecipientsTest extends PHPUnit\Framework\TestCase
{
    /** @var string|null */
    protected static $bootError = null;

    /** @var array the test's objects: groupA, groupB (node ids, object ids, remote ids), users, role */
    protected static $made = array();

    protected $dir;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        try
        {
            $root = dirname( __DIR__, 5 );
            chdir( $root );
            if ( !class_exists( 'eZScript' ) )
                require_once $root . '/autoload.php';
            if ( !eZScript::instance()->isInitialized() )
            {
                $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
                $script->startup();
                $script->setUseSiteAccess( 'admin' );
                $script->initialize();
                eZExecution::setCleanExit();
            }
            if ( !eZDB::instance()->isConnected() )
                throw new RuntimeException( 'no database connection' );
            expAuditTestFixtures::setUp( 'recipients-setup' );
            self::make();
            expAuditTestFixtures::tearDown();
        }
        catch ( Throwable $e )
        {
            self::$bootError = $e->getMessage();
        }
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$made )
        {
            expAuditTestFixtures::setUp( 'recipients-cleanup' );
            self::cleanUp();
            expAuditTestFixtures::tearDown();
        }
        parent::tearDownAfterClass();
    }

    /** Creates two nested user groups, three users (one disabled) and a role. */
    protected static function make()
    {
        $admin = eZUser::fetchByName( 'admin' );
        eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        $tag = bin2hex( random_bytes( 3 ) );
        $userRoot = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'UserRootNode' );
        $group = function ( $name, $parent ) {
            $o = eZContentFunctions::createAndPublishObject( array( 'class_identifier' => 'user_group', 'parent_node_id' => $parent,
                                                                   'attributes' => array( 'name' => $name, 'description' => 'Temporary: audit recipients test' ) ) );
            if ( !$o )
                throw new RuntimeException( "group $name not created" );
            $n = $o->attribute( 'main_node' );
            self::$made['groups'][] = (int)$o->attribute( 'id' );
            return array( 'object' => (int)$o->attribute( 'id' ), 'node' => (int)$n->attribute( 'node_id' ),
                          'object_remote' => $o->attribute( 'remote_id' ), 'node_remote' => $n->attribute( 'remote_id' ) );
        };
        self::$made['A'] = $group( "Audit recipients test $tag", $userRoot );
        self::$made['B'] = $group( "Audit recipients test $tag sub", self::$made['A']['node'] );
        $user = function ( $login, $parent, $enabled ) {
            $email = $login . '@example.invalid';
            $o = eZContentFunctions::createAndPublishObject( array( 'class_identifier' => 'user', 'parent_node_id' => $parent,
                'attributes' => array( 'first_name' => 'Audit test', 'last_name' => $login,
                                       'user_account' => $login . '|' . $email . '|' . eZUser::createHash( $login, bin2hex( random_bytes( 12 ) ), eZUser::site(), eZUser::hashType() )
                                                         . '|' . eZUser::passwordHashTypeName( eZUser::hashType() ) ) ) );
            if ( !$o )
                throw new RuntimeException( "user $login not created" );
            $id = (int)$o->attribute( 'id' );
            self::$made['users'][$login] = array( 'id' => $id, 'email' => $email );
            $setting = eZUserSetting::fetch( $id ) ?: eZUserSetting::create( $id, 1 );
            $setting->setAttribute( 'is_enabled', $enabled ? 1 : 0 );
            $setting->store();
            eZUser::purgeUserCacheByUserId( $id );
            return $id;
        };
        $user( "audit-rcpt-a-$tag", self::$made['A']['node'], true );
        $user( "audit-rcpt-b-$tag", self::$made['B']['node'], true );
        $user( "audit-rcpt-off-$tag", self::$made['B']['node'], false );
        self::$made['tag'] = $tag;
        $role = eZRole::create( "Audit recipients test $tag" );
        $role->store();
        $role->appendPolicy( 'content', 'read' );
        self::$made['role'] = array( 'id' => (int)$role->attribute( 'id' ), 'name' => $role->attribute( 'name' ) );
        $role->assignToUser( self::$made['users']["audit-rcpt-a-$tag"]['id'] );
        $role->assignToUser( self::$made['B']['object'] );
        eZRole::expireCache();
    }

    /** Removes what make() created, users first, then the groups (sub-group first), then the role. */
    protected static function cleanUp()
    {
        if ( isset( self::$made['role']['id'] ) && ( $role = eZRole::fetch( self::$made['role']['id'] ) ) )
            eZRole::removeRole( self::$made['role']['id'] );
        foreach ( isset( self::$made['users'] ) ? self::$made['users'] : array() as $u )
            eZContentObjectOperations::remove( $u['id'], false );
        foreach ( array_reverse( isset( self::$made['groups'] ) ? self::$made['groups'] : array() ) as $id )
            eZContentObjectOperations::remove( $id, false );
        eZRole::expireCache();
        self::$made = array();
    }

    protected function setUp(): void
    {
        parent::setUp();
        if ( self::$bootError !== null )
            $this->markTestSkipped( 'Kernel not available: ' . self::$bootError );
        $this->dir = expAuditTestFixtures::setUp( $this->name(), array( 'MailSettings/AdminEmail' => 'admin@example.com' ) );
    }

    protected function tearDown(): void
    {
        expAuditTestFixtures::tearDown();
        parent::tearDown();
    }

    protected function u( $which )
    {
        return self::$made['users']['audit-rcpt-' . $which . '-' . self::$made['tag']];
    }

    /** @return string[] lower-cased addresses */
    protected function resolved( array $specs )
    {
        return array_keys( expAuditMailRecipients::resolve( $specs )['addresses'] );
    }

    /** @return string[] lower-cased addresses, sorted (the order of users below a group is the tree's) */
    protected function resolvedSorted( array $specs )
    {
        $a = $this->resolved( $specs );
        sort( $a );
        return $a;
    }

    /** RC-01 */
    public function testAddressesAndAdmin()
    {
        $r = expAuditMailRecipients::resolve( array( 'address:ops@example.com', 'soc@example.com', 'admin', 'address:not-an-address',
                                                     "address:x@example.com\nBcc: y@example.com", 'address:a@example.com,b@example.com', 'nonsense', 'kind:x' ) );
        $this->assertSame( array( 'ops@example.com', 'soc@example.com', 'admin@example.com' ), array_keys( $r['addresses'] ) );
        $this->assertCount( 5, $r['problems'], 'malformed, a line break, a list, an unknown word, an unknown kind' );
    }

    /** RC-02 */
    public function testNamedGroups()
    {
        expAuditTestFixtures::configure( $this->dir, array( 'MailSettings/AdminEmail' => 'admin@example.com',
            'AlertRecipients_security/Addresses' => array( 'sec@example.com', 'oncall@example.com' ),
            'AlertRecipients_security/Recipients' => array( 'group:ops', 'login:' . 'audit-rcpt-a-' . self::$made['tag'] ),
            'AlertRecipients_ops/Addresses' => array( 'ops@example.com' ),
            'AlertRecipients_ops/Recipients' => array( 'group:security' ) ) );
        $this->assertSame( array( 'sec@example.com', 'oncall@example.com', 'ops@example.com', $this->u( 'a' )['email'] ),
                           $this->resolved( array( 'group:security' ) ), 'nested, the loop back to security cut' );
        $this->assertArrayHasKey( 'group:nosuch', expAuditMailRecipients::resolve( array( 'group:nosuch' ) )['problems'] );
    }

    /** RC-03 */
    public function testUsersAndLogins()
    {
        $this->assertSame( array( $this->u( 'a' )['email'] ), $this->resolved( array( 'user:' . $this->u( 'a' )['id'] ) ) );
        $this->assertSame( array( $this->u( 'b' )['email'] ), $this->resolved( array( 'login:audit-rcpt-b-' . self::$made['tag'] ) ) );
        $off = expAuditMailRecipients::resolve( array( 'user:' . $this->u( 'off' )['id'] ) );
        $this->assertSame( array(), $off['addresses'], 'a disabled user is left out' );
        $this->assertStringContainsString( 'disabled', reset( $off['problems'] ) );
        $this->assertSame( array(), $this->resolved( array( 'login:audit-rcpt-nobody-' . self::$made['tag'], 'user:abc' ) ) );
        // the address in effect at send time
        $user = eZUser::fetch( $this->u( 'a' )['id'] );
        $user->setAttribute( 'email', 'changed-' . self::$made['tag'] . '@example.invalid' );
        $user->store();
        $this->assertSame( array( 'changed-' . self::$made['tag'] . '@example.invalid' ), $this->resolved( array( 'user:' . $this->u( 'a' )['id'] ) ) );
        $user->setAttribute( 'email', $this->u( 'a' )['email'] );
        $user->store();
    }

    /** RC-04 */
    public function testUserGroups()
    {
        $both = array( $this->u( 'a' )['email'], $this->u( 'b' )['email'] );
        sort( $both );
        $this->assertSame( $both, $this->resolvedSorted( array( 'usergroup:' . self::$made['A']['node'] ) ), 'sub-groups included, disabled left out' );
        $this->assertSame( $both, $this->resolvedSorted( array( 'usergroup:' . self::$made['A']['object_remote'] ) ), 'by the object remote id' );
        $this->assertSame( $both, $this->resolvedSorted( array( 'usergroup:' . self::$made['A']['node_remote'] ) ), 'by the node remote id' );
        $this->assertSame( array( $this->u( 'b' )['email'] ), $this->resolved( array( 'usergroup:' . self::$made['B']['node'] ) ) );
        $this->assertArrayHasKey( 'usergroup:no-such-remote-id', expAuditMailRecipients::resolve( array( 'usergroup:no-such-remote-id' ) )['problems'] );
    }

    /** RC-05 */
    public function testRoles()
    {
        $both = array( $this->u( 'a' )['email'], $this->u( 'b' )['email'] );
        sort( $both );
        $this->assertSame( $both, $this->resolvedSorted( array( 'role:' . self::$made['role']['name'] ) ), 'directly and through the user group' );
        $this->assertSame( $both, $this->resolvedSorted( array( 'role:' . self::$made['role']['id'] ) ) );
        $this->assertArrayHasKey( 'role:No such role ' . self::$made['tag'],
                                  expAuditMailRecipients::resolve( array( 'role:No such role ' . self::$made['tag'] ) )['problems'] );
    }

    /** RC-06 */
    public function testDeduplication()
    {
        $a = $this->u( 'a' );
        $r = expAuditMailRecipients::resolve( array( 'address:' . strtoupper( $a['email'] ), 'user:' . $a['id'], 'usergroup:' . self::$made['A']['node'],
                                                     'role:' . self::$made['role']['name'], 'admin', 'address:ADMIN@example.com' ) );
        $this->assertCount( 3, $r['addresses'], 'user a, user b, admin' );
        $this->assertSame( array( 'address:' . strtoupper( $a['email'] ), 'user:' . $a['id'], 'usergroup:' . self::$made['A']['node'],
                                  'role:' . self::$made['role']['name'] ), $r['addresses'][strtolower( $a['email'] )]['sources'] );
    }

    /** RC-07 */
    public function testWhichListApplies()
    {
        $alert = function ( $rule, array $extra = array() ) {
            return array( 'name' => 'system.audit.alert', 'after' => array( 'rule' => $rule ) + $extra );
        };
        $this->assertSame( array( 'specs' => array( 'admin' ), 'from' => 'default' ), expAuditMailRecipients::specsFor( $alert( 'brute_force' ) ) );
        expAuditTestFixtures::configure( $this->dir, array( 'MailSettings/AdminEmail' => 'admin@example.com',
                                                            'AuditSink_mail/Receivers' => array( 'old@example.com' ) ) );
        $this->assertSame( 'AuditSink_mail', expAuditMailRecipients::specsFor( $alert( 'brute_force' ) )['from'] );
        expAuditTestFixtures::configure( $this->dir, array( 'MailSettings/AdminEmail' => 'admin@example.com',
                                                            'AuditSink_mail/Receivers' => array( 'old@example.com' ),
                                                            'AuditAlertSettings/Recipients' => array( 'address:global@example.com' ),
                                                            'AlertRule_brute_force/Recipients' => array( 'address:rule@example.com' ) ) );
        $this->assertSame( array( 'address:rule@example.com' ), expAuditMailRecipients::specsFor( $alert( 'brute_force' ) )['specs'] );
        $this->assertSame( array( 'address:global@example.com' ), expAuditMailRecipients::specsFor( $alert( 'mass_delete' ) )['specs'], 'a rule without its own list' );
        $this->assertSame( 'AuditAlertSettings', expAuditMailRecipients::specsFor( array( 'name' => 'access.role.assign' ) )['from'], 'any other record' );
        // the event never names recipients: an unknown rule and addresses in the record count for nothing
        $forged = $alert( 'not_a_rule', array( 'recipients' => array( 'evil@example.com' ), 'sinks' => array( 'mail' ) ) );
        $forged['actor'] = array( 'login' => 'evil@example.com' );
        $this->assertSame( array( 'address:global@example.com' ), expAuditMailRecipients::specsFor( $forged )['specs'] );
        $o = expAuditMailRecipients::overview();
        $this->assertSame( array( 'rule@example.com' ), array_column( $o['brute_force']['addresses'], 'address' ) );
        $this->assertTrue( $o['brute_force']['mailed'] );
        $this->assertFalse( $o['settings_out_of_hours']['mailed'], 'its Sinks[] has no mail' );
    }

    /** RC-08 */
    public function testMailPerRecipientThrottledPerRuleAndRecipient()
    {
        expAuditRecipientsTestTransport::$dir = $this->dir . 'mail';
        mkdir( $this->dir . 'mail' );
        expAuditTestFixtures::configure( $this->dir, array( 'MailSettings/AdminEmail' => 'admin@example.com', 'sinks' => true,
            'AuditChannel_access/Sinks' => array(), 'AuditChannel_system/Sinks' => array(),
            'AuditSink_mail/Transport' => 'expAuditRecipientsTestTransport',
            'AuditSink_syslog/Transport' => 'udp', 'AuditSink_syslog/Host' => '',
            'AlertRecipients_security/Addresses' => array( 'sec@example.com' ),
            'AlertRule_brute_force/Recipients' => array( 'group:security', 'role:' . self::$made['role']['name'] ),
            'AuditAlertSettings/Recipients' => array( 'admin', 'address:SEC@example.com' ) ) );
        expAuditSinkRegistry::reset();
        expAudit::setNow( 1790946000 );
        for ( $i = 0; $i < 40; $i++ )
            expAudit::event( 'access.session.login.failed', array( 'object' => array( 'type' => 'user', 'id' => 14 ), 'result' => 'failed' ) );
        // brute_force fired at 20 and 40, brute_force_user at 10, 20 and 40
        $r = expAuditSinkRegistry::deliverSpools( true, 'mail' );
        $this->assertSame( 5, $r['mail']['delivered'] );
        $to = array();
        foreach ( glob( $this->dir . 'mail/mail-*.txt' ) as $f )
            $to[] = substr( strtok( file_get_contents( $f ), "\n" ), 4 );
        sort( $to );
        // brute_force: security + the role's two enabled users, once each; brute_force_user: the default list, once each
        $want = array( 'sec@example.com', $this->u( 'a' )['email'], $this->u( 'b' )['email'], 'admin@example.com', 'SEC@example.com' );
        sort( $want );
        $this->assertSame( $want, $to, 'one mail per rule and recipient within Throttle; the disabled user gets none' );
        expAuditSinkRegistry::reset();
    }
}
