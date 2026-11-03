<?php
/**
 * The overview and detail pages read as dense table rows, in admin and admin4 alike: the card lists (.a4-cards)
 * inside .a4-page are rows with a rule, zebra and hover, the row actions View, Edit, Open, Assign and Copy are icon
 * buttons, tables and the facts of a detail card are dense, and the attributes of class/view are rows. One density,
 * no switch (AdminDensityTest): the block "List rows" of components.css applies to every page.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminListRowsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminListRowsTest extends PHPUnit\Framework\TestCase
{
    /** The card lists that are no data lists and stay cards */
    const CARD_LIST_ROWS = '.a4-page ul.a4-cards:not(.a4-card-body, [aria-labelledby="account-actions-title"] *)';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /**
     * The block "List rows" of components.css, without comments.
     *
     * @return string
     */
    private static function block()
    {
        $css = AdminFiles::read( self::root() . '/design/admin4/stylesheets/components.css' );
        $start = strpos( $css, '/* ---- List rows:' );
        $end = strpos( $css, '/* ---- Forced colors (Windows high contrast) and touch' );
        \PHPUnit\Framework\Assert::assertNotFalse( $start, 'components.css has the block "List rows"' );
        \PHPUnit\Framework\Assert::assertNotFalse( $end );
        \PHPUnit\Framework\Assert::assertLessThan( $end, $start, 'the block comes before the forced colors block, so forced colors still win' );
        return preg_replace( '#/\*.*?\*/#s', '', substr( $css, $start, $end - $start ) );
    }

    public function testAdminAndAdmin4ShareTheBlock()
    {
        $this->assertFileEquals( self::root() . '/design/admin4/stylesheets/components.css', self::root() . '/design/admin/stylesheets/components.css' );
    }

    public function testCardListsAreRowsInsideThePageOnly()
    {
        $block = self::block();
        $this->assertStringContainsString( self::CARD_LIST_ROWS . ' > li.a4-card {', $block );
        $this->assertMatchesRegularExpression( '/' . preg_quote( self::CARD_LIST_ROWS, '/' ) . ' > li\.a4-card \{[^}]*border-radius: 0;[^}]*box-shadow: none;/', $block, 'a row has no card frame' );
        $this->assertStringContainsString( 'grid-template-columns: subgrid;', $block, 'the rows share the column widths of the list' );
        foreach ( preg_split( '/\}/', $block ) as $rule )
        {
            // the first rule of a media block starts with "@media ... {": check its selector too
            $rule = preg_replace( '/^\s*@media[^{]*\{/', '', $rule );
            $selectors = trim( strstr( $rule, '{', true ) ?: '' );
            if ( $selectors === '' )
                continue;
            foreach ( explode( ',', preg_replace( '/\([^()]*(?:\([^()]*\)[^()]*)*\)/', '()', $selectors ) ) as $selector )
            {
                $selector = trim( $selector );
                if ( $selector !== '' )
                    $this->assertStringStartsWith( '.a4-page ', $selector, 'every rule of the block is scoped to .a4-page: ' . $selector );
            }
        }
    }

    public function testRowActionIconsExist()
    {
        $block = self::block();
        $this->assertGreaterThan( 0, preg_match_all( '#url\(\.\./\.\./\.\./share/icons/bootstrap-icons/svg/([a-z0-9-]+\.svg)\)#', $block, $matches ) );
        foreach ( array_unique( $matches[1] ) as $file )
            $this->assertFileExists( self::root() . '/share/icons/bootstrap-icons/svg/' . $file );
        $this->assertStringContainsString( 'font-size: 0;', $block, 'the label stays in the button for screen readers' );
        $this->assertMatchesRegularExpression( '/forced-color-adjust: none; background-color: ButtonText;/', $block, 'the icons show in forced colors' );
    }

    public function testHiddenFactsStayReadable()
    {
        $block = self::block();
        $this->assertStringNotContainsString( 'display: none', $block, 'the rows hide nothing from screen readers' );
        $this->assertStringContainsString( 'clip-path: inset(50%);', $block, 'what class/grouplist hides is hidden visually only' );
    }

    public function testDetailPagesAreDense()
    {
        $block = self::block();
        $this->assertStringContainsString( '.a4-page :is(.a4-card:not(li), .a4-card:not(li) > .a4-card-body, ol.a4-cards > li.a4-card) > .a4-facts {', $block, 'the facts of a detail card are one line' );
        $this->assertMatchesRegularExpression( '/> \.a4-facts dd \{[^}]*overflow-wrap: anywhere;/', $block, 'a long value (an INI path) wraps instead of overflowing' );
        $this->assertStringContainsString( '.a4-page ol.a4-cards > li.a4-card {', $block, 'the attributes of class/view are rows' );
        $this->assertStringContainsString( '.a4-page .a4-section > p.a4-empty {', $block, 'only the one-line empty state of a section is slim' );
    }

    public function testOnlyFourFactsTakeEqualColumns()
    {
        $block = self::block();
        // four facts line up in equal columns; from five they stay running text (the columns broke words)
        $this->assertStringContainsString( '> .a4-facts:has(> div:nth-child(4)):not(:has(> div:nth-child(5))) {', $block );
        $this->assertStringNotContainsString( '> .a4-facts:has(> div:nth-child(4)) {', $block );
    }

    public function testAnAddressTakesTheWholeFirstLineOfItsRow()
    {
        $block = preg_replace( '/\s+/', ' ', self::block() );
        $this->assertStringContainsString( '.a4-page #url-list > li.a4-card > .a4-card-head > .a4-card-title { grid-column: 1 / -1; grid-row: 1; }', $block );
        $this->assertStringContainsString( '.a4-page #url-list > li.a4-card > .a4-facts { grid-column: 1 / 3; grid-row: 2; }', $block );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $this->assertStringContainsString( '<ul class="a4-cards" id="url-list">', AdminFiles::read( self::root() . '/design/' . $design . '/templates/url/list.tpl' ), $design );
        }
    }

    public function testTouchKeepsFullSizeButtons()
    {
        $block = self::block();
        $this->assertSame( 1, preg_match( '/@media \(pointer: coarse\) \{(.*?)\n\}/s', $block, $touch ) );
        $this->assertStringContainsString( self::CARD_LIST_ROWS . ' .a4-btn-small,', $touch[1], 'the touch rule is as specific as the row rule it overrides' );
        $this->assertStringContainsString( 'min-width: 34px; min-height: 34px;', $touch[1], 'icon buttons get a touch target too' );
    }

    public function testClassGroupColumnsDoNotDependOnTheOrderOfTheFacts()
    {
        $block = self::block();
        $this->assertDoesNotMatchRegularExpression( '/\.a4-facts > div:not\(:nth-child/', $block, 'class/grouplist hides its facts by class, not by position' );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $tpl = AdminFiles::read( self::root() . "/design/$design/templates/class/grouplist.tpl" );
            foreach ( array( 'a4-classgroup-last', 'a4-classgroup-modified', 'a4-classgroup-removing', 'a4-classgroup-classes' ) as $hook )
            {
                $this->assertStringContainsString( $hook, $tpl, "$design/class/grouplist.tpl marks $hook" );
                if ( $hook !== 'a4-classgroup-modified' )
                    $this->assertStringContainsString( $hook, $block, "the block uses $hook" );
            }
        }
    }

    /**
     * A row action the block draws as an icon only (View, Edit, Open, Assign, Copy) has a title as tooltip: the
     * screen reader name is the hidden label, sighted users see the title.
     */
    public function testIconOnlyRowActionsHaveATitle()
    {
        $root = self::root();
        $missing = array();
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            foreach ( AdminFiles::files( "$root/design/$design/templates/{,*/,*/*/}*.tpl", GLOB_BRACE ) as $file )
            {
                $tpl = AdminFiles::read( $file );
                if ( !preg_match_all( '#<(a|button)\s+class="a4-btn a4-btn-small[^"]*"([^>]*)>(.*?)</\1>#s', $tpl, $tags, PREG_SET_ORDER ) )
                    continue;
                foreach ( $tags as $tag )
                {
                    list( , , $attrs, $content ) = $tag;
                    // the block leaves external links and buttons with an icon of their own (svg, .a4-icon) as they are
                    if ( strpos( $attrs, 'title=' ) !== false || strpos( $attrs, 'target="_blank"' ) !== false
                         || strpos( $content, '<svg' ) !== false || strpos( $content, 'a4-icon' ) !== false )
                        continue;
                    if ( preg_match( "#/view/|/edit/|/edit'|/edit_|groupedit|group_edit|functions\\.classlist|functions\\.workflowlist|state/group/|\\\$group_url|role/assign|section/assign|/copy/|CopyClassButton|settings_path#", $attrs ) )
                        $missing[] = substr( $file, strlen( $root ) + 1 ) . ': ' . trim( substr( $attrs, 0, 80 ) );
                }
            }
        }
        $this->assertSame( array(), $missing, 'an icon-only row action without title' );
    }
}
