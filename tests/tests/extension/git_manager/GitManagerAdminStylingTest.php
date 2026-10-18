<?php
/**
 * git_manager's admin pages (the dashboard, the commit details, the Backup page and the Git tab's left menu) follow
 * the administration's styling rules (doc/bc/6.0/admin4-styling.md), checked on the files alone; no database.
 *
 * - No <style> block, no style="" attribute and no inline <script> body in any git_manager template; the behaviours
 *   are design/standard/javascript/git_manager.js, required with ezscript_require.
 * - The module stylesheet (design/standard/stylesheets/modules/git_manager.css) is listed in design.ini
 *   [StylesheetSettings] BackendCSSFileList, names only .a4-gm-* classes and takes colours, radii, spacing and fonts
 *   from the --a4-* tokens only (the kernel's check for module stylesheets), so every value has its dark mode.
 * - The pages are built from the shared components and keep every form action and confirmation they had.
 * - The Git tab's views draw the Git left menu (menu.ini [Leftmenu_gitmanager]); its admin4 copy ends with the width
 *   switch (parts/left_width_control.tpl); the Backup page stays in the Setup navigation part.
 * - Every string of the templates is translated in eng-US and ger-DE.
 *
 * Skips when extension/git_manager is not installed; GIT_MANAGER_EXTENSION_DIR=<dir> checks a clone instead.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/git_manager/GitManagerAdminStylingTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class GitManagerAdminStylingTest extends PHPUnit\Framework\TestCase
{
    const TEMPLATES = array(
        'design/standard/templates/git_manager/dashboard.tpl',
        'design/standard/templates/git_manager/backup.tpl',
        'design/standard/templates/git_manager/commit_details.tpl',
        'design/standard/templates/git_manager/parts/upstream.tpl',
    );

    const MODULE_CSS = 'design/standard/stylesheets/modules/git_manager.css';

    private static $root;
    private static $ext;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 4 );
        self::$ext = getenv( 'GIT_MANAGER_EXTENSION_DIR' ) ?: self::$root . '/extension/git_manager';
    }

    protected function setUp(): void
    {
        if ( !is_dir( self::$ext . '/design/standard/templates/git_manager' ) )
        {
            $this->markTestSkipped( 'extension/git_manager is not installed' );
        }
    }

    private static function read( $relative )
    {
        return (string)file_get_contents( self::$ext . '/' . $relative );
    }

    private static function templates()
    {
        $list = array();
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::$ext . '/design', FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
        {
            if ( substr( $file->getFilename(), -4 ) === '.tpl' )
            {
                $list[] = substr( $file->getPathname(), strlen( self::$ext ) + 1 );
            }
        }
        sort( $list );
        return $list;
    }

    private static function withoutComments( $source )
    {
        return preg_replace( '/\{\*.*?\*\}/s', '', $source );
    }

    public static function inlineCssCount( $source )
    {
        $source = self::withoutComments( $source );
        return preg_match_all( '/<style\b/i', $source ) + preg_match_all( '/(?<![\w.\-])style\s*=\s*["\'{]/i', $source );
    }

    public static function inlineScriptCount( $source )
    {
        $source = self::withoutComments( $source );
        return preg_match_all( '/<script\b(?![^>]*\bsrc\s*=)[^>]*>\s*[^<\s]/i', $source );
    }

    public function testCheckersCatchWhatTheyShould()
    {
        $this->assertSame( 2, self::inlineCssCount( '<style>a{}</style><p style="color:red">' ) );
        $this->assertSame( 0, self::inlineCssCount( '{* <p style="x"> *}<p data-style="x">' ) );
        $this->assertSame( 1, self::inlineScriptCount( '<script>go()</script><script src="x.js"></script>' ) );
        $this->assertSame( 0, self::inlineScriptCount( '<script type="text/javascript" src="x.js"></script>' ) );
    }

    public function testNoInlineCssOrScriptInAnyTemplate()
    {
        $templates = self::templates();
        $this->assertNotEmpty( $templates );
        $found = array();
        foreach ( $templates as $path )
        {
            $source = self::read( $path );
            if ( self::inlineCssCount( $source ) || self::inlineScriptCount( $source ) )
            {
                $found[] = $path;
            }
        }
        $this->assertSame( array(), $found, 'Templates with a <style> block, a style="" attribute or an inline <script> body' );
        $this->assertStringContainsString( "ezscript_require( 'git_manager.js' )", self::read( 'design/standard/templates/git_manager/parts/script.tpl' ) );
        $this->assertFileExists( self::$ext . '/design/standard/javascript/git_manager.js' );
        $this->assertStringNotContainsString( '.style.', self::read( 'design/standard/javascript/git_manager.js' ), 'The script sets classes, not inline styles' );
    }

    public function testModuleStylesheetIsLoadedThroughDesignIni()
    {
        $this->assertFileExists( self::$ext . '/' . self::MODULE_CSS );
        $this->assertFileDoesNotExist( self::$ext . '/design/standard/stylesheets/git_manager.css', 'The old stylesheet with its own colors is gone' );
        $ini = self::read( 'settings/design.ini.append' );
        $this->assertMatchesRegularExpression( '/\[StylesheetSettings\][^\[]*BackendCSSFileList\[\]=modules\/git_manager\.css/s', $ini );
        foreach ( self::templates() as $path )
        {
            $this->assertStringNotContainsString( 'ezcss_require', self::read( $path ), $path . ' needs no stylesheet of its own' );
        }
    }

    private static function declarations( $css )
    {
        $css = preg_replace( '#/\*.*?\*/#s', '', $css );
        $css = preg_replace( '/@keyframes[^{]*\{(?:[^{}]*\{[^{}]*\})*[^{}]*\}/s', '', $css );
        $css = preg_replace( '/@(media|supports|container)[^{]*\{((?:[^{}]*\{[^{}]*\})*)[^{}]*\}/s', '$2', $css );
        $list = array();
        preg_match_all( '/([^{}]+)\{([^{}]*)\}/s', $css, $rules, PREG_SET_ORDER );
        foreach ( $rules as $rule )
        {
            $selector = trim( preg_replace( '/\s+/', ' ', $rule[1] ) );
            foreach ( explode( ';', $rule[2] ) as $declaration )
            {
                if ( strpos( $declaration, ':' ) === false )
                {
                    continue;
                }
                list( $property, $value ) = explode( ':', $declaration, 2 );
                $list[] = array( $selector, strtolower( trim( $property ) ), trim( $value ) );
            }
        }
        return $list;
    }

    private static function problem( $selector, $property, $value )
    {
        $design = self::$root . '/tests/tests/design/AdminStylingRulesTest.php';
        if ( is_file( $design ) )
        {
            require_once $design;
            return AdminStylingRulesTest::moduleDeclarationProblem( $selector, $property, $value );
        }
        $plain = preg_replace( '/var\(\s*--[\w-]+\s*\)/', 'var()', strtolower( $value ) );
        if ( strpos( $property, '--' ) === 0 )
        {
            return 'declares its own custom property';
        }
        if ( preg_match( '/#[0-9a-f]{3,8}\b|\b(rgba?|hsla?|hwb|lab|lch|oklab|oklch|color-mix)\(/', $plain ) )
        {
            return 'a colour literal';
        }
        if ( strpos( $property, 'radius' ) !== false && !preg_match( '/^((var\(\)|0|50%|inherit)\s*)+$/', $plain ) )
        {
            return 'a radius of its own';
        }
        return null;
    }

    public function testModuleStylesheetUsesOnlyTokensAndItsOwnClasses()
    {
        $declarations = self::declarations( self::read( self::MODULE_CSS ) );
        $this->assertNotEmpty( $declarations );
        $problems = array();
        foreach ( $declarations as list( $selector, $property, $value ) )
        {
            foreach ( explode( ',', $selector ) as $part )
            {
                if ( !preg_match( '/^\.a4-gm-[\w-]+/', trim( $part ) ) )
                {
                    $problems[] = $part . ': does not start with an .a4-gm-* class';
                }
            }
            $problem = self::problem( $selector, $property, $value );
            if ( $problem !== null )
            {
                $problems[] = $selector . ' { ' . $property . ': ' . $value . ' }: ' . $problem;
            }
        }
        $this->assertSame( array(), array_values( array_unique( $problems ) ) );
    }

    public function testPagesUseTheSharedComponentsAndNoOldLookClasses()
    {
        $leftovers = array();
        foreach ( self::TEMPLATES as $path )
        {
            $source = self::withoutComments( self::read( $path ) );
            preg_match_all( '/\bclass="([^"]*)"/', $source, $m );
            foreach ( $m[1] as $classes )
            {
                foreach ( preg_split( '/\s+/', preg_replace( '/\{[^}]*\}/', ' ', $classes ) ) as $class )
                {
                    if ( preg_match( '/^(gm-|gm$|button$|defaultbutton$|message-error$|message-feedback$)/', $class ) )
                    {
                        $leftovers[] = $path . ': ' . $class;
                    }
                }
            }
        }
        $this->assertSame( array(), $leftovers );
        $components = array(
            'dashboard.tpl' => array( 'a4-page', 'a4-card', 'a4-card-head', 'a4-btn', 'a4-badge', 'a4-notice', 'a4-filterbar', 'a4-field', 'a4-check', 'a4-fold', 'a4-empty' ),
            'backup.tpl' => array( 'a4-page', 'a4-card', 'a4-notice', 'a4-table', 'a4-btn', 'a4-badge', 'a4-toolbar', 'a4-empty', 'a4-sr-only', 'a4-pick' ),
            'commit_details.tpl' => array( 'a4-page', 'a4-card', 'a4-facts', 'a4-fold', 'a4-btn' ),
            'parts/upstream.tpl' => array( 'a4-card', 'a4-facts', 'a4-badge', 'a4-notice', 'a4-empty' ),
        );
        foreach ( $components as $file => $list )
        {
            $source = self::read( 'design/standard/templates/git_manager/' . $file );
            foreach ( $list as $component )
            {
                $this->assertStringContainsString( $component, $source, $file . ' uses .' . $component );
            }
        }
    }

    public function testEveryActionAndConfirmationIsKept()
    {
        $Module = $ViewList = $FunctionList = null;
        include self::$ext . '/modules/git_manager/module.php';
        $templates = array( 'dashboard' => self::read( 'design/standard/templates/git_manager/dashboard.tpl' )
                                         . self::read( 'design/standard/templates/git_manager/parts/upstream.tpl' )
                                         . self::read( 'design/standard/templates/git_manager/commit_details.tpl' ),
                            'backup' => self::read( 'design/standard/templates/git_manager/backup.tpl' ) );
        foreach ( $templates as $view => $source )
        {
            foreach ( array_keys( $ViewList[$view]['single_post_actions'] ) as $action )
            {
                // a button of its own (name="..."), or one of the Backup page's create cards ('action', '...')
                $this->assertMatchesRegularExpression( '/name="' . $action . '"|\'action\', \'' . $action . '\'/', $source, 'git_manager/' . $view . ' keeps the ' . $action . ' button' );
            }
        }
        foreach ( array( 'data-gm-confirm=', 'data-gm-push=', 'data-gm-busy=', 'data-gm-confirm-button=', 'href={$log_clear_url|ezurl}', 'design:navigator/google.tpl', 'name="log_limit"', 'name="filter[ref]"', 'name="filter[grep]"', 'data-preference-url=', 'id="gm-log-find"', 'id="gm-only-unpushed"', 'id="gm-log"' ) as $hook )
        {
            $this->assertStringContainsString( $hook, $templates['dashboard'] );
        }
        foreach ( array( 'data-gm-ask="create"', 'data-gm-ask="delete-one"', 'data-gm-ask="delete-many"', 'a4-gm-select-all', 'a4-gm-caption-select', 'a4-gm-selected-count', 'name="timestamps[]"' ) as $hook )
        {
            $this->assertStringContainsString( $hook, $templates['backup'] );
        }
        $script = self::read( 'design/standard/javascript/git_manager.js' );
        foreach ( array( 'data-gm-confirm', 'data-gm-push', 'data-gm-busy', 'data-gm-confirm-button', 'data-gm-ask', 'a4-gm-select-all', 'window.confirm' ) as $hook )
        {
            $this->assertStringContainsString( $hook, $script );
        }
    }

    public function testGitTabHasItsOwnLeftMenuWithTheWidthSwitch()
    {
        $Module = $ViewList = $FunctionList = null;
        include self::$ext . '/modules/git_manager/module.php';
        $this->assertSame( 'gitmanagernavigationpart', $ViewList['dashboard']['default_navigation_part'] );
        $this->assertSame( 'ezsetupnavigationpart', $ViewList['backup']['default_navigation_part'], 'The Backup page stays under Setup' );
        foreach ( array( 'dashboard', 'commit_details' ) as $view )
        {
            $this->assertStringContainsString( "\$Result['left_menu'] = 'design:parts/gitmanager/menu.tpl';", self::read( 'classes/runnable/views/git_manager/' . $view . '.php' ) );
        }
        $admin4 = self::read( 'design/admin4/templates/parts/gitmanager/menu.tpl' );
        $this->assertStringContainsString( "{include uri='design:parts/gitmanager/menu_items.tpl'}", $admin4 );
        $this->assertStringContainsString( "{include uri='design:parts/left_width_control.tpl'}", $admin4 );
        $this->assertStringContainsString( 'id="widthcontrol-links"', self::read( 'design/standard/templates/parts/gitmanager/menu.tpl' ) );
        $this->assertStringContainsString( "ini_section='Leftmenu_gitmanager'", self::read( 'design/standard/templates/parts/gitmanager/menu_items.tpl' ) );
        $menu = self::read( 'settings/menu.ini.append' );
        $this->assertStringContainsString( '[Leftmenu_gitmanager]', $menu );
        $this->assertStringContainsString( 'Links[dashboard]=git_manager/dashboard', $menu );
        $this->assertStringContainsString( 'Links[backup]=git_manager/backup', $menu );
        $kernel = self::$root . '/design/admin4/templates/parts/left_width_control.tpl';
        if ( is_file( $kernel ) )
        {
            $this->assertStringContainsString( 'widthcontrol-links', (string)file_get_contents( $kernel ) );
        }
    }

    private static function translated( $language )
    {
        $xml = simplexml_load_file( self::$ext . '/translations/' . $language . '/translation.ts' );
        $sources = array();
        foreach ( $xml->context as $context )
        {
            if ( (string)$context->name !== 'extension/git_manager' )
            {
                continue;
            }
            foreach ( $context->message as $message )
            {
                if ( trim( (string)$message->translation ) !== '' )
                {
                    $sources[(string)$message->source] = true;
                }
            }
        }
        return $sources;
    }

    public function testEveryStringOfTheTemplatesIsTranslated()
    {
        $strings = array();
        foreach ( self::templates() as $path )
        {
            preg_match_all( "/'((?:[^'\\\\]|\\\\.)*)'\\|i18n\\(\\s*'extension\\/git_manager'/", self::read( $path ), $m );
            foreach ( $m[1] as $text )
            {
                $strings[] = stripslashes( $text );
            }
        }
        $strings = array_unique( $strings );
        $this->assertGreaterThan( 100, count( $strings ) );
        foreach ( array( 'eng-US', 'ger-DE' ) as $language )
        {
            $known = self::translated( $language );
            $missing = array_values( array_filter( $strings, function ( $text ) use ( $known ) { return !isset( $known[$text] ); } ) );
            $this->assertSame( array(), $missing, $language . ' translates every string of the templates' );
        }
    }
}
