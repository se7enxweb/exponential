<?php
/**
 * "Copy messages" of Setup > Log files (doc/en/us/features/6.0/log-viewer.md, "Copy messages"): the text copied is the text
 * of the lines shown, in display order, as plain text.
 *
 *  LC-01  page/setup_logs_copy.js, run with node: for a filtered view (a search's entries), the copied text is the
 *         text of the rows as they show it (time, level, file, source, request, message), one line each; a message's
 *         line breaks are kept when lines wrap and shown as " ↵ " when they do not; a separator and a cut entry as shown;
 *         nothing of the page's markup (skipped without node)
 *  LC-02  both designs carry the same scripts, the button is a primary button on the row of Copy permalink, first, with
 *         a description, and the copy script loads before the page script; the page script copies through the clipboard
 *         API with a textarea fallback and tells the count
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class LogViewerCopyMessagesTest extends PHPUnit\Framework\TestCase
{
    /** @return string */
    protected static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** LC-01 */
    public function testCopiedTextEqualsTheShownLines()
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not on PATH' );
        $script = self::root() . '/design/admin4/javascript/page/setup_logs_copy.js';
        $entries = array(
            array( 'ts' => 'Oct 17 2026 01:00:00', 'lv' => 'error', 'src' => 'eZDB', 'sa' => 'admin', 'ip' => '192.0.x.x',
                   'u' => 'https://example.test/a', 'p' => array( array( "eZDB:\nError: " ), array( '42', 'node', '42' ), array( ' failed' ) ) ),
            array( 'sep' => true ),
            array( 'ts' => 'Oct 17 2026 01:00:01', 'lv' => 'warning', 'f' => 'var/log/warning.log', 'p' => array( array( 'w***@***.test said <b>' ) ), 'x' => true ),
        );
        // the script attaches to window; give it one
        $harness = "var window = {};\n" . file_get_contents( $script ) . "\n"
                 . 'var e = ' . json_encode( $entries ) . ";\n"
                 . "var names = { error: 'Error', warning: 'Warning' };\n"
                 . "var o = { levelName: function ( l ) { return names[l]; }, rotated: 'ROTATED', cut: '(cut)' };\n"
                 . "o.wrap = true; var a = window.ExpLogCopy.textOf( e, o );\n"
                 . "o.wrap = false; var b = window.ExpLogCopy.textOf( e, o );\n"
                 . "process.stdout.write( JSON.stringify( [ a, b ] ) );\n";
        $dir = sys_get_temp_dir() . '/exp-logcopy-' . getmypid();
        @mkdir( $dir, 0700, true );
        file_put_contents( $dir . '/h.js', $harness );
        $out = shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( $dir . '/h.js' ) );
        @unlink( $dir . '/h.js' );
        @rmdir( $dir );
        $res = json_decode( (string)$out, true );
        $this->assertIsArray( $res, (string)$out );
        $this->assertSame( "Oct 17 2026 01:00:00 Error eZDB admin 192.0.x.x https://example.test/a eZDB:\nError: 42 failed\n"
                           . "ROTATED\n"
                           . "Oct 17 2026 01:00:01 Warning var/log/warning.log w***@***.test said <b> (cut)", $res[0] );
        $this->assertSame( "Oct 17 2026 01:00:00 Error eZDB admin 192.0.x.x https://example.test/a eZDB: \u{21b5} Error: 42 failed\n"
                           . "ROTATED\n"
                           . "Oct 17 2026 01:00:01 Warning var/log/warning.log w***@***.test said <b> (cut)", $res[1] );
    }

    /** LC-02 */
    public function testButtonAndWiringInBothDesigns()
    {
        $root = self::root();
        foreach ( array( 'setup_logs_copy.js', 'setup_logs.js' ) as $js )
            $this->assertFileEquals( "$root/design/admin4/javascript/page/$js", "$root/design/admin/javascript/page/$js", $js );
        $this->assertFileEquals( "$root/design/admin4/templates/setup/logs.tpl", "$root/design/admin/templates/setup/logs.tpl" );
        $tpl = file_get_contents( "$root/design/admin4/templates/setup/logs.tpl" );
        $this->assertMatchesRegularExpression( '#<div class="a4-setup-log-picker-actions"><button type="button" class="a4-btn a4-btn-primary" id="log-copy-messages"[^>]*aria-describedby="log-copy-messages-help"[^>]*>.*?</button><button type="button" class="a4-btn a4-btn-primary" id="log-copy-link"#', $tpl );
        $this->assertStringContainsString( 'id="log-copy-messages-help"', $tpl );
        $this->assertStringContainsString( "{ezscript( array( 'page/setup_logs_copy.js', 'page/setup_logs.js' ) )}", $tpl );
        foreach ( array( 'data-text-lines-copied=', 'data-text-lines-copied-one=', 'data-text-lines-copy-failed=' ) as $attr )
            $this->assertStringContainsString( $attr, $tpl );
        $js = file_get_contents( "$root/design/admin4/javascript/page/setup_logs.js" );
        $this->assertStringContainsString( 'navigator.clipboard.writeText( value )', $js );
        $this->assertStringContainsString( "document.execCommand( 'copy' )", $js );
        $this->assertStringContainsString( 'window.ExpLogCopy.textOf( shown,', $js );
        $this->assertStringContainsString( "text( 'lines-copied' ).replace( '%count'", $js );
    }
}
