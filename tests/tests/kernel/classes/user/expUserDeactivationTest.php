<?php
/**
 * Deactivate and move (user/deactivate, expUserDeactivation): the plain functions and the source files. The kernel
 * paths (the group created on first use, the locations moved and put back) are in expUserDeactivationLiveTest.
 *
 *  UD-01 - Off by default: site.ini ships DeactivateAndMove=disabled and an empty DeactivatedUserGroupNodeID; only
 *          "enabled" switches it on; the node id setting takes positive numbers only
 *  UD-02 - The remembered locations: JSON round trip, the main parent required, the other parents unique and without
 *          the main one, anything else is no record
 *  UD-03 - Which account an action touches: never a protected one, nor one that is gone or unreadable, not a user,
 *          not editable, with a location that may not be read, moved or removed, nor one with sub-items; no group
 *          and none to create; unchanged when already there; reactivate: not an unactivated account, nowhere to go
 *  UD-04 - Where an account goes back to: the remembered main parent, else the default user group; the other
 *          remembered parents that may take it; the rest counted together (gone or not allowed)
 *  UD-05 - The bulk run: a protected account is not even looked at, the confirmation only decides, at most 100,
 *          a failure is reported, the details of each account kept
 *  UD-06 - The page and the entry points: the view needs user/deactivate and answers "not found" when switched off;
 *          changes are POSTs after a confirmation; the way back never leaves the site; every button and menu entry
 *          only with the setting enabled; the same templates in admin and admin4; texts washed and translated; the
 *          audit names in the catalogue
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUserDeactivationTest extends PHPUnit\Framework\TestCase
{
    private static function read( $path )
    {
        return file_get_contents( dirname( __DIR__, 5 ) . '/' . $path );
    }

    /** A state decide() answers 'do' to for both actions where it applies */
    private static function state( array $change = array() )
    {
        return $change + array(
            'exists' => true, 'has_object' => true, 'is_user' => true, 'can_edit' => true, 'enabled' => true, 'has_key' => false,
            'main_node_id' => 100, 'in_group' => false, 'group_exists' => true, 'group_ok' => true, 'remembered' => false, 'target_ok' => true,
            'locations' => array(
                array( 'node_id' => 100, 'parent_node_id' => 12, 'is_main' => true, 'in_group' => false, 'readable' => true, 'movable' => true, 'removable' => false, 'children' => false ),
                array( 'node_id' => 101, 'parent_node_id' => 60, 'is_main' => false, 'in_group' => false, 'readable' => true, 'movable' => false, 'removable' => true, 'children' => false ),
            ),
        );
    }

    /** UD-01 */
    public function testOffByDefault()
    {
        $ini = self::read( 'settings/site.ini' );
        $start = strpos( $ini, "\n[UserSettings]\n" );
        $this->assertNotFalse( $start );
        $section = substr( $ini, $start + 1, (int)strpos( $ini, "\n[", $start + 1 ) - $start );
        $this->assertStringContainsString( "\nDeactivateAndMove=disabled\n", $section );
        $this->assertStringContainsString( "\nDeactivatedUserGroupNodeID=\n", $section );
        foreach ( array( 'enabled', ' enabled ' ) as $on )
            $this->assertTrue( expUserDeactivation::isEnabledValue( $on ), var_export( $on, true ) );
        // exactly "enabled", as the templates compare it
        foreach ( array( 'Enabled', 'ENABLED', 'disabled', '', 'true', '1', 'yes', null, false, true, 1, array( 'enabled' ) ) as $off )
            $this->assertFalse( expUserDeactivation::isEnabledValue( $off ), var_export( $off, true ) );
        foreach ( array( '42' => 42, ' 42 ' => 42, '' => 0, '0' => 0, '-5' => 0, '4.2' => 0, 'abc' => 0 ) as $value => $id )
            $this->assertSame( $id, expUserDeactivation::normaliseNodeID( (string)$value ), "'$value'" );
        $this->assertSame( 0, expUserDeactivation::normaliseNodeID( null ) );
        $this->assertSame( 0, expUserDeactivation::normaliseNodeID( array( 5 ) ) );
    }

    /** UD-02 */
    public function testRecord()
    {
        $record = array( 'main' => 12, 'parents' => array( 60, '61', 60, 12, 0, 'x', 300 ), 'group' => 300, 'was_enabled' => false, 'at' => 1790000000, 'by' => 14 );
        $json = expUserDeactivation::encodeRecord( $record );
        $this->assertSame( array( 'v' => 1, 'main' => 12, 'parents' => array( 60, 61 ), 'group' => 300, 'was_enabled' => false, 'at' => 1790000000, 'by' => 14 ),
                           expUserDeactivation::decodeRecord( $json ), 'the group itself is never a remembered location' );
        $this->assertTrue( expUserDeactivation::decodeRecord( '{"main":12}' )['was_enabled'], 'an older record: enabled before' );
        $this->assertNull( expUserDeactivation::decodeRecord( '{"main":300,"group":300}' ), 'the group as the main location is no record' );

        // what is stored on deactivation
        $now = array( 'main' => 12, 'parents' => array( 60, 300 ) );
        $this->assertSame( array( 'v' => 1, 'main' => 12, 'parents' => array( 60 ), 'group' => 300, 'was_enabled' => true, 'at' => 5, 'by' => 14 ),
                           expUserDeactivation::recordFor( null, $now, 300, true, 14, 5 ), 'not in the group yet: where it is now' );
        $old = expUserDeactivation::normaliseRecord( array( 'main' => 12, 'parents' => array( 60 ), 'group' => 300, 'was_enabled' => false, 'at' => 1, 'by' => 2 ) );
        $this->assertSame( array( 'v' => 1, 'main' => 12, 'parents' => array( 60, 61, 62 ), 'group' => 300, 'was_enabled' => false, 'at' => 1, 'by' => 2 ),
                           expUserDeactivation::recordFor( $old, array( 'main' => 300, 'parents' => array( 61, 62, 60 ) ), 300, false, 14, 5 ),
                           'in the group with locations added since: merged, nothing lost' );
        $this->assertNull( expUserDeactivation::recordFor( $old, array( 'main' => 300, 'parents' => array() ), 300, false, 14, 5 ), 'nothing new: kept as it is' );
        $this->assertSame( 61, expUserDeactivation::recordFor( null, array( 'main' => 300, 'parents' => array( 61, 62 ) ), 300, false, 14, 5 )['main'],
                           'moved into the group by hand: the first location outside becomes the main one' );
        $this->assertNull( expUserDeactivation::recordFor( null, array( 'main' => 300, 'parents' => array() ), 300, false, 14, 5 ) );
        foreach ( array( '', 'null', '[]', '{"main":0}', '{"parents":[1,2]}', 'not json', '{"main":"x"}' ) as $bad )
            $this->assertNull( expUserDeactivation::decodeRecord( $bad ), $bad );
        $this->assertNull( expUserDeactivation::decodeRecord( null ) );
        $this->assertSame( 'exp_user_deactivation_123', expUserDeactivation::recordName( '123' ) );
        $this->assertLessThanOrEqual( 60, strlen( expUserDeactivation::recordName( PHP_INT_MAX ) ), 'fits ezsite_data.name (60)' );
    }

    /** UD-03 */
    public function testDecide()
    {
        $p = array( 10, 14, 7 );
        foreach ( expUserDeactivation::ACTIONS as $action )
        {
            $this->assertSame( 'protected', expUserDeactivation::decide( $action, self::state(), 7, $p ) );
            $this->assertSame( 'gone', expUserDeactivation::decide( $action, null, 50, $p ), 'gone or unreadable: one answer' );
            $this->assertSame( 'not_user', expUserDeactivation::decide( $action, self::state( array( 'is_user' => false ) ), 50, $p ) );
            $this->assertSame( 'denied', expUserDeactivation::decide( $action, self::state( array( 'can_edit' => false ) ), 50, $p ) );
            $this->assertSame( 'no_location', expUserDeactivation::decide( $action, self::state( array( 'main_node_id' => 0 ) ), 50, $p ) );
            $s = self::state();
            $s['locations'][1]['readable'] = false;
            $this->assertSame( 'denied', expUserDeactivation::decide( $action, $s, 50, $p ), "$action: a location you may not read" );
        }

        $d = 'deactivate_move';
        $this->assertSame( 'do', expUserDeactivation::decide( $d, self::state(), 50, $p ) );
        $this->assertSame( 'do', expUserDeactivation::decide( $d, self::state( array( 'enabled' => false ) ), 50, $p ), 'disabled, not moved yet' );
        $inGroup = self::state( array( 'enabled' => false, 'in_group' => true ) );
        $inGroup['locations'] = array( array( 'node_id' => 100, 'parent_node_id' => 300, 'is_main' => true, 'in_group' => true, 'readable' => true, 'movable' => false, 'removable' => false, 'children' => false ) );
        $this->assertSame( 'unchanged', expUserDeactivation::decide( $d, $inGroup, 50, $p ) );
        $this->assertSame( 'do', expUserDeactivation::decide( $d, array( 'enabled' => true ) + $inGroup, 50, $p ), 'in the group but enabled: disabled now' );
        $this->assertSame( 'no_group', expUserDeactivation::decide( $d, self::state( array( 'group_exists' => false, 'group_ok' => false ) ), 50, $p ) );
        $this->assertSame( 'do', expUserDeactivation::decide( $d, self::state( array( 'group_exists' => false, 'group_ok' => true ) ), 50, $p ), 'created on first use' );
        $this->assertSame( 'denied', expUserDeactivation::decide( $d, self::state( array( 'group_ok' => false ) ), 50, $p ), 'may not move into the group' );
        $s = self::state();
        $s['locations'][0]['movable'] = false;
        $this->assertSame( 'denied', expUserDeactivation::decide( $d, $s, 50, $p ), 'may not move the main location' );
        $s = self::state();
        $s['locations'][1]['removable'] = false;
        $this->assertSame( 'denied', expUserDeactivation::decide( $d, $s, 50, $p ), 'may not remove another location' );
        $s = self::state();
        $s['locations'][1]['children'] = true;
        $this->assertSame( 'has_children', expUserDeactivation::decide( $d, $s, 50, $p ) );

        $r = 'reactivate_move';
        $moved = self::state( array( 'enabled' => false, 'in_group' => true, 'remembered' => true ) );
        $moved['locations'] = $inGroup['locations'];
        $moved['locations'][0]['movable'] = true;
        $this->assertSame( 'do', expUserDeactivation::decide( $r, $moved, 50, $p ) );
        $this->assertSame( 'unactivated', expUserDeactivation::decide( $r, array( 'has_key' => true ) + $moved, 50, $p ) );
        $this->assertSame( 'no_target', expUserDeactivation::decide( $r, array( 'target_ok' => false ) + $moved, 50, $p ) );
        $notMovable = $moved;
        $notMovable['locations'][0]['movable'] = false;
        $this->assertSame( 'denied', expUserDeactivation::decide( $r, $notMovable, 50, $p ) );
        $this->assertSame( 'unchanged', expUserDeactivation::decide( $r, self::state(), 50, $p ), 'enabled, not moved, nothing remembered' );
        $this->assertSame( 'do', expUserDeactivation::decide( $r, self::state( array( 'enabled' => false ) ), 50, $p ), 'disabled elsewhere: enabled' );
        $this->assertSame( 'do', expUserDeactivation::decide( $r, self::state( array( 'remembered' => true ) ), 50, $p ), 'enabled by hand, the locations come back' );
        $this->assertSame( 'unchanged', expUserDeactivation::decide( 'remove', self::state(), 50, $p ) );
    }

    /** UD-04 */
    public function testPlanRestore()
    {
        $exists = function ( array $ids ) { return function ( $id, $main ) use ( $ids ) { return in_array( $id, $ids, true ); }; };
        $record = array( 'main' => 12, 'parents' => array( 60, 61 ) );

        $this->assertSame( array( 'main' => 12, 'fallback' => false, 'add' => array( 60, 61 ), 'not_restored' => 0 ),
                           expUserDeactivation::planRestore( $record, $exists( array( 12, 60, 61, 70 ) ), 70, 300 ) );
        $this->assertSame( array( 'main' => 70, 'fallback' => true, 'add' => array( 60 ), 'not_restored' => 2 ),
                           expUserDeactivation::planRestore( $record, $exists( array( 60, 70 ) ), 70, 300 ), 'main and one other gone' );
        $this->assertSame( array( 'main' => 0, 'fallback' => false, 'add' => array(), 'not_restored' => 3 ),
                           expUserDeactivation::planRestore( $record, $exists( array() ), 70, 300 ), 'nowhere to go' );
        $this->assertSame( array( 'main' => 70, 'fallback' => true, 'add' => array(), 'not_restored' => 0 ),
                           expUserDeactivation::planRestore( null, $exists( array( 70 ) ), 70, 300 ), 'nothing remembered: the default user group' );
        $this->assertSame( array( 'main' => 12, 'fallback' => false, 'add' => array( 61 ), 'not_restored' => 0 ),
                           expUserDeactivation::planRestore( $record, $exists( array( 12, 60, 61 ) ), 70, 60 ), 'not the parent it has now' );
        $this->assertSame( 0, expUserDeactivation::planRestore( null, $exists( array( 70 ) ), 0, 300 )['main'], 'no fallback set' );
        // a node that may take another location but not the main one (add location, no create)
        $addOnly = function ( $id, $main ) { return $id === 70 || ( !$main && in_array( $id, array( 12, 60 ), true ) ); };
        $this->assertSame( array( 'main' => 70, 'fallback' => true, 'add' => array( 60 ), 'not_restored' => 2 ),
                           expUserDeactivation::planRestore( array( 'main' => 12, 'parents' => array( 60, 61 ) ), $addOnly, 70, 300 ),
                           'the main location only where the account may be moved' );
    }

    /** UD-05 */
    public function testApplyWith()
    {
        $looked = array();
        $state = function ( $id ) use ( &$looked ) { $looked[] = $id; return $id === 31 ? null : expUserDeactivationTest::stateFor( $id ); };
        $performed = array();
        $perform = function ( $action, $id ) use ( &$performed ) { $performed[] = $id; return $id === 33 ? array( 'ok' => false, 'error' => 'failed' ) : array( 'ok' => true, 'not_restored' => 1 ); };

        $dry = expUserDeactivation::applyWith( 'deactivate_move', array( 14, 30, 31, 32, 33, '30' ), $state, $perform, array( 14 ), true );
        $this->assertSame( array( 30, 32, 33 ), $dry['done'] );
        $this->assertSame( array( 'protected' => array( 14 ), 'gone' => array( 31 ) ), $dry['skipped'] );
        $this->assertSame( array(), $performed, 'the confirmation changes nothing' );
        $this->assertNotContains( 14, $looked, 'a protected account is not even looked at' );

        $done = expUserDeactivation::applyWith( 'deactivate_move', array( 14, 30, 31, 32, 33 ), $state, $perform, array( 14 ) );
        $this->assertSame( array( 30, 32 ), $done['done'] );
        $this->assertSame( array( 33 ), $done['failed'] );
        $this->assertSame( array( 30, 32, 33 ), array_keys( $done['details'] ) );

        $many = range( 1000, 1150 );
        $count = expUserDeactivation::applyWith( 'deactivate_move', $many, $state, $perform, array(), true );
        $this->assertCount( expUserDeactivation::MAX_IDS, $count['done'] );
        $this->assertSame( 100, expUserDeactivation::MAX_IDS );
    }

    /** @internal for testApplyWith() */
    public static function stateFor( $id )
    {
        return self::state();
    }

    /** UD-06 */
    public function testPageAndEntryPoints()
    {
        // the view: its own function, not found when switched off, POST and a confirmation
        $module = self::read( 'kernel/user/module.php' );
        $this->assertMatchesRegularExpression( "/\\\$ViewList\['deactivate'\] = array\(\n    'functions' => array\( 'deactivate' \),/", $module );
        $this->assertStringContainsString( "\$FunctionList['deactivate'] = array();", $module );
        $view = self::read( 'kernel/private/classes/views/user/deactivate.php' );
        $run = substr( $view, strpos( $view, 'public function run(' ) );
        $this->assertMatchesRegularExpression( '/^public function run\( array \$scope \)\s*\{\s*\$Params = \$scope\[\'Params\'\];\s*\$Module = \$Params\[\'Module\'\];\s*'
                                               . 'if \( !\\\\expUserDeactivation::isEnabled\(\) \)\s*return \$this->viewResult\( null, \$Module->handleError\( \\\\eZError::KERNEL_MODULE_VIEW_NOT_FOUND/', $run,
                                               'the first thing the view does when switched off: not found' );
        $this->assertStringContainsString( "\$isPost = \\eZSys::serverVariable( 'REQUEST_METHOD', true ) === 'POST';", $view );
        $this->assertStringContainsString( "if ( \$isPost )", $view );
        $this->assertStringContainsString( "!\$confirmed", $view );

        // the way back never leaves the site
        require_once dirname( __DIR__, 5 ) . '/kernel/private/classes/views/user/deactivate.php';
        $redirect = array( '\Exponential\View\Kernel\User\Deactivate', 'normaliseRedirect' );
        foreach ( array( 'user/hygiene' => 'user/hygiene', '/user/hygiene?filter=disabled&limit=50' => 'user/hygiene?filter=disabled&limit=50',
                         'content/view/full/42' => 'content/view/full/42', '/content/view/full/42' => 'content/view/full/42',
                         'https://example.org/' => '', '//example.org' => '', 'user/hygiene/../../x' => '', 'content/view/full/42/x' => '',
                         'user/hygiene?q=<script>' => '', 'javascript:alert(1)' => '', 'user/logout' => '' ) as $in => $out )
            $this->assertSame( $out, call_user_func( $redirect, $in ), $in );
        $this->assertSame( '', call_user_func( $redirect, array( 'user/hygiene' ) ) );

        // every entry point only with the setting enabled and the right
        $node4 = self::read( 'design/admin4/templates/node/view/a4_full_body.tpl' );
        $node = self::read( 'design/admin/templates/node/view/full.tpl' );
        foreach ( array( $node4, $node ) as $tpl )
        {
            $this->assertStringContainsString( "{if ezini( 'UserSettings', 'DeactivateAndMove' )|eq( 'enabled' )}", $tpl );
            $this->assertStringContainsString( "fetch( 'user', 'deactivation_offers', hash( 'object_id', \$node.contentobject_id ) )", $tpl );
            $this->assertSame( 2, substr_count( $tpl, "formaction={'user/deactivate'|ezurl}" ) );
            $start = strpos( $tpl, "{if ezini( 'UserSettings', 'DeactivateAndMove' )|eq( 'enabled' )}" );
            $this->assertGreaterThan( $start, strpos( $tpl, 'name="DeactivateMoveButton"' ), 'inside the setting check' );
        }
        $popup4 = self::read( 'design/admin4/templates/popupmenu/popup_sub_advance_menu.tpl' );
        $this->assertSame( $popup4, self::read( 'design/admin/templates/popupmenu/popup_sub_advance_menu.tpl' ), 'the same menu in admin and admin4' );
        $guard = "{if and( ezini( 'UserSettings', 'DeactivateAndMove' )|eq( 'enabled' ), fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'deactivate' ) ) )}";
        $this->assertSame( 2, substr_count( $popup4, $guard ), 'the entries and their forms' );
        $this->assertSame( 2, substr_count( $popup4, 'action={"/user/deactivate"|ezurl}' ) );
        $this->assertStringNotContainsString( 'method="get"', $popup4 );
        $hygiene = self::read( 'design/admin4/templates/user/hygiene.tpl' );
        $this->assertSame( $hygiene, self::read( 'design/admin/templates/user/hygiene.tpl' ) );
        $this->assertMatchesRegularExpression( '/\{if \$hygiene_deactivate\}\s*<button class="a4-btn" type="submit" name="DeactivateMoveButton"/', $hygiene );
        $this->assertStringContainsString( "\$deactivate = \\expUserDeactivation::isEnabled() && self::canDeactivate( \$currentUser );",
                                           self::read( 'kernel/private/classes/views/user/hygiene.php' ) );
        $this->assertStringContainsString( "if ( !self::isEnabled() )\n            return \$offers;", self::read( 'kernel/classes/user/expuserdeactivation.php' ) );

        // the confirmation page: the same in admin and admin4, a POST form, everything washed
        $tpl = self::read( 'design/admin4/templates/user/deactivate.tpl' );
        $this->assertSame( $tpl, self::read( 'design/admin/templates/user/deactivate.tpl' ) );
        $this->assertStringContainsString( "<form method=\"post\" action={'user/deactivate'|ezurl}>", $tpl );
        $this->assertStringContainsString( 'name="ConfirmButton"', $tpl );
        $this->assertStringContainsString( '<input type="hidden" name="DeactivateToken" value="{$deactivate_confirm.token|wash}" />', $tpl );
        $this->assertStringContainsString( '$confirmed = $action !== null && self::checkConfirmToken( $http );', $view, 'confirm needs the page\'s own token' );
        $this->assertStringContainsString( 'hash_equals( $expected, $given )', $view );
        $this->assertDoesNotMatchRegularExpression( '/\{\$(account|name|id|back)(\.[a-z_]+)?\}/', $tpl, 'every value is washed' );

        // the texts in both translations
        preg_match_all( "/'((?:[^'\\\\]|\\\\.)+)'\|i18n\( 'design\/admin\/user\/deactivate'/", $tpl, $m );
        $sources = array_unique( array_merge( $m[1], array( 'Deactivate and move', 'Deactivated users', 'Deactivated users (created now)',
                                                            'Disabled accounts, kept instead of removed. No roles; hidden.' ) ) );
        $this->assertGreaterThan( 30, count( $sources ) );
        $texts = array(
            'design/admin/user/deactivate' => $sources,
            'design/admin/node/view/full' => array( 'Deactivate and move', 'Reactivate and move back' ),
            'design/admin/popupmenu' => array( 'Deactivate and move (user accounts)', 'Reactivate and move back (user accounts)' ),
            'design/admin/user/hygiene' => array( 'Deactivate and move the ticked accounts', 'Reactivate and move back the ticked accounts', 'deactivated' ),
            'design/admin/role/sentence' => array( 'deactivate user accounts and move them to the group of deactivated users, and move them back' ),
        );
        foreach ( array( 'ger-DE', 'eng-US' ) as $locale )
        {
            $ts = self::read( "share/translations/$locale/translation.ts" );
            foreach ( $texts as $name => $list )
            {
                $start = strpos( $ts, "<name>$name</name>" );
                $this->assertNotFalse( $start, "$locale: $name" );
                $context = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
                foreach ( $list as $source )
                    $this->assertStringContainsString( '<source>' . htmlspecialchars( $source, ENT_NOQUOTES ) . '</source>', $context, "$locale $name: $source" );
            }
        }

        // the audit names
        $catalogue = expAuditTaxonomy::catalogue();
        foreach ( array( 'access.user.deactivate_move', 'access.user.reactivate_move', 'access.user.deactivated_group.create' ) as $name )
        {
            $this->assertArrayHasKey( $name, $catalogue );
            $this->assertSame( 'access', $catalogue[$name]['channel'] );
            $this->assertStringContainsString( "| `$name` |", self::read( 'doc/bc/6.0/audit.md' ) );
        }
        $this->assertSame( 'create', expAuditHook::withVerb( 'access.user.deactivated_group.create', array() )['verb'] );
    }
}
