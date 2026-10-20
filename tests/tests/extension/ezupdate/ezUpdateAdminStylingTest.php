<?php
/**
 * ezupdate's admin pages (Setup > Updates and packages) follow the administration's styling rules
 * (doc/eng-us/bc/6.0/admin4-styling.md), checked on the files alone; no database.
 *
 * - No <style> block, no style="" attribute and no inline <script> body in any ezupdate template, and the script
 *   (ezupdate.js) sets no inline style either.
 * - The module stylesheet (design/standard/stylesheets/modules/update.css) is listed in design.ini
 *   [StylesheetSettings] BackendCSSFileList, names only .a4-update-* classes and takes colours, radii, spacing and
 *   fonts from the --a4-* tokens only (the same check as the kernel's module stylesheets, so every value has its
 *   dark mode in components.css). It is the module's only stylesheet: no template loads one of its own.
 * - Every view (each of module.php) and every part is built from the shared components and carries none of the
 *   older .ezx-* / .ezupdate-* look classes; each view's template draws its title through parts/header.tpl.
 * - The colours of Composer's output (eZUpdateManager::ansiToHtml()) are classes the module stylesheet draws, not
 *   style attributes, and the output stays escaped.
 * - The status badge and the notices the job page's script swaps while it polls have the classes the templates draw.
 * - Every view sits in the Setup navigation part, so its left column is the kernel's Setup menu, which ends with
 *   the admin4 width switch (parts/left_width_control.tpl).
 * - Every string of every template is translated in eng-US and ger-DE and in the five other completed catalogues
 *   (esl-ES, esl-MX, heb-IL, lat-VA, tlh-QO).
 *
 * Skips when extension/ezupdate is not installed; EZUPDATE_EXTENSION_DIR=<dir> checks a clone instead.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezupdate/ezUpdateAdminStylingTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezUpdateAdminStylingTest extends PHPUnit\Framework\TestCase
{
    /**
     * The languages whose catalogues translate every string of the module
     */
    const LANGUAGES = array( 'eng-US', 'ger-DE', 'esl-ES', 'esl-MX', 'heb-IL', 'lat-VA', 'tlh-QO' );

    const MODULE_CSS = 'design/standard/stylesheets/modules/update.css';

    private static $root;
    private static $ext;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 4 );
        // EZUPDATE_EXTENSION_DIR checks a clone of the extension instead of the installed copy
        self::$ext = getenv( 'EZUPDATE_EXTENSION_DIR' ) ?: self::$root . '/extension/ezupdate';
    }

    protected function setUp(): void
    {
        if ( !is_dir( self::$ext . '/design/standard/templates/ezupdate' ) )
        {
            $this->markTestSkipped( 'extension/ezupdate is not installed' );
        }
    }

    private static function read( $relative )
    {
        return (string)file_get_contents( self::$ext . '/' . $relative );
    }

    /**
     * Every template of the extension, relative to its directory
     */
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
    }

    public function testModuleStylesheetIsLoadedThroughDesignIni()
    {
        $this->assertFileExists( self::$ext . '/' . self::MODULE_CSS );
        $ini = self::read( 'settings/design.ini.append' );
        $this->assertMatchesRegularExpression( '/\[StylesheetSettings\][^\[]*BackendCSSFileList\[\]=modules\/update\.css/s', $ini );
    }

    /**
     * The module stylesheet is the only one: no template loads a stylesheet of its own (ezcss_require, a <link>),
     * and the older design system (ezupdate.css) is gone
     */
    public function testNoTemplateLoadsAStylesheetOfItsOwn()
    {
        $found = array();
        foreach ( self::templates() as $path )
        {
            $source = self::withoutComments( self::read( $path ) );
            if ( preg_match( '/ezcss_require|ezcss_load|<link\b/i', $source ) )
            {
                $found[] = $path;
            }
        }
        $this->assertSame( array(), $found );
        $sheets = array();
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::$ext . '/design', FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
        {
            if ( substr( $file->getFilename(), -4 ) === '.css' )
            {
                $sheets[] = substr( $file->getPathname(), strlen( self::$ext ) + 1 );
            }
        }
        $this->assertSame( array( self::MODULE_CSS ), $sheets );
    }

    /**
     * The script changes looks with classes only, never with an inline style (style-src 'self')
     */
    public function testScriptSetsNoInlineStyle()
    {
        $script = self::read( 'design/standard/javascript/ezupdate.js' );
        $this->assertDoesNotMatchRegularExpression( '/\.style\s*[.\[=]|setAttribute\(\s*[\'"]style|cssText/', $script );
    }

    /**
     * Composer's colours become classes the module stylesheet draws; the output stays escaped
     */
    public function testComposerOutputColoursAreClasses()
    {
        require_once self::$ext . '/classes/ezupdatemanager.php';
        $html = eZUpdateManager::ansiToHtml( "\x1b[32mInstalled\x1b[39m <b>x</b> \x1b[1;31mfailed\x1b[0m \x1b[2Kdone" );
        $this->assertStringNotContainsString( 'style=', $html );
        $this->assertStringNotContainsString( "\x1b", $html );
        $this->assertStringContainsString( '&lt;b&gt;x&lt;/b&gt;', $html );
        $this->assertStringContainsString( '<span class="a4-update-ansi-ok">Installed</span>', $html );
        $this->assertMatchesRegularExpression( '/<span class="(a4-update-ansi-bold a4-update-ansi-bad|a4-update-ansi-bad a4-update-ansi-bold)">failed<\/span>/', $html );
        $css = self::read( self::MODULE_CSS );
        $all = eZUpdateManager::ansiToHtml( "\x1b[1m\x1b[30ma\x1b[31mb\x1b[32mc\x1b[33md\x1b[34me\x1b[35mf\x1b[36mg\x1b[37mh\x1b[90mi\x1b[97mj\x1b[0m" );
        preg_match_all( '/a4-update-ansi-[\w-]+/', $all, $m );
        foreach ( array_unique( $m[0] ) as $class )
        {
            $this->assertMatchesRegularExpression( '/\.' . preg_quote( $class, '/' ) . '\s*\{/', $css, 'update.css draws .' . $class );
        }
    }

    /**
     * Returns the rules of a stylesheet as (selector, property, value), at-rule wrappers opened and keyframes left out
     */
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

    /**
     * What is wrong with one declaration of a module stylesheet, or null (the kernel's rule when it is there)
     */
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
                if ( !preg_match( '/^\.a4-update-[\w-]+/', trim( $part ) ) )
                {
                    $problems[] = $part . ': does not start with an .a4-update-* class';
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

    public function testEveryTemplateUsesTheSharedComponents()
    {
        $leftovers = array();
        foreach ( self::templates() as $path )
        {
            $source = self::withoutComments( self::read( $path ) );
            preg_match_all( '/\bclass="([^"]*)"/', $source, $m );
            foreach ( $m[1] as $classes )
            {
                foreach ( preg_split( '/\s+/', preg_replace( '/\{[^}]*\}/', ' ', $classes ) ) as $class )
                {
                    // the page root keeps .ezx (the "/" search key) and the close button keeps .ezx-dismiss (the
                    // script's hook); no other old look class
                    if ( preg_match( '/^(ezx-|ezupdate-|button$|defaultbutton$|hide$)/', $class ) && $class !== 'ezx-dismiss' )
                    {
                        $leftovers[] = $path . ': ' . $class;
                    }
                }
            }
        }
        $this->assertSame( array(), $leftovers );
        $dashboard = self::read( 'design/standard/templates/ezupdate/dashboard.tpl' );
        foreach ( array( 'a4-figures', 'a4-card', 'a4-facts', 'a4-table', 'a4-btn', 'a4-badge', 'a4-empty', 'a4-notice', 'a4-sr-only' ) as $component )
        {
            $this->assertStringContainsString( $component, $dashboard, 'The dashboard uses .' . $component );
        }
        $header = self::read( 'design/standard/templates/ezupdate/parts/header.tpl' );
        foreach ( array( 'a4-page', 'a4-pagehead', 'a4-pagehead-actions', 'a4-tabs', 'aria-current="page"', 'a4-notice' ) as $part )
        {
            $this->assertStringContainsString( $part, $header );
        }
    }

    /**
     * Every view of module.php has its template, which opens with parts/header.tpl and passes its title (so the page
     * gets its h1 and the shared page scope, .a4-page) and ends with parts/footer.tpl
     */
    public function testEveryViewDrawsItsTitleThroughTheSharedHeader()
    {
        $Module = $ViewList = null;
        include self::$ext . '/modules/update/module.php';
        $this->assertNotEmpty( $ViewList );
        foreach ( array_keys( $ViewList ) as $name )
        {
            $path = 'design/standard/templates/ezupdate/' . $name . '.tpl';
            $this->assertFileExists( self::$ext . '/' . $path, 'update/' . $name . ' has its template' );
            $source = self::withoutComments( self::read( $path ) );
            $this->assertMatchesRegularExpression( "/\\{include uri='design:ezupdate\\/parts\\/header\\.tpl'[^}]*\\btitle=/s", $source, $path . ' passes its title to the header' );
            $this->assertStringContainsString( "{include uri='design:ezupdate/parts/footer.tpl'}", $source, $path );
            $this->assertDoesNotMatchRegularExpression( '/<h1\b/', $source, $path . ': the header draws the h1' );
        }
    }

    /**
     * The forms of the views keep every field and button name their view scripts read
     */
    public function testEveryFormFieldAndButtonIsKept()
    {
        $names = array(
            'dashboard.tpl' => array( 'GetComposerButton', 'SwitchUpdatesOnButton', 'SwitchUpdatesOffButton', 'CheckForUpdatesButton',
                                      'DryRunUpdateButton', 'InstallUpdatesButton', 'RerunButton', 'name="ConfirmBackup"', 'name="Packages[]"',
                                      'data-ezupdate-enables="InstallUpdatesButton"', 'data-ezx-busy', 'data-ezx-ago', 'data-ezx-copy' ),
            'servers.tpl' => array( 'PackagistOffButton', 'PackagistOnButton', 'RemoveComposerServerButton', 'AddComposerServerButton',
                                    'RemovePackageServerButton', 'AddPackageServerButton', 'name="ServerName"', 'name="ServerType"',
                                    'name="ServerURL"', 'data-ezupdate-confirm' ),
            'browse.tpl' => array( 'name="q"', 'name="source"', 'name="type"', 'role="search"', 'update/browse/(page)/' ),
            'packages.tpl' => array( 'FetchPackageButton', 'name="PackageName"', 'name="Replace"', 'data-ezx-filter', 'data-ezx-none' ),
            'job.tpl' => array( 'RerunButton', 'RunForRealButton', 'name="ConfirmBackup"', 'data-ezupdate-when-done', 'id="ezupdate-job-output"',
                                'id="ezupdate-job-status"', 'id="ezupdate-job-result"', 'id="ezupdate-job-notice"', 'data-ezx-follow', 'data-ezx-wrap' ),
            'package.tpl' => array( 'data-ezx-stable-only', 'data-ezx-versions', 'parts/install_form.tpl' ),
            'parts/install_form.tpl' => array( 'name="Constraint"', 'DryRunInstallButton', 'InstallButton', 'name="ConfirmBackup"', 'parts/method.tpl' ),
            'installed.tpl' => array( 'data-ezupdate-installed', 'data-ezupdate-row', 'data-ezupdate-table', 'data-ezupdate-empty', 'data-filter',
                                      'data-sort=', 'data-sort-select', 'data-search', 'data-count', 'aria-controls="ezupdate-inv-detail-' ),
            'fund.tpl' => array( 'update/fund/json', 'update/fund/text', 'update/fund/(direct)/1', 'data-ezx-filter', 'data-ezx-none',
                                 'rel="noopener noreferrer nofollow"' ),
        );
        foreach ( $names as $template => $list )
        {
            $source = self::read( 'design/standard/templates/ezupdate/' . $template );
            foreach ( $list as $name )
            {
                $this->assertStringContainsString( $name, $source, $template );
            }
        }
        $this->assertStringContainsString( 'name="InstallMethod"', self::read( 'design/standard/templates/ezupdate/parts/method.tpl' ) );
        $script = self::read( 'design/standard/javascript/ezupdate.js' );
        foreach ( array( 'data-ezupdate-installed', 'tbody[data-ezupdate-row]', '[data-ezupdate-table]', '[data-ezupdate-empty]', 'ezupdate-inv-detail-' ) as $hook )
        {
            $this->assertStringContainsString( $hook, $script, 'ezupdate.js finds ' . $hook );
        }
    }

    public function testPollingSwapsTheSameStatusBadge()
    {
        $script = self::read( 'design/standard/javascript/ezupdate.js' );
        $status = self::read( 'design/standard/templates/ezupdate/parts/status.tpl' );
        $this->assertStringContainsString( "'a4-badge a4-update-status ' + classes[data.status]", $script );
        foreach ( array( 'running', 'finished', 'stopped' ) as $state )
        {
            $this->assertMatchesRegularExpression( '/' . $state . ": '([\\w -]+)'/", $script );
            preg_match( '/' . $state . ": '([\\w -]+)'/", $script, $js );
            $this->assertMatchesRegularExpression( "/\\{case match='" . $state . "'\\}<span class=\"([^\"]+)\"/", $status );
            preg_match( "/\\{case match='" . $state . "'\\}<span class=\"([^\"]+)\"/", $status, $tpl );
            $fromScript = preg_split( '/\s+/', 'a4-badge a4-update-status ' . $js[1] );
            $fromTemplate = preg_split( '/\s+/', $tpl[1] );
            sort( $fromScript );
            sort( $fromTemplate );
            $this->assertSame( $fromTemplate, $fromScript, 'The badge of a ' . $state . ' run' );
        }
        $this->assertStringContainsString( 'ezupdate-job-output', $script, 'The job page still polls its output' );
        // the notice and the result it fills in are the shared notice the template draws
        $job = self::read( 'design/standard/templates/ezupdate/job.tpl' );
        $this->assertStringContainsString( "notice.className = 'a4-notice a4-update-note '", $script );
        $this->assertStringContainsString( 'class="a4-notice is-warn a4-update-note" id="ezupdate-job-notice"', $job );
        $this->assertStringContainsString( "result.className = 'a4-notice a4-update-section is-'", $script );
        $this->assertMatchesRegularExpression( '/id="ezupdate-job-result" class="a4-notice is-[^"]*a4-update-section"/', $job );
    }

    public function testViewsUseTheSetupMenuWithTheWidthSwitch()
    {
        $Module = $ViewList = null;
        include self::$ext . '/modules/update/module.php';
        $this->assertNotEmpty( $ViewList );
        foreach ( $ViewList as $name => $view )
        {
            $this->assertSame( 'ezsetupnavigationpart', $view['default_navigation_part'] ?? null, 'update/' . $name . ' is in the Setup navigation part' );
        }
        $menu = self::$root . '/design/admin4/templates/parts/setup/menu.tpl';
        if ( is_file( $menu ) )
        {
            $this->assertStringContainsString( "{include uri='design:parts/left_width_control.tpl'}", (string)file_get_contents( $menu ) );
        }
        $this->assertStringContainsString( 'Links[ezupdate]=update/dashboard', self::read( 'settings/menu.ini.append' ) );
    }

    /**
     * The source texts of the context extension/ezupdate in a translation file
     */
    private static function translated( $language )
    {
        $xml = simplexml_load_file( self::$ext . '/translations/' . $language . '/translation.ts' );
        $sources = array();
        foreach ( $xml->context as $context )
        {
            if ( (string)$context->name !== 'extension/ezupdate' )
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

    public function testEveryStringOfEveryTemplateIsTranslated()
    {
        $strings = array();
        foreach ( self::templates() as $path )
        {
            preg_match_all( "/'((?:[^'\\\\]|\\\\.)*)'\\|i18n\\(\\s*'extension\\/ezupdate'/", self::withoutComments( self::read( $path ) ), $m );
            foreach ( $m[1] as $text )
            {
                $strings[] = stripslashes( $text );
            }
        }
        $strings = array_unique( $strings );
        $this->assertGreaterThan( 200, count( $strings ) );
        foreach ( self::LANGUAGES as $language )
        {
            $known = self::translated( $language );
            $missing = array_values( array_filter( $strings, function ( $text ) use ( $known ) { return !isset( $known[$text] ); } ) );
            $this->assertSame( array(), $missing, $language . ' translates every string of the templates' );
        }
    }
}
