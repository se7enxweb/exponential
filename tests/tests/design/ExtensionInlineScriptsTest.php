<?php
/**
 * The extensions whose templates were moved off inline script code stay off it, so the administration's Content
 * Security Policy (script-src 'self', doc/en/us/features/6.0/content-security-policy.md) can be enforced with them installed.
 * Checked on the files alone; no database.
 *
 * - EXT-01: every template of a listed extension (all its designs: administration and site) carries no inline
 *   <script> with code, no on...= event handler attribute and no javascript: URL - counted as
 *   AdminContentSecurityPolicyTest::findings() counts them (template comments are not read; a <script> with a src, a
 *   JSON data block and a <template> are fine). Inline styles are not part of this test.
 *   An extension is checked when it is in the checkout (extension/<name>) at the listed version or later
 *   (extension.xml <version>), so an installation with an older release is not failed by this repository; composer.json
 *   requires the listed versions, so CI installs them. What an extension still has is listed in its 'files' with the
 *   reason; the list only shrinks: a listed file with fewer findings than listed, or gone, fails until the entry is
 *   lowered or removed.
 * - EXT-02: the counting itself.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/ExtensionInlineScriptsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminContentSecurityPolicyTest.php';

class ExtensionInlineScriptsTest extends PHPUnit\Framework\TestCase
{
    /** The kinds of inline script code: inline <script> elements, event handler attributes, javascript: URLs */
    const KINDS = array( 'script', 'handler', 'jsurl' );

    /**
     * extension => array( 'since' => first release without inline script code, 'why' => what is left and why (empty
     * when nothing is), 'files' => array( path below the extension directory => array( kind => count ) ) )
     */
    const EXTENSIONS = array(
        'AdminAid' => array( 'since' => '1.1.4', 'why' => '', 'files' => array() ),
        'bccie' => array( 'since' => '1.1.16', 'why' => '', 'files' => array() ),
        'bcwebsitestatistics' => array( 'since' => '1.0.12', 'why' => '', 'files' => array() ),
        'cjw_newsletter' => array( 'since' => '4.2.12', 'why' => '', 'files' => array() ),
        'cjw_themes_jumper' => array( 'since' => '0.1.6', 'why' => '', 'files' => array() ),
        'enhancedezbinaryfile' => array( 'since' => '4.4.8', 'why' => '', 'files' => array() ),
        'exp_adminui' => array( 'since' => '1.0.0.40', 'why' => 'the TinyMCE 3 edit template starts the editor with an inline script, as ezoe\'s own (see AdminContentSecurityPolicyTest)', 'files' => array( 'design/adminui/templates/content/datatype/edit/ezxmltext_ezoe.tpl' => array( 'script' => 2 ) ) ),
        'expai' => array( 'since' => '0.0.1.12', 'why' => '', 'files' => array() ),
        'expchangeclass' => array( 'since' => '1.0.9', 'why' => '', 'files' => array() ),
        'explayouts' => array( 'since' => '1.4.37', 'why' => '', 'files' => array() ),
        'explayouts_content_browser_ui' => array( 'since' => '1.0.8', 'why' => '', 'files' => array() ),
        'explayouts_ui' => array( 'since' => '1.3.20', 'why' => '', 'files' => array() ),
        'explayouts_ui_api' => array( 'since' => '1.3.24', 'why' => '', 'files' => array() ),
        'expservices' => array( 'since' => '0.1.0', 'why' => '', 'files' => array() ),
        'expui' => array( 'since' => '1.0.0.10', 'why' => '', 'files' => array() ),
        'ezauthorize' => array( 'since' => '1.4.3', 'why' => '', 'files' => array() ),
        'ezautosave' => array( 'since' => '6.0.12', 'why' => '', 'files' => array() ),
        'ezdemo' => array( 'since' => '6.0.12', 'why' => '', 'files' => array() ),
        'ezfind' => array( 'since' => '3.0.6', 'why' => '', 'files' => array() ),
        'ezflow' => array( 'since' => '6.1.9', 'why' => '', 'files' => array() ),
        'ezgmaplocation' => array( 'since' => '6.0.10', 'why' => '', 'files' => array() ),
        'ezie' => array( 'since' => '6.0.13', 'why' => '', 'files' => array() ),
        'ezmbpaex' => array( 'since' => '6.0.7', 'why' => '', 'files' => array() ),
        'ezmultiupload' => array( 'since' => '6.1.7', 'why' => '', 'files' => array() ),
        'ezownerchange' => array( 'since' => '1.4.2', 'why' => '', 'files' => array() ),
        'ezpm' => array( 'since' => '0.10.1', 'why' => '', 'files' => array() ),
        'ezstarrating' => array( 'since' => '6.0.12', 'why' => '', 'files' => array() ),
        'eztags' => array( 'since' => '2.4.17', 'why' => '', 'files' => array() ),
        'ezwebin' => array( 'since' => '6.0.22', 'why' => 'the footer script is content: it prints the code an editor enters in the site design object (footer_script)', 'files' => array( 'design/ezwebin/templates/page_footer_script.tpl' => array( 'script' => 1 ) ) ),
        'ezwt' => array( 'since' => '6.0.11', 'why' => '', 'files' => array() ),
        'hcaptcha' => array( 'since' => '1.4', 'why' => '', 'files' => array() ),
        'recaptcha' => array( 'since' => '1.4.8', 'why' => '', 'files' => array() ),
        'sevenx_authentication_2fa' => array( 'since' => '1.0.9', 'why' => '', 'files' => array() ),
        'sevenx_themes_media' => array( 'since' => '1.5.70', 'why' => '', 'files' => array() ),
        'sevenx_themes_simple' => array( 'since' => '1.0.24', 'why' => '', 'files' => array() ),
        'syndication' => array( 'since' => '1.3.8', 'why' => '', 'files' => array() ),
        'xrowextract' => array( 'since' => '2.5.17', 'why' => '', 'files' => array() ),
        'xrowmetadata' => array( 'since' => '1.4.13', 'why' => '', 'files' => array() ),
    );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** The version an extension states: extension.xml (its first <version>, the one of <metadata>), else ezinfo.php; or null */
    public static function version( $extension )
    {
        $dir = self::root() . "/extension/$extension";
        if ( is_file( "$dir/extension.xml" ) && preg_match( '#<version>\s*([0-9][0-9.]*)\s*</version>#', (string)file_get_contents( "$dir/extension.xml" ), $m ) )
            return $m[1];
        if ( is_file( "$dir/ezinfo.php" ) && preg_match( '#[\'"]Version[\'"]\s*=>\s*[\'"]([0-9][0-9.]*)[\'"]#', (string)file_get_contents( "$dir/ezinfo.php" ), $m ) )
            return $m[1];
        return null;
    }

    /** The inline script code of every template of an extension that has some: path below the extension => kind => count */
    public static function scan( $extension )
    {
        $dir = self::root() . "/extension/$extension";
        $found = array();
        if ( !is_dir( "$dir/design" ) )
            return $found;
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( "$dir/design", FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
        {
            if ( substr( $file->getFilename(), -4 ) !== '.tpl' )
                continue;
            $kinds = array_intersect_key( AdminContentSecurityPolicyTest::findings( (string)file_get_contents( $file->getPathname() ) ),
                                          array_flip( self::KINDS ) );
            if ( $kinds )
                $found[substr( $file->getPathname(), strlen( $dir ) + 1 )] = $kinds;
        }
        ksort( $found );
        return $found;
    }

    /** EXT-01 */
    public function testTheMovedExtensionsCarryNoInlineScriptCode()
    {
        $checked = 0;
        $unexpected = array();
        $fewer = array();
        foreach ( self::EXTENSIONS as $extension => $entry )
        {
            $this->assertMatchesRegularExpression( '/^[0-9]+(\.[0-9]+)+$/', $entry['since'], $extension );
            if ( $entry['files'] )
                $this->assertNotEmpty( $entry['why'], "$extension lists files: say why they are left" );
            $version = self::version( $extension );
            if ( $version === null || version_compare( $version, $entry['since'], '<' ) )
                continue;
            $checked++;
            $found = self::scan( $extension );
            foreach ( array_unique( array_merge( array_keys( $found ), array_keys( $entry['files'] ) ) ) as $path )
            {
                foreach ( self::KINDS as $kind )
                {
                    $now = isset( $found[$path][$kind] ) ? $found[$path][$kind] : 0;
                    $listed = isset( $entry['files'][$path][$kind] ) ? $entry['files'][$path][$kind] : 0;
                    if ( $now > $listed )
                        $unexpected[] = "extension/$extension/$path: $now $kind (allowed: $listed)";
                    else if ( $now < $listed )
                        $fewer[] = "extension/$extension/$path: $now $kind, listed with $listed - lower or remove the entry";
                }
            }
        }
        if ( !$checked )
            $this->markTestSkipped( 'none of the listed extensions is in this checkout at its listed version' );
        $this->assertSame( array(), $unexpected,
            'Inline script code in an extension template: move the script into a file (ezscript), the handler to a '
            . 'listener in that file with data-* attributes, the javascript: URL to a link or button with a listener '
            . '(doc/en/us/features/6.0/content-security-policy.md)' );
        $this->assertSame( array(), $fewer );
    }

    /** EXT-02 */
    public function testOnlyScriptCodeIsCounted()
    {
        $template = '<script src="/a.js"></script><script type="application/json" id="x">{"a":1}</script>'
            . '<template><a href="#">x</a></template>{* <script>x()</script> onclick="y" *}'
            . '<div style="color: red" data-onclick="y" class="onclick"></div>';
        $this->assertSame( array(), array_intersect_key( AdminContentSecurityPolicyTest::findings( $template ), array_flip( self::KINDS ) ) );
        $found = AdminContentSecurityPolicyTest::findings( '<script>var a;</script><a href="javascript:void(0)" onclick="x()" onmouseover=\'y()\'>a</a>' );
        $this->assertSame( array( 'script' => 1, 'handler' => 2, 'jsurl' => 1 ), array_intersect_key( $found, array_flip( self::KINDS ) ) );
    }

    /** EXT-01: the version gate */
    public function testTheVersionIsReadFromExtensionXml()
    {
        $this->assertNull( self::version( 'no_such_extension_here' ) );
        $this->assertMatchesRegularExpression( '/^[0-9]+(\.[0-9]+)+$/', (string)self::version( 'ezjscore' ) );
    }
}
