<?php
/**
 * admin4's notices and toasts. A notice shows its state by an icon too, not by colour alone (WCAG 1.4.1); the
 * classic div.message-* that modules render after an action carry a role (an error is an alert, the rest a
 * status), and the page has one pair of live regions for toasts, the short feedback of an action done in the page
 * (window.a4Toast in admin4.js).
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminNoticesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminNoticesTest extends PHPUnit\Framework\TestCase
{
    const STATES = array( 'ok' => 'check-circle-fill', 'bad' => 'x-circle-fill', 'warn' => 'exclamation-triangle-fill', 'info' => 'info-circle-fill' );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function templates()
    {
        $files = array();
        foreach ( array( 'design/admin4/templates', 'design/admin4/override/templates' ) as $dir )
        {
            if ( !is_dir( self::root() . '/' . $dir ) )
                continue;
            foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . '/' . $dir, FilesystemIterator::SKIP_DOTS ) ) as $file )
            {
                if ( substr( (string)$file, -4 ) === '.tpl' )
                    $files[] = (string)$file;
            }
        }
        return $files;
    }

    public function testEveryStateOfANoticeHasAnIconThatExists()
    {
        $css = (string)file_get_contents( self::root() . '/design/admin4/stylesheets/components.css' );
        foreach ( self::STATES as $state => $icon )
        {
            $this->assertMatchesRegularExpression( '/\.a4-notice\.is-' . $state . '::before \{[^}]*mask-image: url\([^)]*\/' . preg_quote( $icon, '/' ) . '\.svg\)/', $css,
                "a notice in state $state shows no $icon icon" );
            $this->assertFileExists( self::root() . "/share/icons/bootstrap-icons/svg/$icon.svg" );
        }
        $this->assertStringContainsString( '.a4-notice-title', $css );
        // the shared ::before rule outweighs the state rules: a mask shorthand there would reset their image
        $start = strpos( $css, '.a4-notice:is(.is-ok, .is-bad, .is-warn, .is-info):not(.no-icon)::before {' );
        $this->assertNotFalse( $start );
        $shared = substr( $css, $start, strpos( $css, '}', $start ) - $start );
        $this->assertDoesNotMatchRegularExpression( '/(^|[;{\\s])(-webkit-)?mask\\s*:/', $shared );
        // in forced colors the mask is drawn in the text colour, else it would vanish into the canvas
        $this->assertMatchesRegularExpression( '/@media \(forced-colors: active\) \{[^@]*\.a4-notice[^{]*::before \{[^}]*forced-color-adjust: none/', $css );
    }

    public function testToastsHaveTheirComponentAndLiveRegions()
    {
        $css = (string)file_get_contents( self::root() . '/design/admin4/stylesheets/components.css' );
        foreach ( array( '.a4-toasts {', '.a4-toasts > [aria-live] {', '.a4-toast {', '.a4-toast .a4-toast-close {' ) as $rule )
            $this->assertStringContainsString( $rule, $css );
        $this->assertMatchesRegularExpression( '/@media \(prefers-reduced-motion: reduce\) \{\s*\.a4-toast \{ animation: none; \}/', $css );

        foreach ( array( 'admin4', 'admin4l' ) as $design )
        {
            $layout = (string)file_get_contents( self::root() . "/design/$design/templates/pagelayout.tpl" );
            $this->assertMatchesRegularExpression( '/<div id="a4-toasts" aria-live="polite"[^>]*data-close-label="\{\'Dismiss\'\|i18n\( \'design\/admin\/pagelayout\' \)\|wash\}"/', $layout );
            $this->assertMatchesRegularExpression( '/<div id="a4-toasts-alert" aria-live="assertive"/', $layout );
            // both regions in one stack, so two toasts never cover each other
            $this->assertMatchesRegularExpression( '/<div class="a4-toasts">\\s*<div id="a4-toasts" [^>]*><\\/div>\\s*<div id="a4-toasts-alert" [^>]*><\\/div>\\s*<\\/div>/', $layout );
        }
        // above the debug bar, on the layer scale; an error stays until it is dismissed
        $this->assertStringContainsString( 'z-index: var(--a4-z-toast);', $css );
        $this->assertStringContainsString( 'body.exp-debug-bar .a4-toasts {', $css );
        $this->assertStringContainsString( "kind !== 'bad'", (string)file_get_contents( self::root() . '/design/admin4/javascript/admin4.js' ) );

        $js = (string)file_get_contents( self::root() . '/design/admin4/javascript/admin4.js' );
        $this->assertStringContainsString( 'window.a4Toast = function (message, kind', $js );
        $start = strpos( $js, 'window.a4Toast = function' );
        $toast = substr( $js, $start, strpos( $js, '})();', $start ) - $start );
        // the message is text, never markup
        $this->assertStringContainsString( 'text.textContent = String(message);', $toast );
        $this->assertStringNotContainsString( 'innerHTML', $toast );
        // the focus stays where it is; only the close button gives it back to the opener
        $this->assertDoesNotMatchRegularExpression( '/\.focus\(\)/', str_replace( 'opener.focus()', '', $toast ) );
    }

    public function testTheClassicMessagesCarryARole()
    {
        $missing = array();
        foreach ( self::templates() as $file )
        {
            preg_match_all( '/<div\s+class="message-(error|warning|feedback|confirmation|information)[^"]*"([^>]*)>/', (string)file_get_contents( $file ), $m, PREG_SET_ORDER );
            foreach ( $m as $div )
            {
                // a warning that reports a failed action (did not validate, could not be stored) is an alert too
                $expected = $div[1] === 'error' ? 'alert' : 'status';
                if ( strpos( $div[2], 'role="' . $expected . '"' ) === false && !( $div[1] === 'warning' && strpos( $div[2], 'role="alert"' ) !== false ) )
                    $missing[] = substr( $file, strlen( self::root() ) + 1 ) . ': message-' . $div[1] . ' without role="' . $expected . '"';
            }
        }
        $this->assertSame( array(), $missing );
    }

    public function testNoNoticeStartsWithAHandWrittenGlyph()
    {
        $found = array();
        foreach ( self::templates() as $file )
        {
            if ( preg_match_all( '/a4-notice[^>]*>\s*<(strong|b|span)[^>]*>\s*(!|i|&#10003;|&#x2713;|\x{2713}|\x{2714})\s*<\//u', (string)file_get_contents( $file ), $m ) )
                $found[] = substr( $file, strlen( self::root() ) + 1 );
        }
        $this->assertSame( array(), $found, 'the state class draws the icon; a "!" or tick beside it shows the state twice' );
        $css = (string)file_get_contents( self::root() . '/design/admin4/stylesheets/components.css' );
        $this->assertStringContainsString( 'li.a4-notice { list-style: none; }', $css );
    }

    public function testToastsStayFewAndGiveTheFocusBack()
    {
        $js = (string)file_get_contents( self::root() . '/design/admin4/javascript/admin4.js' );
        $this->assertStringContainsString( 'window.a4Toast = function (message, kind, opener, delay)', $js );
        $this->assertStringContainsString( 'timer = setTimeout(remove, delay > 0 ? delay : DELAY)', $js, 'a toast with an action may stay longer' );
        $this->assertStringContainsString( 'MAX = 3', $js );
        $this->assertStringContainsString( "t.textContent === String(message)", $js );
        $this->assertStringContainsString( 'opener.focus()', $js );
        // leaving the toast with the focus while the pointer is still on it does not start the timer
        $this->assertStringContainsString( "hold(toast.matches(':hover'))", $js );
    }

    public function testACopyThatFailsSaysSo()
    {
        $body = (string)file_get_contents( self::root() . '/design/admin4/templates/node/view/a4_full_body.tpl' );
        $this->assertSame( substr_count( $body, 'data-a4-copy=' ), substr_count( $body, 'data-a4-copy-failed=' ) );
        $js = (string)file_get_contents( self::root() . '/design/admin4/javascript/admin4.js' );
        $this->assertStringContainsString( "window.a4Toast(chip.getAttribute('data-a4-copy-failed'), 'bad', chip)", $js );
        $menu = (string)file_get_contents( self::root() . '/design/admin4/templates/visual/menuconfig.tpl' );
        $this->assertStringContainsString( "window.a4Toast(button.getAttribute('data-copy-failed'), 'bad', button)", $menu );
        foreach ( array( 'eng-US', 'ger-DE' ) as $lang )
        {
            $ts = (string)file_get_contents( self::root() . "/share/translations/$lang/translation.ts" );
            $this->assertGreaterThanOrEqual( 2, substr_count( $ts, '<source>Could not copy</source>' ), $lang );
        }
    }

    public function testCopyFeedbackIsAlsoAToast()
    {
        $body = (string)file_get_contents( self::root() . '/design/admin4/templates/node/view/a4_full_body.tpl' );
        $this->assertSame( substr_count( $body, 'data-a4-copy=' ), substr_count( $body, 'data-a4-copied=' ), 'every copy chip names its toast' );
        $js = (string)file_get_contents( self::root() . '/design/admin4/javascript/admin4.js' );
        $this->assertStringContainsString( "window.a4Toast(chip.getAttribute('data-a4-copied')", $js );
        $menu = (string)file_get_contents( self::root() . '/design/admin4/templates/visual/menuconfig.tpl' );
        $this->assertStringContainsString( "window.a4Toast(button.getAttribute('data-copied'), 'ok', button)", $menu );
    }
}
