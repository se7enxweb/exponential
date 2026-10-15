<?php
/**
 * The Upload button of the administration's node view (#478): a link to ezmultiupload's upload page for the node,
 * shown only where the current user can upload. Checked without a database:
 *
 *  UB-01 - fetch( 'ezmultiupload', 'can_upload', hash( 'node_id', N ) ) (expMultiuploadFunctionCollection) asks
 *          expMultiuploadService::canUploadAt() for a positive node id and answers its result as a bool; anything
 *          else, or a service without the check, is false without asking (skipped without ezmultiupload 6.1)
 *  UB-02 - node/view/upload_button.tpl, rendered by eZTemplate::factory() with the decision given: allowed, one link
 *          to ezmultiupload/upload/<node id> with the shared .a4-btn class, a Bootstrap Icon and the label; not
 *          allowed, nothing; a draft (object status other than published), nothing even when the check says yes
 *  UB-03 - the button sits after Edit and before Move in design/admin and design/admin4 (admin4l falls back to
 *          admin4), and first in the "..." menu of exp_adminui's node view, before Move, with the same check
 *  UB-04 - the icon class is in both identical components.css copies with its SVG file, the button follows the
 *          classic action bar's height (modules/content.css) and
 *          narrow-width rules of the actions, there is no inline CSS, and "Upload" is translated (ger-DE: Hochladen)
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminNodeUploadButtonTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$expMultiuploadCollection = dirname( __DIR__, 3 ) . '/extension/ezmultiupload/classes/expmultiuploadfunctioncollection.php';
if ( is_file( $expMultiuploadCollection ) )
    require_once $expMultiuploadCollection;
unset( $expMultiuploadCollection );

/** A stand-in for expMultiuploadService: answers canUploadAt() from a list and records the calls */
class AdminNodeUploadButtonTestService
{
    public static $allowed = array();
    public static $calls = array();

    public static function canUploadAt( $node )
    {
        self::$calls[] = $node;
        return in_array( $node, self::$allowed, true ) ? 1 : 0;
    }
}

/** A service of an older ezmultiupload, without the check */
class AdminNodeUploadButtonTestOldService
{
}

class AdminNodeUploadButtonTest extends PHPUnit\Framework\TestCase
{
    const BUTTON = 'design/admin/templates/node/view/upload_button.tpl';
    const DECISION = "{if ezmodule( 'ezmultiupload/upload' )}{set \$exp_can_upload = fetch( 'ezmultiupload', 'can_upload', hash( 'node_id', \$node.node_id ) )}{/if}";

    /** @var string private directory of the rendered templates */
    private static $dir;

    private $savedGlobals = array();
    private $savedService;

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    public static function setUpBeforeClass(): void
    {
        chdir( self::root() );
        self::$dir = 'var/tmp/phpunit-upload-button-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( glob( self::$dir . '/*' ) as $file )
            unlink( $file );
        rmdir( self::$dir );
    }

    protected function setUp(): void
    {
        chdir( self::root() );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        if ( class_exists( 'expMultiuploadFunctionCollection', false ) )
            $this->savedService = expMultiuploadFunctionCollection::$service;
        AdminNodeUploadButtonTestService::$allowed = array();
        AdminNodeUploadButtonTestService::$calls = array();
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
        if ( $this->savedService !== null )
            expMultiuploadFunctionCollection::$service = $this->savedService;
    }

    /** UB-01 */
    public function testTheFetchFunctionAsksTheServiceForPositiveIdsOnly()
    {
        if ( !class_exists( 'expMultiuploadFunctionCollection', false ) )
            $this->markTestSkipped( 'ezmultiupload without its fetch functions (before 6.1)' );

        $definition = self::read( 'extension/ezmultiupload/modules/ezmultiupload/function_definition.php' );
        $this->assertStringContainsString( "\$FunctionList['can_upload']", $definition );
        $this->assertStringContainsString( "'method' => 'fetchCanUpload'", $definition );
        $this->assertStringContainsString( "'name' => 'node_id'", $definition );

        expMultiuploadFunctionCollection::$service = 'AdminNodeUploadButtonTestService';
        AdminNodeUploadButtonTestService::$allowed = array( 2, 61 );
        $this->assertSame( array( 'result' => true ), expMultiuploadFunctionCollection::fetchCanUpload( 2 ) );
        $this->assertSame( array( 'result' => true ), expMultiuploadFunctionCollection::fetchCanUpload( '61' ) );
        $this->assertSame( array( 'result' => false ), expMultiuploadFunctionCollection::fetchCanUpload( 77 ) );
        $this->assertSame( array( 2, 61, 77 ), AdminNodeUploadButtonTestService::$calls, 'the id is passed as an integer' );

        AdminNodeUploadButtonTestService::$calls = array();
        foreach ( array( 0, -2, '0', '2a', ' 2', '2.0', '', null, false, true, array( 2 ), 2.5 ) as $bad )
            $this->assertSame( array( 'result' => false ), expMultiuploadFunctionCollection::fetchCanUpload( $bad ), var_export( $bad, true ) );
        $this->assertSame( array(), AdminNodeUploadButtonTestService::$calls, 'no bad id reaches the check' );

        expMultiuploadFunctionCollection::$service = 'AdminNodeUploadButtonTestOldService';
        $this->assertSame( array( 'result' => false ), expMultiuploadFunctionCollection::fetchCanUpload( 2 ) );
        expMultiuploadFunctionCollection::$service = 'AdminNodeUploadButtonTestNoSuchClass';
        $this->assertSame( array( 'result' => false ), expMultiuploadFunctionCollection::fetchCanUpload( 2 ) );
    }

    /** The button's template with the decision (module enabled, fetch) given as $stub_can_upload */
    private function render( $canUpload, $status = 1 )
    {
        $source = self::read( self::BUTTON );
        $this->assertSame( 1, substr_count( $source, self::DECISION ), 'the decision is the module check and the fetch' );
        $file = self::$dir . '/button.tpl';
        file_put_contents( $file, str_replace( self::DECISION, '{set $exp_can_upload = $stub_can_upload}', $source ) );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'node', array( 'node_id' => 4242, 'object' => array( 'status' => $status ) ) );
        $tpl->setVariable( 'stub_can_upload', $canUpload );
        $html = trim( $tpl->fetch( $file ) );
        $tpl->unsetVariable( 'node' );
        $tpl->unsetVariable( 'stub_can_upload' );
        return $html;
    }

