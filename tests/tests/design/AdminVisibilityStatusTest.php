<?php
/**
 * The status marks of the administration: a node's visibility (visible, hidden, hidden by a parent) and the state
 * of a user account (disabled, locked) as one component, components.css .a4-mark.
 *
 *  VS-01 - The shape tells the state, not only the colour: a filled disc, a ring, a ring with a bar, a crossed
 *          ring, a filled square; in forced colours the shapes and fills stay (system colour)
 *  VS-02 - Each mark's colour reaches 3:1 (WCAG 1.4.11) on the card, the zebra row, the hovered row and its badge
 *          tint, in light and dark mode
 *  VS-03 - The include parts/visibility_status.tpl: three states, the badge for the node view's header, the
 *          quiet form for lists, named for screen readers unless visible; the header, the browse list and both
 *          static trees use it or its markup; the header shows a disabled or locked account
 *  VS-04 - The sub-items table and the dynamic tree draw the marks (node harness js/visibility_status.js): before
 *          the name in the table, after it in the tree and only where something differs, a badge for a disabled
 *          or locked account, the Visibility column with its mark, nothing without the flags
 *  VS-05 - The texts are in children_detailed.tpl of both designs and in ger-DE and eng-US, once per context
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminVisibilityStatusTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminBaseStylesheetsTest.php';
require_once __DIR__ . '/AdminTokenContrastTest.php';

class AdminVisibilityStatusTest extends PHPUnit\Framework\TestCase
{
    const CONTEXT = 'design/admin/node/view/full';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function file( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    /** selector => property => value, each selector of a list on its own */
    private static function rules( $forced = false )
    {
        $css = preg_replace( '#/\*.*?\*/#s', '', self::file( 'design/admin4/stylesheets/components.css' ) );
        $out = array();
        foreach ( AdminBaseStylesheetsTest::rules( $css, $forced ) as $list => $declarations )
        {
            foreach ( AdminBaseStylesheetsTest::selectors( $list ) as $selector )
            {
                $out[$selector] = isset( $out[$selector] ) ? array_merge( $out[$selector], $declarations ) : $declarations;
            }
        }
        return $out;
    }

    /**
     * VS-01: the mark is its own class. .a4-status is the older component of a status with a dot and its text
     * (setup/cronjobs, setup/preload, workflow/processlist): the mark's size, ring and radius must never reach it.
     */
    public function testTheMarkLeavesTheStatusComponentAlone()
    {
        $r = self::rules();
        foreach ( array( 'width', 'height', 'border', 'border-radius', 'position' ) as $property )
            $this->assertArrayNotHasKey( $property, $r['.a4-status'], ".a4-status has no $property of the mark" );
        $this->assertSame( 'inline-flex', $r['.a4-status']['display'] );
        foreach ( array( 'design/admin4/stylesheets/components.css', 'design/admin/stylesheets/components.css' ) as $path )
            $this->assertDoesNotMatchRegularExpression( '#\.a4-status\.is-(visible|hidden|hidden-by-parent|disabled|locked)\b#', self::file( $path ), $path );
        foreach ( array( 'design/admin4/templates/setup/cronjobs.tpl', 'design/admin4/templates/setup/preload.tpl', 'design/admin4/templates/workflow/processlist.tpl' ) as $path )
        {
            $template = self::file( $path );
            $this->assertStringContainsString( 'a4-status', $template, $path );
            $this->assertStringNotContainsString( 'a4-mark', $template, $path );
        }
    }

    /** VS-01 */
    public function testTheShapeTellsTheState()
    {
        $r = self::rules();
        $this->assertSame( '50%', $r['.a4-mark']['border-radius'] );
        $this->assertSame( '2px solid currentColor', $r['.a4-mark']['border'] );
        $this->assertSame( '10px', $r['.a4-mark']['width'] );
        $this->assertSame( '10px', $r['.a4-mark']['height'] );

        // visible: a filled disc
        $this->assertSame( 'var(--a4-ok)', $r['.a4-mark.is-visible']['color'] );
        $this->assertSame( 'currentColor', $r['.a4-mark.is-visible']['background-color'] );
        // hidden: a ring (no fill, no bar)
        $this->assertSame( 'var(--a4-bad)', $r['.a4-mark.is-hidden']['color'] );
        $this->assertArrayNotHasKey( 'background-color', $r['.a4-mark.is-hidden'] );
        $this->assertArrayNotHasKey( '.a4-mark.is-hidden::after', $r );
        // hidden by a parent: the ring with a bar across
        $this->assertSame( 'var(--a4-bad)', $r['.a4-mark.is-hidden-by-parent']['color'] );
        $this->assertArrayNotHasKey( 'background-color', $r['.a4-mark.is-hidden-by-parent'] );
        $this->assertSame( '""', $r['.a4-mark.is-hidden-by-parent::after']['content'] );
        $this->assertSame( 'currentColor', $r['.a4-mark.is-hidden-by-parent::after']['background-color'] );
        $this->assertArrayNotHasKey( 'transform', $r['.a4-mark.is-hidden-by-parent::after'] );
        // a disabled account: the ring crossed out
        $this->assertSame( 'var(--a4-muted)', $r['.a4-mark.is-disabled']['color'] );
        $this->assertSame( 'rotate(-45deg)', $r['.a4-mark.is-disabled::after']['transform'] );
        // a locked account: a filled square
        $this->assertSame( 'var(--a4-warn)', $r['.a4-mark.is-locked']['color'] );
        $this->assertSame( '2px', $r['.a4-mark.is-locked']['border-radius'] );
        $this->assertSame( 'currentColor', $r['.a4-mark.is-locked']['background-color'] );

        $f = self::rules( true );
        $this->assertSame( 'none', $f['.a4-mark']['forced-color-adjust'], 'the fills would be dropped otherwise' );
        $this->assertSame( 'CanvasText', $f['.a4-mark']['color'] );
        $this->assertSame( 'CanvasText', $f['.a4-mark.is-visible']['background-color'] );
        $this->assertSame( 'CanvasText', $f['.a4-mark.is-locked']['background-color'] );

        $this->assertFileEquals( self::root() . '/design/admin4/stylesheets/components.css', self::root() . '/design/admin/stylesheets/components.css' );
    }

    /** VS-02 */
    public function testEachMarkReachesThreeToOne()
    {
        $components = self::file( 'design/admin4/stylesheets/components.css' );
        $tokens = function ( $selector ) use ( $components ) {
            $css = preg_replace( '#/\*.*?\*/#s', '', $components );
            preg_match_all( '/(^|\})\s*' . preg_quote( $selector, '/' ) . '\s*\{([^}]*)\}/s', $css, $blocks );
            $out = array();
            foreach ( $blocks[2] as $block )
            {
                preg_match_all( '/(--[\w-]+)\s*:\s*([^;]+);/', $block, $pairs, PREG_SET_ORDER );
                foreach ( $pairs as $pair )
                    $out[$pair[1]] = trim( $pair[2] );
            }
            return $out;
        };
        $light = $tokens( ':root' );
        $modes = array( 'light' => $light, 'dark' => $tokens( 'html[data-a4-theme="dark"]' ) + $light );
        $marks = array( '--a4-ok' => '--a4-ok-bg', '--a4-bad' => '--a4-bad-bg', '--a4-warn' => '--a4-warn-bg', '--a4-muted' => '--a4-soft' );
        $failures = array();
        foreach ( $modes as $mode => $t )
        {
            foreach ( $marks as $front => $tint )
            {
                foreach ( array( '--a4-card', '--a4-zebra', '--a4-info-bg', $tint ) as $back )
                {
                    $under = AdminTokenContrastTest::opaque( $t[$back], $t['--a4-card'] );
                    $ratio = AdminTokenContrastTest::ratio( $t[$front], $under );
                    if ( $ratio < 3.0 )
                        $failures[] = sprintf( '%s: %s on %s is %.2F:1', $mode, $front, $back, $ratio );
                }
            }
        }
        $this->assertSame( array(), $failures );
    }

    /** VS-03 */
    public function testTheTemplatesDrawTheMarks()
    {
        $part = self::file( 'design/admin4/templates/parts/visibility_status.tpl' );
        $this->assertStringContainsString( "cond( \$status_node.is_hidden, 'hidden', \$status_node.is_invisible, 'hidden-by-parent', 'visible' )", $part,
            'hidden wins over hidden by a parent: a hidden node is invisible too' );
        $this->assertStringContainsString( '<span class="a4-mark is-{$a4_vis_state}" role="img" aria-label="{$a4_vis_name|wash}" title="{$a4_vis_title|wash}"></span>', $part );
        $this->assertStringContainsString( '<span class="a4-mark is-visible" title="{$a4_vis_title|wash}" aria-hidden="true"></span>', $part );
        $this->assertStringContainsString( '<span class="a4-mark is-{$a4_vis_state}" aria-hidden="true"></span>{$a4_vis_name|wash}</span>', $part,
            'in the badge the mark is beside its text and hidden from screen readers' );
        $this->assertStringContainsString( '{undef $a4_vis_state $a4_vis_name $a4_vis_title}', $part );
        foreach ( array( 'Visible', 'Hidden', 'Hidden by a parent', 'Visible: visitors of the site can see it.',
                         'Hidden: visitors of the site do not see it.', 'An item above it is hidden, so visitors of the site do not see it.' ) as $text )
        {
            $this->assertStringContainsString( "'$text'|i18n( '" . self::CONTEXT . "' )", $part );
        }

        $head = self::file( 'design/admin4/templates/node/view/a4_full_body.tpl' );
        $this->assertStringContainsString( "{include uri='design:parts/visibility_status.tpl' status_node=\$node status_badge=true() status_quiet=false()}", $head );
        $this->assertStringContainsString( "fetch( 'user', 'account_status', hash( 'user_id', \$node.contentobject_id ) )", $head );
        $this->assertStringContainsString( '<span class="a4-mark is-disabled" aria-hidden="true"></span>', $head );
        $this->assertStringContainsString( '<span class="a4-mark is-locked" aria-hidden="true"></span>', $head );
        $this->assertStringNotContainsString( 'a4-badge-warn', $head );

        $browse = self::file( 'design/admin4/templates/content/browse_mode_list.tpl' );
        $this->assertStringContainsString( "{include uri='design:parts/visibility_status.tpl' status_node=\$Nodes.item status_badge=false() status_quiet=true()}", $browse );

        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $tree = self::file( "design/$design/templates/contentstructuremenu/show_content_structure.tpl" );
            // decorative: the "(Hidden)" text after the mark names the state
            $this->assertSame( 2, substr_count( $tree, '<span class="a4-mark is-hidden" aria-hidden="true" title=' ), "$design: both branches of the static tree" );
            $this->assertSame( 2, substr_count( $tree, '<span class="a4-mark is-hidden-by-parent" aria-hidden="true" title=' ), $design );
        }
        // admin4 keeps that text for screen readers instead of display: none
        $pagelayout = AdminBaseStylesheetsTest::rules( preg_replace( '#/\*.*?\*/#s', '', self::file( 'design/admin4/stylesheets/pagelayout.css' ) ) );
        $hiddenText = $pagelayout['div#contentstructure span.node-hidden, div#contentstructure span.node-hiddenbyparent'];
        $this->assertArrayNotHasKey( 'display', $hiddenText );
        $this->assertSame( 'absolute', $hiddenText['position'] );
        $this->assertSame( 'rect(0 0 0 0)', $hiddenText['clip'] );
    }

    /** VS-04 */
    public function testTheScriptsDrawTheMarks()
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/visibility_status.js' ) . ' '
                                       . escapeshellarg( self::root() . "/design/$design/javascript/ezajaxsubitems_expdatatable.js" ) . ' '
                                       . escapeshellarg( self::root() . "/design/$design/javascript/contentstructuremenu_dynamic.js" ) . ' 2>&1' );
            $s = array();
            foreach ( array_filter( explode( "\n", $out ) ) as $line )
            {
                $row = json_decode( $line, true );
                $this->assertIsArray( $row, "$design: the harness ran: $out" );
                $s[$row['script'] . ': ' . $row['name']] = $row;
            }
            $this->assertCount( 13, $s, $design );

            // the table: a mark before the icon and name of every row with the flags
            $this->assertStringStartsWith( '<span class="a4-subitem-name"><span class="a4-mark is-visible" title="V title" aria-hidden="true"></span><a href="/n"',
                $s['subitems: visible']['cell'] );
            $this->assertStringContainsString( '<span class="a4-mark is-hidden" role="img" aria-label="Versteckt" title="H title"></span><a href',
                $s['subitems: hidden']['cell'], 'is_hidden wins over is_invisible' );
            $this->assertStringContainsString( '<span class="a4-mark is-hidden-by-parent" role="img" aria-label="Versteckt durch übergeordneten Eintrag" title="P title &#34;quoted&#34;">',
                $s['subitems: hidden by parent']['cell'], 'the texts are escaped for the attribute' );
            $this->assertStringNotContainsString( 'a4-mark', $s['subitems: no flags']['cell'], 'an older server: no mark rather than a wrong one' );
            $this->assertSame( 'Text', $s['subitems: no flags']['visibility'] );
            $this->assertStringContainsString( 'Tom &amp; Jerry</a></span>', $s['subitems: visible']['cell'], 'the encoded name stays encoded once' );

            // the table: a badge after the name of a disabled or locked account, none for an enabled one
            $this->assertStringEndsWith( '</a><span class="a4-badge" title="D title"><span class="a4-mark is-disabled" aria-hidden="true"></span>Deaktiviert</span></span>',
                $s['subitems: disabled user']['cell'] );
            $this->assertStringEndsWith( '</a><span class="a4-badge is-warn" title="L title"><span class="a4-mark is-locked" aria-hidden="true"></span>Gesperrt</span></span>',
                $s['subitems: locked user']['cell'] );
            $this->assertStringNotContainsString( 'a4-badge', $s['subitems: enabled user']['cell'] );
            $this->assertSame( '<span class="a4-subitem-status"><span class="a4-mark is-hidden-by-parent" aria-hidden="true"></span>Text</span>',
                $s['subitems: hidden by parent']['visibility'] );
            $this->assertStringContainsString( 'aria-label="Hidden" title="Hidden: visitors of the site do not see it."', $s['subitems: no labels']['cell'] );

            // the tree: a mark after the name, only where something differs
            $this->assertStringNotContainsString( 'a4-mark', $s['tree: visible']['html'] );
            $this->assertStringContainsString( '<span class="node-name-hidden">Node</span><span class="a4-mark is-hidden" title="Versteckt" aria-hidden="true"></span><span class="node-hidden"> (Versteckt)</span>',
                $s['tree: hidden']['html'], 'decorative; the text after it is read once' );
            $this->assertStringContainsString( '<span class="a4-mark is-hidden-by-parent" title="Vom übergeordneten Element versteckt" aria-hidden="true"></span><span class="node-hiddenbyparent">',
                $s['tree: hidden by parent']['html'] );
            $this->assertStringContainsString( '<span class="a4-mark is-disabled" role="img" aria-label="Konto deaktiviert" title="Konto deaktiviert"></span></a>',
                $s['tree: disabled user']['html'] );
            $this->assertStringNotContainsString( 'a4-mark', $s['tree: locked user']['html'], 'the tree marks only disabled accounts' );
        }
    }

    /** VS-05 */
    public function testTheTextsAreTranslatedOnce()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $tpl = self::file( "design/$design/templates/children_detailed.tpl" );
            foreach ( array( 'visible', 'hidden', 'hidden_by_parent', 'visible_title', 'hidden_title', 'hidden_by_parent_title',
                             'disabled', 'disabled_title', 'locked', 'locked_title' ) as $key )
            {
                $this->assertMatchesRegularExpression( '/\b' . $key . ': "\{\'[^\']+\'\|i18n\( \'' . preg_quote( self::CONTEXT, '/' ) . '\' \)\|wash\(\'javascript\'\)\}"/', $tpl, "$design: $key" );
            }
            $this->assertStringContainsString( "\"account_disabled\":\"{'Account disabled'|i18n('design/admin/contentstructuremenu')|wash(xhtml)|wash(javascript)}\"",
                self::file( "design/$design/templates/contentstructuremenu/content_structure_menu_dynamic.tpl" ) );
        }

        $texts = array( self::CONTEXT => array( 'Visible', 'Hidden', 'Hidden by a parent', 'Visible: visitors of the site can see it.',
                                                'Hidden: visitors of the site do not see it.', 'An item above it is hidden, so visitors of the site do not see it.',
                                                'Disabled', 'Account disabled: this user cannot log in.', 'Locked', 'Locked after too many failed logins.' ),
                        'design/admin/contentstructuremenu' => array( 'Account disabled', 'Hidden', 'Hidden by superior' ) );
        foreach ( array( 'ger-DE', 'eng-US' ) as $locale )
        {
            $dom = new DOMDocument();
            $this->assertTrue( $dom->load( self::root() . "/share/translations/$locale/translation.ts" ) );
            $xpath = new DOMXPath( $dom );
            foreach ( $texts as $context => $sources )
            {
                foreach ( $sources as $source )
                {
                    $found = $xpath->query( "//context[name='$context']/message[source=" . self::xpathString( $source ) . ']/translation' );
                    $this->assertSame( 1, $found->length, "$locale, $context: '$source' once" );
                    $this->assertNotSame( '', trim( $found->item( 0 )->textContent ), "$locale: '$source'" );
                }
            }
        }
        $dom = new DOMDocument();
        $dom->load( self::root() . '/share/translations/ger-DE/translation.ts' );
        $german = ( new DOMXPath( $dom ) )->query( "//context[name='" . self::CONTEXT . "']/message[source='Hidden by a parent']/translation" )->item( 0 )->textContent;
        $this->assertSame( 'Versteckt durch übergeordneten Eintrag', $german, 'the same word as Hidden (Versteckt)' );
        // the Visibility column (hidden_status_string, kernel/content) and the tree say the same in German
        $xpath = new DOMXPath( $dom );
        foreach ( array( 'kernel/content', 'design/admin/contentstructuremenu', self::CONTEXT ) as $context )
        {
            $this->assertSame( 'Versteckt durch übergeordneten Eintrag',
                $xpath->query( "//context[name='$context']/message[source='Hidden by superior']/translation" )->item( 0 )->textContent, $context );
        }
        $this->assertSame( '(Versteckt durch übergeordneten Eintrag)',
            $xpath->query( "//context[name='design/admin/contentstructuremenu']/message[source='(Hidden by parent)']/translation" )->item( 0 )->textContent );
        $this->assertStringNotContainsString( 'übergeordneten Element versteckt', file_get_contents( self::root() . '/share/translations/ger-DE/translation.ts' ) );
    }

    private static function xpathString( $s )
    {
        return strpos( $s, "'" ) === false ? "'$s'" : 'concat(\'' . str_replace( "'", "',\"'\",'", $s ) . '\')';
    }
}
