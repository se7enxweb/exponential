<?php
/**
 * The 4.x compatibility path, the settings writes, the keys and the ownership of the files (doc/bc/6.0/audit.md,
 * "Compatibility mapping", "Keys (Z7)", acceptance test B3).
 *
 *  AC-01 — each of the 15 old names through eZAudit::writeAudit() gives its mapped name; the call site decides
 *          content-delete, content-hide and order-delete; Email without UserID is a reset request
 *  AC-02 — HashKey and secrets never reach a file; Comment is dropped; the other attributes are in after.legacy;
 *          x.legacy names the old name
 *  AC-03 — an unmapped old name becomes system.legacy.<name>; UnmappedAsLegacy=disabled drops it; Audit=disabled:
 *          writeAudit() returns false
 *  AC-04 — the object and target come from the old attributes (Node ID, Object ID, Content Name, New parent node ID,
 *          Section ID, Role ID, Assign to content object ID, Order ID)
 *  AC-05 — keys: generated on first use into <keyDir>/audit.ini.append.php (mode 0640) with an installation id, a
 *          signing key with its key id and a pseudonym key; system.audit.key.create records the fingerprint, never
 *          the key; a second process reads the same keys from the file
 *  AC-06 — ownership: run as root, the log directory and files get the owner and group of the parent directory
 *  AC-07 — a settings write through expIniEditor records system.setting.write per changed variable with before and
 *          after; a secret is [secret]; audit.ini also records system.audit.setting.write and, for Audit=disabled,
 *          system.audit.disable
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';
require_once __DIR__ . '/../ini/fixtures/expinienginetestfixtures.php';

class expAuditCompatTest extends PHPUnit\Framework\TestCase
{
    protected $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = expAuditTestFixtures::setUp( $this->name() );
    }

    protected function tearDown(): void
    {
        expIniEditor::setRoot( null );
        expAuditTestFixtures::tearDown();
        parent::tearDown();
    }

    protected function all()
    {
        $out = array();
        foreach ( array( 'content', 'access', 'system', 'commerce' ) as $c )
            foreach ( expAuditTestFixtures::events( $this->dir, $c ) as $r )
                $out[] = $r;
        usort( $out, function ( $a, $b ) { return strcmp( $a['id'], $b['id'] ); } );
        return $out;
    }

    protected function legacy( $old, array $attrs, $caller = '' )
    {
        $id = expAudit::legacy( $old, $attrs, $caller );
        expAudit::flush();
        foreach ( $this->all() as $r )
            if ( $r['id'] === $id )
                return $r;
        return null;
    }

    /** AC-01 */
    public function testFifteenOldNames()
    {
        $cases = array(
            array( 'user-login', array( 'User id' => 14, 'User login' => 'admin' ), '', 'access.session.login' ),
            array( 'user-failed-login', array( 'User login' => 'nobody', 'Comment' => 'Failed login attempt: eZUser::loginUser()' ), '', 'access.session.login.failed' ),
            array( 'content-delete', array( 'Node ID' => 275, 'Object ID' => 273, 'Content Name' => 'Workout' ), 'eZContentObjectTreeNode::removeThis', 'content.node.remove.trash' ),
            array( 'content-delete', array( 'Object ID' => 273 ), 'eZContentObject::removeThis', 'content.object.remove' ),
            array( 'content-delete', array( 'Object ID' => 273 ), 'eZContentObject::purge', 'content.object.purge' ),
            array( 'content-move', array( 'Node ID' => 275, 'Old parent node ID' => 2, 'New parent node ID' => 89 ), '', 'content.node.move' ),
            array( 'content-hide', array( 'Node ID' => 275 ), 'eZContentObjectTreeNode::hideSubTree', 'content.node.hide' ),
            array( 'content-hide', array( 'Node ID' => 275 ), 'eZContentObjectTreeNode::unhideSubTree', 'content.node.reveal' ),
            array( 'role-change', array( 'Role ID' => 3, 'Role name' => 'Editor' ), '', 'access.role.change' ),
            array( 'role-assign', array( 'Role ID' => 3, 'Role name' => 'Editor', 'Assign to content object ID' => 13 ), '', 'access.role.assign' ),
            array( 'section-assign', array( 'Node ID' => 275, 'Section ID' => 3, 'Section name' => 'Media' ), '', 'content.node.section' ),
            array( 'state-assign', array( 'Content object ID' => 273, 'Selected State ID Array' => array( 1, 5 ) ), '', 'content.object.state' ),
            array( 'order-delete', array( 'Order ID' => 1001 ), 'eZOrder::cleanupOrder', 'commerce.order.delete' ),
            array( 'order-delete', array(), 'eZOrder::cleanup', 'commerce.order.purge' ),
            array( 'user-password-change', array( 'UserID' => 15, 'Login' => 'editor2' ), '', 'access.user.password.change' ),
            array( 'user-password-change-self', array( 'UserID: ' => 14, 'Login: ' => 'editor1' ), '', 'access.user.password.change' ),
            array( 'user-password-change-self-fail', array( 'UserID: ' => 14, 'Login: ' => 'editor1', 'Comment: ' => 'Old password incorrect' ), '', 'access.user.password.change.failed' ),
            array( 'user-forgotpassword', array( 'Email' => 'editor1@example.com', 'Comment' => 'Forgotpassword email sent' ), '', 'access.user.password.reset.request' ),
            array( 'user-forgotpassword', array( 'UserID' => 14, 'Login' => 'editor1', 'Comment: ' => 'Password changed successfully' ), '', 'access.user.password.reset' ),
            array( 'user-forgotpassword-fail', array( 'HashKey' => 'deadbeefcafe', 'Comment' => 'HashKey not found' ), '', 'access.user.password.reset.failed' ),
        );
        foreach ( $cases as list( $old, $attrs, $caller, $name ) )
        {
            $r = $this->legacy( $old, $attrs, $caller );
            $this->assertNotNull( $r, $old );
            $this->assertSame( $name, $r['name'], "$old from $caller" );
            $this->assertSame( $old, $r['x']['legacy']['name'] );
        }
        $this->assertCount( 15, array_unique( array_column( $cases, 0 ) ) + array(), 'the 15 old names' );
        $this->assertSame( 15, count( array_unique( array_column( $cases, 0 ) ) ) );
    }

    /** AC-02 */
    public function testDeniedAttributes()
    {
        $r = $this->legacy( 'user-forgotpassword-fail', array( 'HashKey' => 'deadbeefcafe', 'Comment' => 'HashKey expired, proceed to remove' ) );
        $this->assertSame( 'expired', $r['reason'] );
        $this->assertSame( 'refused', $r['result'] );
        $r = $this->legacy( 'my-own-thing', array( 'Node ID' => 1, 'Password' => 'pw1', 'Api token' => 'tok-value-1', 'Comment' => 'free text', 'Color' => 'blue' ) );
        $this->assertSame( array( 'Api token' => '[secret]', 'Color' => 'blue' ), $r['after']['legacy'] );
        $json = '';
        foreach ( glob( $this->dir . 'log/*.jsonl' ) as $f )
            $json .= file_get_contents( $f );
        foreach ( array( 'deadbeefcafe', 'pw1', 'tok-value-1', 'free text' ) as $never )
            $this->assertStringNotContainsString( $never, $json );
        // via eZAudit, the old API
        $this->assertTrue( eZAudit::writeAudit( 'user-forgotpassword-fail', array( 'HashKey' => 'feedfacefeed', 'Comment' => 'HashKey not found' ) ) );
        $this->assertStringNotContainsString( 'feedfacefeed', implode( '', array_map( 'file_get_contents', glob( $this->dir . 'log/*.jsonl' ) ) ) );
    }

    /** AC-03 */
    public function testUnmappedAndDisabled()
    {
        $r = $this->legacy( 'My-Old Name', array( 'Node ID' => 1 ) );
        $this->assertSame( 'system.legacy.my_old_name', $r['name'] );
        $this->assertSame( 'system', $r['channel'] );
        expAuditTestFixtures::configure( $this->dir, array( 'AuditCompatSettings/UnmappedAsLegacy' => 'disabled' ) );
        $this->assertNull( expAudit::legacy( 'my-old-name', array() ) );
        expAuditTestFixtures::configure( $this->dir, array( 'AuditSettings/Audit' => 'disabled' ) );
        $this->assertFalse( eZAudit::writeAudit( 'user-login', array( 'User id' => 14 ) ) );
        $this->assertFalse( eZAudit::isAuditEnabled() );
    }

    /** AC-04 */
    public function testObjectsFromAttributes()
    {
        $r = $this->legacy( 'content-move', array( 'Node ID' => 275, 'Object ID' => 273, 'Content Name' => 'Workout', 'Old parent node ID' => 2, 'New parent node ID' => 89 ) );
        $this->assertEquals( array( 'type' => 'node', 'id' => 275, 'object_id' => 273, 'name' => 'Workout' ), $r['object'] );
        $this->assertEquals( array( 'type' => 'node', 'id' => 89 ), $r['target'] );
        $this->assertSame( array( 'parent' => 2 ), $r['before'] );
        $this->assertSame( array( 'parent' => 89 ), $r['after'] );
        $r = $this->legacy( 'section-assign', array( 'Node ID' => 275, 'Section ID' => 3, 'Section name' => 'Media' ) );
        $this->assertEquals( array( 'type' => 'section', 'id' => 3, 'name' => 'Media' ), $r['target'] );
        $r = $this->legacy( 'role-assign', array( 'Role ID' => 3, 'Role name' => 'Editor', 'Assign to content object ID' => 13 ) );
        $this->assertEquals( array( 'type' => 'role', 'id' => 3, 'name' => 'Editor' ), $r['object'] );
        $this->assertEquals( array( 'type' => 'user', 'id' => 13 ), $r['target'] );
        $r = $this->legacy( 'order-delete', array( 'Order ID' => 1001 ), 'eZOrder::cleanupOrder' );
        $this->assertEquals( array( 'type' => 'order', 'id' => 1001 ), $r['object'] );
        $r = $this->legacy( 'user-login', array( 'User id' => 14, 'User login' => 'admin' ) );
        $this->assertSame( 14, $r['actor']['user_id'], 'the user logging in is the actor' );
        $this->assertSame( 'admin', $r['actor']['login'] );
    }

    /** AC-05 */
    public function testKeysGeneratedOnFirstUse()
    {
        $dir = expAuditTestFixtures::setUp( 'keys', array(), false );
        expAudit::event( 'access.session.login' );
        $file = $dir . 'keys/audit.ini.append.php';
        $this->assertFileExists( $file );
        $this->assertSame( 0640, fileperms( $file ) & 0777 );
        $values = expIniWriter::fromFile( $file )->values()['AuditKeySettings'];
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $values['InstallationID'] );
        $this->assertMatchesRegularExpression( '/^k1-\d{8}-[0-9a-f]{8}$/', $values['ActiveSigningKey'] );
        $raw = base64_decode( $values['SigningKey'][$values['ActiveSigningKey']] );
        $this->assertSame( 32, strlen( $raw ) );
        $this->assertSame( 32, strlen( base64_decode( $values['PseudonymKey'] ) ) );
        $this->assertTrue( expIniEditor::isSecret( 'SigningKey' ) && expIniEditor::isSecret( 'PseudonymKey' ) );
        $created = array_values( array_filter( expAuditTestFixtures::events( $dir, 'system' ), function ( $r ) { return $r['name'] === 'system.audit.key.create'; } ) );
        $this->assertCount( 1, $created );
        $this->assertSame( expAuditKeys::fingerprint( $raw ), $created[0]['after']['fingerprint'] );
        $this->assertStringNotContainsString( $values['SigningKey'][$values['ActiveSigningKey']], file_get_contents( glob( $dir . 'log/system-*.jsonl' )[0] ) );
        // another process: reads them from the file, generates nothing
        expAuditKeys::reset();
        $keys = new expAuditKeys( expAuditConfig::get() );
        $this->assertSame( $values['InstallationID'], $keys->installationId() );
        $this->assertSame( array(), expAuditKeys::takeCreated() );
        $this->assertSame( 'intact', expAuditTestFixtures::verifier( $dir )->verifyChannel( 'access' )['result'] );
    }

    /** AC-06 */
    public function testOwnershipAsRoot()
    {
        if ( !function_exists( 'posix_geteuid' ) || posix_geteuid() !== 0 )
            $this->markTestSkipped( 'needs root (Velocity and the commands run as root)' );
        $parent = $this->dir . 'owned/';
        mkdir( $parent, 0770 );
        $site = posix_getpwnam( 'alpha' );
        $this->assertNotFalse( $site, 'the site user alpha' );
        chown( $parent, $site['uid'] );
        chgrp( $parent, $site['gid'] );
        expAuditTestFixtures::configure( $this->dir, array( 'logDir' => $parent . 'var/log/audit' ) );
        expAudit::event( 'access.session.login' );
        foreach ( array( $parent . 'var', $parent . 'var/log', $parent . 'var/log/audit' ) as $d )
        {
            $this->assertSame( $site['uid'], fileowner( $d ), $d );
            $this->assertSame( $site['gid'], filegroup( $d ), $d );
            $this->assertSame( 0, fileperms( $d ) & 0007, "$d is not world readable" );
        }
        foreach ( glob( $parent . 'var/log/audit/{,.}*', GLOB_BRACE ) as $f )
        {
            if ( is_dir( $f ) )
                continue;
            $this->assertSame( $site['uid'], fileowner( $f ), $f );
            $this->assertSame( $site['gid'], filegroup( $f ), $f );
            $this->assertSame( 0640, fileperms( $f ) & 0777, $f );
        }
    }

    /** AC-07 */
    public function testSettingsWrites()
    {
        $root = expIniEngineTestFixtures::makeRoot( 'audit-settings' );
        expAuditTestFixtures::configure( $this->dir, array() );
        $editor = new expIniEditor( expIniEditor::scope( 'global' ), 'site.ini' );
        $editor->set( 'DebugSettings', 'DebugOutput', 'enabled' );
        $editor->set( 'MailSettings', 'TransportPassword', 'hunter2' );
        $editor->save( array( 'backup' => false ) );
        $events = array_values( array_filter( expAuditTestFixtures::events( $this->dir, 'system' ), function ( $r ) { return $r['name'] === 'system.setting.write'; } ) );
        $this->assertCount( 2, $events );
        $byVar = array_column( array_map( function ( $r ) { return array( 'v' => $r['object']['variable'], 'r' => $r ); }, $events ), 'r', 'v' );
        $this->assertSame( 'enabled', $byVar['DebugOutput']['after']['value'] );
        $this->assertFalse( $byVar['DebugOutput']['before']['set'] );
        $this->assertSame( 'settings/override/site.ini.append.php', $byVar['DebugOutput']['object']['path'] );
        $this->assertSame( 'global', $byVar['DebugOutput']['object']['scope'] );
        $this->assertSame( '[secret]', $byVar['TransportPassword']['after']['value'] );
        $this->assertArrayNotHasKey( 'diff', $byVar['DebugOutput']['after'], 'no diff when a secret changed' );
        $this->assertStringNotContainsString( 'hunter2', file_get_contents( glob( $this->dir . 'log/system-*.jsonl' )[0] ) );

        $audit = new expIniEditor( expIniEditor::scope( 'global' ), 'audit.ini' );
        $audit->set( 'AuditSettings', 'Audit', 'disabled' );
        $audit->save( array( 'backup' => false ) );
        $names = array_column( expAuditTestFixtures::events( $this->dir, 'system' ), 'name' );
        $this->assertContains( 'system.audit.setting.write', $names );
        $this->assertContains( 'system.audit.disable', $names );
        $this->assertStringContainsString( 'TransportPassword', file_get_contents( $root . 'settings/override/site.ini.append.php' ) );

        // a fixture root is not recorded when the audit uses the live settings
        expAuditConfig::setOverride( null );
        $this->assertSame( 0, expAudit::settingWrite( 'site.ini', 'global', 'x', '', "[A]\nB=c\n", '' ) );
    }
}
