<?php
/**
 * Hidden custom tags and the custom tag controls of the online editor, without the database.
 *
 *  CT-01 - content.ini [CustomTagSettings] IsHidden[] lists the hidden tags (trimmed, once each, empty entries and
 *          an empty list ignored); without the setting none is hidden
 *  CT-02 - The tag list of ezoe/tags and the editors leaves hidden tags out, keeps the hidden tag being edited and
 *          keeps the order of AvailableCustomTags[]
 *  CT-03 - The custom tag definitions (TinyMCE 8, Tiptap) carry hidden and preview for every tag, hidden ones too
 *  CT-04 - A hidden custom tag in a text is still rendered by the output handler
 *  CT-05 - A hidden custom tag in a text is still shown to the editor and saved back unchanged
 *  CT-06 - ezoe/tags asks offeredCustomTags() with the tag being edited
 *  CT-07 - The example image of a tag is images/editor/custom_tags_thumbs/<name>.png in a design, '' without one
 *          and for a name that is not a plain identifier
 *  CT-08 - ezoe.ini [EditorSettings] CustomTagControls ships disabled; enabled switches the controls on
 *  CT-09 - New installations write CustomTagControls=enabled into the common settings, keep a site package's
 *          own value and its other ezoe.ini settings
 *  CT-10 - The three editors take the switch: TinyMCE 3 loads the plugin exp_customtag_controls, TinyMCE 8 and
 *          Tiptap get the option; the plugin, its texts and the stylesheet are there
 *
 * Hidden custom tags are rebuilt from LoveStack (mugoweb/ezpublish-legacy, commit e21ef46691), the custom tag
 * controls from commit 63334f1681, with thanks to Mugo Web.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expOETestCase.php';

class expOECustomTagListTest extends expOETestCase
{
    const HEAD = '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">';

    protected $savedHidden = null;
    protected $hadHidden = false;
    protected $savedControls = null;
    protected $previewDir = null;

    public function setUp(): void
    {
        parent::setUp();
        $content = eZINI::instance( 'content.ini' );
        $this->hadHidden = $content->hasVariable( 'CustomTagSettings', 'IsHidden' );
        $this->savedHidden = $this->hadHidden ? $content->variable( 'CustomTagSettings', 'IsHidden' ) : null;
        $ezoe = eZINI::instance( 'ezoe.ini' );
        $this->savedControls = $ezoe->hasVariable( 'EditorSettings', 'CustomTagControls' ) ? $ezoe->variable( 'EditorSettings', 'CustomTagControls' ) : null;
    }

    public function tearDown(): void
    {
        $content = eZINI::instance( 'content.ini' );
        if ( $this->hadHidden )
            $content->setVariable( 'CustomTagSettings', 'IsHidden', $this->savedHidden );
        else
            $content->removeSetting( 'CustomTagSettings', 'IsHidden' );
        $ezoe = eZINI::instance( 'ezoe.ini' );
        if ( $this->savedControls === null )
            $ezoe->removeSetting( 'EditorSettings', 'CustomTagControls' );
        else
            $ezoe->setVariable( 'EditorSettings', 'CustomTagControls', $this->savedControls );
        if ( $this->previewDir !== null )
            eZDir::recursiveDelete( $this->previewDir );
        parent::tearDown();
    }

    protected function hide( array $tags )
    {
        eZINI::instance( 'content.ini' )->setVariable( 'CustomTagSettings', 'IsHidden', $tags );
    }

    protected function root()
    {
        return dirname( __DIR__, 4 );
    }

    /** CT-01 */
    public function testIsHiddenListsTheHiddenTags()
    {
        eZINI::instance( 'content.ini' )->removeSetting( 'CustomTagSettings', 'IsHidden' );
        $this->assertSame( array(), eZOEXMLInput::hiddenCustomTags() );
        $this->hide( array() );
        $this->assertSame( array(), eZOEXMLInput::hiddenCustomTags() );
        $this->hide( array( ' sub ', '', 'quote', 'sub' ) );
        $this->assertSame( array( 'sub', 'quote' ), eZOEXMLInput::hiddenCustomTags() );
    }

    /** CT-02 */
    public function testTheTagListLeavesHiddenTagsOutButKeepsTheOneBeingEdited()
    {
        $tags = array( 'factbox' => 'Fact box', 'quote' => 'Quote', 'sub' => 'sub', 'sup' => 'sup' );
        $this->hide( array( 'quote', 'sub' ) );
        $this->assertSame( array( 'factbox' => 'Fact box', 'sup' => 'sup' ), eZOEXMLInput::offeredCustomTags( $tags ) );
        $this->assertSame( array( 'factbox' => 'Fact box', 'quote' => 'Quote', 'sup' => 'sup' ), eZOEXMLInput::offeredCustomTags( $tags, 'quote' ) );
        $this->assertSame( $tags, eZOEXMLInput::offeredCustomTags( $tags, '', array() ), 'nothing hidden: the list as it was' );
    }

    /** CT-03 */
    public function testTheDefinitionsCarryHiddenAndPreview()
    {
        $available = eZINI::instance( 'content.ini' )->variable( 'CustomTagSettings', 'AvailableCustomTags' );
        $first = reset( $available );
        $this->hide( array( $first ) );
        $definitions = eZOEXMLInput::getCustomTagDefinitions();
        $this->assertNotEmpty( $definitions );
        $byName = array();
        foreach ( $definitions as $definition )
        {
            $this->assertArrayHasKey( 'hidden', $definition );
            $this->assertArrayHasKey( 'preview', $definition );
            $byName[$definition['name']] = $definition;
        }
        $this->assertArrayHasKey( $first, $byName, 'a hidden tag stays defined, so it can still be edited' );
        $this->assertTrue( $byName[$first]['hidden'] );
        foreach ( $byName as $name => $definition )
            if ( $name !== $first )
                $this->assertFalse( $definition['hidden'], $name );
    }

    /** CT-04 */
    public function testAHiddenCustomTagIsStillRendered()
    {
        $this->hide( array( 'sup', 'factbox' ) );
        $xml = self::HEAD . '<paragraph>E = mc<custom name="sup">2</custom></paragraph><paragraph><custom name="factbox"><paragraph>A fact</paragraph></custom></paragraph></section>';
        $output = new eZXHTMLXMLOutput( $xml, false );
        $html = $output->outputText();
        $this->assertMatchesRegularExpression( '#<sup[^>]*>\s*2\s*</sup>#', $html );
        $this->assertStringContainsString( 'A fact', $html );
    }

    /** CT-05 */
    public function testAHiddenCustomTagIsStillShownToTheEditorAndSavedBack()
    {
        $this->hide( array( 'factbox' ) );
        $xml = self::HEAD . '<paragraph><custom name="factbox"><paragraph>A fact</paragraph></custom></paragraph></section>';
        $attribute = new eZContentObjectAttribute( array( 'id' => 0, 'contentobject_id' => 0, 'version' => 1, 'data_text' => $xml ) );
        $handler = new eZOEXMLInput( $xml, false, $attribute );
        $html = html_entity_decode( $handler->inputXML(), ENT_QUOTES, 'UTF-8' );
        $this->assertMatchesRegularExpression( '#<div[^>]*class="ezoeItemCustomTag factbox"#', $html );
        $parser = new eZOEInputParser();
        $this->assertStringContainsString( '<custom name="factbox">', eZXMLTextType::domString( $parser->process( $html ) ) );
    }

    /** CT-06 */
    public function testTheTagsViewAsksForTheOfferedTags()
    {
        $source = file_get_contents( $this->root() . '/extension/ezoe/classes/runnable/views/ezoe/tags.php' );
        $this->assertStringContainsString( 'eZOEXMLInput::offeredCustomTags( $classList, $customTagName )', $source );
        $this->assertStringContainsString( "setVariable( 'custom_tag_previews'", $source );
    }

    /** CT-07 */
    public function testTheExampleImageComesFromADesign()
    {
        $this->previewDir = 'var/tmp/expoe-customtag-preview-' . getmypid();
        eZDir::mkdir( $this->previewDir . '/images/editor/custom_tags_thumbs', false, true );
        file_put_contents( $this->previewDir . '/images/editor/custom_tags_thumbs/factbox.png', "\x89PNG\r\n\x1a\n" );
        $bases = array( $this->previewDir );
        $this->assertStringEndsWith( $this->previewDir . '/images/editor/custom_tags_thumbs/factbox.png', eZOEXMLInput::customTagPreview( 'factbox', $bases ) );
        $this->assertSame( '', eZOEXMLInput::customTagPreview( 'quote', $bases ) );
        $this->assertSame( '', eZOEXMLInput::customTagPreview( '../factbox', $bases ) );
        $this->assertSame( '', eZOEXMLInput::customTagPreview( '', $bases ) );
    }

    /** CT-08 */
    public function testTheControlsShipDisabled()
    {
        $shipped = file_get_contents( $this->root() . '/extension/ezoe/settings/ezoe.ini' );
        $this->assertMatchesRegularExpression( '/^CustomTagControls=disabled$/m', $shipped );
        $ini = eZINI::instance( 'ezoe.ini' );
        $ini->setVariable( 'EditorSettings', 'CustomTagControls', 'disabled' );
        $this->assertFalse( eZOEXMLInput::customTagControlsEnabled() );
        $ini->setVariable( 'EditorSettings', 'CustomTagControls', 'enabled' );
        $this->assertTrue( eZOEXMLInput::customTagControlsEnabled() );
        $ini->removeSetting( 'EditorSettings', 'CustomTagControls' );
        $this->assertFalse( eZOEXMLInput::customTagControlsEnabled() );
        $handler = new eZOEXMLInput( '', false, null );
        $this->assertTrue( $handler->hasAttribute( 'custom_tag_controls_enabled' ) );
    }

    /** CT-09 */
    public function testNewInstallationsSwitchTheControlsOn()
    {
        require_once $this->root() . '/kernel/setup/steps/ezstep_installer.php';
        require_once $this->root() . '/kernel/setup/steps/ezstep_create_sites.php';
        $written = eZStepCreateSites::withCustomTagControls( array( array( 'name' => 'site.ini', 'settings' => array() ) ) );
        $this->assertSame( array( 'name' => 'ezoe.ini', 'settings' => array( 'EditorSettings' => array( 'CustomTagControls' => 'enabled' ) ) ), $written[1] );

        $package = array( array( 'name' => 'ezoe.ini', 'settings' => array( 'EditorSettings' => array( 'Skin' => 'default' ) ) ) );
        $written = eZStepCreateSites::withCustomTagControls( $package );
        $this->assertCount( 1, $written );
        $this->assertSame( array( 'Skin' => 'default', 'CustomTagControls' => 'enabled' ), $written[0]['settings']['EditorSettings'] );

        $package[0]['settings']['EditorSettings']['CustomTagControls'] = 'disabled';
        $this->assertSame( $package, eZStepCreateSites::withCustomTagControls( $package ), 'a site package\'s own value stays' );
    }

    /** CT-10 */
    public function testTheEditorsTakeTheSwitch()
    {
        $root = $this->root() . '/extension/ezoe/design/standard';
        $tiny3 = file_get_contents( $root . '/templates/content/datatype/edit/ezxmltext_ezoe.tpl' );
        $this->assertStringContainsString( "append( 'exp_customtag_controls' )", $tiny3 );
        $this->assertStringContainsString( "ezcss_require( 'ezoe_customtags.css' )", $tiny3 );
        $tiny8 = file_get_contents( $root . '/templates/content/datatype/edit/ezxmltext_ezoe_tinymce8.tpl' );
        $this->assertStringContainsString( 'ez_custom_tag_controls:', $tiny8 );
        $plugin = file_get_contents( $root . '/javascript/plugins/exp_customtag_controls/editor_plugin.js' );
        $this->assertStringContainsString( "PluginManager.add( 'exp_customtag_controls'", $plugin );
        $this->assertStringContainsString( 'Copyright (C) Mugo Web', $plugin );
        $this->assertFileExists( $root . '/stylesheets/ezoe_customtags.css' );

        if ( !class_exists( 'ezoeServerFunctions' ) )
            require_once $this->root() . '/extension/ezoe/classes/ezoeserverfunctions.php';
        $i18n = ezoeServerFunctions::i18n( array( '-en' ), 'js' );
        $this->assertStringContainsString( '"customtagcontrols"', $i18n );

        $tiptap = $this->root() . '/extension/exp_oe_tiptap/design/standard/templates/content/datatype/edit/ezxmltext_exp_oe_tiptap_editor.tpl';
        if ( is_file( $tiptap ) )
            $this->assertStringContainsString( 'customTagControls:', file_get_contents( $tiptap ) );
    }
}
