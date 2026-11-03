<?php
/**
 * The User tab of the debug bar (kernel/classes/debugbar/expdebugbaruser.php, expDebugBarReport::userHtml(),
 * userChip()) and the user's parts of the content view cache key (eZNodeviewfunctions::userCacheKeys()):
 *   - UC-01 the user's parts as generateViewCacheFile() takes them; each ViewCacheTweak ignore_* leaves its part out;
 *           an interface switch adds uiswitch
 *   - UC-02 the fingerprint: the same parts give the same one, another value or another order another one
 *   - UC-03 the profile as text, with the view's own parts when the page had them
 *   - UC-04 the chip: the profile, and the view's profile after it; per user with pr_user
 *   - UC-05 the tab: every part, listener keys marked, values escaped, the roles with where they come from
 *   - UC-06 the copy button's words are in the script's strings
 *   - UC-08 a logged-in user sees the profile of anonymous visitors, same or different
 *   - UC-09 the preferences: the key part as generateViewCacheFile() made it before; every view mode's preferences
 *           once in the profile, with the user's value or the default; the table in the tab
 *   - UC-07 the profile from the view's key: a part a listener left out is dropped, one it changed takes the key's
 *           value; the view's own parts make the view profile; pr_user makes the profile per user
 *
 * No database: the user is a stand-in object with the methods the view cache reads; the discounts (a query) are
 * left out with the tweak ignore_discountlist.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDebugBarUserTest extends PHPUnit\Framework\TestCase
{
    private $uiSwitch;

    protected function setUp(): void
    {
        $this->uiSwitch = array_key_exists( 'expUISwitchVariantKey', $GLOBALS ) ? array( $GLOBALS['expUISwitchVariantKey'] ) : null;
        unset( $GLOBALS['expUISwitchVariantKey'] );
    }

    protected function tearDown(): void
    {
        if ( $this->uiSwitch === null )
            unset( $GLOBALS['expUISwitchVariantKey'] );
        else
            $GLOBALS['expUISwitchVariantKey'] = $this->uiSwitch[0];
    }

    private function user( array $roles, array $limits )
    {
        return new class( $roles, $limits ) {
            private $roles;
            private $limits;
            public function __construct( $roles, $limits ) { $this->roles = $roles; $this->limits = $limits; }
            public function roleIDList() { return $this->roles; }
            public function limitValueList() { return $this->limits; }
            public function attribute( $name ) { return $name === 'contentobject_id' ? 14 : null; }
        };
    }

    private function profile( array $overrides = array() )
    {
        return $overrides + array(
            'id' => 14, 'login' => 'editor', 'logged_in' => true, 'siteaccess' => 'site', 'session' => true,
            'node_id' => 2, 'tweak' => '', 'per_user' => false,
            'keys' => array( 'userroles' => '1.2.7', 'userlimitedlist' => '/1/2/42/', 'discountlist' => '' ),
            'profile' => expDebugBarUser::fingerprint( array( 'userroles' => '1.2.7', 'userlimitedlist' => '/1/2/42/', 'discountlist' => '' ) ),
            'removed' => array(), 'changed' => array(),
            'view_keys' => array(), 'from_listeners' => array(), 'view_profile' => null,
            'assignments' => array(
                array( 'role_id' => 1, 'role' => 'Anonymous', 'limit_identifier' => '', 'limit_value' => '', 'from_id' => 12, 'from_name' => 'Members', 'direct' => false ),
                array( 'role_id' => 7, 'role' => 'Editor', 'limit_identifier' => 'Subtree', 'limit_value' => '/1/2/42/', 'from_id' => 14, 'from_name' => '', 'direct' => true ),
            ),
        );
    }

    /** UC-01 */
    public function testTheUsersPartsOfTheKey()
    {
        $user = $this->user( array( 1, 2, 7 ), array( '/1/2/42/' ) );
        $this->assertSame( array( 'userroles' => '1.2.7', 'userlimitedlist' => '/1/2/42/' ),
                           eZNodeviewfunctions::userCacheKeys( $user, 'ignore_discountlist' ) );
        $this->assertSame( array( 'userlimitedlist' => '/1/2/42/' ),
                           eZNodeviewfunctions::userCacheKeys( $user, 'ignore_userroles;ignore_discountlist' ) );
        $this->assertSame( array(), eZNodeviewfunctions::userCacheKeys( $user, 'ignore_userroles;ignore_userlimitedlist;ignore_discountlist' ) );
        $this->assertSame( array( 'userroles' => '', 'userlimitedlist' => '' ),
                           eZNodeviewfunctions::userCacheKeys( $this->user( array(), array() ), 'ignore_discountlist' ), 'no roles: empty, still a part' );
        $GLOBALS['expUISwitchVariantKey'] = 'ger-DE';
        $this->assertSame( array( 'userroles' => '1.2.7', 'userlimitedlist' => '/1/2/42/', 'uiswitch' => 'ger-DE' ),
                           eZNodeviewfunctions::userCacheKeys( $user, 'ignore_discountlist' ) );
    }

    /** UC-02 */
    public function testTheFingerprint()
    {
        $a = array( 'userroles' => '1.2.7', 'userlimitedlist' => '' );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{8}$/', expDebugBarUser::fingerprint( $a ) );
        $this->assertSame( expDebugBarUser::fingerprint( $a ), expDebugBarUser::fingerprint( array( 'userroles' => '1.2.7', 'userlimitedlist' => '' ) ) );
        $this->assertNotSame( expDebugBarUser::fingerprint( $a ), expDebugBarUser::fingerprint( array( 'userroles' => '1.2.8', 'userlimitedlist' => '' ) ) );
        $this->assertNotSame( expDebugBarUser::fingerprint( $a ), expDebugBarUser::fingerprint( array( 'userlimitedlist' => '', 'userroles' => '1.2.7' ) ), 'order counts, as in the key' );
        // the name is part of it: the same value under another part is another profile
        $this->assertNotSame( expDebugBarUser::fingerprint( array( 'userroles' => '5' ) ), expDebugBarUser::fingerprint( array( 'discountlist' => '5' ) ) );
        $this->assertSame( expDebugBarUser::fingerprint( array( 'x' => '' ) ), expDebugBarUser::fingerprint( array( 'x' => null ) ), 'null hashes as the empty string' );
    }

    /** UC-03 */
    public function testTheProfileAsText()
    {
        $text = expDebugBarUser::text( $this->profile() );
        $this->assertStringStartsWith( "user_id=14\nsiteaccess=site\nuserroles=1.2.7\n", $text );
        $this->assertStringContainsString( "profile=" . $this->profile()['profile'] . "\n", $text );
        $this->assertStringNotContainsString( 'view_profile', $text );
        $text = expDebugBarUser::text( $this->profile( array( 'tweak' => 'ignore_discountlist', 'view_keys' => array( 'inr5' => 'BY126' ), 'view_profile' => 'abcd1234' ) ) );
        $this->assertStringContainsString( "viewcachetweaks=ignore_discountlist\n", $text );
        $this->assertStringContainsString( "view=node 2\ninr5=BY126\nview_profile=abcd1234\n", $text );
    }

    /** UC-04 */
    public function testTheChip()
    {
        $chip = expDebugBarReport::userChip( $this->profile() );
        $this->assertSame( 'user', $chip[0], 'opens the User tab' );
        $this->assertSame( $this->profile()['login'] . ' · VC ' . $this->profile()['profile'], $chip[3], 'the login, then the profile' );
        $this->assertSame( $this->profile()['login'], $chip['rows']['Login'] );
        $login = expDebugBarReport::loginChip( array( 'login' => 'editor', 'id' => 14 ) );
        $this->assertSame( array( 'messages', 'userlogin', 'ok', 'editor' ), array_slice( $login, 0, 4 ), 'without the User tab: the login alone' );
        $chip = expDebugBarReport::userChip( $this->profile( array( 'view_profile' => 'abcd1234', 'per_user' => true ) ) );
        $this->assertStringEndsWith( ' · abcd1234 · pr_user', $chip[3] );
        $this->assertContains( 'per user (pr_user)', $chip['rows'] );
    }

    /** UC-05 */
    public function testTheTab()
    {
        $html = expDebugBarReport::userHtml( $this->profile( array(
            'login' => 'ed<i>tor',
            'view_keys' => array( 'userpreferences' => 'p:admin_list_limit=2;', 'inr5' => 'BY126' ),
            'from_listeners' => array( 'inr5' ), 'view_profile' => 'abcd1234' ) ) );
        $this->assertStringContainsString( 'ed&lt;i&gt;tor', $html, 'escaped' );
        $this->assertStringNotContainsString( '<i>', $html );
        $this->assertStringContainsString( '<code>' . $this->profile()['profile'] . '</code>', $html );
        $this->assertStringContainsString( '<code>abcd1234</code>', $html );
        $this->assertStringContainsString( '<th>userroles</th><td>1.2.7</td>', $html );
        $this->assertStringContainsString( '<th>discountlist</th><td><em>empty</em></td>', $html );
        $this->assertMatchesRegularExpression( '#<th>inr5 <span class="exp-debug-badge"[^>]*>listener</span></th>#', $html );
        $this->assertMatchesRegularExpression( '#<th>userpreferences <span class="exp-debug-badge">this view</span></th>#', $html );
        $this->assertStringContainsString( "data-exp-debug-copy=\"user_id=14\nsiteaccess=site\n", $html, 'the text to copy, line breaks kept' );
        $this->assertStringContainsString( '<td>Subtree /1/2/42/</td><td>the user</td>', $html );
        $this->assertStringContainsString( 'group Members (object 12)', $html );
        $this->assertMatchesRegularExpression( '#<td><a href="[^"]*role/view/7">7</a></td><td>Editor</td>#', $html, 'the role id links to role/view' );
        $html = expDebugBarReport::userHtml( $this->profile( array( 'keys' => array(), 'assignments' => array(), 'tweak' => 'ignore_userroles;ignore_userlimitedlist;ignore_discountlist' ) ) );
        $this->assertStringContainsString( 'No part depends on the user', $html );
        $this->assertStringContainsString( 'No role assignments could be read.', $html );
        $this->assertStringContainsString( 'ViewCacheTweaks in effect: ignore_userroles;ignore_userlimitedlist;ignore_discountlist', $html );
    }

    /** UC-06 */
    public function testTheCopyButtonsWordsAreInTheScriptsStrings()
    {
        foreach ( array( 'Copied', 'Could not copy.' ) as $word )
            $this->assertContains( $word, expDebugBarReport::scriptStrings() );
    }

    /** UC-07 */
    public function testTheProfileFromTheViewsKey()
    {
        $keys = array( 'userroles' => '1.2.7', 'userlimitedlist' => '', 'discountlist' => '' );
        $plain = expDebugBarUser::profile( $keys );
        $this->assertSame( $keys, $plain['keys'] );
        $this->assertSame( expDebugBarUser::fingerprint( $keys ), $plain['profile'] );
        $this->assertNull( $plain['view_profile'] );

        // a listener left userroles out and added inr5; the view has preferences of its own
        $view = array( 'parts' => array( 'node_id' => 2, 'viewmode' => 'full', 'userlimitedlist' => '', 'discountlist' => '',
                                         'access_path' => '', 'userpreferences' => 'p:x=1;', 'inr5' => 'BY126' ),
                       'from_listeners' => array( 'inr5' ) );
        $p = expDebugBarUser::profile( $keys, $view );
        $this->assertSame( array( 'userroles' ), $p['removed'] );
        $this->assertSame( array( 'userlimitedlist' => '', 'discountlist' => '' ), $p['keys'] );
        $this->assertSame( expDebugBarUser::fingerprint( array( 'userlimitedlist' => '', 'discountlist' => '' ) ), $p['profile'],
                           'another user with other roles shares the files: the same profile' );
        $this->assertSame( array( 'inr5' => 'BY126' ), $p['view_keys'], 'the view\'s preferences are in the profile, not here' );
        $this->assertSame( array( 'inr5' ), $p['from_listeners'] );
        $this->assertSame( expDebugBarUser::fingerprint( $p['view_keys'] ), $p['view_profile'] );

        // a listener changed userroles: the key's value counts, marked
        $view = array( 'parts' => array( 'node_id' => 2, 'userroles' => '1.2', 'userlimitedlist' => '', 'discountlist' => '' ),
                       'from_listeners' => array( 'userroles' ) );
        $p = expDebugBarUser::profile( $keys, $view );
        $this->assertSame( '1.2', $p['keys']['userroles'] );
        $this->assertSame( array( 'userroles' ), $p['changed'] );
        $this->assertSame( array(), $p['view_keys'], 'a user part is not counted twice' );

        // pr_user: the user id is in the file name, so in the profile
        $this->assertNotSame( expDebugBarUser::profile( $keys, null, 14 )['profile'], expDebugBarUser::profile( $keys, null, 15 )['profile'] );
        $this->assertNotSame( $plain['profile'], expDebugBarUser::profile( $keys, null, 14 )['profile'] );

        // the tab and the text show it
        $u = $this->profile( array( 'per_user' => true, 'removed' => array( 'userroles' ), 'changed' => array( 'userlimitedlist' ),
                                    'keys' => array( 'userlimitedlist' => '/1/2/', 'discountlist' => '' ) ) );
        $html = expDebugBarReport::userHtml( $u );
        $this->assertMatchesRegularExpression( '#<th>userroles <span class="exp-debug-badge"[^>]*>listener</span></th><td><em>left out by a listener</em></td>#', $html );
        $this->assertMatchesRegularExpression( '#<th>userlimitedlist <span class="exp-debug-badge"[^>]*>listener</span></th><td>/1/2/</td>#', $html );
        $this->assertStringContainsString( '<th>pr_user</th><td>14</td>', $html );
        $this->assertStringEndsWith( ' · pr_user', expDebugBarReport::userChip( $u )[3] );
        $text = expDebugBarUser::text( $u );
        $this->assertStringContainsString( "pr_user=14\nuserroles=(left out by a listener)\n", $text );
    }

    /** UC-08 */
    public function testTheProfileOfAnonymousVisitors()
    {
        $same = expDebugBarReport::userHtml( $this->profile( array( 'anonymous_profile' => $this->profile()['profile'] ) ) );
        $this->assertStringContainsString( 'Anonymous visitors <code>' . $this->profile()['profile'] . '</code>', $same );
        $this->assertStringContainsString( 'data-level="ok">same as yours</span>', $same );
        $other = expDebugBarReport::userHtml( $this->profile( array( 'anonymous_profile' => '0000aaaa' ) ) );
        $this->assertStringContainsString( 'data-level="warn">different</span>', $other );
        // the anonymous visitor themselves, or no anonymous user to read: no comparison
        $this->assertStringNotContainsString( 'Anonymous visitors', expDebugBarReport::userHtml( $this->profile( array( 'anonymous_profile' => null ) ) ) );
        $this->assertStringNotContainsString( 'Anonymous visitors', expDebugBarReport::userHtml( $this->profile() ) );
    }

    /** The part userpreferences as generateViewCacheFile() made it inline before userPreferencesKey(). */
    private function oldPreferencesKey( $list, array $preferences )
    {
        $pString = '';
        foreach ( explode( ';', $list ) as $pref )
        {
            $pref = explode( '=', $pref );
            if ( isset( $pref[0] ) )
            {
                if ( isset( $preferences[$pref[0]] ) )
                    $pString .= 'p:' . $pref[0] . '='. $preferences[$pref[0]]. ';';
                else if ( isset( $pref[1] ) )
                    $pString .= 'p:' . $pref[0] . '='. $pref[1]. ';';
            }
        }
        return $pString;
    }

    /** UC-09 */
    public function testThePreferences()
    {
        $values = array( 'admin_list_limit' => '2', 'admin_navigation_content' => '0' );
        foreach ( array( 'admin_navigation_content=1;admin_children_viewmode=list;admin_list_limit=1;', 'a;b=1', '', 'x=1=2', ';;' ) as $list )
        {
            foreach ( array( $values, array() ) as $v )
                $this->assertSame( $this->oldPreferencesKey( $list, $v ), eZNodeviewfunctions::userPreferencesKey( $list, $v ), $list );
        }

        $p = expDebugBarUser::preferences( array( 'sitemap' => 'user_sitemap_max_depth=5', 'line' => '',
                                                  'full' => 'admin_navigation_content=1;admin_children_viewmode=list;admin_list_limit=1;' ), $values );
        $this->assertSame( 'full=p:admin_navigation_content=0;p:admin_children_viewmode=list;p:admin_list_limit=2;|sitemap=p:user_sitemap_max_depth=5;', $p['key'],
                           'each view mode as its key takes it, by name of the view mode' );
        $this->assertCount( 4, $p['rows'] );
        $this->assertSame( array( 'view_mode' => 'full', 'name' => 'admin_list_limit', 'value' => '2', 'default' => '1', 'set' => true ), $p['rows'][2] );
        $this->assertSame( array( 'view_mode' => 'sitemap', 'name' => 'user_sitemap_max_depth', 'value' => null, 'default' => '5', 'set' => false ), $p['rows'][3] );
        $this->assertSame( array( 'rows' => array(), 'key' => '' ), expDebugBarUser::preferences( array(), $values ) );

        // one name, two defaults: unset and set to the first default are different parts in the second view mode
        $modes = array( 'full' => 'limit=1', 'line' => 'limit=2' );
        $this->assertNotSame( expDebugBarUser::preferences( $modes, array() )['key'], expDebugBarUser::preferences( $modes, array( 'limit' => '1' ) )['key'] );

        // another list limit: another part, so another profile
        $other = expDebugBarUser::preferences( array( 'full' => 'admin_list_limit=1' ), array( 'admin_list_limit' => '3' ) );
        $this->assertNotSame( $p['key'], $other['key'] );

        // the profile keeps them when the view's key has its own mode's preferences only (or none)
        $p2 = expDebugBarUser::profile( array( 'userroles' => '1', 'userpreferences' => $p['key'] ),
                                        array( 'parts' => array( 'node_id' => 2, 'userroles' => '1' ), 'from_listeners' => array() ) );
        $this->assertSame( array(), $p2['removed'] );
        $this->assertSame( $p['key'], $p2['keys']['userpreferences'] );

        $html = expDebugBarReport::userHtml( $this->profile( array( 'preferences' => $p['rows'] ) ) );
        $this->assertStringContainsString( '<tr><td>full</td><td>admin_list_limit</td><td>2</td><td>1</td></tr>', $html );
        $this->assertStringContainsString( '<tr><td>sitemap</td><td>user_sitemap_max_depth</td><td><em>not set</em></td><td>5</td></tr>', $html );
        $this->assertStringContainsString( 'No view mode depends on user preferences', expDebugBarReport::userHtml( $this->profile() ) );
    }
}
