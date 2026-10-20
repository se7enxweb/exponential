<?php
/**
 * The pre-paint code of the administration pagelayouts runs under an enforced Content Security Policy with
 * script-src 'self' (doc/en/us/features/6.0/content-security-policy.md): what must run before the first paint (the color
 * mode, the remembered width of the left column) is a script FILE loaded synchronously in <head>, never an inline
 * script, so no nonce or hash is needed and nothing flashes. Checked on the files alone; no database.
 *
 * - PPG-01: the pagelayouts of admin3, admin4 and admin4l and the login pagelayouts carry no inline <script> (a
 *   <script> with a src and a JSON data block are fine). The classic admin design is left out on purpose: it is a
 *   report-only design ([ContentSecurityPolicy] ReportOnlyDesigns[]) and still has inline scripts in its templates.
 * - PPG-02: admin3 loads its head script admin3_head.js with ezscript inside <head> (ezscript writes no defer or async,
 *   so it runs before the body is drawn), and the file applies a dragged width only in the form the stylesheets accept.
 * - PPG-03: no administration template prints csp_nonce() inside a {cache-block}: the nonce is per request, a cached
 *   block would carry an old one and the browser would block the script.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminPrePaintGuardsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminPrePaintGuardsTest extends PHPUnit\Framework\TestCase
{
    const PAGELAYOUTS = array(
        'design/admin3/templates/pagelayout.tpl',
        'design/admin3/templates/loginpagelayout.tpl',
        'design/admin4/templates/pagelayout.tpl',
        'design/admin4/templates/loginpagelayout.tpl',
        'design/admin4l/templates/pagelayout.tpl',
    );

    const DESIGNS = array( 'admin', 'admin2', 'admin3', 'admin4', 'admin4l' );

    protected static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** The template without its {* comments *} */
    protected static function read( $path )
    {
        $file = self::root() . '/' . $path;
        if ( !is_file( $file ) )
            return null;
        return preg_replace( '/\{\*.*?\*\}/s', '', file_get_contents( $file ) );
    }

    /** PPG-01 */
    public function testThePagelayoutsCarryNoInlineScript()
    {
        $checked = 0;
        foreach ( self::PAGELAYOUTS as $path )
        {
            $tpl = self::read( $path );
            if ( $tpl === null )
                continue;
            $checked++;
            preg_match_all( '/<script\b([^>]*)>(.*?)<\/script>/is', $tpl, $m, PREG_SET_ORDER );
            foreach ( $m as $script )
            {
                if ( preg_match( '/\bsrc\s*=/i', $script[1] ) || preg_match( '/type\s*=\s*["\']?application\/(ld\+)?json/i', $script[1] ) )
                    continue;
                $this->fail( $path . ' has an inline script: ' . substr( trim( $script[2] ), 0, 120 ) );
            }
        }
        $this->assertGreaterThanOrEqual( 3, $checked );
    }

    /** PPG-02 */
    public function testAdmin3LoadsItsHeadScriptFromAFile()
    {
        $tpl = self::read( 'design/admin3/templates/pagelayout.tpl' );
        if ( $tpl === null )
            $this->markTestSkipped( 'no admin3 design' );
        $head = substr( $tpl, 0, (int) strpos( $tpl, '</head>' ) );
        $this->assertStringContainsString( "{ezscript( array( 'admin3_head.js' ) )}", $head );
        $this->assertStringNotContainsString( "data-a4-left-width'); if", $tpl, 'the old inline guard is gone' );

        $js = file_get_contents( self::root() . '/design/admin3/javascript/admin3_head.js' );
        $this->assertStringContainsString( "getAttribute('data-a4-left-width')", $js );
        $this->assertStringContainsString( '/^\d{2,4}(\.\d+)?px$/.test(w)', $js );
        $this->assertStringContainsString( "setProperty('--left-sidebar-width', w)", $js );
        $this->assertDoesNotMatchRegularExpression( '/\beval\s*\(|new Function|document\.write/', $js );
    }

    /** PPG-03 */
    public function testNoNonceInsideACacheBlock()
    {
        $found = 0;
        foreach ( self::DESIGNS as $design )
        {
            $dir = self::root() . '/design/' . $design;
            if ( !is_dir( $dir ) )
                continue;
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
            {
                if ( substr( $file->getFilename(), -4 ) !== '.tpl' )
                    continue;
                $tpl = preg_replace( '/\{\*.*?\*\}/s', '', file_get_contents( $file->getPathname() ) );
                if ( strpos( $tpl, 'csp_nonce(' ) === false )
                    continue;
                $found++;
                $depth = 0;
                foreach ( preg_split( '/(\{cache-block\b[^}]*\}|\{\/cache-block\}|csp_nonce\()/', $tpl, -1, PREG_SPLIT_DELIM_CAPTURE ) as $part )
                {
                    if ( strncmp( $part, '{cache-block', 12 ) === 0 )
                        $depth++;
                    else if ( $part === '{/cache-block}' )
                        $depth = max( 0, $depth - 1 );
                    else if ( $part === 'csp_nonce(' )
                        $this->assertSame( 0, $depth, $file->getPathname() . ' prints csp_nonce() inside a cache-block' );
                }
            }
        }
        $this->assertGreaterThanOrEqual( 0, $found );
    }
}
