<?php
/** What each engine builds: assets, toolbar map, plugins, config; the shipped defaults; the vendored TinyMCE 8. */

require_once __DIR__ . '/expOETestCase.php';

class expOEEngineConfigTest extends expOETestCase
{
    protected function root()
    {
        return dirname( __DIR__, 4 ) . '/extension/ezoe';
    }

    public function testShippedDefaultIsTinyMCE3AndSwitchDisabled()
    {
        $ini = file_get_contents( $this->root() . '/settings/ezoe.ini' );
        $this->assertMatchesRegularExpression( '/^EditorEngine=tinymce3$/m', $ini );
        // EngineSwitch is not set: switchEnabled() is false without it, and a siteaccess can turn it on
        $this->assertDoesNotMatchRegularExpression( '/^EngineSwitch=/m', $ini );
        $this->assertMatchesRegularExpression( '/^UploadExtensionCheck=engine$/m', $ini );
    }

    public function testShippedEnginesAreListedWithoutResettingTheList()
    {
        $ini = file_get_contents( $this->root() . '/settings/ezoe.ini' );
        // an empty Engines[] here, read at the extension level, would drop engines registered by siteaccess
        // settings or by an extension activated for one siteaccess only
        $this->assertDoesNotMatchRegularExpression( '/^Engines\[\]\s*$/m', $ini );
        $this->assertMatchesRegularExpression( '/^Engines\[tinymce3\]=expOETinyMCE3Engine$/m', $ini );
        $this->assertMatchesRegularExpression( '/^Engines\[tinymce8\]=expOETinyMCE8Engine$/m', $ini );
    }

    public function testSwitchIsOffWhenNotSet()
    {
        $ini = eZINI::instance( 'ezoe.ini' );
        $ini->setVariable( 'EditorSettings', 'EngineSwitch', '' );
        $this->assertFalse( expOEEditor::switchEnabled() );
    }

    public function testSwitchIsOffWhenTheSettingIsMissing()
    {
        // the shipped ezoe.ini sets no EngineSwitch: hasVariable() is false, and switchEnabled() must not read it
        $ini = eZINI::instance( 'ezoe.ini' );
        $ini->removeSetting( 'EditorSettings', 'EngineSwitch' );
        $this->assertFalse( $ini->hasVariable( 'EditorSettings', 'EngineSwitch' ) );
        $this->assertFalse( expOEEditor::switchEnabled() );
    }

    public function testSwitchIsOnOnlyWhenEnabled()
    {
        $ini = eZINI::instance( 'ezoe.ini' );
        $ini->setVariable( 'EditorSettings', 'EngineSwitch', 'enabled' );
        $this->assertTrue( expOEEditor::switchEnabled() );
        $ini->setVariable( 'EditorSettings', 'EngineSwitch', 'disabled' );
        $this->assertFalse( expOEEditor::switchEnabled() );
    }

    public function testTinyMCE3IsRenderedByTheBuiltInTemplate()
    {
        $info = expOEEditor::info( 'tinymce3' );
        $this->assertSame( '', $info['template'] );
        $this->assertSame( array(), $info['toolbar_map'] );
        $this->assertSame( array(), $info['plugins'] );
        $this->assertSame( array(), $info['config'] );
    }

    public function testTinyMCE8HasItsOwnTemplate()
    {
        $info = expOEEditor::info( 'tinymce8' );
        $this->assertSame( 'design:content/datatype/edit/ezxmltext_ezoe_tinymce8.tpl', $info['template'] );
        $this->assertFileExists( $this->root() . '/design/standard/templates/content/datatype/edit/ezxmltext_ezoe_tinymce8.tpl' );
    }

    public function testTinyMCE8ConfigUsesTheGplLicense()
    {
        $config = expOEEditor::info( 'tinymce8' )['config'];
        $this->assertSame( 'gpl', $config['license_key'] );
    }

    public function testTinyMCE8CacheKeyIsShortAndStable()
    {
        $a = expOEEditor::info( 'tinymce8' )['config']['cache_key'];
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{10}$/', $a );
        $this->assertSame( $a, eZOEXMLInput::getTinyMCE8CacheKey() );
    }

    public function testTinyMCE8ConfigCarriesTheUploadTypes()
    {
        $this->assertSame( expOEEditor::uploadExtensions(), expOEEditor::info( 'tinymce8' )['config']['upload_extensions'] );
    }

    public function testToolbarMapCoversTheButtonsOfTheDefaultLayout()
    {
        $map = expOEEditor::info( 'tinymce8' )['toolbar_map'];
        $buttons = eZINI::instance( 'ezoe.ini' )->variable( 'EditorLayout', 'Buttons' );
        $unmapped = array();
        foreach ( $buttons as $button )
        {
            if ( $button !== '|' && !isset( $map[$button] ) )
                $unmapped[] = $button;
        }
        // buttons the prototype has no counterpart for are dropped on purpose; the common ones must be there
        foreach ( array( 'bold', 'italic', 'link', 'bullist', 'numlist', 'image', 'table', 'literal', 'custom' ) as $common )
            $this->assertArrayHasKey( $common, $map, $common );
        $this->assertLessThan( 12, count( $unmapped ), 'unmapped: ' . implode( ', ', $unmapped ) );
    }

