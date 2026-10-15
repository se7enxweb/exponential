<?php
/**
 * The compact view of Setup > Cronjobs: one table row per part, chosen by the user preference admin_cronjobs_view
 * (compact by default, cards for the detailed view). Both administration designs carry it, its rows have the
 * attributes the search, the filter and the console use on the cards, and the console's filter looks at them.
 *
 * Reads the templates; no database, no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */
class expCronjobsCompactViewTest extends PHPUnit\Framework\TestCase
{
    /**
     * @return array[]
     */
    public static function designs()
    {
        return array( 'admin' => array( 'admin' ), 'admin4' => array( 'admin4' ) );
    }

    private function template( $design )
    {
        $file = __DIR__ . '/../../../../design/' . $design . '/templates/setup/cronjobs.tpl';
        $this->assertFileExists( $file );
        return file_get_contents( $file );
    }

    /**
     * @dataProvider designs
     */
    #[\PHPUnit\Framework\Attributes\DataProvider( 'designs' )]
    public function testTheViewIsAPreferenceAndCompactIsTheDefault( $design )
    {
        $tpl = $this->template( $design );
        $this->assertStringContainsString( "cond( ezpreference( 'admin_cronjobs_view' )|eq( 'cards' ), 'cards', 'compact' )", $tpl );
        // Written by a POST (the buttons send the page's form, with its token, to user/preferences/set), never a GET link.
        $this->assertStringNotContainsString( '/user/preferences/set/admin_cronjobs_view', $tpl );
        $this->assertStringContainsString( '<input type="hidden" name="Key" value="admin_cronjobs_view" />', $tpl );
        $this->assertStringContainsString( '<input type="hidden" name="RedirectURIAfterSet" value="setup/cronjobs" />', $tpl );
        foreach ( array( 'compact', 'cards' ) as $value )
        {
            $this->assertMatchesRegularExpression(
                '#<button type="submit" formaction=\{\'/user/preferences/set\'\|ezurl\} name="Value" value="' . $value . '" aria-pressed="\{if \$cronjob_view\|eq\(\'' . $value . '\'\)\}true\{else\}false\{/if\}">#',
                $tpl );
        }
        $this->assertStringContainsString( '<span class="a4-tabs is-segmented" role="group"', $tpl );
    }

    /**
     * @dataProvider designs
     */
    #[\PHPUnit\Framework\Attributes\DataProvider( 'designs' )]
    public function testRowsCarryWhatTheFilterAndTheConsoleUse( $design )
    {
        $tpl = $this->template( $design );
        $this->assertSame( 1, preg_match( '#<table class="a4-table" id="cronjob-table">(.*?)</table>#s', $tpl, $table ) );
        foreach ( array( 'class="a4-setup-part-row', 'data-part=', 'data-state=', 'data-attention=', 'data-search=',
                         'name="LaunchCronjobButton"', 'a4-setup-run', 'data-blocked=', 'name="PauseCronjobButton"',
                         'name="ResumeCronjobButton"', '{foreach $cronjob_parts as $cronjob_part}',
                         '{foreach $cronjob_part.scripts as $cronjob_script}', '{$cronjob_script.description|wash}' ) as $needle )
            $this->assertStringContainsString( $needle, $table[1], $needle );
        // The filter of the page looks at the rows of the table as well as at the cards.
        $this->assertStringContainsString( "'#cronjob-list > .a4-card, #cronjob-table .a4-setup-part-row'", $tpl );
        // Every variable the rows define is dropped again.
        $this->assertStringContainsString( '{undef $cronjob_paused $cronjob_blocked $cronjob_runnable}', $table[1] );
    }

    public function testBothDesignsDrawTheSameTable()
    {
        $this->assertSame( 1, preg_match( '#<table class="a4-table" id="cronjob-table">.*?</table>#s', $this->template( 'admin' ), $admin ) );
        $this->assertSame( 1, preg_match( '#<table class="a4-table" id="cronjob-table">.*?</table>#s', $this->template( 'admin4' ), $admin4 ) );
        $this->assertSame( $admin[0], $admin4[0] );
    }

    /**
     * @dataProvider designs
     */
    #[\PHPUnit\Framework\Attributes\DataProvider( 'designs' )]
    public function testTheRunAndFilterControlsAreFoldedAndTheScriptsOpenInARowOfTheirOwn( $design )
    {
        $tpl = $this->template( $design );
        // A <details> without "open": the page opens on the overview and the list.
        $this->assertSame( 1, preg_match( '#<details class="a4-fold" id="cronjob-controls">#', $tpl ) );
        $this->assertStringContainsString( 'id="cronjob-siteaccess" name="CronjobSiteAccess"', $tpl );
        // The scripts of a row are a <details> of their own; the search opens it when it matches a script.
        $this->assertStringContainsString( '<details class="a4-setup-compact-scripts">', $tpl );
        $this->assertStringContainsString( "card.querySelector( '.a4-setup-scripts, .a4-setup-compact-scripts' )", $tpl );
        // With javascript they open in a row of their own below the part, so several can be open at once.
        $this->assertStringContainsString( '<tr class="a4-setup-compact-detail" data-part="{$cronjob_part.name|wash}" hidden>', $tpl );
        $this->assertStringContainsString( "compactTable.classList.add( 'is-scripted' );", $tpl );
        // Each script says where it comes from: the kernel or the extension it belongs to, and its directory.
        $this->assertStringContainsString( '{if $cronjob_script.origin|eq(\'kernel\')}', $tpl );
        $this->assertStringContainsString( '{$cronjob_script.directory|wash}/', $tpl );
        // In columns: origin, the path in front of the file name, then what it does; in the cell and in the row below.
        $this->assertSame( 2, preg_match_all( '#<li(?: role="listitem")?><span class="a4-setup-compact-origin">.*?</span><span class="a4-setup-compact-file">.*?\{\$cronjob_script\.directory\|wash\}/</span>\{/if\}<code>\{\$cronjob_script\.name\|wash\}</code>.*?<span class="a4-setup-compact-desc">\{\$cronjob_script\.description\|wash\}</span></li>#', $tpl ) );
        // The grid drops the list semantics in some screen readers; the row below says it is a list.
        $this->assertStringContainsString( '<td colspan="5"><ul role="list">', $tpl );
    }
}
