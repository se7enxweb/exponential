<?php
/**
 * Administration views that left warnings in the debug report although nothing was wrong:
 *   - PDF export and RSS export lists called the deprecated eZUser::isLoggedIn(), which writes a strict
 *     "Deprecation" message on every visit; they use isRegistered(), what isLoggedIn() returns anyway
 *   - Setup > System information looped over its health checks as $check, the variable parts/ini_menu.tpl
 *     defines for the left menu, so the menu warned "Variable 'check' is already defined"; the loop variable
 *     is $si_check like the page's other variables
 *
 * Static checks of the sources, no database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expAdminViewsDebugCleanTest extends PHPUnit\Framework\TestCase
{
    private $root;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 4 );
    }

    public function testViewsDoNotCallDeprecatedIsLoggedIn()
    {
        $found = array();
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->root . '/kernel/private/classes/views', FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
        {
            if ( substr( $file->getFilename(), -4 ) !== '.php' )
                continue;
            if ( strpos( file_get_contents( $file->getPathname() ), '->isLoggedIn()' ) !== false )
                $found[] = substr( $file->getPathname(), strlen( $this->root ) + 1 );
        }
        $this->assertSame( array(), $found, 'views calling the deprecated eZUser::isLoggedIn()' );
    }

    public function testPdfAndRssListsUseIsRegistered()
    {
        foreach ( array( 'pdf/list.php', 'rss/list.php' ) as $view )
            $this->assertStringContainsString( '->isRegistered()', file_get_contents( $this->root . '/kernel/private/classes/views/' . $view ), $view );
    }

    public function testVersionViewWithoutLanguageWritesNoDeprecation()
    {
        // content/versionview/<id>/<version> without a language and FromLanguage passed null to htmlspecialchars()
        $source = file_get_contents( $this->root . '/kernel/private/classes/views/content/versionview.php' );
        $this->assertStringContainsString( 'htmlspecialchars( (string)$LanguageCode,', $source );
        $this->assertStringContainsString( "htmlspecialchars( (string)( \$Params['FromLanguage'] ?? '' ),", $source );
        $deprecations = array();
        set_error_handler( function ( $no, $str ) use ( &$deprecations ) { $deprecations[] = $str; return true; }, E_DEPRECATED );
        try
        {
            $this->assertSame( '', htmlspecialchars( (string)null, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertSame( array(), $deprecations );
    }

    public function testSystemInformationDoesNotReuseMenuVariable()
    {
        $menu = file_get_contents( $this->root . '/design/admin/templates/parts/ini_menu.tpl' );
        $this->assertMatchesRegularExpression( '/\$check\s*=\s*array\(\)/', $menu, 'ini_menu.tpl still defines $check' );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $info = file_get_contents( $this->root . '/design/' . $design . '/templates/setup/info.tpl' );
            $this->assertDoesNotMatchRegularExpression( '/\$check\b/', $info, "design/$design setup/info.tpl uses \$check" );
            $this->assertStringContainsString( 'as $si_check}', $info, "design/$design setup/info.tpl" );
        }
    }
}
