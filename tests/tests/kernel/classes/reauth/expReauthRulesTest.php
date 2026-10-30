<?php
/**
 * "Verify it's you" (doc/eng-us/guides/verify-its-you.md): the rules and the decision, without a database. The groups
 * of the shipped site.ini are read from the file itself, so the defaults the owner decided are checked as shipped:
 * account, users, system and export on, content off.
 *
 *  RA-01 rules parse: view, module/*, "*", fields, self/other, credentials, exceptions; malformed ones are null
 *  RA-02 a POST matches, a GET only with "*"; fields narrow a rule to the POSTs that carry one of them
 *  RA-03 self and other ask whose account the request addresses, only when a rule needs it
 *  RA-04 credentials: a changed e-mail address, login or password counts, "_ezpassword" and unchanged values do not
 *  RA-05 an exception (!module/view) wins over module/* in its group
 *  RA-06 the shipped groups: account, users, system, export, delete on; content off; every rule parses
 *  RA-07 matchGroup(): only switched-on groups; user/login and user/logout never; the switch of the whole feature
 *  RA-08 the shipped rules ask for the four groups' typical actions and not for reading pages
 *  RA-09 the replayed POST: nested fields flattened to name[key], the form token left out; the form's own fields (its passkey fields only in its answer)
 *        are taken out before the view runs
 *  RA-10 the settings page: the form's values (groups ticked, minutes kept within 1 to 1440)
 *  RA-11 the approve and deny of a new user account waiting for approval (collaboration/action of an item of the event
 *        "Approve user accounts", which addresses that account) ask in the users group; a content approval (it addresses
 *        no account), a comment and the content group (off, and without collaboration rules) do not
 *  RA-12 an upload is a file actually chosen: file inputs left empty (UPLOAD_ERR_NO_FILE, also in nested arrays) are
 *        none, so such a form is replayed whole; a chosen file (or one that did not arrive) is one; the mark of an
 *        upload the page sends back is one of the form's own fields
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expReauthRulesTest extends PHPUnit\Framework\TestCase
{
    /** @var array the shipped groups, read from settings/site.ini */
    private static $shipped;

    public static function setUpBeforeClass(): void
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
                $groups[$m[1]] = array( 'enabled' => false, 'rules' => array() );
            if ( $block !== null && strpos( $block, 'ReauthGroup_' ) === 0 )
            {
                $name = substr( $block, 12 );
                if ( preg_match( '/^Enabled=(.+)$/', $line, $m ) )
                    $groups[$name]['enabled'] = expReauth::isOn( $m[1] );
                if ( preg_match( '/^Rules\[\]=(.+)$/', $line, $m ) )
                    $groups[$name]['rules'][] = $m[1];
            }
        }
        self::$shipped = array( 'enabled' => true, 'minutes' => 10, 'max_failures' => 5, 'groups' => $groups );
    }

    /** RA-01 */
    public function testParse()
    {
        $this->assertSame( array( 'module' => 'user', 'view' => 'password', 'every' => false, 'fields' => array(), 'who' => 'self',
                                  'credentials' => false, 'conditions' => array(), 'exception' => false ), expReauthRules::parse( 'user/password self' ) );
        $p = expReauthRules::parse( 'content/edit other credentials PublishButton' );
        $this->assertSame( array( 'other', true, array( 'PublishButton' ) ), array( $p['who'], $p['credentials'], $p['fields'] ) );
        $p = expReauthRules::parse( 'package/upload *' );
        $this->assertTrue( $p['every'] );
        $this->assertSame( '*', expReauthRules::parse( 'role/*' )['view'] );
        $this->assertTrue( expReauthRules::parse( '!setup/info' )['exception'] );
        foreach ( array( '', 'user', 'user/', '/x', 'user/pass word?', 'a/b c=d', 'a/b <x>' ) as $bad )
            $this->assertNull( expReauthRules::parse( $bad ), $bad );
    }

    /** RA-02 */
    public function testMethodsAndFields()
    {
        $rules = array( 'setup/cache', 'package/upload *', 'content/trash EmptyButton RemoveButton' );
        $this->assertSame( 'setup/cache', expReauthRules::match( $rules, 'setup', 'cache', 'POST', array( 'ClearAllCacheButton' => 1 ) ) );
        $this->assertNull( expReauthRules::match( $rules, 'setup', 'cache', 'GET', array() ) );
        $this->assertSame( 'package/upload *', expReauthRules::match( $rules, 'package', 'upload', 'GET', array() ) );
        $this->assertSame( 'content/trash EmptyButton RemoveButton', expReauthRules::match( $rules, 'content', 'trash', 'POST', array( 'RemoveButton' => '' ) ) );
        $this->assertNull( expReauthRules::match( $rules, 'content', 'trash', 'POST', array( 'FilterButton' => 1 ) ) );
        $this->assertNull( expReauthRules::match( $rules, 'content', 'trash', 'GET', array( 'EmptyButton' => 1 ) ) );
        $this->assertNull( expReauthRules::match( $rules, 'setup', 'other', 'POST', array() ) );
    }

    /** RA-03 */
    public function testWhose()
    {
        $asked = 0;
        $self = function () use ( &$asked ) { $asked++; return 'self'; };
        $rules = array( 'user/password self', 'user/setting' );
        $this->assertSame( 'user/password self', expReauthRules::match( $rules, 'user', 'password', 'POST', array(), $self ) );
        $this->assertSame( 1, $asked );
        $this->assertNull( expReauthRules::match( array( 'user/password other' ), 'user', 'password', 'POST', array(), $self ) );
        $this->assertNull( expReauthRules::match( array( 'content/edit other' ), 'content', 'edit', 'POST', array(), function () { return null; } ),
                           'a request that addresses no account never matches self or other' );
        $asked = 0;
        expReauthRules::match( $rules, 'user', 'setting', 'POST', array(), $self );
        $this->assertSame( 0, $asked, 'a rule without self/other does not ask' );
    }

    /** RA-04 */
    public function testCredentials()
    {
        $base = 'ContentObjectAttribute_data_user_';
        $this->assertFalse( expReauthRules::changesCredentials( array( $base . 'email_12' => 'Jane@Example.invalid', $base . 'password_12' => '_ezpassword',
                                                                      $base . 'password_confirm_12' => '_ezpassword' ), 'jane', 'jane@example.invalid' ) );
        $this->assertTrue( expReauthRules::changesCredentials( array( $base . 'email_12' => 'new@example.invalid' ), 'jane', 'jane@example.invalid' ) );
        $this->assertTrue( expReauthRules::changesCredentials( array( $base . 'password_12' => 'S3cret!pass' ), 'jane', 'jane@example.invalid' ) );
        $this->assertTrue( expReauthRules::changesCredentials( array( $base . 'login_12' => 'janet' ), 'jane', 'jane@example.invalid' ) );
        $this->assertFalse( expReauthRules::changesCredentials( array( $base . 'login_12' => '', 'ContentObjectAttribute_ezstring_data_text_13' => 'x' ), 'jane', 'j@example.invalid' ) );
        $yes = function () { return true; };
        $no = function () { return false; };
        $rules = array( 'content/edit self credentials' );
        $self = function () { return 'self'; };
        $this->assertNotNull( expReauthRules::match( $rules, 'content', 'edit', 'POST', array(), $self, $yes ) );
        $this->assertNull( expReauthRules::match( $rules, 'content', 'edit', 'POST', array(), $self, $no ) );
    }

    /** RA-05 */
    public function testExceptions()
    {
        $rules = array( 'setup/*', '!setup/info' );
        $this->assertSame( 'setup/*', expReauthRules::match( $rules, 'setup', 'cache', 'POST', array() ) );
        $this->assertNull( expReauthRules::match( $rules, 'setup', 'info', 'POST', array() ) );
        $this->assertNull( expReauthRules::match( array( '!setup/info', 'setup/*' ), 'setup', 'info', 'POST', array() ), 'order does not matter' );
    }

    /** RA-06 */
    public function testShippedGroups()
    {
        $g = self::$shipped['groups'];
        $this->assertSame( array( 'account', 'users', 'system', 'export', 'delete', 'content' ), array_keys( $g ) );
        foreach ( array( 'account', 'users', 'system', 'export', 'delete' ) as $on )
            $this->assertTrue( $g[$on]['enabled'], "$on is on by default" );
        $this->assertFalse( $g['content']['enabled'], 'content is off by default' );
        foreach ( $g as $name => $group )
        {
            $this->assertNotEmpty( $group['rules'], $name );
            foreach ( $group['rules'] as $rule )
                $this->assertNotNull( expReauthRules::parse( $rule ), "$name: $rule" );
        }
    }

    /** RA-07 */
    public function testMatchGroup()
    {
        $s = self::$shipped;
        $this->assertSame( 'system', expReauth::matchGroup( $s, 'setup', 'cache', 'POST', array( 'ClearAllCacheButton' => 1 ) )['group'] );
        $this->assertNull( expReauth::matchGroup( $s, 'content', 'removeobject', 'POST', array( 'ConfirmButton' => 1 ) ), 'content is off' );
        $s['groups']['content']['enabled'] = true;
        $this->assertSame( 'content', expReauth::matchGroup( $s, 'content', 'removeobject', 'POST', array( 'ConfirmButton' => 1 ) )['group'] );
        $s['groups']['bogus'] = array( 'enabled' => true, 'rules' => array( 'user/login', 'user/logout *' ) );
        $this->assertNull( expReauth::matchGroup( $s, 'user', 'login', 'POST', array( 'LoginButton' => 1 ) ) );
        $this->assertNull( expReauth::matchGroup( $s, 'user', 'logout', 'GET', array() ) );
        $s['enabled'] = false;
        $this->assertNull( expReauth::matchGroup( $s, 'setup', 'cache', 'POST', array() ) );
    }

    /** RA-08 */
    public function testShippedRulesCoverTheFourGroups()
    {
        $s = self::$shipped;
        $self = function () { return 'self'; };
        $other = function () { return 'other'; };
        $yes = function () { return true; };
        $cases = array(
            array( 'account', 'user', 'password', 'POST', array( 'OKButton' => 1 ), $self ),
            array( 'account', 'user', 'passkeys', 'GET', array(), null ),
            array( 'account', 'user2fa', 'setup', 'POST', array( 'EnableButton' => 1 ), null ),
            array( 'account', 'apikey', 'list', 'POST', array( 'CreateKeyButton' => 1 ), null ),
            array( 'account', 'content', 'edit', 'POST', array( 'PublishButton' => 1 ), $self, $yes ),
            array( 'users', 'content', 'edit', 'POST', array( 'PublishButton' => 1 ), $other ),
            array( 'users', 'user', 'password', 'POST', array( 'OKButton' => 1 ), $other ),
            array( 'users', 'role', 'assign', 'POST', array( 'AssignButton' => 1 ) ),
            array( 'users', 'role', 'policyedit', 'POST', array( 'StoreButton' => 1 ) ),
            array( 'users', 'user', 'setting', 'POST', array( 'UpdateSettingButton' => 1 ) ),
            array( 'system', 'settings', 'edit', 'POST', array( 'SaveButton' => 1 ) ),
            array( 'system', 'setup', 'extensions', 'POST', array( 'ActivateExtensionsButton' => 1 ) ),
            array( 'system', 'setup', 'cache', 'POST', array( 'ClearAllCacheButton' => 1 ) ),
            array( 'system', 'package', 'upload', 'GET', array() ),
            array( 'system', 'git_manager', 'index', 'POST', array( 'PullButton' => 1 ) ),
            array( 'system', 'setup', 'reauth', 'POST', array( 'SaveReauthButton' => 1 ) ),
            array( 'export', 'infocollector', 'export', 'GET', array() ),
            array( 'export', 'audit', 'export', 'GET', array() ),
            array( 'export', 'newsletter', 'subscriber_export', 'GET', array() ),
        );
        foreach ( $cases as $c )
        {
            $m = expReauth::matchGroup( $s, $c[1], $c[2], $c[3], $c[4], isset( $c[5] ) ? $c[5] : null, isset( $c[6] ) ? $c[6] : null );
            $this->assertSame( $c[0], $m ? $m['group'] : null, "$c[1]/$c[2] $c[3]" );
        }
        // reading pages and one's own profile without a credential change are never asked
        foreach ( array( array( 'content', 'view', 'GET' ), array( 'role', 'view', 'GET' ), array( 'setup', 'info', 'POST' ),
                         array( 'setup', 'logs', 'POST' ), array( 'user', 'edit', 'GET' ), array( 'settings', 'view', 'GET' ) ) as $c )
            $this->assertNull( expReauth::matchGroup( $s, $c[0], $c[1], $c[2], array() ), "$c[0]/$c[1] $c[2]" );
        $no = function () { return false; };
        $this->assertNull( expReauth::matchGroup( $s, 'content', 'edit', 'POST', array( 'PublishButton' => 1 ), $self, $no ),
                           'publishing one\'s own profile without a credential change' );
        // "Your devices": switching off one's own sign-in mail asks (account), signing another user out asks (users);
        // signing out all one's other devices asks too (task 270: a step-up, so a stolen session cannot lock the owner
        // out); signing out one other device does not wait for a question
        $this->assertSame( 'account', expReauth::matchGroup( $s, 'user', 'sessions', 'POST', array( 'SaveMailButton' => 1 ), $self )['group'] );
        $this->assertSame( 'users', expReauth::matchGroup( $s, 'user', 'sessions', 'POST', array( 'RevokeOthersButton' => 1 ), $other )['group'] );
        $this->assertSame( 'account', expReauth::matchGroup( $s, 'user', 'sessions', 'POST', array( 'RevokeOthersButton' => 1 ), $self )['group'] );
        $this->assertNull( expReauth::matchGroup( $s, 'user', 'sessions', 'POST', array( 'RevokeSessionButton' => 3 ), $self ) );
        $this->assertSame( 'users', expReauth::matchGroup( $s, 'user', 'sessionreport', 'POST', array( 'ForceSignOutButton' => 1 ) )['group'] );
        // another user's devices in full (task 273: "full device details") ask before the page opens; one's own list does not
        $this->assertSame( 'users', expReauth::matchGroup( $s, 'user', 'sessions', 'GET', array(), $other )['group'] );
        $this->assertSame( 'users', expReauth::matchGroup( $s, 'user', 'sessionreport', 'GET', array() )['group'] );
        $this->assertNull( expReauth::matchGroup( $s, 'user', 'sessions', 'GET', array(), $self ) );
    }

    /** RA-09 */
    public function testReplay()
    {
        $flat = expReauth::flatten( array( 'a' => '1', 'ezxform_token' => 'T', 'list' => array( 'x', 'k' => array( 'y' ) ), 'n' => null ) );
        $this->assertSame( array( array( 'name' => 'a', 'value' => '1' ), array( 'name' => 'list[0]', 'value' => 'x' ),
                                  array( 'name' => 'list[k][0]', 'value' => 'y' ), array( 'name' => 'n', 'value' => '' ) ), $flat );
        $this->assertSame( array( 'PublishButton' => 1 ), expReauth::withoutOwnFields( array( 'PublishButton' => 1, 'ExpReauthPassword' => 'p',
            'ExpReauthConfirmButton' => 1, 'ExpReauthMethod' => 'password', 'PasskeySignature' => 's', 'ExpReauthReplayGet' => 1 ) ) );
        // the passkey fields of a view's own form (a registration on user/passkeys) stay
        $this->assertSame( array( 'RegisterPasskeyButton' => 1, 'PasskeyClientData' => 'c' ),
                           expReauth::withoutOwnFields( array( 'RegisterPasskeyButton' => 1, 'PasskeyClientData' => 'c' ) ) );
    }

    /** RA-10 */
    public function testSettingsForm()
    {
        $v = expReauthSettings::fromForm( array( 'account', 'content' ), array( 'ReauthEnabled' => '1', 'ReauthMinutes' => '5000',
                                                                               'ReauthGroups' => array( 'content', 'bogus' ) ) );
        $this->assertSame( array( 'enabled' => true, 'minutes' => 1440, 'groups' => array( 'account' => false, 'content' => true ) ), $v );
        $v = expReauthSettings::fromForm( array( 'account' ), array( 'ReauthMinutes' => 'x' ) );
        $this->assertSame( array( 'enabled' => false, 'minutes' => 10, 'groups' => array( 'account' => false ) ), $v );
    }

    /** RA-12 */
    public function testUploads()
    {
        $none = function ( $name ) {
            return array( 'name' => $name, 'type' => '', 'tmp_name' => '', 'error' => UPLOAD_ERR_NO_FILE, 'size' => 0 );
        };
        $chosen = array( 'name' => 'me.png', 'type' => 'image/png', 'tmp_name' => '/php/tmp/phpA1', 'error' => UPLOAD_ERR_OK, 'size' => 120 );
        // no form with files, and a form without file inputs
        $this->assertFalse( expReauth::hasUploads( array() ) );
        // the edit form of a user: its image and another file input, both left empty
        $this->assertFalse( expReauth::hasUploads( array( 'ContentObjectAttribute_data_imagename_4792' => $none( '' ),
                                                          'ContentObjectAttribute_data_binaryfilename_4793' => $none( '' ) ) ) );
        // the same form with the image chosen
        $this->assertTrue( expReauth::hasUploads( array( 'ContentObjectAttribute_data_imagename_4792' => $chosen,
                                                         'ContentObjectAttribute_data_binaryfilename_4793' => $none( '' ) ) ) );
        // a file that was chosen but did not arrive has to be chosen again as well
        $this->assertTrue( expReauth::hasUploads( array( 'f' => array( 'error' => UPLOAD_ERR_INI_SIZE ) + $none( 'big.iso' ) ) ) );
        $this->assertTrue( expReauth::hasUploads( array( 'f' => array( 'error' => UPLOAD_ERR_PARTIAL ) + $none( 'half.pdf' ) ) ) );
        // inputs named as arrays: name[] and name[key][sub], PHP's layout with the arrays below each property
        $this->assertFalse( expReauth::hasUploads( array( 'files' => array(
            'name' => array( '', '' ), 'type' => array( '', '' ), 'tmp_name' => array( '', '' ),
            'error' => array( UPLOAD_ERR_NO_FILE, UPLOAD_ERR_NO_FILE ), 'size' => array( 0, 0 ) ) ) ) );
        $this->assertTrue( expReauth::hasUploads( array( 'files' => array(
            'name' => array( '', 'b.png' ), 'type' => array( '', 'image/png' ), 'tmp_name' => array( '', '/php/tmp/phpB2' ),
            'error' => array( UPLOAD_ERR_NO_FILE, UPLOAD_ERR_OK ), 'size' => array( 0, 99 ) ) ) ) );
        $this->assertFalse( expReauth::hasUploads( array( 'Attr' => array(
            'name' => array( 'a' => array( 'x' => '', 'y' => '' ) ),
            'error' => array( 'a' => array( 'x' => UPLOAD_ERR_NO_FILE, 'y' => UPLOAD_ERR_NO_FILE ) ) ) ) ) );
        $this->assertTrue( expReauth::hasUploads( array( 'Attr' => array(
            'name' => array( 'a' => array( 'x' => '', 'y' => 'deep.txt' ) ),
            'error' => array( 'a' => array( 'x' => UPLOAD_ERR_NO_FILE, 'y' => UPLOAD_ERR_OK ) ) ) ) ) );
        // entries that are no uploads at all are ignored
        $this->assertFalse( expReauth::hasUploads( array( 'odd' => 'text', 'other' => array( 'name' => 'x' ) ) ) );
        // $_FILES itself when no array is given
        $saved = $_FILES;
        try
        {
            $_FILES = array( 'ContentObjectAttribute_data_imagename_4792' => $none( '' ) );
            $this->assertFalse( expReauth::hasUploads() );
            $_FILES['ContentObjectAttribute_data_imagename_4792'] = $chosen;
            $this->assertTrue( expReauth::hasUploads() );
        }
        finally
        {
            $_FILES = $saved;
        }
        // the page's mark of an upload is the form's own field: it never reaches the view
        $this->assertSame( array( 'PublishButton' => 1 ), expReauth::withoutOwnFields( array( 'PublishButton' => 1, 'ExpReauthReplayFiles' => 1 ) ) );
    }

    /** RA-11 */
    public function testAccountApprovals()
    {
        $s = self::$shipped;
        $other = function () { return 'other'; };
        $none = function () { return null; };
        $form = array( 'CollaborationActionCustom' => 'custom', 'CollaborationTypeIdentifier' => 'ezapprove', 'CollaborationItemID' => '7',
                       'Collaboration_ApproveComment' => '' );
        foreach ( array( 'CollaborationAction_Accept', 'CollaborationAction_Deny', 'CollaborationAction_Defer' ) as $button )
        {
            $m = expReauth::matchGroup( $s, 'collaboration', 'action', 'POST', $form + array( $button => 'x' ), $other );
            $this->assertSame( 'users', $m ? $m['group'] : null, "$button of an account approval" );
            $this->assertNull( expReauth::matchGroup( $s, 'collaboration', 'action', 'POST', $form + array( $button => 'x' ), $none ),
                               "$button of a content approval" );
        }
        $this->assertNull( expReauth::matchGroup( $s, 'collaboration', 'action', 'POST', $form + array( 'CollaborationAction_Comment' => 'x' ), $other ),
                           'a comment on an account approval' );
        $this->assertNull( expReauth::matchGroup( $s, 'collaboration', 'item', 'GET', array(), $other ), 'opening the item' );
        // with the content group on, a content approval still does not ask: no rule of it names collaboration/action
        $s['groups']['content']['enabled'] = true;
        $this->assertNull( expReauth::matchGroup( $s, 'collaboration', 'action', 'POST', $form + array( 'CollaborationAction_Accept' => 'x' ), $none ) );
        // the item of the decision, without a database
        $this->assertSame( 7, expReauth::approvalItemID( 'collaboration', 'action', $form ) );
        $this->assertSame( 0, expReauth::approvalItemID( 'collaboration', 'action', array( 'CollaborationItemID' => '7' ) ), 'another collaboration type' );
        $this->assertSame( 0, expReauth::approvalItemID( 'collaboration', 'action', array( 'CollaborationTypeIdentifier' => 'ezapprove', 'CollaborationItemID' => '7 OR 1' ) ) );
        $this->assertSame( 0, expReauth::approvalItemID( 'collaboration', 'action', array( 'CollaborationTypeIdentifier' => 'ezapprove', 'CollaborationItemID' => array( 7 ) ) ) );
        $this->assertSame( 0, expReauth::approvalItemID( 'content', 'action', $form ) );
        $this->assertNull( expReauth::addressedUser( 'collaboration', 'action', array(), array( 'CollaborationActionCustom' => 'custom' ) ),
                           'a collaboration action without an approval item addresses no account' );
    }
}
