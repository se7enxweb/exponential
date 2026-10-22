<?php
/**
 * admin4's scripts where a choice is saved, a request fails or a result is pressed, run in node against small
 * stand-ins for the browser (tests/tests/design/js/*.js; each test is skipped without node):
 *
 *  - remembered choices (window.a4Pref, a4_head.js; admin4.js): a value already stored is not sent again,
 *    so the left menu's groups write nothing on a page view; the color mode reaches the other open tabs; a width the
 *    server did not get comes back from the browser; the sign-in button gets its label back after back/forward;
 *  - the left column's grip (ezadmin_menubar.js) saves through window.a4Pref, and only a changed width;
 *  - the mega menu's load is given up after 10 s (AdminMegaMenuTest);
 *  - the relation search (ezajaxrelations_jquery.js): a result is added once, reached by Tab and pressed with Space,
 *    and an error (the server's text or a failed request) is a toast;
 *  - the sub-items table's options (ezajaxsubitems_expdatatable.js): a preset, the rows per page or the columns that
 *    were not saved are said, and the presets go back to what is stored;
 *  - the upload dialog (expajaxuploader.js) shows an error for an empty answer instead of throwing;
 *  - the context menus (ezpopupmenu.js): every placeholder is filled, a menu opened without a disable list disables
 *    nothing, and a click on a disabled item or a submenu opener does not follow its "#";
 *  - the content tree (contentstructuremenu_dynamic.js): the open/close button has no inline handler, a node on the
 *    path without sub items is marked, the cookie is encoded (an old one still reads);
 *  - the node tabs (node_tabs.js): the cookie's value is no selector unless it is a tab name.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminScriptsBehaviourTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminScriptsBehaviourTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    /** The harness's JSON for the given files (paths from the root); skipped without node */
    private function harness( $script, array $files )
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        $command = escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/' . $script );
        foreach ( $files as $file )
            $command .= ' ' . escapeshellarg( self::root() . '/' . $file );
        $out = (string)shell_exec( $command . ' 2>&1' );
        $result = json_decode( $out, true );
        $this->assertIsArray( $result, 'the harness ran: ' . $out );
        return $result;
    }

    public function testOnlyAChangedChoiceIsSaved()
    {
        $s = $this->harness( 'preferences.js', array( 'design/admin4/javascript/admin4.js', 'design/admin4/javascript/a4_head.js' ) );
        // a value already on <html> is not sent again; a new one once
        $this->assertSame( array( 'admin_x=2' ), $s['unchanged is not sent']['fetches'] );
        // the left menu's groups: closing the stored-closed groups on load saved them again on every page view
        $this->assertSame( array(), $s['groups']['onLoad'] );
        $this->assertSame( array( false, false, true ), $s['groups']['closedOnLoad'] );
        $this->assertSame( array( 'admin_navgroups=a,b,c' ), $s['groups']['afterClose'] );
        $this->assertSame( array( 'admin_navgroups=a,b,c', 'admin_navgroups=b,c' ), $s['groups']['afterOpen'] );
        $this->assertSame( array( 'open' => true, 'fetches' => array() ), $s['current group'] );
    }

    public function testTheColorModeReachesTheOtherTabs()
    {
        $s = $this->harness( 'preferences.js', array( 'design/admin4/javascript/admin4.js', 'design/admin4/javascript/a4_head.js' ) );
        // shared: written to the browser once saved; without share nothing is kept in the browser
        $this->assertSame( array( 'key-x' => 'a' ), $s['shared when saved']['store'] );
        $this->assertSame( array( 'admin_color_mode=dark' ), $s['color mode reaches the other tabs']['fetches'] );
        $this->assertSame( array( 'exp-admin4-theme' => 'dark' ), $s['color mode reaches the other tabs']['store'] );
        $this->assertSame( 'dark', $s['color mode reaches the other tabs']['otherTheme'] );
    }

    public function testTheLeftColumnWidthIsSavedOnlyWhenItChanged()
    {
        $s = $this->harness( 'left_resize.js', array( 'design/admin4/javascript/ezadmin_menubar.js' ) );
        $this->assertSame( array(), $s['a click on the grip saves nothing'] );
        $this->assertSame( array( 'admin_left_menu_size=350px' ), $s['a drag saves its width'] );
        $this->assertSame( array(), $s['a drag back to where it was saves nothing'] );
        $this->assertSame( array( 'admin_left_menu_size=316px', 'admin_left_menu_size=300px' ), $s['a key saves once per change'] );
        $this->assertSame( array( 'admin_left_menu_size=224px' ), $s['Home at the narrowest saves once'] );
        // a width the server did not get (window.a4Pref kept it in the browser) is drawn, unless a preset or the
        // preference says otherwise
        $p = $this->harness( 'preferences.js', array( 'design/admin4/javascript/admin4.js', 'design/admin4/javascript/a4_head.js' ) );
        $this->assertSame( '300px', $p['left width from the browser'] );
        $this->assertNull( $p['left width: a preset wins'] );
        $this->assertSame( '280px', $p['left width: the preference wins'] );
        $this->assertStringNotContainsString( '$.post(', self::read( 'design/admin4/javascript/ezadmin_menubar.js' ) );
    }

    public function testTheSignInButtonGetsItsLabelBack()
    {
        $s = $this->harness( 'preferences.js', array( 'design/admin4/javascript/admin4.js', 'design/admin4/javascript/a4_head.js' ) );
        $this->assertSame( array( 'busy' => "Signing in\u{2026}", 'back' => 'Sign in', 'ariaBusy' => null ), $s['sign-in label'] );
    }

    public function testARelationSearchResultIsAddedOnceAndReachedByTheKeyboard()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $s = $this->harness( 'relations.js', array( "design/$design/javascript/ezajaxrelations_jquery.js" ) );
            $this->assertSame( array( '<a href="#" role="button"></a>' ), $s['a result is a link Tab reaches']['resultMarkup'], $design );
            // the second click on the same result added the object as a second relation row
            $this->assertSame( 1, $s['a second click adds nothing']['clones'], $design );
            $this->assertSame( 1, $s['a second click adds nothing']['idsSet'], $design );
            $this->assertSame( 'true', $s['a second click adds nothing']['disabled'], $design );
            $this->assertTrue( $s['space presses a result']['spacePrevented'], $design );
            $this->assertSame( 1, $s['space presses a result']['idsSet'], $design );
            // errors: a toast, alert() only without one; a failed request is no longer silent
            $this->assertSame( array( 'bad: Search is off' ), $s['a server error text is a toast']['toasts'], $design );
            $this->assertSame( array(), $s['a server error text is a toast']['alerts'], $design );
            $this->assertSame( array( 'Search is off' ), $s['without the toast an alert']['alerts'], $design );
            $this->assertSame( array( 'bad: HTTP 500' ), $s['a failed request says so']['toasts'], $design );
            $this->assertSame( array( 'bad: The search failed.' ), $s['a failed request with the page text']['toasts'], $design );
        }
    }

    public function testSubItemsTableOptionsThatWereNotSavedAreSaid()
    {
        $s = $this->harness( 'subitems_save.js', array( 'design/admin4/javascript/ezajaxsubitems_expdatatable.js' ) );
        $saved = $s['a saved preset is kept'];
        $this->assertCount( 2, $saved['presets'] );
        $this->assertSame( $saved['presets'][1], $saved['current'] );
        $this->assertSame( array(), $saved['toasts'] );
        // not saved: the new preset is gone again, the choice as before, and a toast says why
        $lost = $s['a preset not saved is taken back and said'];
        $this->assertSame( array( 'mine' ), $lost['presets'] );
        $this->assertNull( $lost['current'] );
        $this->assertSame( array( 'bad: You are no longer signed in. (expsubitems::savepreference)' ), $lost['toasts'] );
        $this->assertSame( 'resolved', $lost['resolved'], 'the table draws the presets again from what is stored' );
        $this->assertSame( array( 'bad: Not saved: You are no longer signed in. (expsubitems::savepreference)' ),
                           $s['a preset not saved, with the template text']['toasts'] );
        $this->assertSame( array( 'mine' ), $s['a preset not deleted stays and is said']['presets'] );
        $this->assertCount( 1, $s['a preset not deleted stays and is said']['toasts'] );
        $this->assertSame( array(), $s['a deleted preset goes']['presets'] );
        $this->assertSame( array( 'bad: You are no longer signed in. (admin_list_limit)' ), $s['rows per page not saved is said']['toasts'] );
        $this->assertSame( 0, $s['columns not saved are said']['savedBeforeDelay'] );
        $this->assertCount( 1, $s['columns not saved are said']['toasts'] );
        $this->assertSame( array(), $s['columns saved say nothing']['toasts'] );
        $this->assertFileEquals( self::root() . '/design/admin4/javascript/ezajaxsubitems_expdatatable.js',
                                 self::root() . '/design/admin/javascript/ezajaxsubitems_expdatatable.js' );
    }

    public function testTheUploadDialogShowsAnEmptyAnswerAsAnError()
    {
        $s = $this->harness( 'uploader.js', array( 'design/admin4/javascript/expajaxuploader.js' ) );
        $this->assertSame( array( 'content: <form></form>' ), $s['a step']['shown'] );
        foreach ( array( 'an empty answer', 'an answer without html' ) as $name )
        {
            $this->assertSame( array( 'error: Unable to parse the JSON response.' ), $s[$name]['shown'], $name );
            $this->assertNull( $s[$name]['thrown'], $name );
            $this->assertFalse( $s[$name]['meta'], $name );
        }
    }

    public function testTheContextMenuFillsEveryPlaceholderAndKeepsDisabledItemsStill()
    {
        $s = $this->harness( 'popup_substitute.js', array( 'design/admin4/javascript/popupmenu/ezpopupmenu.js' ) );
        // every occurrence, and a $& in the template stays as it is
        $this->assertSame( '/content/view/full/7/7/$&', $s['href'] );
        $this->assertSame( '/a/8/b/8', $s['redirect'] );
        $this->assertSame( 'NodeID=8&Pair=8-8&Custom=x$&y -> /content/action', $s['form'] );
        $this->assertSame( array( '%nodeID%', '%nodeID%-%nodeID%', '%extra%' ), $s['formRestored'] );
        // the disable list belongs to the menu it was given with
        $this->assertSame( 'menu-item-disabled', $s['editDisabled'] );
        $this->assertSame( '', $s['otherDisabled'] );
        // a disabled item and a submenu opener do not follow "#"; an item and a link outside the menus do
        $this->assertTrue( $s['clickDisabled'] );
        $this->assertTrue( $s['clickMore'] );
        $this->assertFalse( $s['clickItem'] );
        $this->assertFalse( $s['clickOutside'] );
    }

    public function testTheTreeToggleHasNoInlineHandlerAndItsCookieIsEncoded()
    {
        $s = $this->harness( 'tree_toggle.js', array( 'design/admin4/javascript/contentstructuremenu_dynamic.js' ) );
        $entry = $s['storage']['withChildren'];
        $this->assertStringContainsString( 'id="a5" href="#" role="button"', $entry );
        $this->assertStringContainsString( 'data-tree-load="5" data-tree-modified="1700000000"', $entry );
        $this->assertStringNotContainsString( 'onclick', $entry );
        $this->assertStringNotContainsString( 'onkeydown', $entry );
        $this->assertStringNotContainsString( 'treeMenu', $entry );
        // a click and Space load the node's sub items through the tree's own listeners; a button of another tree not
        $this->assertTrue( $s['storage']['clickPrevented'] );
        $this->assertSame( array( array( 'a5', 5, 1700000000 ), array( 'a5', 5, 1700000000 ) ), $s['storage']['loads'] );
        // a node on the path without sub items in the tree is marked (a number compared with strings never matched)
        $this->assertTrue( $s['storage']['onPathWithout'] );
        $this->assertFalse( $s['storage']['offPathWithout'] );
        // the cookie (localStorage blocked): encoded, same site, secure over https; an old escape()d one reads
        $this->assertStringStartsWith( 'contentStructureMenu=7%2F8;', $s['cookie']['cookieWritten'] );
        $this->assertStringEndsWith( '; path=/; SameSite=Lax; Secure', $s['cookie']['cookieWritten'] );
        $this->assertSame( array( '2', '5', '43', 7, 8 ), $s['oldCookie']['openFromCookie'] );
        $js = self::read( 'design/admin4/javascript/contentstructuremenu_dynamic.js' );
        // no call of escape() or unescape() (a comment may name them)
        $this->assertDoesNotMatchRegularExpression( '/\b(un)?escape\(\s*[^)\s]/', $js );
        // this.collapse() stays: a public method of the tree that an extension may call (the merge with main kept it)
        $this->assertStringNotContainsString( '_delCookie', $js );
    }

    public function testTheTabCookieIsNoSelector()
    {
        $s = $this->harness( 'node_tabs.js', array( 'design/admin4/javascript/node_tabs.js' ) );
        $this->assertNull( $s['a tab name opens its tab']['error'] );
        $this->assertSame( 'node-tab-details', $s['a tab name opens its tab']['selected'] );
        $this->assertSame( 'node-tab-details-content', $s['a tab name opens its tab']['panel'], 'the panel of the tab block is shown' );
        $this->assertSame( 'tablist', $s['a tab name opens its tab']['tablist'] );
        // a selector would throw on it
        $this->assertNull( $s['a broken value is not a selector']['error'] );
        $this->assertSame( 'node-tab-view', $s['a broken value is not a selector']['selected'] );
        $this->assertSame( 'node-tab-view', $s['a cookie of another name is not read']['selected'] );
        $this->assertStringContainsString( "document.querySelector('div.tab-block ul.tabs.tabs-by-cookie #node-tab-' + openTab)",
                                           self::read( 'design/admin4/javascript/node_tabs.js' ) );
        $this->assertCount( 1, $s['opening a tab writes the cookie']['written'] );
        $this->assertStringEndsWith( '; path=/; SameSite=Lax; Secure', $s['opening a tab writes the cookie']['written'][0] );
        $this->assertSame( array( 'admin_navigation_content=0' ), $s['the switch saves through a4Pref']['saved'] );
        // without a4Pref the legacy post carries the token URL-encoded (a + or & in it must not split or change the field),
        // a space as + as jQuery's $.post() sent it
        $this->assertSame( array( 'post /user/preferences/set_and_exit/admin_navigation_content/0 ezxform_token=a%2Bb%26c%3Dd%25e+f' ),
                           $s['the token is URL-encoded in the legacy post']['saved'] );
    }

    public function testTheThemeStylesheetIsNotNamedByAPreference()
    {
        $head = self::read( 'design/admin4/templates/page_head_style.tpl' );
        $this->assertStringNotContainsString( '$admin_theme', $head );
        $this->assertSame( 2, substr_count( $head, "'theme/rounded.css'," ) );
        $this->assertFileExists( self::root() . '/design/admin4/stylesheets/theme/rounded.css' );
    }

    public function testPageScriptsReadEventKey()
    {
        // the scripts of the RSS export and RAD pages (page scripts since the Content Security Policy wave)
        foreach ( array( 'design/admin4/javascript/page/rss_edit_export.js' => "event.key !== 'Enter' || event.isComposing",
                         'design/admin4/javascript/page/setup_rad.js' => "event.key === 'Escape'" ) as $path => $check )
        {
            $tpl = self::read( $path );
            $this->assertStringContainsString( $check, $tpl, $path );
            $this->assertDoesNotMatchRegularExpression( '/\.keyCode\s*[!=]==?\s*\d/', $tpl, $path );
        }
    }

    public function testTheSubItemsListSaysOnceWhenItsScriptsAreMissing()
    {
        // one note where the list cannot be drawn: the one of AdminSubitemsWithoutExpUITest, drawn by the page script
        // (the merge of this review with main kept that one; a second note of its own was dropped)
        $tpl = self::read( 'design/admin4/templates/children_detailed.tpl' );
        $this->assertStringNotContainsString( '<script', $tpl );
        $this->assertStringNotContainsString( 'the scripts of the list (Exponential UI) are not loaded', $tpl );
        $js = self::read( 'design/admin4/javascript/page/children_detailed.js' );
        $this->assertSame( 1, substr_count( $js, "note.className = 'a4-notice is-warn exp-no-subitems-table';" ) );
        // a sub-items option the server did not save says so (TABLE_OPTIONS.save_failed)
        $this->assertStringContainsString( "'save_failed', 'Not saved: %error'|i18n( 'design/admin/node/view/full' )", $tpl );
    }
}