    public function testEveryMappedItemIsANonEmptyString()
    {
        foreach ( expOEEditor::info( 'tinymce8' )['toolbar_map'] as $button => $item )
            $this->assertNotSame( '', $item, $button );
    }

    public function testEveryPluginFileExists()
    {
        $plugins = expOEEditor::info( 'tinymce8' )['plugins'];
        $this->assertCount( 7, $plugins );
        foreach ( $plugins as $name => $path )
            $this->assertFileExists( $this->root() . '/design/standard/' . $path, $name );
    }

    public function testPluginNamesMatchTheirDirectory()
    {
        foreach ( expOEEditor::info( 'tinymce8' )['plugins'] as $name => $path )
            $this->assertStringContainsString( "/plugins/$name/plugin.js", $path );
    }

    public function testTheEngineIsAvailableBecauseItsScriptIsThere()
    {
        $this->assertFileExists( $this->root() . '/design/standard/' . expOETinyMCE8Engine::SCRIPT );
        $this->assertTrue( ( new expOETinyMCE8Engine() )->isAvailable() );
    }

    public function testVendoredTinyMCE8IsTheGplDistribution()
    {
        $dir = $this->root() . '/design/standard/javascript/tinymce8';
        $this->assertFileExists( $dir . '/license.md' );
        $this->assertFileExists( $dir . '/notices.txt' );
        $this->assertStringContainsString( 'GNU General Public License Version 2 or later', file_get_contents( $dir . '/license.md' ) );
        $this->assertStringContainsString( '8.9.2', file_get_contents( $dir . '/tinymce.min.js' ) );
    }

    public function testTheTemplateNeverCallsTheCloud()
    {
        $tpl = file_get_contents( $this->root() . '/design/standard/templates/content/datatype/edit/ezxmltext_ezoe_tinymce8.tpl' );
        $this->assertStringContainsString( "license_key: 'gpl'", $tpl );
        $this->assertDoesNotMatchRegularExpression( '#cdn\.tiny\.cloud|api-key|apiKey|cloud\.tinymce#i', $tpl );
    }

    public function testThePluginsFetchNothingFromAnotherHost()
    {
        foreach ( glob( $this->root() . '/design/standard/javascript/tinymce8_ez/plugins/*/plugin.js' ) as $file )
            $this->assertDoesNotMatchRegularExpression( '#(fetch|open|\\.src|src\\s*:|url\\s*:)\\s*\\(?\\s*[\'"]https?:#', file_get_contents( $file ), basename( dirname( $file ) ) );
    }

    public function testThePluginsDoNotUseJQuery()
    {
        // the admin runs jQuery 4: the TinyMCE 8 plugins are plain JavaScript and break on no jQuery API
        foreach ( glob( $this->root() . '/design/standard/javascript/tinymce8_ez/{,plugins/*/}*.js', GLOB_BRACE ) as $file )
            $this->assertDoesNotMatchRegularExpression( '/(\$\(|jQuery|\.live\(|\.bind\(|\.size\(\))/', file_get_contents( $file ), basename( $file ) );
    }

    public function testTheBuiltInEnginesCarryEnglishLabelsForTranslation()
    {
        foreach ( array( 'TinyMCE 3', 'TinyMCE 8' ) as $label )
        {
            $this->assertStringContainsString( "<source>$label</source>", file_get_contents( $this->root() . '/translations/ger-DE/translation.ts' ) );
            $this->assertStringContainsString( "<source>$label</source>", file_get_contents( $this->root() . '/translations/eng-US/translation.ts' ) );
        }
    }

    public function testEveryNewStringIsTranslatedInGerman()
    {
        $de = file_get_contents( $this->root() . '/translations/ger-DE/translation.ts' );
        foreach ( array( 'Switch to the editor %engine', 'This file type is not accepted by the editor: %file',
                         'Editor for text fields', 'The editor of your user was saved.', 'This editor is not available.' ) as $source )
        {
            $this->assertStringContainsString( '<source>' . htmlspecialchars( $source, ENT_NOQUOTES ) . '</source>', $de, $source );
        }
    }

    public function testTheGermanPlaceholdersMatchTheEnglishOnes()
    {
        $de = new DOMDocument();
        $de->load( $this->root() . '/translations/ger-DE/translation.ts' );
        $checked = 0;
        foreach ( $de->getElementsByTagName( 'message' ) as $message )
        {
            $source = $message->getElementsByTagName( 'source' )->item( 0 )->textContent;
            if ( !in_array( $source, array( 'Switch to the editor %engine', 'This file type is not accepted by the editor: %file', 'Default of this site (%engine)' ), true ) )
                continue;
            $translation = $message->getElementsByTagName( 'translation' )->item( 0 )->textContent;
            preg_match_all( '/%\w+/', $source, $a );
            preg_match_all( '/%\w+/', $translation, $b );
            $this->assertSame( $a[0], $b[0], $source );
            $checked++;
        }
        $this->assertSame( 3, $checked );
    }

