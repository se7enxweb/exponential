<?php
/**
 * admin4's sub items table: the rows reordered by drag and drop and saved as the children's priorities
 * (design/admin4/javascript/subitems_priority_drag.js), checked on the files alone (no database):
 *
 *  PD-01 - the behaviour (the script's pure parts run in node, tests/tests/design/js/priority_drag.js; skipped
 *          without node): the grips only while the parent sorts by priority, the user may change it, the table is
 *          sorted the same way and not filtered; the page's priorities given out again in the parent's direction;
 *          equal priorities, also with the last row of the page before or the first of the page after, made distinct
 *          between those neighbours, and a renumbering of all children when there is no room; the keys of a grip;
 *          saving only what changed with the shown priorities and the neighbours' as expected ones; a conflict and
 *          an error write nothing; undo posts the old values, checked against the new ones
 *  PD-02 - the condition in the template is the one of the classic priority fields (sorted by priority and
 *          $node.can_edit), the script is loaded before the table's, the table script (the same in both admin
 *          designs) adds the column only with it and edits no cell while a move is saved
 *  PD-03 - every text of the move is translated in ger-DE and eng-US
 *  PD-04 - the grip is a button with the grip-vertical icon of the list, the stylesheets have forced colours,
 *          reduced motion and the toast's action button
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSubitemsPriorityDragTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminSubitemsPriorityDragTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    /** The DRAG labels of children_detailed.tpl: key => English text */
    private static function labels()
    {
        $tpl = self::read( 'design/admin4/templates/children_detailed.tpl' );
        $start = strpos( $tpl, 'DRAG: {ldelim}' );
        self::assertNotFalse( $start, 'children_detailed.tpl hands the texts over as labelsObj.DRAG' );
        $end = strpos( $tpl, '{rdelim}', $start );
        preg_match_all( "/(\\w+): \"\\{'((?:[^'\\\\]|\\\\.)*)'\\|i18n\\( 'design\\/admin\\/node\\/view\\/full' \\)\\|wash\\('javascript'\\)\\}\"/",
                        substr( $tpl, $start, $end - $start ), $m, PREG_SET_ORDER );
        $labels = array();
        foreach ( $m as $match )
            $labels[$match[1]] = $match[2];
        return $labels;
    }

    /** PD-01 */
    public function testTheOrderIsGivenOutAndSavedSafely()
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/priority_drag.js' ) . ' '
                                   . escapeshellarg( self::root() . '/design/admin4/javascript/subitems_priority_drag.js' ) . ' 2>&1' );
        $s = array();
        foreach ( array_filter( explode( "\n", $out ) ) as $line )
        {
            $row = json_decode( $line, true );
            $this->assertIsArray( $row, 'the harness ran: ' . $out );
            $s[$row['name']] = $row['value'];
        }
        $this->assertCount( 34, $s, $out );

        // the grips: only with the right and the parent's sorting, as the table shows it
        $this->assertTrue( $s['active: priority, may edit'] );
        $this->assertTrue( $s['active: descending parent'] );
        foreach ( array( 'may not edit', 'not sorted by priority', 'table sorted by name', 'table in the other direction',
                         'name filter', 'table filter', 'one row', 'priorityDrag must be true' ) as $off )
            $this->assertFalse( $s["active: $off"], $off );

        // distinct values: the same values in a new order, in the parent's direction
        $this->assertSame( array( 'mode' => 'permute', 'values' => array( 'c' => 10, 'a' => 20, 'b' => 30 ) ), $s['permute ascending'] );
        $this->assertSame( array( 'mode' => 'permute', 'values' => array( 'c' => 30, 'a' => 20, 'b' => 10 ) ), $s['permute descending'] );
        $this->assertSame( array( 'b' => 10, 'c' => 20, 'a' => 30 ), $s['permute between neighbours']['values'] );
        // equal values: distinct ones, between the neighbours, in steps of 10 where there is room
        $this->assertSame( array( 'mode' => 'spread', 'values' => array( 'b' => 0, 'a' => 10, 'c' => 20 ) ), $s['equal, no neighbours'] );
        $this->assertSame( array( 'c' => 6, 'b' => 7, 'a' => 8 ), $s['equal, narrow room']['values'], 'strictly between 5 and 9' );
        $this->assertSame( array( 'mode' => 'renumber' ), $s['equal, no room'] );
        $this->assertSame( array( 'mode' => 'spread', 'values' => array( 'c' => 8, 'a' => 11, 'b' => 14 ) ), $s['tie with the page before'],
                           'a value shared with the page before is not kept: the row could change pages' );
        $this->assertSame( array( 'c' => 40, 'a' => 30, 'b' => 20 ), $s['equal, descending']['values'], 'between 50 and 10, falling' );
        $this->assertSame( array( 'a' => -30, 'c' => -20, 'b' => -10 ), $s['equal, first page']['values'], 'below the next page' );
        $this->assertSame( array( 'b' => 15, 'a' => 25 ), $s['equal, last page']['values'], 'above the page before' );
        $this->assertSame( array( 'b' => 20, 'a' => 10 ), $s['equal, first page, descending']['values'] );

        // keys
        $this->assertSame( array( 'space' => 'pickup', 'enter' => 'pickup', 'arrow' => null, 'altUp' => 'stepUp', 'altDown' => 'stepDown',
                                  'ctrlSpace' => null, 'grabbedUp' => 'up', 'grabbedDown' => 'down', 'grabbedHome' => 'first',
                                  'grabbedEnd' => 'last', 'grabbedSpace' => 'drop', 'grabbedEnter' => 'drop', 'grabbedEscape' => 'cancel',
                                  'grabbedTab' => null, 'grabbedAltUp' => null, 'escape' => null ), $s['keys'] );

        // saving: only the changed rows, the shown and the neighbours' priorities as expected, the neighbours asked for
        $p = $s['save: permute'];
        $this->assertSame( 'saved', $p['res']['status'] );
        $this->assertSame( array( 'ContentNodeID' => 2, 'ContentObjectID' => 1, 'ExpectedPriorityList' => '2:20,3:30,4:40,5:50,6:60',
                                  'PriorityList' => '3:40,4:50,5:30' ), $p['call'] );
        $this->assertSame( array( 1, 5 ), $p['fetched'] );
        $this->assertSame( array( '3' => array( 30, 40 ), '4' => array( 40, 50 ), '5' => array( 50, 30 ) ), $p['res']['changed'] );
        $this->assertSame( array( 'status' => 'unchanged' ), $s['save: unchanged']['res'] );
        $this->assertSame( 0, $s['save: unchanged']['calls'] );
        $this->assertSame( array( 3 ), $s['save: first page, equal priorities']['fetched'], 'no row before the first page' );
        $this->assertSame( '1:10,2:20', $s['save: first page, equal priorities']['call']['PriorityList'], 'row 3 keeps its 0' );
        $r = $s['save: renumber'];
        $this->assertSame( array( '1', '4,3', 2 ), array( $r['call']['Renumber'], $r['call']['PageOrder'], $r['call']['PageOffset'] ) );
        $this->assertArrayNotHasKey( 'PriorityList', $r['call'] );
        $this->assertSame( array( '3' => 40, '4' => 30 ), $r['res']['values'] );
        // conflicts and errors write nothing
        $this->assertSame( array( 'status' => 'conflict', 'nodes' => array( 2 ) ), $s['save: conflict']['res'] );
        $this->assertSame( array( '1' => 10, '2' => 25, '3' => 30 ), $s['save: conflict']['priorities'] );
        $this->assertSame( array( 'status' => 'conflict', 'nodes' => array( 1 ) ), $s['save: neighbour conflict']['res'] );
        $this->assertSame( array( 'status' => 'error', 'message' => 'HTTP 500' ), $s['save: error'] );
        $this->assertSame( 'error', $s['save: older server']['status'], 'an answer without the check is not taken for saved' );
        $this->assertSame( 'error', $s['undo: older server']['status'] );
        // undo
        $this->assertSame( array( 'status' => 'saved' ), $s['undo']['res'] );
        $this->assertSame( '3:30,4:40,5:50', $s['undo']['call']['PriorityList'] );
        $this->assertSame( '3:40,4:50,5:30', $s['undo']['call']['ExpectedPriorityList'] );
        $this->assertSame( array( '1' => 10, '2' => 20, '3' => 30, '4' => 40, '5' => 50, '6' => 60, '7' => 70 ), $s['undo']['priorities'] );
        $this->assertSame( 'conflict', $s['undo: conflict']['res']['status'] );
        $this->assertSame( 99, $s['undo: conflict']['priorities']['1'] );
        $this->assertSame( array( 'status' => 'unchanged' ), $s['undo: nothing changed'] );
    }

    /** PD-02 */
    public function testTheGripsNeedPrioritySortingAndTheRight()
    {
        $children = self::read( 'design/admin4/templates/children.tpl' );
        $detailed = self::read( 'design/admin4/templates/children_detailed.tpl' );
        $this->assertStringContainsString( "\$priority    = and( eq( \$node.sort_array[0][0], 'priority' ), \$node.can_edit, \$children_count )", $children );
        $this->assertStringContainsString( "priorityDrag: {if and( eq( \$node.sort_array[0][0], 'priority' ), \$node.can_edit )}true{else}false{/if},", $detailed );
        $this->assertStringContainsString( 'parentObjectID: {$node.contentobject_id|int()},', $detailed );
        $this->assertStringContainsString( "{ezscript_require( array( 'subitems_priority_drag.js', 'ezajaxsubitems_expdatatable.js' ) )}", $children );

        $this->assertFileEquals( self::root() . '/design/admin4/javascript/ezajaxsubitems_expdatatable.js',
                                 self::root() . '/design/admin/javascript/ezajaxsubitems_expdatatable.js' );
        $table = self::read( 'design/admin4/javascript/ezajaxsubitems_expdatatable.js' );
        $this->assertStringContainsString( 'var drag = confObj.priorityDrag && window.a4SubitemsPriorityDrag ? window.a4SubitemsPriorityDrag.create(confObj, labelsObj.DRAG) : null;', $table );
        $this->assertStringContainsString( "canEdit: function (row) { return row.can_edit === true && !(drag && drag.busy()); },", $table );
        $this->assertStringContainsString( "if (drag) { drag.attach(\$list.data('expDataTable')); }", $table );
        $this->assertFileDoesNotExist( self::root() . '/design/admin/javascript/subitems_priority_drag.js', 'admin4 only' );

        // it saves through the inline priority editor's server function, with the parent's object for the cache
        $js = self::read( 'design/admin4/javascript/subitems_priority_drag.js' );
        $this->assertStringContainsString( "window.Exp.io.call('ezjscnode::updatepriority', [], { method: 'POST', data: data })", $js );
        $this->assertStringContainsString( 'objectID: conf.parentObjectID', $js );
        $this->assertStringNotContainsString( 'draggable', $js, 'pointer events, not HTML5 drag and drop' );
        $this->assertStringNotContainsString( 'sortable(', $js, 'no jQuery UI' );
    }

    /** PD-03 */
    public function testEveryTextIsTranslated()
    {
        $labels = self::labels();
        $this->assertCount( 19, $labels );
        $js = self::read( 'design/admin4/javascript/subitems_priority_drag.js' );
        foreach ( $labels as $key => $text )
            $this->assertStringContainsString( "L('$key', '" . str_replace( "\\'", "\\'", $text ) . "'", $js, "$key: the script's fallback is the template's text" );

        foreach ( array( 'ger-DE', 'eng-US' ) as $locale )
        {
            $dom = new DOMDocument();
            $this->assertTrue( $dom->load( self::root() . "/share/translations/$locale/translation.ts" ) );
            $xpath = new DOMXPath( $dom );
            foreach ( $labels as $key => $text )
            {
                $text = stripslashes( $text );
                $found = $xpath->query( "//context[name='design/admin/node/view/full']/message[source=" . self::xpathString( $text ) . "]/translation" );
                $this->assertSame( 1, $found->length, "$locale: $text" );
                $this->assertNotSame( '', trim( $found->item( 0 )->textContent ), "$locale: $text" );
                foreach ( array( '%name', '%position', '%count', '%error' ) as $param )
                {
                    if ( strpos( $text, $param ) !== false )
                        $this->assertStringContainsString( $param, $found->item( 0 )->textContent, "$locale: $text keeps $param" );
                }
            }
        }
        $de = new DOMXPath( ( function () { $d = new DOMDocument(); $d->load( self::root() . '/share/translations/ger-DE/translation.ts' ); return $d; } )() );
        $this->assertSame( 'Verschoben auf Position %position von %count',
                           $de->query( "//context[name='design/admin/node/view/full']/message[source='Moved to position %position of %count']/translation" )->item( 0 )->textContent );
        $this->assertSame( 'Reihenfolge gespeichert',
                           $de->query( "//context[name='design/admin/node/view/full']/message[source='Order saved']/translation" )->item( 0 )->textContent );
    }

    private static function xpathString( $text )
    {
        if ( strpos( $text, "'" ) === false )
            return "'$text'";
        return 'concat(\'' . str_replace( "'", "', \"'\", '", $text ) . '\')';
    }

    /** PD-04 */
    public function testTheGripAndItsStyles()
    {
        $js = self::read( 'design/admin4/javascript/subitems_priority_drag.js' );
        $this->assertStringContainsString( '<button type="button" class="a4-grip a4-content-drag-grip"></button>', $js );
        $this->assertStringContainsString( "'aria-describedby': hintId", $js );
        $this->assertStringContainsString( '<span class="a4-icon a4-bi-grip-vertical" aria-hidden="true"></span>', $js );
        $this->assertStringContainsString( "IconList[]=grip-vertical\n", self::read( 'settings/design.ini' ) );
        $this->assertFileExists( self::root() . '/share/icons/bootstrap-icons/svg/grip-vertical.svg' );
        $this->assertStringContainsString( '.a4-bi-grip-vertical {', self::read( 'design/admin4/stylesheets/components.css' ) );

        $css = preg_replace( '#/\*.*?\*/#s', '', self::read( 'design/admin4/stylesheets/modules/content.css' ) );
        $this->assertMatchesRegularExpression( '/\.a4-content-drag-ghost \{[^}]*position: fixed;[^}]*top: var\(--a4-content-drag-y, 0\);[^}]*z-index: var\(--a4-z-drag\);[^}]*pointer-events: none;/s', $css );
        $this->assertMatchesRegularExpression( '/@media \(prefers-reduced-motion: reduce\) \{\s*\.a4-content-drag-ghost \{ transform: none; \}/', $css );
        $this->assertMatchesRegularExpression( '/@media \(forced-colors: active\) \{\s*\.a4-content-drag-on tr\.a4-content-drag-placeholder \{ outline-color: Highlight; \}/', $css );
        $this->assertStringContainsString( '.a4-content-drag-grip:focus-visible { outline: 3px solid var(--a4-ring); outline-offset: 2px; }', $css );
        foreach ( array( '--a4-content-drag-x', '--a4-content-drag-y', '--a4-content-drag-w' ) as $var )
            $this->assertStringContainsString( "style.setProperty('$var'", $js );

        $components = self::read( 'design/admin4/stylesheets/components.css' );
        $this->assertStringContainsString( '.a4-toast .a4-toast-action {', $components );
        $this->assertStringContainsString( '.a4-toast .a4-toast-action { border-color: ButtonText; color: ButtonText; }', $components );
    }
}
