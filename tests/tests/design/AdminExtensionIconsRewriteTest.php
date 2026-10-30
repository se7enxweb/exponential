<?php
/**
 * Icon themes can live in an extension (icon.ini [ExtensionSettings] IconExtensions[], extension/<name>/icons/), and
 * the icon operators then point the browser at those files. The .htaccess templates serve share/icons directly; without
 * the same for extension/<name>/icons/ every such icon request falls through to index.php and the icon is broken.
 * The rule takes only images with a single extension, so a name like a.php.svg is never served from there.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminExtensionIconsRewriteTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminExtensionIconsRewriteTest extends PHPUnit\Framework\TestCase
{
    const RULE = 'RewriteRule ^extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$ - [L]';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /**
     * The rewrite templates of the distribution, plus an installed .htaccess when there is one.
     */
    public static function htaccessFiles()
    {
        $files = array( array( '.htaccess_root' ), array( '.htaccess_root_static' ) );
        if ( file_exists( self::root() . '/.htaccess' ) )
            $files[] = array( '.htaccess' );
        return $files;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider( 'htaccessFiles' )]
    public function testTheExtensionIconRuleIsThereBeforeTheFrontController( $file )
    {
        $lines = array_map( 'trim', explode( "\n", AdminFiles::read( self::root() . '/' . $file ) ) );
        $rule = array_search( self::RULE, $lines, true );
        $this->assertNotFalse( $rule, "$file does not serve extension/<name>/icons/ directly" );
        $frontController = array_search( 'RewriteRule .* index.php', $lines, true );
        $this->assertNotFalse( $frontController, "$file has no front controller rule" );
        $this->assertLessThan( $frontController, $rule, "$file sends extension icons to index.php before the rule is reached" );
    }

    public function testTheRuleTakesIconImagesOnly()
    {
        $pattern = '#^extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$#';
        $this->assertSame( 1, preg_match( $pattern, 'extension/myicons/icons/theme/svg/house.svg' ) );
        $this->assertSame( 1, preg_match( $pattern, 'extension/myicons/icons/theme/16x16/mimetypes/pdf.png' ) );
        $this->assertSame( 0, preg_match( $pattern, 'extension/myicons/icons/a.php.svg' ) );
        $this->assertSame( 0, preg_match( $pattern, 'extension/myicons/icons/run.php' ) );
        $this->assertSame( 0, preg_match( $pattern, 'extension/myicons/settings/icon.ini.append.php' ) );
    }
}