    public function testEngineSwitchTemplateIsOneGroupOverTheRegistry()
    {
        $tpl = file_get_contents( $this->root() . '/design/standard/templates/content/datatype/edit/ezxmltext_ezoe_engine_switch.tpl' );
        $code = preg_replace( '/\{\*.*?\*\}/s', '', $tpl );
        $this->assertStringContainsString( '{if $input_handler.engine_switch_enabled}', $code, 'shown only with EngineSwitch=enabled' );
        $this->assertStringContainsString( '$input_handler.engine_switch_options', $code, 'every engine of the registry, the one in use included' );
        $this->assertStringContainsString( 'gt( $ezoe_engine_options|count, 1 )', $code, 'nothing to show with one engine' );
        // one group named by its visible label "Editor"
        $this->assertMatchesRegularExpression( '/<div class="ezoe-engine-switch" role="group" aria-labelledby="ezoe-engine-switch-label-\{\$attribute\.id\}">/', $code );
        $this->assertMatchesRegularExpression( '/<span class="ezoe-engine-switch-label" id="ezoe-engine-switch-label-\{\$attribute\.id\}">\{\'Editor\'\|i18n\( \'design\/standard\/ezoe\' \)\|wash\}<\/span>/', $code );
        // one segmented control of toggle buttons: the engine in use is the pressed one (type="button", it posts
        // nothing), so it stays in the Tab order and a screen reader names the engine and its state
        $this->assertMatchesRegularExpression( '/<button class="button ezoe-engine-switch-option current" type="button" aria-pressed="true">\{\$ezoe_engine_option\.label\|wash\}<\/button>/', $code );
        $this->assertStringNotContainsString( 'aria-current', $code, 'the state of a toggle is aria-pressed' );
        // each other engine posts the same custom action as before, a submit button of the edit form, not pressed
        $this->assertMatchesRegularExpression( '/<button class="button ezoe-engine-switch-option" type="submit" name="CustomActionButton\[\{\$attribute\.id\}_switch_engine_\{\$ezoe_engine_option\.identifier\|wash\}\]" value="\{\$ezoe_engine_option\.identifier\|wash\}" aria-pressed="false"/', $code );
        // its name is the visible engine name (no aria-label that differs from it); the title says what pressing does
        $this->assertStringNotContainsString( 'aria-label=', $code );
        $this->assertStringContainsString( "title=\"{'Switch to the editor %engine'|i18n( 'design/standard/ezoe',, hash( '%engine', \$ezoe_engine_option.label ) )|wash}: {'The text is kept, the choice is saved for your user.'|i18n( 'design/standard/ezoe' )|wash}\"", $code );
        $this->assertStringNotContainsString( '<input', $code );
        $this->assertSame( 2, substr_count( $code, '<button' ) );
    }

    public function testEveryEditTemplateIncludesTheEngineSwitch()
    {
        foreach ( array( 'ezxmltext_ezoe.tpl', 'ezxmltext_ezoe_tinymce8.tpl' ) as $name )
        {
            $tpl = file_get_contents( $this->root() . '/design/standard/templates/content/datatype/edit/' . $name );
            $this->assertStringContainsString( "{include uri='design:content/datatype/edit/ezxmltext_ezoe_engine_switch.tpl' attribute=\$attribute input_handler=\$input_handler}", $tpl, $name );
        }
    }

    public function testTheHandlerExposesTheEngineAttributes()
    {
        $handler = ( new ReflectionClass( 'eZOEXMLInput' ) )->newInstanceWithoutConstructor();
        foreach ( array( 'engine', 'engine_switch_choices', 'engine_switch_options', 'engine_switch_enabled', 'tinymce8_cache_key' ) as $name )
            $this->assertTrue( $handler->hasAttribute( $name ), $name );
    }

    public function testTheHandlerResolvesTheEngineThroughTheRegistry()
    {
        $handler = ( new ReflectionClass( 'eZOEXMLInput' ) )->newInstanceWithoutConstructor();
        $this->preference( 'tinymce8' );
        $this->assertSame( 'tinymce8', $handler->attribute( 'engine' )['identifier'] );
        $this->preference( 'tinymce3' );
        $this->assertSame( 'tinymce3', $handler->attribute( 'engine' )['identifier'] );
        // every other engine: ezoe's tinymce8 and the engines of other active extensions (exp_oe_tiptap's tiptap)
        $this->assertSame( array_values( array_diff( array_keys( expOEEditor::engines() ), array( 'tinymce3' ) ) ),
            array_column( $handler->attribute( 'engine_switch_choices' ), 'identifier' ) );
        $this->assertContains( 'tinymce8', array_column( $handler->attribute( 'engine_switch_choices' ), 'identifier' ) );
        $options = $handler->attribute( 'engine_switch_options' );
        $this->assertSame( array( 'tinymce3' ), array_column( array_filter( $options, function ( $o ) { return $o['current']; } ), 'identifier' ) );
    }
}
