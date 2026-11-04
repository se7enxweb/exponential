<?php
/**
 * Dashboard > Recent content (the module moderation) without a database: the filters, the page sizes and the paging, the
 * conditions and the order of the query, the status of a row, the submission from the audit, the actions and their
 * form, the module, its settings and its templates.
 *
 *  MO-1  filters(): dates (and their order), choices that fall back to their default, the page size from the allowed
 *        ones, the offset at the first row of a page
 *  MO-2  viewParameters(): only what differs from the default; filters() reads back what it writes
 *  MO-3  where(): one condition per filter on ezcontentobject only, IN and NOT IN for filters on other tables, null
 *        when nothing can match
 *  MO-4  order(): the chosen indexed column, then the id; sortLinks() reverse the current column
 *  MO-5  the page sizes: 25, 100, 300, 700, 1000 shipped, the remembered choice, never above 1000
 *  MO-6  status() and submission()
 *  MO-7  the actions: their form, destructive ones, the job threshold, the counts of a report, the address to go back to
 *  MO-8  the module: views, policies, the fetch function; the settings: the module, the menu link, the dashboard block,
 *        "Verify it's you" for the destructive confirmation, the job type, the editors' policies
 *  MO-9  the templates are the same in design/admin and design/admin4, and so is the page script
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/moderation/expModerationTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

// a checkout whose class map was built before the classes existed
foreach ( array( 'expModerationList' => 'kernel/classes/moderation/expmoderationlist.php', 'expModerationAction' => 'kernel/classes/moderation/expmoderationaction.php',
                 'expModerationAdmin' => 'kernel/classes/moderation/expmoderationadmin.php', 'expReviewHoldAuto' => 'kernel/classes/expreviewholdauto.php' ) as $class => $file )
    if ( !class_exists( $class ) )
        require_once dirname( __DIR__, 5 ) . '/' . $file;
if ( !class_exists( 'Exponential\\View\\Kernel\\Moderation\\Recent' ) )
    require_once dirname( __DIR__, 5 ) . '/kernel/private/classes/views/moderation/recent.php';
if ( !class_exists( 'Exponential\\View\\Kernel\\Moderation\\Action' ) )
    require_once dirname( __DIR__, 5 ) . '/kernel/private/classes/views/moderation/action.php';

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expModerationTest extends PHPUnit\Framework\TestCase
{
    const SIZES = array( 25, 100, 300, 700, 1000 );

    protected function tearDown(): void
    {
        expModerationList::$settings = null;
        parent::tearDown();
    }

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function f( array $in = array() )
    {
        return expModerationList::filters( $in, self::SIZES, 25 );
    }

    /** MO-1 */
    public function testFilters()
    {
        $f = self::f();
        $this->assertSame( '', $f['from'] );
        $this->assertSame( 'modified', $f['date'] );
        $this->assertSame( 'modified', $f['sort'], 'sorted by the date filtered on' );
        $this->assertSame( 'desc', $f['order'] );
        $this->assertSame( 25, $f['limit'] );
        $this->assertSame( 0, $f['offset'] );

        $f = self::f( array( 'from' => '2026-10-31', 'to' => '2026-10-01', 'date' => 'published', 'class' => 'comment', 'status' => 'pending',
                             'hidden' => 'no', 'held' => 'yes', 'section' => '3', 'siteaccess' => 'eng', 'creator' => '14', 'order' => 'ASC',
                             'limit' => '300', 'offset' => '650' ) );
        $this->assertSame( array( '2026-10-01', '2026-10-31' ), array( $f['from'], $f['to'] ), 'a range given backwards is turned round' );
        $this->assertSame( mktime( 0, 0, 0, 10, 1, 2026 ), $f['from_ts'] );
        $this->assertSame( mktime( 23, 59, 59, 10, 31, 2026 ), $f['to_ts'] );
        $this->assertSame( 'published', $f['sort'] );
        $this->assertSame( 'asc', $f['order'] );
        $this->assertSame( array( 'comment', 'pending', 'no', 'yes', 3, 'eng', 14 ),
                           array( $f['class'], $f['status'], $f['hidden'], $f['held'], $f['section'], $f['siteaccess'], $f['creator'] ) );
        $this->assertSame( 300, $f['limit'] );
        $this->assertSame( 600, $f['offset'], 'the first row of the page it falls in' );

        $f = self::f( array( 'from' => '2026-02-30', 'date' => 'x', 'class' => 'a b', 'status' => 'gone', 'hidden' => 'maybe', 'section' => '-1',
                             'siteaccess' => '../x', 'creator' => 'admin', 'sort' => 'password', 'limit' => '5000', 'offset' => 'x' ) );
        $this->assertSame( array( '', 'modified', '', '', '', 0, '', 0, 'modified', 25, 0 ),
                           array( $f['from'], $f['date'], $f['class'], $f['status'], $f['hidden'], $f['section'], $f['siteaccess'], $f['creator'],
                                  $f['sort'], $f['limit'], $f['offset'] ), 'anything else falls back to the default' );
        $this->assertSame( 0, expModerationList::parseDate( '31.10.2026' ) );
        $this->assertSame( 0, self::f( array( 'from' => array( '2026-01-01' ) ) )['from_ts'], 'no arrays' );
    }

    /** MO-2 */
    public function testViewParameters()
    {
        $this->assertSame( '', expModerationList::viewParameters( self::f() ) );
        $f = self::f( array( 'from' => '2026-10-01', 'class' => 'comment', 'held' => 'yes', 'creator' => '14', 'sort' => 'name', 'order' => 'asc', 'offset' => '50', 'limit' => '25' ) );
        $suffix = expModerationList::viewParameters( $f );
        $this->assertSame( '/(from)/2026-10-01/(class)/comment/(held)/yes/(creator)/14/(sort)/name/(order)/asc', $suffix );
        $this->assertSame( $suffix . '/(offset)/50', expModerationList::viewParameters( $f, array(), true ) );
        $this->assertSame( '/(from)/2026-10-01/(class)/comment/(creator)/14/(sort)/name/(order)/asc', expModerationList::viewParameters( $f, array( 'held' => '' ) ) );
        // read back
        preg_match_all( '#/\(([a-z]+)\)/([^/]+)#', $suffix, $m );
        $again = self::f( array_combine( $m[1], array_map( 'rawurldecode', $m[2] ) ) + array( 'offset' => '50' ) );
        $this->assertSame( $f, $again );
    }

    /** MO-3 */
    public function testWhere()
    {
        $this->assertSame( array(), expModerationList::where( self::f(), array() ) );
        $f = self::f( array( 'from' => '2026-10-01', 'to' => '2026-10-02', 'class' => 'comment', 'status' => 'draft', 'section' => '3', 'creator' => '14' ) );
        $where = expModerationList::where( $f, array( 'class_id' => 13, 'in' => array( array( 5, 6, 6 ) ), 'not_in' => array( array( 9 ), array() ) ) );
        $this->assertSame( array( 'modified >= ' . $f['from_ts'], 'modified <= ' . $f['to_ts'], 'contentclass_id = 13', 'status = 0', 'section_id = 3',
                                  'owner_id = 14', 'id IN (5, 6)', 'id NOT IN (9)' ), $where );
        foreach ( $where as $condition )
            $this->assertDoesNotMatchRegularExpression( '/\b(JOIN|SELECT|ezcontentobject_tree|ezcobj)\b/i', $condition, 'one table only (MongoDB)' );
        $this->assertNull( expModerationList::where( self::f( array( 'class' => 'nosuch' ) ), array( 'class_id' => 0 ) ), 'no such class: nothing' );
        $this->assertNull( expModerationList::where( self::f( array( 'held' => 'yes' ) ), array( 'in' => array( array() ) ) ), 'nothing held: nothing' );
        $this->assertSame( array(), expModerationList::where( self::f( array( 'held' => 'no' ) ), array( 'not_in' => array( array() ) ) ), 'nothing held: all' );
        $this->assertSame( array( 'status = 1' ), expModerationList::where( self::f( array( 'status' => 'published' ) ), array() ) );
        $this->assertSame( array( 'status = 2' ), expModerationList::where( self::f( array( 'status' => 'archived' ) ), array() ) );
        $this->assertSame( array( 'published > 0' ), expModerationList::where( self::f( array( 'date' => 'published' ) ), array() ), 'created: published ones' );
    }

    /** MO-4 */
    public function testOrderAndSortLinks()
    {
        $this->assertSame( 'modified DESC, id DESC', expModerationList::order( self::f() ) );
        $this->assertSame( 'published ASC, id ASC', expModerationList::order( self::f( array( 'sort' => 'published', 'order' => 'asc' ) ) ) );
        $this->assertSame( 'contentclass_id DESC, id DESC', expModerationList::order( self::f( array( 'sort' => 'class' ) ) ) );
        $this->assertSame( 'id DESC', expModerationList::order( self::f( array( 'sort' => 'id' ) ) ) );
        $links = \Exponential\View\Kernel\Moderation\Recent::sortLinks( self::f( array( 'held' => 'yes' ) ) );
        $this->assertTrue( $links['modified']['current'] );
        $this->assertSame( '/moderation/recent/(held)/yes/(order)/asc', $links['modified']['url'], 'the current column reversed' );
        $this->assertSame( '/moderation/recent/(held)/yes/(sort)/name/(order)/asc', $links['name']['url'], 'names from A' );
        $this->assertSame( '/moderation/recent/(held)/yes/(sort)/published', $links['published']['url'], 'dates newest first' );
    }

    /** MO-5 */
    public function testPageSizes()
    {
        $ini = file_get_contents( self::root() . '/settings/admininterface.ini' );
        preg_match_all( '/^ItemsPerPageList_moderation_recent\[\]=(\d+)$/m', $ini, $m );
        $this->assertSame( array( '25', '100', '300', '700', '1000' ), $m[1] );
        expModerationList::$settings = array( 'PageSizes' => array( 25, 100, 300, 700, 1000, 5000, 'x', 100 ) );
        $s = expModerationList::settings();
        $this->assertSame( self::SIZES, $s['sizes'], 'never above 1000, no doubles' );
        $this->assertSame( 25, $s['default'] );
        $this->assertSame( 100, $s['job_threshold'] );
        $this->assertSame( array( 'content.object.hold.auto', 'content.object.publish', 'content.object.create' ), $s['events'] );
        $this->assertSame( 25, expModerationList::chosenSize( self::SIZES, '' ), 'nothing chosen: the first' );
        $this->assertSame( 700, expModerationList::chosenSize( self::SIZES, '4' ) );
        $this->assertSame( 25, expModerationList::chosenSize( self::SIZES, '9' ) );
        $this->assertSame( 'admin_moderation_limit', expModerationList::PREFERENCE );
    }

    /** MO-6 */
    public function testStatusAndSubmission()
    {
        $this->assertSame( array( 'status' => 'published', 'pending' => false, 'draft' => false ), expModerationList::status( 1, array() ) );
        $this->assertSame( array( 'status' => 'published', 'pending' => true, 'draft' => true ), expModerationList::status( 1, array( 0, 2 ) ) );
        $this->assertSame( array( 'status' => 'draft', 'pending' => false, 'draft' => false ), expModerationList::status( 0, array( 0 ) ), 'a new draft is not "a newer draft"' );
        $this->assertSame( 'archived', expModerationList::status( 2, array() )['status'] );

        $this->assertNull( expModerationList::submission( array() ) );
        $s = expModerationList::submission( array(
            array( 'name' => 'content.object.publish', 'ip' => '203.0.113.0/24', 'ua' => 'Late', 'siteaccess' => 'eng', 'time_ms' => 2000 ),
            array( 'name' => 'content.object.hold.auto', 'ip' => '198.51.100.0/24', 'ua' => 'Mozilla/5.0', 'siteaccess' => 'site', 'time_ms' => 1500 ) ) );
        $this->assertSame( array( 'ip' => '198.51.100.0/24', 'ua' => 'Mozilla/5.0', 'siteaccess' => 'site', 'time' => 1, 'event' => 'content.object.hold.auto' ), $s, 'the earliest' );
    }

    /** MO-7 */
    public function testActions()
    {
        $defs = expModerationAction::definitions();
        $this->assertSame( array( 'release', 'hold', 'hide', 'reveal', 'trash', 'delete', 'section', 'state' ), array_keys( $defs ) );
        $this->assertSame( array( 'trash', 'delete' ), array_keys( array_filter( $defs, function ( $d ) { return $d['destructive']; } ) ) );
        $this->assertTrue( expModerationAction::isDestructive( 'delete' ) );
        $this->assertFalse( expModerationAction::isDestructive( 'release' ) );
        $this->assertFalse( expModerationAction::isDestructive( 'nosuch' ) );

        $v = expModerationAction::validate( array( 'action' => 'release', 'ids' => array( '5', '5', 'x', '-3', '7', array( 1 ) ) ) );
        $this->assertSame( array(), $v['errors'] );
        $this->assertSame( array( 5, 7 ), $v['ids'] );
        $this->assertSame( array( 'action', 'ids' ), expModerationAction::validate( array( 'action' => 'drop' ) )['errors'] );
        $this->assertSame( array( 'reason' ), expModerationAction::validate( array( 'action' => 'hold', 'ids' => array( 1 ), 'reason' => " \n" ) )['errors'] );
        $this->assertSame( array(), expModerationAction::validate( array( 'action' => 'hold', 'ids' => array( 1 ), 'reason' => 'Spam?' ) )['errors'] );
        $this->assertSame( array( 'section' ), expModerationAction::validate( array( 'action' => 'section', 'ids' => array( 1 ) ) )['errors'] );
        $this->assertSame( array( 'state' ), expModerationAction::validate( array( 'action' => 'state', 'ids' => array( 1 ), 'state_id' => 'x' ) )['errors'] );
        $this->assertSame( 4, expModerationAction::validate( array( 'action' => 'section', 'ids' => array( 1 ), 'section_id' => '4' ) )['section_id'] );
        $this->assertSame( array( 'too_many' ), expModerationAction::validate( array( 'action' => 'hide', 'ids' => range( 1, 1001 ) ) )['errors'] );

        $this->assertFalse( expModerationAction::runsAsJob( 100, 100 ) );
        $this->assertTrue( expModerationAction::runsAsJob( 101, 100 ) );
        $this->assertFalse( expModerationAction::runsAsJob( 1000, 0 ), '0: never in the background' );

        $this->assertSame( array( 'done' => 2, 'refused' => 1, 'failed' => 0, 'total' => 3 ), expModerationAction::counts( array(
            array( 'id' => 1, 'name' => 'a', 'status' => 'done', 'message' => '' ), array( 'id' => 2, 'name' => 'b', 'status' => 'refused', 'message' => '' ),
            array( 'id' => 3, 'name' => 'c', 'status' => 'done', 'message' => '' ) ) ) );

        $return = array( 'Exponential\\View\\Kernel\\Moderation\\Action', 'returnURL' );
        $this->assertSame( 'moderation/recent/(held)/yes/(offset)/25', call_user_func( $return, '/moderation/recent/(held)/yes/(offset)/25' ) );
        $this->assertSame( 'moderation/recent', call_user_func( $return, 'https://example.com/' ) );
        $this->assertSame( 'moderation/recent', call_user_func( $return, 'content/view/full/2' ) );
        $this->assertSame( 'moderation/recent', call_user_func( $return, 'moderation/recent/"><script>' ) );
    }

    /** MO-8 */
    public function testModuleAndSettings()
    {
        $Module = $ViewList = $FunctionList = null;
        include self::root() . '/kernel/moderation/module.php';
        $this->assertSame( array( 'recent', 'action', 'report' ), array_keys( $ViewList ) );
        $this->assertSame( array( 'read' ), $ViewList['recent']['functions'] );
        $this->assertSame( array( 'act' ), $ViewList['action']['functions'] );
        $this->assertSame( array( 'read', 'act', 'personaldata' ), array_keys( $FunctionList ) );
        foreach ( $ViewList as $name => $view )
        {
            $this->assertFileExists( self::root() . '/kernel/moderation/' . $view['script'] );
            $this->assertSame( 'ezmynavigationpart', $view['default_navigation_part'], $name );
        }
        $this->assertSame( 'Confirm', $ViewList['action']['single_post_actions']['ConfirmDestructiveButton'] );
        $FunctionList = null;
        include self::root() . '/kernel/moderation/function_definition.php';
        $this->assertSame( array( 'class' => 'expModerationAdmin', 'method' => 'fetchSummary' ), $FunctionList['summary']['call_method'] );

        $module = file_get_contents( self::root() . '/settings/module.ini' );
        $this->assertMatchesRegularExpression( '/^ModuleList\[\]=moderation$/m', $module );
        $menu = file_get_contents( self::root() . '/settings/menu.ini' );
        $this->assertMatchesRegularExpression( '/\[Leftmenu_my\]\nName=dashboard\nLinks\[\]\nLinkNames\[\]\nLinks\[dashboard\]=content\/dashboard\nPolicyList_dashboard\[\]=content\/dashboard\n\n(#[^\n]*\n)*Links\[recent_content\]=moderation\/recent\n/', $menu );
        $this->assertMatchesRegularExpression( '/^Links\[recent_content\]=moderation\/recent$/m', $menu );
        $this->assertMatchesRegularExpression( '/^PolicyList_recent_content\[\]=moderation\/read$/m', $menu );
        // the Dashboard menu's second link, in the plain left menu and in admin4's mega menu; no longer in Setup
        $this->assertDoesNotMatchRegularExpression( '/^GroupLinks_[a-z_]+\[\]=recent_content$/m', $menu );
        $this->assertSame( 1, substr_count( $menu, 'Links[recent_content]=' ) );
        $this->assertMatchesRegularExpression( '/^MegaMenuLinks\[\]=overview-dashboard\nMegaMenuLinks\[\]=overview-recent_content$/m', $menu );
        $dashboard = file_get_contents( self::root() . '/settings/dashboard.ini' );
        $this->assertMatchesRegularExpression( '/^DashboardBlocks\[\]=moderation$/m', $dashboard );
        $this->assertMatchesRegularExpression( '/\[DashboardBlock_moderation\][^\[]*PolicyList\[\]=moderation\/read/', $dashboard );
        $content = file_get_contents( self::root() . '/settings/content.ini' );
        $this->assertMatchesRegularExpression( '/^JobTypes\[moderation\]=expContentJobModeration$/m', $content );

        // "Verify it's you": the destructive confirmation, never the list or the other actions
        $site = file_get_contents( self::root() . '/settings/site.ini' );
        $this->assertMatchesRegularExpression( '/^Rules\[\]=moderation\/action ConfirmDestructiveButton$/m', $site );
        $rule = expReauthRules::parse( 'moderation/action ConfirmDestructiveButton' );
        $this->assertSame( array( 'ConfirmDestructiveButton' ), $rule['fields'] );

        // the editors' everyday policies (the installers and the upgrade step 6.0.15-editor-policies)
        $names = array();
        foreach ( expEditorPolicies::definitions() as $d )
            $names[] = $d['module'] . '/' . $d['function'];
        $this->assertContains( 'moderation/read', $names );
        $this->assertContains( 'moderation/act', $names );
        $this->assertNotContains( 'moderation/personaldata', $names, 'personal data: not by default' );
    }

    /** MO-9 */
    public function testTemplatesInBothDesigns()
    {
        foreach ( array( 'templates/moderation/recent.tpl', 'templates/moderation/confirm.tpl', 'templates/moderation/report.tpl',
                         'templates/dashboard/moderation.tpl', 'javascript/page/moderation_recent.js' ) as $file )
        {
            $this->assertFileExists( self::root() . '/design/admin4/' . $file );
            $this->assertFileEquals( self::root() . '/design/admin4/' . $file, self::root() . '/design/admin/' . $file, $file );
        }
        $recent = file_get_contents( self::root() . '/design/admin4/templates/moderation/recent.tpl' );
        $this->assertStringNotContainsString( '<style', $recent );
        $this->assertStringNotContainsString( 'style="', $recent );
        $this->assertStringContainsString( "user/preferences/set/admin_moderation_limit/", $recent );
        $this->assertStringContainsString( '{$rows_html}', $recent );
        $this->assertMatchesRegularExpression( '/\{if \$personal\}<th/', $recent, 'the personal data column only with moderation/personaldata' );
    }

    /** MO-10 the rows drawn in PHP: escaped, the checkbox and the personal column only when allowed */
    public function testRowsHtml()
    {
        $row = array( 'id' => 7, 'name' => '<b>Spam</b> & co', 'modified' => 100, 'published' => 0, 'node_id' => 9, 'class_name' => 'Comment',
                      'status' => 'published', 'pending' => true, 'hidden' => true, 'invisible' => true, 'held' => true, 'auto' => true,
                      'hold_reason' => 'Link "x"', 'hold_by' => '', 'version_status' => 'pending', 'version' => 2, 'current_version' => 1,
                      'scheduled' => 200, 'section_name' => 'Standard', 'owner_node_id' => 0, 'owner_name' => 'Anonymous User', 'owner_id' => 10,
                      'recorded' => true, 'ip' => '198.51.100.0/24', 'ua' => 'Firefox 132 / Windows', 'siteaccess' => 'site' );
        $tr = function ( $t, array $a = array() ) { return strtr( $t, $a ); };
        $url = function ( $p ) { return '/admin' . $p; };
        $date = function ( $ts ) { return 'T' . $ts; };
        $html = expModerationAdmin::rowsHtml( array( $row ), true, true, $tr, $url, $date );
        $this->assertStringContainsString( '<input type="checkbox" name="ObjectIDList[]" value="7"', $html );
        $this->assertStringContainsString( '&lt;b&gt;Spam&lt;/b&gt; &amp; co', $html );
        $this->assertStringNotContainsString( '<b>Spam', $html );
        $this->assertStringContainsString( 'href="/admin/content/view/full/9"', $html );
        $this->assertStringContainsString( 'Held: new submission', $html );
        $this->assertStringContainsString( 'title="Link &quot;x&quot;"', $html );
        $this->assertStringContainsString( 'Version 2 pending', $html );
        $this->assertStringContainsString( 'Scheduled for T200', $html );
        $this->assertStringContainsString( '198.51.100.0/24', $html );
        $this->assertStringContainsString( 'not yet', $html );
        $plain = expModerationAdmin::rowsHtml( array( $row ), false, false, $tr, $url, $date );
        $this->assertStringNotContainsString( 'ObjectIDList', $plain );
        $this->assertStringNotContainsString( '198.51.100', $plain, 'no personal data without moderation/personaldata' );
        $this->assertSame( 1, substr_count( $plain, '<tr>' ) );
    }
}
