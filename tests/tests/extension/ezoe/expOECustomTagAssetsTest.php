<?php
/**
 * The files of the custom tag controls and examples in the Online Editor (L22, L23): the stylesheet, the TinyMCE 3
 * plugin, the TinyMCE 8 custom tag plugin, the dialog's script and the tag dialog template.
 *
 *  CA-01 - design.ini lists ezoe_customtags.css for the editor and for its dialogs; the file is there
 *  CA-02 - the three scripts are valid JavaScript (node --check; skipped where node is not installed)
 *  CA-03 - every class the plugins put on custom tags (ezoe-custom-tag-controls, -labels, -preview) is styled, and
 *          the stylesheet's rules are balanced
 *  CA-04 - the tag dialog (ezoe/tag_custom.tpl) shows the example image of the tag being edited and hides the
 *          others; a tag without an example has none; the image's alternative text names the tag
 *  CA-05 - the dialog's script (popup_utils.js) shows the example of the tag chosen in the list and hides the others
 *
 * The template block is rendered as written by eZTemplate::factory(); the settings are read from the extension's
 * shipped file alone. No database.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commits e21ef46691 and 63334f1681), Copyright (C)
 * Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezoe/expOECustomTagAssetsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expOECustomTagAssetsTest extends PHPUnit\Framework\TestCase
{
    const DESIGN = 'extension/ezoe/design/standard';

    const SCRIPTS = array(
        'TinyMCE 3 plugin' => 'extension/ezoe/design/standard/javascript/plugins/exp_customtag_controls/editor_plugin.js',
        'TinyMCE 8 plugin' => 'extension/ezoe/design/standard/javascript/tinymce8_ez/plugins/ezcustomtag/plugin.js',
        'dialog script'    => 'extension/ezoe/design/standard/javascript/ezoe/popup_utils.js',
    );

    /** @var string */
    private $dir;

    private $savedGlobals = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/phpunit-ezoe-customtag-assets-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0700, true );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
        foreach ( (array)glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    /** CA-01 */
    public function testTheStylesheetIsListedForTheEditorAndItsDialogs()
    {
        $design = eZINI::fetchFromFile( 'extension/ezoe/settings/design.ini.append.php' );
        $this->assertContains( 'ezoe_customtags.css', (array)$design->variable( 'StylesheetSettings', 'EditorCSSFileList' ) );
        $this->assertContains( 'ezoe_customtags.css', (array)$design->variable( 'StylesheetSettings', 'EditorDialogCSSFileList' ) );
        $this->assertFileExists( self::DESIGN . '/stylesheets/ezoe_customtags.css' );
    }

    /** CA-02 */
    public function testTheScriptsAreValidJavaScript()
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        foreach ( self::SCRIPTS as $name => $file )
        {
            $this->assertFileExists( $file );
            exec( escapeshellarg( $node ) . ' --check ' . escapeshellarg( $file ) . ' 2>&1', $output, $status );
            $this->assertSame( 0, $status, "$name: " . implode( "\n", $output ) );
        }
    }

    /** CA-03 */
    public function testTheClassesThePluginsSetAreStyled()
    {
        $css = file_get_contents( self::DESIGN . '/stylesheets/ezoe_customtags.css' );
        $this->assertSame( substr_count( $css, '{' ), substr_count( $css, '}' ), 'balanced rules' );
        $scripts = file_get_contents( self::SCRIPTS['TinyMCE 3 plugin'] ) . file_get_contents( self::SCRIPTS['TinyMCE 8 plugin'] );
        foreach ( array( 'ezoe-custom-tag-controls', 'ezoe-custom-tag-labels', 'ezoe-custom-tag-preview' ) as $class )
        {
            $this->assertStringContainsString( $class, $scripts, "$class is set by a plugin" );
            $this->assertStringContainsString( '.' . $class, $css, "$class is styled" );
        }
        $this->assertStringContainsString( "PluginManager.add( 'exp_customtag_controls'", $scripts );
        $this->assertStringContainsString( "addContextToolbar( 'ezcustomtag'", $scripts );
    }

    /** The example block of tag_custom.tpl rendered for the tags of $previews while $editing is edited */
    private function renderPreviews( $editing, array $classList, array $previews )
    {
        $source = file_get_contents( self::DESIGN . '/templates/ezoe/tag_custom.tpl' );
        $start = strpos( $source, '{if and( is_set( $custom_tag_previews[$custom_tag] ), $custom_tag_previews[$custom_tag] )}' );
        $this->assertNotFalse( $start, 'the example block is in tag_custom.tpl' );
        $end = strpos( $source, '{/if}', strpos( $source, '</div>', $start ) );
        $file = $this->dir . '/previews.tpl';
        file_put_contents( $file, '{foreach $class_list as $custom_tag => $text}' . substr( $source, $start, $end + 5 - $start ) . '{/foreach}' );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'class_list', $classList );
        $tpl->setVariable( 'custom_tag_name', $editing );
        $tpl->setVariable( 'custom_tag_previews', $previews );
        try
        {
            return $tpl->fetch( $file );
        }
        finally
        {
            foreach ( array( 'class_list', 'custom_tag_name', 'custom_tag_previews' ) as $name )
                $tpl->unsetVariable( $name );
        }
    }

    /** CA-04 */
    public function testTheDialogShowsTheExampleOfTheTagBeingEdited()
    {
        $html = $this->renderPreviews( 'factbox', array( 'factbox' => 'Fact box', 'quote' => 'Quote', 'plain' => 'Plain' ),
                                       array( 'factbox' => '/t1/factbox.png', 'quote' => '/t1/quote.png', 'plain' => '' ) );
        $this->assertSame( 2, preg_match_all( '#<div class="ezoe-custom-tag-preview" id="([a-z]+)_preview"([^>]*)>#', $html, $m, PREG_SET_ORDER ), $html );
        $this->assertSame( array( 'factbox', '' ), array( $m[0][1], $m[0][2] ), 'the edited tag: shown' );
        $this->assertSame( array( 'quote', ' style="display: none;"' ), array( $m[1][1], $m[1][2] ), 'another tag: hidden' );
        $this->assertStringContainsString( 'src="/t1/factbox.png"', $html );
        $this->assertStringNotContainsString( 'plain_preview', $html, 'no example, no block' );
        $this->assertStringContainsString( 'Fact box', $html, 'the alternative text names the tag' );
    }

    /** CA-05 */
    public function testTheDialogScriptFollowsTheChosenTag()
    {
        $script = file_get_contents( self::SCRIPTS['dialog script'] );
        $this->assertStringContainsString( "jQuery('div.ezoe-custom-tag-preview')", $script );
        $this->assertStringContainsString( "node[0].value + '_preview'", $script );
    }
}
