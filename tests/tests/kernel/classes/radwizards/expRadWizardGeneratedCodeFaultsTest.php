<?php
/**
 * Faults a docs writer found in what the Setup > RAD wizards generate, each pinned:
 * - template extension wizard: operator calls in the README and examples are positional ({$v|op( 3, 'text' )}), the
 *   parser refuses name=value inside an operator's brackets; operatorTemplateHints() has no duplicate key; the
 *   README's autoload command is one ezpgenerateautoloads.php accepts (--extension takes no value)
 * - datatype wizard: templates under design/standard/templates, where DesignExtensions[] finds them; the edit
 *   template posts the field the storage chosen implies, and the class names the same; no DataTypeGroup[]
 * - module wizard: templates under design/standard/templates; only number parameters are cast to int
 * - design wizard: the README does not claim the design is used without SiteDesign
 * - handler wizard: the mail transport stub reports the mail as not sent, with a TODO
 *
 * No database: an empty one stands in. Nothing is written to disk (files() only returns the contents).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expRadWizardTestHelper.php';

class expRadWizardGeneratedCodeFaultsTest extends PHPUnit\Framework\TestCase
{
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

    private static function allParts( $class )
    {
        return array_keys( $class::parts() );
    }

    // ── template extension wizard ───────────────────────────────────────────

    private function templateFiles( array $input = array() )
    {
        $settings = expTemplateExtensionWizard::settings( $input + array(
            'name' => 'k1e_tpl_ext', 'module' => 'k1etpl', 'licence' => 'MIT',
            'operators' => 'k1e_shout', 'functions' => 'k1e_box', 'parameters' => "limit integer\nlabel string\nflag boolean",
            'input' => 1, 'output' => 1, 'submitted' => 1, 'parts' => self::allParts( 'expTemplateExtensionWizard' ) ) );
        return array( $settings, expTemplateExtensionWizard::files( $settings ) );
    }

    public function testOperatorCallsArePositional()
    {
        list( $settings, $files ) = $this->templateFiles();
        $usage = expTemplateExtensionWizard::usage( $settings );

        $this->assertSame( "{\$value|k1e_shout( 3, 'text', true() )|wash}", $usage[0]['example'] );
        // a function still takes its parameters by name
        $this->assertSame( "{k1e_box limit=3 label='text' flag=true()}", strtok( $usage[1]['example'], "\n" ) );

        foreach ( array( 'README.md', 'doc/examples.php' ) as $path )
        {
            $this->assertArrayHasKey( $path, $files );
            $this->assertDoesNotMatchRegularExpression( '/\|k1e_shout\([^)]*\w=/', $files[$path], $path );
            $this->assertStringContainsString( "|k1e_shout( 3, 'text', true() )", $files[$path], $path );
        }
    }

    public function testOperatorHintsHaveEachKeyOnce()
    {
        list( , $files ) = $this->templateFiles( array( 'hints' => array( 'input-as-parameter' ) ) );
        $class = $files['autoloads/k1etplextoperators.php'];
        foreach ( array_keys( expTemplateExtensionWizard::hints() ) as $key )
            $this->assertSame( 1, substr_count( $class, "'" . $key . "' =>" ), $key );
        $this->assertStringContainsString( "'input-as-parameter' => true,", $class );
    }

    /**
     * ezpgenerateautoloads.php's --extension is a switch: "--extension=<name>" is refused.
     */
    public function testAutoloadCommandsAreOnesTheScriptAccepts()
    {
        $outputs = array(
            'template' => $this->templateFiles()[1],
            'datatype' => expDatatypeWizard::files( expDatatypeWizard::settings( array( 'name' => 'k1e_dt_ext', 'licence' => 'MIT',
                                                                                     'parts' => self::allParts( 'expDatatypeWizard' ) ) ) ),
        );
        foreach ( $outputs as $wizard => $files )
        {
            $this->assertStringContainsString( "php bin/php/ezpgenerateautoloads.php --extension\n", $files['README.md'], $wizard );
            $this->assertStringNotContainsString( '--extension=', $files['README.md'], $wizard );
        }

        $source = file_get_contents( expRadWizardTestHelper::root() . '/kernel/private/classes/commands/ezpgenerateautoloads.php' );
        $this->assertStringContainsString( "new \\ezcConsoleOption( 'e', 'extension', \\ezcConsoleInput::TYPE_NONE )", $source );
    }

    // ── datatype wizard ─────────────────────────────────────────────────────

    private function datatypeFiles( array $storage )
    {
        $settings = expDatatypeWizard::settings( array( 'name' => 'k1e_dt_ext', 'type' => 'k1erating', 'licence' => 'MIT',
                                                       'storage' => $storage, 'submitted' => 1, 'Submitted' => 1,
                                                       'capabilities' => array_keys( expDatatypeWizard::capabilities() ),
                                                       'parts' => self::allParts( 'expDatatypeWizard' ) ) );
        return array( $settings, expDatatypeWizard::files( $settings ) );
    }

    public function testDatatypeTemplatesAreWhereTheDesignChainLooks()
    {
        list( , $files ) = $this->datatypeFiles( array( 'data_text' ) );
        $templates = array_filter( array_keys( $files ), function ( $path ) { return substr( $path, -4 ) === '.tpl'; } );
        $this->assertNotEmpty( $templates );
        foreach ( $templates as $path )
            $this->assertStringStartsWith( 'design/standard/templates/', $path );
        $this->assertArrayHasKey( 'design/standard/templates/content/datatype/edit/k1erating.tpl', $files );
        $this->assertStringContainsString( 'DesignExtensions[]=k1e_dt_ext', $files['settings/design.ini.append.php'] );
    }

    public static function storageProvider()
    {
        return array(
            'text'    => array( array( 'data_text', 'sort_key_string' ), 'data_text' ),
            'integer' => array( array( 'data_int', 'sort_key_int' ), 'data_int' ),
            'float'   => array( array( 'data_float' ), 'data_float' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('storageProvider')]
    public function testTheEditTemplatePostsTheFieldOfTheChosenStorage( array $storage, $column )
    {
        list( $settings, $files ) = $this->datatypeFiles( $storage );
        $this->assertSame( $column, expDatatypeWizard::valueColumn( $settings ) );

        $variable = '_k1erating_' . $column . '_';
        $edit = $files['design/standard/templates/content/datatype/edit/k1erating.tpl'];
        $this->assertStringContainsString( 'name="{$attribute_base}' . $variable . '{$attribute.id}"', $edit );
        if ( $column !== 'data_text' )
            $this->assertStringNotContainsString( '_data_text_', $edit );

        $class = $files['datatypes/k1erating/k1eratingtype.php'];
        $this->assertStringContainsString( "const VALUE_VARIABLE = '" . $variable . "';", $class );
        $this->assertStringContainsString( "const VALUE_FIELD = '" . $column . "';", $class );
        $this->assertStringContainsString( 'return $contentObjectAttribute->attribute( self::VALUE_FIELD );', $class );
        expRadWizardTestHelper::assertPhpParses( $this, $class, 'datatype class' );
    }

    public function testNoDataTypeGroupIsWritten()
    {
        list( $settings, $files ) = $this->datatypeFiles( array( 'data_text' ) );
        $this->assertArrayNotHasKey( 'group', $settings );
        $this->assertStringNotContainsString( 'DataTypeGroup', $files['settings/content.ini.append.php'] );
        $this->assertStringContainsString( 'AvailableDataTypes[]=k1erating', $files['settings/content.ini.append.php'] );
    }

    // ── module wizard ───────────────────────────────────────────────────────

    public function testModuleTemplatesAreInTheStandardDesignAndOnlyNumbersAreCast()
    {
        $settings = expModuleWizard::settings( array( 'name' => 'k1e_mod_ext', 'module' => 'k1emodule', 'licence' => 'MIT',
                                                     'views' => "show: read ID\nedit: edit NodeID Language?\nfind: read Identifier",
                                                     'policies' => "read\nedit", 'parts' => self::allParts( 'expModuleWizard' ) ) );
        $files = expModuleWizard::files( $settings );

        foreach ( array( 'show', 'edit', 'find' ) as $view )
            $this->assertArrayHasKey( "design/standard/templates/k1emodule/$view.tpl", $files );
        foreach ( array_keys( $files ) as $path )
            $this->assertStringStartsNotWith( 'design/k1e_mod_ext/', $path );

        $this->assertStringContainsString( "? (int) \$Params['ID'] : 0;", $files['modules/k1emodule/show.php'] );
        $this->assertStringContainsString( "? (int) \$Params['NodeID'] : 0;", $files['modules/k1emodule/edit.php'] );
        $this->assertStringContainsString( "? (string) \$Params['Language'] : '';", $files['modules/k1emodule/edit.php'] );
        $this->assertStringContainsString( "? (string) \$Params['Identifier'] : '';", $files['modules/k1emodule/find.php'] );
        $this->assertStringContainsString( "if ( \$identifier === '' )", $files['modules/k1emodule/find.php'] );
        $this->assertStringContainsString( "if ( \$iD <= 0 )", $files['modules/k1emodule/show.php'] );
        foreach ( array( 'show', 'edit', 'find' ) as $view )
            expRadWizardTestHelper::assertPhpParses( $this, $files["modules/k1emodule/$view.php"], $view );

        $this->assertTrue( expModuleWizard::isNumberParameter( 'ObjectID' ) );
        $this->assertTrue( expModuleWizard::isNumberParameter( 'Offset' ) );
        $this->assertFalse( expModuleWizard::isNumberParameter( 'Language' ) );
        $this->assertFalse( expModuleWizard::isNumberParameter( 'RemoteHash' ) );
    }

    // ── design wizard ───────────────────────────────────────────────────────

    public function testDesignReadmeSaysASiteaccessHasToNameTheDesign()
    {
        foreach ( array( array( 'pagelayout' ), array( 'pagelayout', 'siteaccess', 'readme' ) ) as $parts )
        {
            $settings = expDesignExtensionWizard::settings( array( 'name' => 'k1e_design_ext', 'licence' => 'MIT', 'siteaccess' => 'k1e_site',
                                                                  'parts' => array_merge( $parts, array( 'readme' ) ) ) );
            $files = expDesignExtensionWizard::files( $settings );
            $readme = $files['README.md'];
            $this->assertStringNotContainsString( 'without anything else being set', $readme );
            $this->assertStringContainsString( 'a design is used only when a', $readme );
            $this->assertStringContainsString( 'SiteDesign=k1e_design_ext', in_array( 'siteaccess', $parts, true )
                                               ? $files['settings/siteaccess/k1e_site/site.ini.append.php'] : $readme );
            $this->assertStringNotContainsString( 'into the design chain of every siteaccess', $files['settings/design.ini.append.php'] );
        }
    }

    // ── handler wizard ──────────────────────────────────────────────────────

    public function testTheMailTransportStubSendsNothingAndSaysSo()
    {
        $settings = expHandlerWizard::settings( array( 'name' => 'k1e_handler_ext', 'kind' => 'mail', 'licence' => 'MIT',
                                                      'parts' => self::allParts( 'expHandlerWizard' ) ) );
        $files = expHandlerWizard::files( $settings );
        $class = null;
        foreach ( $files as $path => $contents )
            if ( strpos( $contents, 'function sendMail(' ) !== false )
                $class = $contents;
        $this->assertNotNull( $class );
        expRadWizardTestHelper::assertPhpParses( $this, $class, 'mail transport' );

        $body = substr( $class, strpos( $class, 'function sendMail(' ) );
        $body = substr( $body, 0, strpos( $body, "\n    }" ) );
        $this->assertStringContainsString( '// TODO:', $body );
        $this->assertStringContainsString( 'eZDebug::writeError(', $body );
        $this->assertStringContainsString( 'return false;', $body );
        $this->assertStringNotContainsString( 'return true;', $body );
    }
}
