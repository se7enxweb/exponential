<?php
/**
 * "Verify it's you" (doc/eng-us/guides/verify-its-you.md): the group "delete", the rule condition bulk, the user groups
 * a group asks (UserGroups[]) and the trigger handlers of extensions, without a database.
 *
 *  RG-01 the rule token bulk parses as a condition and matches only while the condition holds; the condition is asked
 *        once per decision, and only for rules that carry it
 *  RG-02 delete (on by default): emptying the trash and removing from it ask; removing several items at once asks,
 *        removing one does not
 *  RG-03 content (off by default) asks, once switched on, for publishing, storing a draft and removing one item
 *  RG-04 UserGroups[]: a group with user groups asks only their members; the member list is fetched once and only for
 *        a group that names user groups
 *  RG-06 the settings page: the scope (every siteaccess or a known one), the file it writes, the user groups of a form
 *  RG-05 trigger handlers: a handler names a group; only an existing, switched-on group that applies to the user counts;
 *        a class that is not a handler is ignored
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expReauthGroupsTest extends PHPUnit\Framework\TestCase
{
    /** @return array the groups of the shipped settings/site.ini, with UserGroups[] */
    private static function shipped()
    {
        $ini = file_get_contents( dirname( __DIR__, 5 ) . '/settings/site.ini' );
        $groups = array();
        $block = null;
        foreach ( explode( "\n", $ini ) as $line )
        {
            $line = rtrim( $line, "\r" );
            if ( preg_match( '/^\[(.+)\]\s*$/', $line, $m ) )
            {
                $block = $m[1];
                continue;
            }
            if ( $block === 'ReauthSettings' && preg_match( '/^Groups\[\]=(.+)$/', $line, $m ) )
                $groups[$m[1]] = array( 'enabled' => false, 'rules' => array(), 'user_groups' => array() );
            if ( $block !== null && strpos( $block, 'ReauthGroup_' ) === 0 )
            {
                $name = substr( $block, 12 );
                if ( preg_match( '/^Enabled=(.+)$/', $line, $m ) )
                    $groups[$name]['enabled'] = expReauth::isOn( $m[1] );
                if ( preg_match( '/^Rules\[\]=(.+)$/', $line, $m ) )
                    $groups[$name]['rules'][] = $m[1];
                if ( preg_match( '/^UserGroups\[\]=(\d+)$/', $line, $m ) )
                    $groups[$name]['user_groups'][] = (int)$m[1];
            }
        }
        return array( 'enabled' => true, 'minutes' => 10, 'max_failures' => 5, 'bulk_count' => 2, 'trigger_handlers' => array(),
                      'factors' => expReauth::parseFactors( array() ), 'groups' => $groups );
    }

    /** RG-01 */
    public function testBulkCondition()
    {
        $p = expReauthRules::parse( 'content/removeobject * bulk' );
        $this->assertSame( array( 'bulk' ), $p['conditions'] );
        $this->assertTrue( $p['every'] );
        $asked = 0;
        $yes = array( 'bulk' => function () use ( &$asked ) { $asked++; return true; } );
        $no = array( 'bulk' => function () { return false; } );
        $rules = array( 'content/removeobject * bulk', 'content/removeobject ConfirmButton bulk' );
        $this->assertSame( 'content/removeobject * bulk', expReauthRules::match( $rules, 'content', 'removeobject', 'POST', array( 'ConfirmButton' => 1 ), null, null, $yes ) );
        $this->assertSame( 1, $asked );
        $this->assertNull( expReauthRules::match( $rules, 'content', 'removeobject', 'POST', array( 'ConfirmButton' => 1 ), null, null, $no ) );
        $this->assertNull( expReauthRules::match( $rules, 'content', 'removeobject', 'GET', array() ), 'no condition given: it does not hold' );
        $asked = 0;
        expReauthRules::match( array( 'setup/cache' ), 'setup', 'cache', 'POST', array(), null, null, $yes );
        $this->assertSame( 0, $asked, 'a rule without the condition does not ask it' );
    }

    /** RG-02 */
    public function testDeleteGroup()
    {
        $s = self::shipped();
        $this->assertTrue( $s['groups']['delete']['enabled'] );
        $many = array( 'bulk' => function () { return true; } );
        $one = array( 'bulk' => function () { return false; } );
        $this->assertSame( 'delete', expReauth::matchGroup( $s, 'content', 'trash', 'POST', array( 'EmptyButton' => 1 ) )['group'] );
        $this->assertSame( 'delete', expReauth::matchGroup( $s, 'content', 'trash', 'POST', array( 'RemoveButton' => 1 ) )['group'] );
        $this->assertSame( 'delete', expReauth::matchGroup( $s, 'content', 'removeobject', 'POST', array( 'ConfirmButton' => 1 ), null, null, $many )['group'] );
        $this->assertSame( 'delete', expReauth::matchGroup( $s, 'content', 'removeobject', 'GET', array(), null, null, $many )['group'],
                           'removal without the confirmation page (HideRemoveConfirmation) is a GET' );
        $this->assertNull( expReauth::matchGroup( $s, 'content', 'removeobject', 'POST', array( 'ConfirmButton' => 1 ), null, null, $one ) );
        $this->assertNull( expReauth::matchGroup( $s, 'content', 'trash', 'GET', array() ), 'looking into the trash' );
    }

    /** RG-03 */
    public function testContentGroup()
    {
        $s = self::shipped();
        $this->assertFalse( $s['groups']['content']['enabled'] );
        $this->assertSame( array(), $s['groups']['content']['user_groups'], 'every user by default' );
        $this->assertNull( expReauth::matchGroup( $s, 'content', 'edit', 'POST', array( 'StoreButton' => 1 ) ) );
        $s['groups']['content']['enabled'] = true;
        foreach ( array( 'PublishButton', 'StoreButton', 'StoreExitButton' ) as $button )
            $this->assertSame( 'content', expReauth::matchGroup( $s, 'content', 'edit', 'POST', array( $button => 1 ), function () { return null; } )['group'], $button );
        $this->assertSame( 'content', expReauth::matchGroup( $s, 'content', 'removeobject', 'POST', array( 'ConfirmButton' => 1 ) )['group'] );
        $this->assertNull( expReauth::matchGroup( $s, 'content', 'edit', 'POST', array( 'CustomActionButton' => 1 ), function () { return null; } ),
                           'adding a row to a matrix in the edit form is not a save' );
    }

    /** RG-04 */
    public function testUserGroups()
    {
        $s = self::shipped();
        $s['groups']['content']['enabled'] = true;
        $s['groups']['content']['user_groups'] = array( 13, 42 );
        $calls = 0;
        $editor = function () use ( &$calls ) { $calls++; return array( 12, '42' ); };
        $member = function () { return array( 11 ); };
        $post = array( 'PublishButton' => 1 );
        $none = function () { return null; };
        $this->assertSame( 'content', expReauth::matchGroup( $s, 'content', 'edit', 'POST', $post, $none, null, array(), $editor )['group'] );
        $this->assertSame( 1, $calls );
        $this->assertNull( expReauth::matchGroup( $s, 'content', 'edit', 'POST', $post, $none, null, array(), $member ) );
        $this->assertNull( expReauth::matchGroup( $s, 'content', 'edit', 'POST', $post, $none ), 'no member list: not a member' );
        $calls = 0;
        expReauth::matchGroup( $s, 'setup', 'cache', 'POST', array(), null, null, array(), $editor );
        $this->assertSame( 0, $calls, 'a group without user groups never fetches them' );
    }

    /** RG-06 */
    public function testSettingsPageScopeAndUserGroups()
    {
        $this->assertSame( '', expReauthSettings::scope( 'nosuch', array( 'admin', 'editor' ) ) );
        $this->assertSame( 'editor', expReauthSettings::scope( 'editor', array( 'admin', 'editor' ) ) );
        $this->assertSame( '', expReauthSettings::scope( array( 'editor' ), array( 'editor' ) ) );
        $this->assertSame( 'override', expReauthSettings::scopeDirectory( '' ) );
        $this->assertSame( 'siteaccess/editor', expReauthSettings::scopeDirectory( 'editor' ) );
        $v = expReauthSettings::fromForm( array( 'account', 'content' ), array(
            'ReauthGroups' => array( 'content' ), 'ReauthMinutes' => '15', 'ReauthUserGroupsShown' => array( 'content' ),
            'ReauthUserGroups' => array( 'content' => array( '42', '13', '42', 'x', '-1', '0' ), 'account' => array( '7' ) ) ) );
        $this->assertSame( array( 'content' => array( 13, 42 ) ), $v['user_groups'], 'only the groups the form showed a picker for' );
        $v = expReauthSettings::fromForm( array( 'content' ), array( 'ReauthUserGroupsShown' => array( 'content' ) ) );
        $this->assertSame( array( 'content' => array() ), $v['user_groups'], 'none ticked: every user' );
        $v = expReauthSettings::fromForm( array( 'content' ), array() );
        $this->assertArrayNotHasKey( 'user_groups', $v, 'a form without the picker leaves UserGroups[] as it is' );
    }

    /** RG-05 */
    public function testTriggerHandlers()
    {
        $s = self::shipped();
        $user = $this->getMockBuilder( 'eZUser' )->disableOriginalConstructor()->getMock();
        $s['trigger_handlers'] = array( 'expReauthGroupsTestNotAHandler', 'expReauthGroupsTestMissing', 'expReauthGroupsTestHandler' );
        $m = expReauth::matchHandlers( $s, 'myext', 'wipe', 'POST', array( 'WipeButton' => 1 ), array(), $user );
        $this->assertSame( array( 'group' => 'delete', 'rule' => 'handler:expReauthGroupsTestHandler' ), $m );
        $this->assertNull( expReauth::matchHandlers( $s, 'myext', 'list', 'GET', array(), array(), $user ) );
        $s['groups']['delete']['enabled'] = false;
        $this->assertNull( expReauth::matchHandlers( $s, 'myext', 'wipe', 'POST', array( 'WipeButton' => 1 ), array(), $user ), 'the group is off' );
        $this->assertNull( expReauth::matchHandlers( $s, 'myext', 'publishall', 'POST', array(), array(), $user ), 'a group that does not exist' );
        $s['groups']['delete']['enabled'] = true;
        $s['groups']['delete']['user_groups'] = array( 99 );
        $this->assertNull( expReauth::matchHandlers( $s, 'myext', 'wipe', 'POST', array( 'WipeButton' => 1 ), array(), $user, function () { return array( 12 ); } ) );
        $this->assertNotNull( expReauth::matchHandlers( $s, 'myext', 'wipe', 'POST', array( 'WipeButton' => 1 ), array(), $user, function () { return array( 99 ); } ) );
    }
}

/** RG-05: an extension's trigger handler */
class expReauthGroupsTestHandler implements expReauthTriggerHandler
{
    public function reauthGroup( $module, $view, $method, array $post, array $params, eZUser $user )
    {
        if ( $module === 'myext' && $view === 'wipe' && $method === 'POST' )
            return 'delete';
        if ( $module === 'myext' && $view === 'publishall' )
            return 'nosuchgroup';
        return null;
    }
}

/** RG-05: a class named in TriggerHandlers[] that is not a handler */
class expReauthGroupsTestNotAHandler
{
    public function reauthGroup()
    {
        return 'system';
    }
}
