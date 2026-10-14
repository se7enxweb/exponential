<?php
/**
 * The card component of the admin designs (.a4-card in components.css): its parts (head, body, foot, actions),
 * the flush variant for a table to the edges, the link card and the busy state; and that admin4's cards use the
 * card's own head instead of a section head.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminCardsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminCardsTest extends PHPUnit\Framework\TestCase
{
    private static function css( $design )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . "/design/$design/stylesheets/components.css" );
    }

    public function testBothCopiesDefineTheCardParts()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $css = self::css( $design );
            foreach ( array( '.a4-card-head', '.a4-card-body', '.a4-card-foot', '.a4-card-actions', '.a4-card.is-flush',
                             '.a4-card.is-link', '.a4-card.is-busy' ) as $part )
                $this->assertStringContainsString( $part, $css, "$design/components.css has no $part" );
        }
    }

    public function testTheFootAndTheFlushHeadReadTheCardsPadding()
    {
        // --a4-card-pad-x/-y come from a density setting when one defines them; else the spacing scale the card uses
        $css = self::css( 'admin4' );
        $x = 'var(--a4-card-pad-x, var(--a4-space-4))';
        $y = 'var(--a4-card-pad-y, var(--a4-space-3))';
        $this->assertMatchesRegularExpression( '/\.a4-card-foot \{[^}]*margin: var\(--a4-space-3\) calc\(-1 \* ' . preg_quote( $x, '/' ) . '\) calc\(-1 \* ' . preg_quote( $y, '/' ) . '\);[^}]*padding: var\(--a4-space-3\) ' . preg_quote( $x, '/' ) . ';/', $css );
        $this->assertStringContainsString( '.a4-card.is-flush > .a4-card-head { padding: ' . $y . ' ' . $x . ';', $css );
        // the density setting (normal and compact) defines the card padding tokens; the card component only reads them
        $this->assertSame( 2, substr_count( $css, '--a4-card-pad-x:' ), 'the density setting defines the card padding tokens, not the card component' );
    }

    public function testTheLinkCardRingsTheCardAndKeepsOtherControlsClickable()
    {
        $css = self::css( 'admin4' );
        $this->assertStringContainsString( '.a4-card.is-link .a4-card-title a::after { content: ""; position: absolute; inset: 0;', $css );
        $this->assertStringContainsString( '.a4-card.is-link:has(.a4-card-title a:focus-visible) { outline: 3px solid var(--a4-ring);', $css );
        $this->assertStringContainsString( '.a4-card.is-link .a4-card-body :is(a, button, input, select, textarea, label),', $css );
        $this->assertStringNotContainsString( ':not(.a4-card-title a)', $css, 'a complex :not() would drop the whole rule in older engines' );
        // without :has() the title link keeps its own focus ring
        $this->assertMatchesRegularExpression( '/@supports selector\(:has\(\*\)\) \{\s*\.a4-card\.is-link \.a4-card-title a:focus-visible \{ outline-color: transparent; \}/', $css );
    }

    public function testTheBusyCardStopsItsSpinnerForReducedMotion()
    {
        $css = self::css( 'admin4' );
        $this->assertStringContainsString( 'animation: a4-card-busy', $css );
        // the state needs aria-busy too, and the veil lies above the controls a link card lifts (z-index 1)
        $this->assertMatchesRegularExpression( '/\.a4-card\.is-busy\[aria-busy="true"\]::after \{[^}]*z-index: var\(--a4-z-card-veil\);/', $css );
        $this->assertStringNotContainsString( '.a4-card.is-busy::', $css );
        $this->assertMatchesRegularExpression( '/@media \(prefers-reduced-motion: reduce\) \{\s*\.a4-card\.is-busy\[aria-busy="true"\]::before \{ animation: none;/', $css );
        $this->assertMatchesRegularExpression( '/@media \(forced-colors: active\) \{\s*\.a4-card\.is-busy\[aria-busy="true"\]::before/', $css );
    }

    /** A card names its title in its own head; a4-section-head as the first part of a card was the old way. */
    public function testAdmin4CardsUseTheCardHead()
    {
        $root = dirname( __DIR__, 3 );
        $found = array();
        $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( "$root/design/admin4/templates", FilesystemIterator::SKIP_DOTS ) );
        foreach ( $files as $file )
        {
            if ( substr( (string)$file, -4 ) !== '.tpl' )
                continue;
            if ( preg_match( '/class="a4-card"[^>]*>\s*<div class="a4-section-head">/', (string)file_get_contents( (string)$file ) ) )
                $found[] = substr( (string)$file, strlen( $root ) + 1 );
        }
        $this->assertSame( array(), $found, 'Cards that open with a4-section-head: use a4-card-head (and a4-card-body for the rest)' );
    }
}
