<?php
/**
 * The templates the design extension wizard of Setup > RAD writes: every template operator they use is one the
 * kernel registers (eZTemplate::factory(), plus ezjscore's, which the generated design loads its files with), and
 * the stylesheets and scripts of design.ini are actually printed (ezcss_load / ezscript_load, not only required).
 *
 * No database: an empty one stands in. Nothing is written to disk (files() only returns the contents).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expRadWizardTestHelper.php';

class expDesignExtensionWizardTest extends PHPUnit\Framework\TestCase
{
    const NAME = 'k1e_design_tpl';

    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
    }

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        expRadWizardTestHelper::useEmptyDatabase();
    }

    protected function tearDown(): void
    {
        expRadWizardTestHelper::restoreDatabase();
    }

    /**
     * The files of a design extension with every part chosen.
     *
     * @return array path => contents
     */
    private function generated()
    {
        // a posted form lists the ticked parts
        $parts = array_keys( expDesignExtensionWizard::parts() );

        $settings = expDesignExtensionWizard::settings( array(
            'name' => self::NAME, 'title' => 'K1e Design', 'author' => 'Ada Example', 'licence' => 'MIT',
            'version' => '1.0.0', 'vendor' => 'acme', 'siteaccess' => 'k1e_site', 'base_design' => 'standard',
            'parts' => $parts ) );
        $this->assertSame( array(), expDesignExtensionWizard::problems( $settings ) );

        return expDesignExtensionWizard::files( $settings );
    }

    private function templates()
    {
        $templates = array();
        foreach ( $this->generated() as $path => $contents )
        {
            if ( substr( $path, -4 ) === '.tpl' )
                $templates[$path] = $contents;
        }
        return $templates;
    }

    private function template( $file )
    {
        $path = 'design/' . self::NAME . '/templates/' . $file;
        $templates = $this->templates();
        $this->assertArrayHasKey( $path, $templates );
        return $templates[$path];
    }

    /**
     * The operators a template uses: names after a pipe and names called with brackets, inside template tags,
     * comments and quoted strings left out.
     *
     * @return array operator name => true
     */
    public static function operatorsUsed( $template )
    {
        $template = preg_replace( '/\{\*.*?\*\}/s', '', $template );
        preg_match_all( '/\{([^{}]*)\}/', $template, $tags );

        $used = array();
        foreach ( $tags[1] as $tag )
        {
            $tag = preg_replace( array( "/'[^']*'/", '/"[^"]*"/' ), "''", $tag );
            preg_match_all( '/\|\s*([a-z_][a-z0-9_]*)/i', $tag, $piped );
            preg_match_all( '/(?<![\$\.\w])([a-z_][a-z0-9_]*)\s*\(/i', $tag, $called );
            foreach ( array_merge( $piped[1], $called[1] ) as $operator )
                $used[$operator] = true;
        }
        return $used;
    }

    /**
     * What a site with this design can call: the operators eZTemplate::factory() registers, and ezjscore's.
     *
     * @return array operator name => true
     */
    private static function operatorsRegistered()
    {
        $registered = array();
        foreach ( array_keys( eZTemplate::factory()->Operators ) as $operator )
            $registered[$operator] = true;

        $ezjscoreAutoload = expRadWizardTestHelper::root() . '/extension/ezjscore/autoloads/eztemplateautoload.php';
        $ezjscore = ( static function ( $file )
        {
            $eZTemplateOperatorArray = array();
            include $file;
            return $eZTemplateOperatorArray;
        } )( $ezjscoreAutoload );
        foreach ( $ezjscore as $definition )
        {
            foreach ( isset( $definition['operator_names'] ) ? $definition['operator_names'] : array() as $operator )
                $registered[$operator] = true;
        }
        return $registered;
    }

    public function testTheOperatorListIsReadable()
    {
        $used = self::operatorsUsed( "{\$a.path|reverse|wash} {ezini( 'A', 'B' )|extract_left( 2 )} {def \$x=fetch( 'content', 'list', hash( 'a', array( 1, true() ) ) )}" );
        $this->assertSame( array( 'array', 'extract_left', 'ezini', 'fetch', 'hash', 'reverse', 'true', 'wash' ),
                           self::sortedKeys( $used ) );

        $registered = self::operatorsRegistered();
        foreach ( array( 'wash', 'ezurl', 'ezini', 'ezcss_load', 'ezscript_load' ) as $operator )
            $this->assertArrayHasKey( $operator, $registered );
        $this->assertArrayNotHasKey( 'implode_key', $registered );
    }

    public function testEveryOperatorTheTemplatesUseIsRegistered()
    {
        $registered = self::operatorsRegistered();
        $templates = $this->templates();
        $this->assertNotEmpty( $templates );

        foreach ( $templates as $path => $contents )
        {
            foreach ( array_keys( self::operatorsUsed( $contents ) ) as $operator )
                $this->assertArrayHasKey( $operator, $registered, "$path uses the operator '$operator', which no one registers" );
        }
    }

    public function testThePageHeadPrintsTheStylesheetsOfDesignIni()
    {
        $head = $this->template( 'page_head.tpl' );

        $this->assertStringContainsString( "ezcss_load(", $head );
        $this->assertStringContainsString( "ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' )", $head );
        $this->assertStringNotContainsString( "ezcss_require(", self::withoutComments( $head ) );
    }

    public function testThePagelayoutPrintsTheScriptsBeforeTheEndOfTheBody()
    {
        $layout = $this->template( 'pagelayout.tpl' );

        $load = strpos( $layout, 'ezscript_load(' );
        $this->assertNotFalse( $load );
        $this->assertLessThan( strpos( $layout, '</body>' ), $load );
        $this->assertStringContainsString( "ezini( 'JavaScriptSettings', 'JavaScriptList', 'design.ini' )", $layout );
        $this->assertStringNotContainsString( "ezscript_require(", self::withoutComments( $layout ) );
    }

    public function testThePageHeadHasAPlainCharsetAndATitleFromThePath()
    {
        $head = $this->template( 'page_head.tpl' );

        $this->assertStringContainsString( '<meta charset="utf-8" />', $head );
        $this->assertStringNotContainsString( 'extract_left( 0 )', $head );
        $this->assertMatchesRegularExpression( '#<title>\{foreach \$module_result\.path\|reverse as \$(\w+)\}\{\$\1\.text\|wash\}\{delimiter\} / \{/delimiter\}\{/foreach\}#', $head );
    }

    public function testThePrintStylesheetIsLookedUpWhereTheWizardPutsIt()
    {
        $files = $this->generated();
        $this->assertArrayHasKey( 'design/' . self::NAME . '/stylesheets/print.css', $files );
        $this->assertStringContainsString( "{'stylesheets/print.css'|ezdesign}", $this->template( 'page_head.tpl' ) );
    }

    public function testThePageTemplatesReadTheFrontendLists()
    {
        $this->assertStringContainsString( "ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' )", $this->template( 'page_head.tpl' ) );
        $this->assertStringContainsString( "ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' )", $this->template( 'pagelayout.tpl' ) );
    }

    /**
     * design.ini marks CSSFileList[] and JavaScriptList[] as deprecated; the wizard writes the Frontend lists,
     * which the generated page_head.tpl and pagelayout.tpl load.
     */
    public function testDesignIniUsesNoDeprecatedKeys()
    {
        $ini = $this->generated()['settings/design.ini.append.php'];

        $this->assertMatchesRegularExpression( '/^FrontendCSSFileList\[\]=site\.css$/m', $ini );
        $this->assertMatchesRegularExpression( '/^FrontendJavaScriptList\[\]=site\.js$/m', $ini );
        $this->assertDoesNotMatchRegularExpression( '/^\s*CSSFileList\[\]/m', $ini );
        $this->assertDoesNotMatchRegularExpression( '/^\s*JavaScriptList\[\]/m', $ini );
    }

    /**
     * The folder the legacy extension installer puts the package in, by the rule of
     * LegacyExtensionInstaller::getInstallPath(): extra.ezpublish-legacy-extension-name, else the part of the
     * package name behind the slash.
     */
    private static function installerFolder( array $composer )
    {
        if ( isset( $composer['extra']['ezpublish-legacy-extension-name'] ) )
            return $composer['extra']['ezpublish-legacy-extension-name'];
        list( , $folder ) = explode( '/', $composer['name'], 2 );
        return $folder;
    }

    public function testComposerJsonInstallsIntoAFolderNamedAfterTheExtension()
    {
        $composer = json_decode( $this->generated()['composer.json'], true );

        $this->assertSame( 'ezpublish-legacy-extension', $composer['type'] );
        $this->assertSame( 'acme/k1e-design-tpl', $composer['name'] );
        $this->assertSame( self::NAME, self::installerFolder( $composer ) );

        // the rule above is the installer's: it reads this key and no other
        $installer = expRadWizardTestHelper::root() . '/vendor/se7enxweb/exponential-legacy-installer/src/eZ/Publish/Composer/LegacyExtensionInstaller.php';
        if ( is_file( $installer ) )
        {
            $source = file_get_contents( $installer );
            $this->assertStringContainsString( "\$extra['ezpublish-legacy-extension-name']", $source );
            $this->assertStringNotContainsString( 'installer-name', $source );
        }
    }

    public function testTheTemplateOperatorsPartWritesTheAutoloadFile()
    {
        $files = $this->generated();

        $this->assertArrayHasKey( 'autoloads/eztemplateautoload.php', $files );
        $this->assertArrayHasKey( 'autoloads/' . self::NAME . 'operators.php', $files );
        $this->assertStringContainsString( "ExtensionAutoloadPath[]=" . self::NAME . "\n", $files['settings/site.ini.append.php'] );
        expRadWizardTestHelper::assertPhpParses( $this, $files['autoloads/eztemplateautoload.php'], 'eztemplateautoload.php' );
        expRadWizardTestHelper::assertPhpParses( $this, $files['autoloads/' . self::NAME . 'operators.php'], 'operators.php' );

        // without the part, neither file
        $settings = expDesignExtensionWizard::settings( array( 'name' => self::NAME, 'licence' => 'MIT', 'parts' => array( 'pagelayout' ) ) );
        $this->assertArrayNotHasKey( 'autoloads/eztemplateautoload.php', expDesignExtensionWizard::files( $settings ) );
    }

    /**
     * The extension switched on: the template engine is handed its autoloads directory, as eZTemplate::factory()
     * does for every ExtensionAutoloadPath[], and the operator is then registered and works.
     */
    public function testTheOperatorIsRegisteredWhenTheExtensionIsActive()
    {
        $files = $this->generated();
        $scratch = expRadWizardTestHelper::scratch( 'design-operators' );
        try
        {
            mkdir( $scratch . '/autoloads' );
            foreach ( array( 'autoloads/eztemplateautoload.php', 'autoloads/' . self::NAME . 'operators.php' ) as $path )
                file_put_contents( $scratch . '/' . $path, $files[$path] );

            // the script path in the autoload file is where the extension lives once installed
            $definitions = ( static function ( $file )
            {
                $eZTemplateOperatorArray = array();
                include $file;
                return $eZTemplateOperatorArray;
            } )( $scratch . '/autoloads/eztemplateautoload.php' );
            $this->assertCount( 1, $definitions );
            $this->assertSame( 'extension/' . self::NAME . '/autoloads/' . self::NAME . 'operators.php', $definitions[0]['script'] );
            $this->assertSame( self::NAME . 'Operators', $definitions[0]['class'] );

            // the class is loaded from the scratch copy, so the engine does not look for it under extension/
            if ( !class_exists( self::NAME . 'Operators', false ) )
                require $scratch . '/autoloads/' . self::NAME . 'operators.php';

            $tpl = new eZTemplate();
            $tpl->setAutoloadPathList( array( substr( $scratch, strlen( expRadWizardTestHelper::root() ) + 1 ) . '/autoloads/' ) );
            $tpl->autoload();
            $this->assertArrayHasKey( self::NAME . '_excerpt', $tpl->Operators );

            $tpl->loadAndRegisterOperators( $tpl->Operators[self::NAME . '_excerpt'] );
            $operator = $tpl->Operators[self::NAME . '_excerpt'];
            $this->assertInstanceOf( self::NAME . 'Operators', $operator );

            $value = '<p>A sentence that is longer than ten characters</p>';
            $operator->modify( $tpl, self::NAME . '_excerpt', array(), '', '', $value, array( 'length' => 10 ) );
            $this->assertSame( 'A sentence...', $value );
        }
        finally
        {
            expRadWizardTestHelper::removeTree( $scratch );
        }
    }

    private static function withoutComments( $template )
    {
        return preg_replace( '/\{\*.*?\*\}/s', '', $template );
    }

    private static function sortedKeys( array $map )
    {
        $keys = array_keys( $map );
        sort( $keys );
        return $keys;
    }
}