    /** UB-02 */
    public function testTheButtonShowsOnlyWhereTheUserCanUpload()
    {
        $html = $this->render( true );
        $this->assertSame( 1, substr_count( $html, '<a ' ), $html );
        $this->assertSame( 1, preg_match( '#<a class="a4-btn a4-node-upload" href="([^"]*)" title="([^"]*)">(.*)</a>#s', $html, $m ), $html );
        $this->assertStringEndsWith( '/ezmultiupload/upload/4242', $m[1] );
        $this->assertSame( 'Upload files to this location.', $m[2] );
        $this->assertSame( '<span class="a4-icon a4-bi-upload" aria-hidden="true"></span>Upload', $m[3], 'an icon hidden from assistive technology and a visible name' );

        $this->assertSame( '', $this->render( false ), 'not allowed: nothing' );
        $this->assertSame( '', $this->render( true, 0 ), 'a draft: nothing' );
        $this->assertSame( '', $this->render( true, 2 ), 'an archived version: nothing' );
    }

    /** UB-03 */
    public function testPlacedAfterEditAndBeforeMove()
    {
        $include = "{include uri='design:node/view/upload_button.tpl' node=\$node}";
        foreach ( array( 'design/admin/templates/node/view/full.tpl', 'design/admin4/templates/node/view/a4_full_body.tpl' ) as $path )
        {
            $source = self::read( $path );
            $this->assertSame( 1, substr_count( $source, $include ), $path );
            $at = strpos( $source, $include );
            $this->assertGreaterThan( strrpos( substr( $source, 0, $at ), 'name="EditButton"' ), $at, "$path: after Edit" );
            $this->assertLessThan( strpos( $source, 'name="MoveNodeButton"' ), $at, "$path: before Move" );
        }
        $this->assertFalse( is_file( self::root() . '/design/admin4l/templates/node/view/full.tpl' ), 'admin4l takes admin4\'s node view' );

        $adminui = 'extension/exp_adminui/design/adminui/templates/window_preview_toolbar.tpl';
        if ( !is_file( self::root() . '/' . $adminui ) )
            return;
        $source = self::read( $adminui );
        $decision = strpos( $source, self::DECISION );
        $this->assertNotFalse( $decision, 'exp_adminui asks the same check' );
        $this->assertGreaterThan( strpos( $source, 'class="dropdown-menu' ), $decision, 'inside the "..." menu' );
        $this->assertLessThan( strpos( $source, 'name="MoveNodeButton"' ), $decision, 'before Move' );
        $this->assertStringContainsString( "eq( \$node.object.status, 1 )", $source );
        $this->assertStringContainsString( "concat( 'ezmultiupload/upload/', \$node.node_id )|ezurl", $source );
        $this->assertStringContainsString( '<span class="a4-icon a4-bi-upload" aria-hidden="true"></span>', $source );
    }

    /** UB-04 */
    public function testIconStylesAndTranslations()
    {
        $rule = '.a4-bi-upload { -webkit-mask-image: url(../../../share/icons/bootstrap-icons/svg/upload.svg); mask-image: url(../../../share/icons/bootstrap-icons/svg/upload.svg); }';
        foreach ( array( 'admin', 'admin4' ) as $design )
            $this->assertStringContainsString( $rule, self::read( "design/$design/stylesheets/components.css" ), $design );
        $this->assertFileExists( self::root() . '/share/icons/bootstrap-icons/svg/upload.svg' );

        $css = self::read( 'design/admin4/stylesheets/admin4.css' );
        $this->assertSame( 2, substr_count( $css, '.controlbar.a4-node-actions .block .a4-node-upload' ), 'the narrow-width rules of the actions take the button' );

        foreach ( array( 'admin', 'admin4' ) as $design )
            $this->assertStringContainsString( '.controlbar:not(.a4-node-actions) .block .a4-node-upload {', self::read( "design/$design/stylesheets/modules/content.css" ),
                                               "$design: the classic action bar keeps the button as high as its legacy buttons" );

        $button = self::read( self::BUTTON );
        $this->assertDoesNotMatchRegularExpression( '#<style|\sstyle=#i', $button );

        foreach ( array( 'eng-US' => 'Upload', 'ger-DE' => 'Hochladen' ) as $locale => $word )
        {
            $ts = self::read( "share/translations/$locale/translation.ts" );
            $at = strpos( $ts, "<name>design/admin/node/view/full</name>\n    <message>\n        <source>Upload</source>\n        <translation>$word</translation>" );
            $this->assertNotFalse( $at, $locale );
            $this->assertStringContainsString( '<source>Upload files to this location.</source>', $ts, $locale );
        }
    }
}
