<?php
/**
 * Security alerts by e-mail (expUserSecurityAlerts): which stored row of the user audit log alerts the owner of an
 * account. No installation and no mail: decide() is a plain function; nothing here reaches a mail transport.
 *
 *  AL-01 - Two-step sign-in on or off, a recovery code used and a lock alert; failed sign-ins alert once, when the count
 *          in a row reaches the threshold; nothing else does (password changes have their own mail)
 *  AL-02 - Nothing when the site turned the alerts off, for a row about a group or nobody, or for a row older than an
 *          hour (a spool delivered late)
 *  AL-03 - The wiring: the user audit log hands the rows it stored for the first time to notify(); the settings; the
 *          mail template in the shared mail layout with a "Secure your account" button and no way to turn it off
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../datatypes/eZDatatypeTestFixtures.php';

class expUserSecurityAlertsTest extends eZDatatypeTestCase
{
    const NOW = 1800000000;

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function row( $name, array $after = array(), $created = self::NOW, $type = 'user', $id = 14 )
    {
        return array( 'event_name' => $name, 'target_type' => $type, 'target_id' => $id, 'created' => $created,
                      'value_after' => $after ? json_encode( $after ) : '' );
    }

    /** AL-01 */
    public function testWhichRowsAlert()
    {
        $on = array( 'enabled' => true, 'threshold' => 5 );
        $this->assertSame( 'twofactor_on', expUserSecurityAlerts::decide( self::row( 'access.user.two_factor.enroll', array( 'method' => 'totp' ) ), $on, self::NOW ) );
        $this->assertSame( 'twofactor_off', expUserSecurityAlerts::decide( self::row( 'access.user.two_factor.remove' ), $on, self::NOW ) );
        $this->assertSame( 'recovery_used', expUserSecurityAlerts::decide( self::row( 'access.user.two_factor.recovery_used', array( 'left' => 7 ) ), $on, self::NOW ) );
        $this->assertSame( 'locked', expUserSecurityAlerts::decide( self::row( 'access.user.lock' ), $on, self::NOW ) );
        $this->assertNull( expUserSecurityAlerts::decide( self::row( 'access.session.login.failed', array( 'attempts' => 4 ) ), $on, self::NOW ) );
        $this->assertSame( 'failures', expUserSecurityAlerts::decide( self::row( 'access.session.login.failed', array( 'attempts' => 5 ) ), $on, self::NOW ) );
        $this->assertNull( expUserSecurityAlerts::decide( self::row( 'access.session.login.failed', array( 'attempts' => 6 ) ), $on, self::NOW ), 'once per run of failures' );
        $this->assertNull( expUserSecurityAlerts::decide( self::row( 'access.session.login.failed', array( 'attempts' => 5 ) ), array( 'enabled' => true, 'threshold' => 0 ), self::NOW ) );
        foreach ( array( 'access.session.login', 'access.user.password.change', 'access.session.new_device', 'access.user.email.change' ) as $name )
            $this->assertNull( expUserSecurityAlerts::decide( self::row( $name ), $on, self::NOW ), $name );
    }

    /** AL-02 */
    public function testWhenNothingIsSent()
    {
        $on = array( 'enabled' => true, 'threshold' => 5 );
        $this->assertNull( expUserSecurityAlerts::decide( self::row( 'access.user.lock' ), array( 'enabled' => false, 'threshold' => 5 ), self::NOW ) );
        $this->assertNull( expUserSecurityAlerts::decide( self::row( 'access.user.lock', array(), self::NOW, 'group' ), $on, self::NOW ) );
        $this->assertNull( expUserSecurityAlerts::decide( self::row( 'access.user.lock', array(), self::NOW, 'user', 0 ), $on, self::NOW ) );
        $this->assertNull( expUserSecurityAlerts::decide( self::row( 'access.user.lock', array(), self::NOW - 7200 ), $on, self::NOW ) );
        $this->assertSame( 'locked', expUserSecurityAlerts::decide( self::row( 'access.user.lock', array(), self::NOW - 600 ), $on, self::NOW ) );
    }

    /** AL-03 */
    public function testWiring()
    {
        $audit = file_get_contents( self::root() . '/kernel/classes/user/expuseraudit.php' );
        $this->assertStringContainsString( 'expUserSecurityAlerts::notify( array_values( $rows ) )', $audit );
        $site = eZINI::instance();
        $this->assertSame( 'enabled', $site->variable( 'UserSessionSettings', 'SecurityAlertMail' ) );
        $this->assertSame( '5', $site->variable( 'UserSessionSettings', 'FailedSignInAlertThreshold' ) );
        $this->assertSame( array( 'enabled' => true, 'threshold' => 5 ), expUserSecurityAlerts::settings() );
        $tpl = file_get_contents( self::root() . '/design/standard/templates/user/security_alert_mail.tpl' );
        foreach ( array( "design:mail/layout.tpl", "design:mail/layout_text.tpl", "'Secure your account'", 'set-block scope=root variable=subject',
                         "match='twofactor_on'", "match='twofactor_off'", "match='recovery_used'", "match='failures'", "match='locked'" ) as $needle )
            $this->assertStringContainsString( $needle, $tpl );
        $this->assertStringNotContainsString( 'turn these e-mails off', $tpl );
        $this->assertSame( array(), expUserSecurityAlerts::$sent, 'no mail was sent by this test' );
    }

    /** AL-03: the data protection side, a removed account loses its sessions; the own data can be downloaded */
    public function testErasureAndExportWiring()
    {
        $this->assertStringContainsString( 'expUserSessionTracker::eraseForUser( (int)$userID )',
                                           file_get_contents( self::root() . '/kernel/classes/datatypes/ezuser/ezuser.php' ) );
        $view = file_get_contents( self::root() . '/kernel/private/classes/views/user/security.php' );
        $this->assertStringContainsString( "'data.export.json'", $view );
        $this->assertStringContainsString( '$own &&', $view, 'only one\'s own data is downloaded' );
        $this->assertArrayHasKey( 'data.export.json', expAuditTaxonomy::catalogue() );
        $this->assertContains( 'user/security DownloadButton', eZINI::instance()->variable( 'ReauthGroup_export', 'Rules' ) );
    }
}
