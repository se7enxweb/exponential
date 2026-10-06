<?php
/**
 * Every extension wizard of Setup > RAD, asked for a whole extension: the settings it makes of a form, that a
 * sensible form has no problems, and that every file it would write is sound - php that parses, ini files that are
 * one php comment (an ini file here is a php file), xml that is well formed, json that decodes. The same again with
 * every text field filled with an attempt to end the comment it is written into and run code, which must reach no
 * generated file as code; and with each part chosen on its own and with none.
 *
 * No database: an empty one stands in (no content class or class group exists). Nothing is written to disk
 * (files() only returns the contents).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expRadWizardTestHelper.php';

class expRadWizardsGeneratedFilesTest extends PHPUnit\Framework\TestCase
{
    const ATTACK = "Nice k1e_injected */ k1e_injected(); /* *// k1e_injected(); ?> <?php k1e_injected();";

    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
    }

    private $hadPathList;
    private $pathList;
    private $cache;
    private $injected;

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        // the kernel's modules, as a request has them (the triggers are read from the content module)
        $this->hadPathList = array_key_exists( 'eZModuleGlobalPathList', $GLOBALS );
        $this->pathList = $this->hadPathList ? $GLOBALS['eZModuleGlobalPathList'] : null;
        if ( !is_array( $this->pathList ) )
            eZModule::setGlobalPathList( array( 'kernel' ) );
        expRadWizardTestHelper::useEmptyDatabase();
        // what the kernel caches while a wizard reads it goes to a scratch directory
        $this->cache = expRadWizardTestHelper::scratch( 'wizards-cache' );
        $this->injected = expRadWizardTestHelper::injectSiteIni( array( 'FileSettings' => array( 'CacheDir' => $this->cache ) ) );
    }

    protected function tearDown(): void
    {
        expRadWizardTestHelper::restoreDatabase();
        expRadWizardTestHelper::restoreInjected( $this->injected );
        expRadWizardTestHelper::removeTree( $this->cache );
        if ( $this->hadPathList )
            $GLOBALS['eZModuleGlobalPathList'] = $this->pathList;
        else
            unset( $GLOBALS['eZModuleGlobalPathList'] );
    }

    /**
     * Lists that are read off the installation, filled in when the test runs rather than in the data provider.
     */
    private static function resolve( array $input )
    {
        if ( isset( $input['triggers'] ) && $input['triggers'] === '@triggers' )
            $input['triggers'] = self::someTriggers();
        return $input;
    }

    /**
     * A sensible form for each wizard.
     */
    public static function inputs()
    {
        $common = array( 'author' => 'Ada Example', 'licence' => 'MIT', 'version' => '1.2.3', 'vendor' => 'acme' );
        $inputs = array(
            'module' => array( 'expModuleWizard', $common + array(
                'name' => 'k1e_mod_ext', 'module' => 'k1emodule', 'title' => 'K1e Module',
                'views' => "list\nshow: read ID\nedit: edit read ID Language?\n",
                'policies' => "read: Class Section\nedit: Class" ) ),
            'datatype' => array( 'expDatatypeWizard', $common + array(
                'name' => 'k1e_dt_ext', 'type' => 'k1erating', 'group' => 'K1e',
                'storage' => array( 'data_text', 'data_int', 'data_float', 'sort_key_int', 'sort_key_string' ),
                'capabilities' => array_keys( expDatatypeWizard::capabilities() ),
                'class_setting_names' => array( 'data_int1' => 'max value', 'data_text1' => 'pattern' ) ) ),
            'design' => array( 'expDesignExtensionWizard', $common + array(
                'name' => 'k1e_design_ext', 'siteaccess' => 'k1e_site', 'base_design' => 'standard' ) ),
            'kernel override' => array( 'expKernelOverrideWizard', $common + array(
                'name' => 'k1e_override_ext', 'class' => 'eZURI', 'reason' => 'To test the override wizard.', 'find' => 'eZURI' ) ),
            'settings' => array( 'expSettingsExtensionWizard', $common + array(
                'name' => 'k1e_settings_ext', 'siteaccess' => 'k1e_site', 'theme' => 'k1etheme',
                'aliases' => "k1e_thumb: geometry/scalewidth=100\nk1e_wide: geometry/scaledownonly=800;600, colorspace/gray",
                'rules' => "article: siblings, parent\nfolder: all",
                'forms' => "feedback_form: feedback",
                'operations' => 'k1e_operation, other_operation',
                'overrides' => "site.ini [SiteSettings] SiteName=K1e Site\nsite.ini [SiteSettings] DefaultPage=content/view/full/2",
                'roots' => "k1e_extra",
                'sizes' => "normal: 32x32\nsmall: 16x16" ) ),
            'template' => array( 'expTemplateExtensionWizard', $common + array(
                'name' => 'k1e_tpl_ext', 'module' => 'k1etpl',
                'operators' => 'k1e_shout, k1e_whisper', 'functions' => "k1e_box", 'fetches' => 'k1e_list k1e_count',
                'aliases' => 'k1e_alias',
                'parameters' => "limit integer\nlabel string\nflag boolean", 'input' => 1, 'output' => 1, 'children' => 1, 'submitted' => 1 ) ),
            'content' => array( 'expContentExtensionWizard', $common + array(
                'name' => 'k1e_content_ext', 'class' => 'k1e_article', 'class_name' => 'K1e Article', 'locale' => 'eng-GB',
                'attributes' => "title, ezstring, Title, required, searchable\nbody, ezxmltext, Body\nrating, ezinteger, Rating",
                'tags' => "k1e_note: tone, size\nk1e_quote",
                'strings' => "k1e/content|Hello\nk1e/content|Goodbye" ) ),
            'workflow event' => array( 'expWorkflowEventWizard', $common + array(
                'name' => 'k1e_event_ext', 'event' => 'k1eapprove', 'label' => 'K1e approve',
                'triggers' => '@triggers', 'statuses' => array_keys( expWorkflowEventWizard::statuses() ) ) ),
        );
        foreach ( array_keys( expHandlerWizard::kinds() ) as $kind )
            $inputs['handler ' . $kind] = array( 'expHandlerWizard', $common + array( 'name' => 'k1e_handler_ext', 'kind' => $kind ) );
        return $inputs;
    }

    public static function someTriggers()
    {
        $triggers = array();
        foreach ( expWorkflowEventWizard::triggers() as $module => $operations )
            foreach ( $operations as $operation => $points )
                foreach ( $points as $point )
                    $triggers[] = "$module/$operation/$point";
        return array_slice( $triggers, 0, 3 );
    }

    public static function wizardProvider()
    {
        $cases = array();
        foreach ( self::inputs() as $label => $case )
            $cases[$label] = $case;
        return $cases;
    }

    private static function allParts( $class )
    {
        $parts = array_keys( $class::parts() );
        // Event listeners need the events the survey finds in every extension's code; the settings wizard's own
        // test covers them
        if ( $class === 'expSettingsExtensionWizard' )
            $parts = array_values( array_diff( $parts, array( 'event' ) ) );
        return $parts;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('wizardProvider')]
    public function testASensibleFormGivesSoundFiles( $class, array $input )
    {
        $input = self::resolve( $input );
        $settings = $class::settings( $input + array( 'parts' => self::allParts( $class ) ) );
        $this->assertSame( $input['name'], $settings['name'] );
        $this->assertSame( 'MIT', $settings['licence'] );
        $this->assertSame( array(), array_values( $class::problems( $settings ) ), 'problems: ' . implode( ' | ', $class::problems( $settings ) ) );

        $files = $class::files( $settings );
        $this->assertNotEmpty( $files );
        expRadWizardTestHelper::assertFilesAreSafe( $this, $files );
        $this->assertSame( array_keys( $files ), array_unique( array_keys( $files ) ) );
        foreach ( array_keys( $files ) as $path )
        {
            $this->assertStringNotContainsString( '..', $path );
            $this->assertStringStartsNotWith( '/', $path );
        }
        $this->assertStringContainsString( $input['name'], $class::activation( $settings ) );
        $this->assertNotSame( '', $class::wizardName() );
        $this->assertSame( self::allParts( $class ), $class::chosenParts( $settings ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('wizardProvider')]
    public function testTypedTextNeverBecomesCode( $class, array $input )
    {
        $input = self::resolve( $input );
        foreach ( array( 'title', 'summary', 'author', 'version', 'group', 'class_name', 'pattern', 'reason', 'label', 'find' ) as $field )
            $input[$field] = self::ATTACK;
        $settings = $class::settings( $input + array( 'parts' => self::allParts( $class ) ) );
        $files = $class::files( $settings );
        $this->assertNotEmpty( $files );
        expRadWizardTestHelper::assertFilesAreSafe( $this, $files );
        foreach ( $files as $path => $contents )
            $this->assertStringNotContainsString( '*/ k1e_injected', $contents, $path );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('wizardProvider')]
    public function testEachPartOnItsOwn( $class, array $input )
    {
        $input = self::resolve( $input );
        $none = $class::files( $class::settings( $input + array( 'parts' => array() ) ) );
        expRadWizardTestHelper::assertFilesAreSafe( $this, $none );
        $all = $class::files( $class::settings( $input + array( 'parts' => self::allParts( $class ) ) ) );
        $union = array();
        foreach ( self::allParts( $class ) as $part )
        {
            $settings = $class::settings( $input + array( 'parts' => array( $part ) ) );
            $this->assertSame( array( $part ), $class::chosenParts( $settings ) );
            $files = $class::files( $settings );
            expRadWizardTestHelper::assertFilesAreSafe( $this, $files );
            $union += $files;
        }
        // nothing a part writes on its own is missing from the whole extension
        $this->assertSame( array(), array_values( array_diff( array_keys( $union ), array_keys( $all ) ) ) );
        $this->assertLessThanOrEqual( count( $all ), count( $none ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('wizardProvider')]
    public function testAnEmptyFormNeedsAName( $class, array $input )
    {
        $settings = $class::settings( array() );
        $this->assertSame( '', $settings['name'] );
        $this->assertNotEmpty( $class::problems( $settings ) );
        // the first visit: every part at its default
        foreach ( $class::parts() as $key => $part )
            $this->assertSame( (bool)$part['default'], (bool)$settings['parts'][$key], $key );
        $this->assertSame( '1.0.0', $settings['version'] );
        $this->assertSame( 'exponential', $settings['vendor'] );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('wizardProvider')]
    public function testAnExistingExtensionIsAProblemTheArchiveLeavesOut( $class, array $input )
    {
        $input = self::resolve( $input );
        $input['name'] = 'ezjscore';
        $problems = $class::problems( $class::settings( $input + array( 'parts' => self::allParts( $class ) ) ) );
        $this->assertArrayHasKey( 'exists', $problems );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('wizardProvider')]
    public function testAnUnconfiguredLicenceIsAProblem( $class, array $input )
    {
        $input = self::resolve( $input );
        $input['licence'] = 'WTFPL';
        $problems = $class::problems( $class::settings( $input ) );
        $this->assertArrayHasKey( 'licence', $problems );
    }

    /**
     * A class that parses can still fail to load: a method incompatible with the one it overrides, an abstract
     * method left out, a parent or interface that does not exist. Each generated class is loaded on top of the
     * kernel's autoloads in a php process of its own (a fatal error there is reported, not the end of this run).
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('wizardProvider')]
    public function testGeneratedClassesLoadOnTopOfTheKernel( $class, array $input )
    {
        if ( !function_exists( 'exec' ) )
            $this->markTestSkipped( 'exec() is disabled' );
        $input = self::resolve( $input );
        $files = $class::files( $class::settings( $input + array( 'parts' => self::allParts( $class ) ) ) );
        $loader = $this->cache . '/load.php';
        file_put_contents( $loader, "<?php\nchdir( \$argv[1] );\nrequire 'vendor/autoload.php';\n"
                                  . "\$before = get_declared_classes();\nrequire \$argv[2];\n"
                                  . "foreach ( array_diff( get_declared_classes(), \$before ) as \$c ) { \$r = new ReflectionClass( \$c ); if ( realpath( (string)\$r->getFileName() ) === realpath( \$argv[2] ) && \$r->isAbstract() && !\$r->isInterface() ) { echo \"ABSTRACT \$c\\n\"; } }\n"
                                  . "echo \"LOADED\\n\";\n" );
        $loaded = 0;
        foreach ( $files as $path => $contents )
        {
            if ( substr( $path, -4 ) !== '.php' || !preg_match( '/^(abstract |final )?class \w+/m', $contents ) )
                continue;
            $file = $this->cache . '/' . str_replace( '/', '_', $path );
            file_put_contents( $file, $contents );
            $output = array();
            exec( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $loader ) . ' ' . escapeshellarg( expRadWizardTestHelper::root() )
                  . ' ' . escapeshellarg( $file ) . ' 2>&1', $output, $status );
            $this->assertSame( 0, $status, "$path: " . implode( "\n", $output ) );
            $this->assertSame( array( 'LOADED' ), $output, "$path: " . implode( "\n", $output ) );
            $loaded++;
        }
        $this->assertGreaterThan( 0, $loaded );
    }
}
