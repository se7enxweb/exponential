<?php
/**
 * The module wizard of Setup > RAD (expModuleWizard): how the view and policy lists of its form are read (ordered
 * and optional parameters, policies, limitations), contexts, addresses, the problems it finds, and the module it
 * writes - the generated module.php is loaded and must declare what was asked for, the generated ini files are read
 * back with eZINI.
 *
 * No database: an empty one stands in. Generated files are put under var/tmp to be read back, and removed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expRadWizardTestHelper.php';

class expModuleWizardTest extends PHPUnit\Framework\TestCase
{
    private $scratch;
    private $hadPathList;
    private $pathList;

    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
    }

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        expRadWizardTestHelper::useEmptyDatabase();
        $this->hadPathList = array_key_exists( 'eZModuleGlobalPathList', $GLOBALS );
        $this->pathList = $this->hadPathList ? $GLOBALS['eZModuleGlobalPathList'] : null;
        eZModule::setGlobalPathList( array( 'kernel' ) );
    }

    protected function tearDown(): void
    {
        expRadWizardTestHelper::restoreDatabase();
        if ( $this->hadPathList )
            $GLOBALS['eZModuleGlobalPathList'] = $this->pathList;
        else
            unset( $GLOBALS['eZModuleGlobalPathList'] );
        if ( $this->scratch )
            expRadWizardTestHelper::removeTree( $this->scratch );
    }

    public function testViewList()
    {
        $views = expModuleWizard::viewList( "list\nShow: read ID\nedit: edit, read ID Language? edit\n\nshow: duplicate\n9bad\n: nothing\nsearch: Query? Page? read" );
        $this->assertSame( array( 'list', 'show', 'edit', 'search' ), array_column( $views, 'name' ) );
        $this->assertSame( array( 'name' => 'list', 'script' => 'list.php', 'policies' => array(), 'parameters' => array(), 'optional' => array(), 'undeclared' => array() ), $views[0] );
        $this->assertSame( array( 'read' ), $views[1]['policies'] );
        $this->assertSame( array( 'ID' ), $views[1]['parameters'] );
        $this->assertSame( array( 'edit', 'read' ), $views[2]['policies'] );
        $this->assertSame( array( 'ID' ), $views[2]['parameters'] );
        $this->assertSame( array( 'Language' ), $views[2]['optional'] );
        $this->assertSame( array( 'Query', 'Page' ), $views[3]['optional'] );
        $this->assertSame( array(), expModuleWizard::viewList( array( 'list' ) ) );
        $this->assertCount( 30, expModuleWizard::viewList( implode( "\n", array_map( function ( $i ) { return "v$i"; }, range( 1, 40 ) ) ) ) );
    }

    public function testPolicyList()
    {
        $policies = expModuleWizard::policyList( "read: class, SECTION, nonsense, Class\nedit\nread: again\nadmin: SiteAccess Language Owner" );
        $this->assertSame( array(
            array( 'name' => 'read', 'limitations' => array( 'Class', 'Section' ) ),
            array( 'name' => 'edit', 'limitations' => array() ),
            array( 'name' => 'admin', 'limitations' => array( 'SiteAccess', 'Language', 'Owner' ) ),
        ), $policies );
        $this->assertSame( array(), expModuleWizard::policyList( null ) );
    }

    public static function identifierProvider()
    {
        return array( array( 'My Module', 'my_module' ), array( 'x', 'x' ), array( '_x', 'x' ), array( '1x', '' ), array( array(), '' ), array( 'a-b', 'a_b' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('identifierProvider')]
    public function testSafeIdentifier( $value, $expected )
    {
        $this->assertSame( $expected, expModuleWizard::safeIdentifier( $value ) );
    }

    public function testContexts()
    {
        $this->assertSame( 'edit', expModuleWizard::safeContext( 'edit' ) );
        $this->assertSame( '', expModuleWizard::safeContext( '' ) );
        $this->assertSame( 'administration', expModuleWizard::safeContext( 'nonsense' ) );
        $this->assertSame( 'administration', expModuleWizard::safeContext( array() ) );
        $this->assertArrayHasKey( 'browse', expModuleWizard::contexts() );
        $parts = expModuleWizard::navigationParts();
        $this->assertArrayHasKey( 'ezsetupnavigationpart', $parts );
        $this->assertArrayHasKey( 'ezcontentnavigationpart', $parts );
        $keys = array_keys( $parts );
        $sorted = $keys;
        sort( $sorted );
        $this->assertSame( $sorted, $keys );
        $this->assertSame( array( 'Section', 'Class', 'Owner', 'SiteAccess', 'Language' ), array_keys( expModuleWizard::limitations() ) );
    }

    private function settings( array $input = array() )
    {
        return expModuleWizard::settings( $input + array(
            'name' => 'k1e_mod_ext', 'module' => 'k1emodule', 'licence' => 'MIT', 'author' => 'Ada Example',
            'views' => "list: read\nshow: read ID\nedit: edit ID Language?", 'policies' => "read: Class Section\nedit: Class",
            'context' => 'administration', 'navigation' => 'ezcontentnavigationpart' ) );
    }

    public function testSettingsFillTheGaps()
    {
        $settings = expModuleWizard::settings( array( 'name' => 'k1e_gaps', 'views' => 'list' ) );
        $this->assertSame( 'k1e_gaps', $settings['module'] );
        $this->assertSame( 'K1e Gaps', $settings['title'] );
        $this->assertSame( 'ezsetupnavigationpart', $settings['navigation'] );
        $this->assertSame( 'A module for Exponential.', $settings['summary'] );
        // nothing posted is the empty context, a page on the site rather than in the admin
        $this->assertSame( '', $settings['context'] );
    }

    public function testUndeclaredPoliciesAreFoundAndAProblem()
    {
        $settings = $this->settings( array( 'views' => "list: read publish\nshow: read", 'policies' => 'read' ) );
        $this->assertSame( array( 'publish' ), $settings['views'][0]['undeclared'] );
        $this->assertSame( array(), $settings['views'][1]['undeclared'] );
        $problems = implode( ' ', expModuleWizard::problems( $settings ) );
        $this->assertStringContainsString( 'publish', $problems );
        $this->assertStringContainsString( 'list', $problems );
    }

    public function testProblems()
    {
        $this->assertSame( array(), expModuleWizard::problems( $this->settings() ) );
        $problems = expModuleWizard::problems( expModuleWizard::settings( array( 'licence' => 'MIT' ) ) );
        $this->assertCount( 3, $problems, implode( ' | ', $problems ) );
        // a module this installation has
        $problems = expModuleWizard::problems( $this->settings( array( 'module' => 'content' ) ) );
        $this->assertArrayHasKey( 'exists_module', $problems );
        $this->assertSame( array(), expModuleWizard::files( expModuleWizard::settings( array( 'name' => 'k1e_x' ) ) ) );
    }

    public function testAddressOf()
    {
        $settings = $this->settings();
        $this->assertSame( '/k1emodule/list', expModuleWizard::addressOf( $settings, $settings['views'][0] ) );
        $this->assertSame( '/k1emodule/show/<id>', expModuleWizard::addressOf( $settings, $settings['views'][1] ) );
        $this->assertSame( '/k1emodule/edit/<id>/(language)/<value>', expModuleWizard::addressOf( $settings, $settings['views'][2] ) );
        $this->assertSame( "[ExtensionSettings]\nActiveExtensions[]=k1e_mod_ext", expModuleWizard::activation( $settings ) );
    }

    public function testGeneratedModuleDeclaresTheViewsAndPolicies()
    {
        $files = expModuleWizard::files( $this->settings() );
        expRadWizardTestHelper::assertFilesAreSafe( $this, $files );
        $this->assertArrayHasKey( 'modules/k1emodule/module.php', $files );
        foreach ( array( 'list', 'show', 'edit' ) as $view )
        {
            $this->assertArrayHasKey( "modules/k1emodule/$view.php", $files );
            $this->assertArrayHasKey( "design/k1e_mod_ext/templates/k1emodule/$view.tpl", $files );
        }

        $this->scratch = expRadWizardTestHelper::scratch( 'module-wizard' );
        file_put_contents( $this->scratch . '/module.php', $files['modules/k1emodule/module.php'] );
        $declared = ( function ( $file ) {
            $Module = $ViewList = $FunctionList = null;
            include $file;
            return array( $Module, $ViewList, $FunctionList );
        } )( $this->scratch . '/module.php' );
        list( $module, $viewList, $functionList ) = $declared;
        $this->assertSame( 'K1emodule', $module['name'] );
        $this->assertSame( array( 'list', 'show', 'edit' ), array_keys( $viewList ) );
        $this->assertSame( 'list.php', $viewList['list']['script'] );
        $this->assertSame( array( 'ID' ), $viewList['show']['params'] );
        // an unordered parameter is named in the address in lower case: /(language)/<value>
        $this->assertSame( array( 'language' => 'Language' ), $viewList['edit']['unordered_params'] );
        $this->assertSame( array( 'read', 'edit' ), array_keys( $functionList ) );
        $this->assertArrayHasKey( 'Class', $functionList['read'] );
        $this->assertArrayHasKey( 'Section', $functionList['read'] );
        $this->assertSame( array(), $functionList['edit'] === array() ? array() : array_diff( array_keys( $functionList['edit'] ), array( 'Class' ) ) );

        $moduleIni = $this->readIni( $files['settings/module.ini.append.php'], 'module.ini' );
        $this->assertSame( array( 'k1e_mod_ext' ), $moduleIni->variable( 'ModuleSettings', 'ExtensionRepositories' ) );
        $this->assertSame( array( 'k1emodule' ), $moduleIni->variable( 'ModuleSettings', 'ModuleList' ) );
        $designIni = $this->readIni( $files['settings/design.ini.append.php'], 'design.ini' );
        $this->assertSame( array( 'k1e_mod_ext' ), $designIni->variable( 'ExtensionSettings', 'DesignExtensions' ) );
        $menuIni = $this->readIni( $files['settings/menu.ini.append.php'], 'menu.ini' );
        $this->assertSame( '/k1emodule/list', $menuIni->variable( 'NavigationPartMenu_ezcontentnavigationpart', 'MenuURL' )['k1emodule'] ?? null );
        $this->assertStringContainsString( '/k1emodule/edit/<id>/(language)/<value>', $files['README.md'] );
    }

    private function readIni( $contents, $name )
    {
        $dir = $this->scratch . '/' . md5( $contents );
        mkdir( $dir );
        file_put_contents( $dir . '/' . $name, $contents );
        return eZINI::fetchFromFile( $dir . '/' . $name );
    }

    public function testViewScriptsReadTheirParameters()
    {
        $files = expModuleWizard::files( $this->settings() );
        $show = $files['modules/k1emodule/show.php'];
        $this->assertStringContainsString( "\$Params['ID']", $show );
        $this->assertStringContainsString( "\$Result", $show );
        $this->assertStringContainsString( 'design:k1emodule/show.tpl', $show );
        $edit = $files['modules/k1emodule/edit.php'];
        $this->assertStringContainsString( "Language", $edit );
    }

    public function testOnlyTheChosenParts()
    {
        $files = expModuleWizard::files( $this->settings( array( 'parts' => array( 'module', 'licence' ) ) ) );
        $this->assertSame( array( 'LICENSE', 'modules/k1emodule/module.php' ), array_keys( $files ) );
        $this->assertStringContainsString( 'MIT License', $files['LICENSE'] );
    }
}
