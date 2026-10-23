<?php
/**
 * The design files of the image editor, read as files (no kernel, no database).
 *  ID-01 - the edit templates (beside an image: ezimage_editor.tpl; without one: ezimage_new.tpl, a new SVG drawing)
 *          behind the policy expimageeditor/edit, the settings once per page from expimageeditor/config.tpl (not in the
 *          save view's answer), the button hidden until the script is there, the data attributes the frame reads; the
 *          new drawing only where SVG is accepted, and admin4's ezimage.tpl includes ezimage_new.tpl without an image
 *  ID-02 - every text the scripts ask for (t('key')) is in the template's data-labels, and every label is used
 *  ID-03 - every icon the scripts use has its file (Bootstrap Icons) and its class in the stylesheet
 *  ID-04 - the scripts the template names exist, and the frame script is the only one the page loads at once
 *  ID-05 - the scripts build no inline code or style: no eval, new Function, string timers, innerHTML with markup
 *          from data other than the server's fragment, no setAttribute( 'style' ), no style="" in markup
 *
 * Run: php vendor/bin/phpunit --bootstrap tests/bootstrap.php extension/expimageeditor/tests/phpunit/unit/
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expimageeditor
 */

class expImageEditorDesignTest extends PHPUnit\Framework\TestCase
{
    /** Our own scripts (the libraries are built elsewhere and not read here) */
    const SCRIPTS = array( 'expimageeditor.js', 'expimageeditor_raster.js', 'expimageeditor_svg.js' );

    private static function design()
    {
        return dirname( __DIR__, 3 ) . '/design/admin4';
    }

    private static function read( $path )
    {
        $file = self::design() . '/' . $path;
        if ( !is_file( $file ) )
            throw new RuntimeException( "Missing file $file" );
        return file_get_contents( $file );
    }

    /** A template without its {* comments *} */
    private static function code( $name = 'expimageeditor/config.tpl' )
    {
        return preg_replace( '/\{\*.*?\*\}/s', '', self::read( 'templates/' . $name ) );
    }

    /** The button of an edit template, its attributes */
    private function button( $code )
    {
        $this->assertSame( 1, preg_match( '/<button class="button expimageeditor-open" type="button" hidden(.*?)>/s', $code, $button ) );
        foreach ( array( 'save', 'src', 'type', 'name' ) as $data )
            $this->assertStringContainsString( "data-expimageeditor-$data=", $button[1] );
        $this->assertStringContainsString( "concat( 'expimageeditor/save/', \$attribute.id, '/', \$attribute.version )|ezurl( 'no' )", $button[1] );
        return $button[1];
    }

    /** ID-01 */
    public function testTheTemplateIsGuardedAndCarriesTheData()
    {
        $access = "fetch( 'user', 'has_access_to', hash( 'module', 'expimageeditor', 'function', 'edit' ) )";
        $include = "{include uri='design:expimageeditor/config.tpl'}";

        $editor = trim( self::code( 'content/datatype/edit/ezimage_editor.tpl' ) );
        $this->assertStringStartsWith( "{if $access}", $editor );
        $this->assertStringEndsWith( '{/if}', $editor );
        $this->assertStringContainsString( $include, $editor );
        $this->assertStringContainsString( 'data-expimageeditor-src="{$attribute.content.original.url|ezroot( \'no\' )}"', $this->button( $editor ) );

        $new = trim( self::code( 'content/datatype/edit/ezimage_new.tpl' ) );
        $this->assertStringStartsWith( "{if and( $access,", $new );
        foreach ( array( "ezini( 'ImageUploadSettings', 'AllowedMIMETypes', 'image.ini' )|contains( 'image/svg+xml' )",
                         "ezini( 'ImageUploadSettings', 'SanitizeSVG', 'image.ini' )|ne( 'disabled' )",
                         "ezini( 'EditorSettings', 'OutputMIMETypes', 'expimageeditor.ini' )|contains( 'image/svg+xml' )" ) as $condition )
            $this->assertStringContainsString( $condition, $new, 'a new drawing only where SVG is accepted' );
        $this->assertStringEndsWith( '{/if}', $new );
        $this->assertStringContainsString( $include, $new );
        $button = $this->button( $new );
        $this->assertStringContainsString( 'data-expimageeditor-src=""', $button );
        $this->assertStringContainsString( 'data-expimageeditor-type="image/svg+xml"', $button );
        // admin4's image template includes it where there is no image, and draws nothing of its own
        $core = dirname( __DIR__, 5 ) . '/design/admin4/templates/content/datatype/edit/';
        $this->assertMatchesRegularExpression( "/\\{else\\}\\s*\\{include uri='design:content\\/datatype\\/edit\\/ezimage_new.tpl'\\}/", (string)file_get_contents( $core . 'ezimage.tpl' ) );
        $this->assertSame( '', trim( preg_replace( '/\{\*.*?\*\}/s', '', (string)file_get_contents( $core . 'ezimage_new.tpl' ) ) ) );

        $code = trim( self::code() );
        $this->assertMatchesRegularExpression( '/^\{if is_set\( \$expimageeditor_response \)\|not\(\)\}\s*\{run-once\}/', $code,
            'the settings are written once per page, and not into the save view\'s answer' );
        $this->assertStringEndsWith( "{/run-once}\n{/if}", $code );
        $this->assertStringContainsString( "{ezscript_require( 'expimageeditor.js' )}", $code );
        $this->assertStringContainsString( "{ezcss_require( 'expimageeditor.css' )}", $code );
        $this->assertSame( 1, preg_match_all( '/<div id="expimageeditor-config" hidden/', $code ) );
        $this->assertMatchesRegularExpression( '/data-conf="\{json_encode\( hash\(.*?\) \)\|wash\}"/s', $code );
        $this->assertMatchesRegularExpression( '/data-labels="\{json_encode\( hash\(.*?\) \)\|wash\}"/s', $code );
        // the module's view and its policy function
        $module = file_get_contents( dirname( __DIR__, 3 ) . '/modules/expimageeditor/module.php' );
        $this->assertMatchesRegularExpression( "/\\\$ViewList\\['save'\\].*'functions' => array\\( 'edit' \\)/s", $module );
    }

    /** The label keys of the template */
    private static function labels()
    {
        preg_match( '/data-labels="\{json_encode\( hash\((.*?)\) \)\|wash\}"/s', self::code(), $block );
        preg_match_all( "/'(\\w+)', '(?:[^'\\\\]|\\\\.)*'\\|i18n\\( 'extension\\/expimageeditor' \\)/", $block[1], $keys );
        return $keys[1];
    }

    /** ID-02 */
    public function testEveryTextIsTranslatedAndUsed()
    {
        $labels = self::labels();
        $this->assertGreaterThan( 50, count( $labels ) );
        $this->assertSame( count( $labels ), count( array_unique( $labels ) ), 'no label twice' );

        $asked = array();
        $named = array();
        foreach ( self::SCRIPTS as $script )
        {
            $js = self::read( "javascript/$script" );
            preg_match_all( "/\\bt\\(\\s*'(\\w+)'/", $js, $calls );
            $asked = array_merge( $asked, $calls[1] );
            // a key may also be named in a table of tools and passed to t() later
            preg_match_all( "/'(\\w+)'/", $js, $strings );
            $named = array_merge( $named, $strings[1] );
        }
        $this->assertSame( array(), array_values( array_diff( array_unique( $asked ), $labels ) ), 'texts the scripts ask for and the template lacks' );
        $this->assertSame( array(), array_values( array_diff( $labels, $named ) ), 'labels no script names' );
    }

    /** ID-03 */
    public function testEveryIconHasItsFileAndClass()
    {
        $css = self::read( 'stylesheets/expimageeditor.css' );
        $icons = array();
        foreach ( self::SCRIPTS as $script )
        {
            preg_match_all( "/\\bicon:\\s*'([a-z0-9-]+)'/", self::read( "javascript/$script" ), $found );
            $icons = array_merge( $icons, $found[1] );
        }
        $icons = array_unique( $icons );
        $this->assertNotEmpty( $icons );
        foreach ( $icons as $icon )
        {
            $this->assertFileExists( self::design() . "/images/expimageeditor/icons/$icon.svg" );
            $this->assertStringContainsString( ".expimageeditor-icon-$icon { --expimageeditor-icon: url(../images/expimageeditor/icons/$icon.svg); }", $css );
        }
        $this->assertFileExists( self::design() . '/images/expimageeditor/icons/LICENSE' );
    }

    /** ID-04 */
    public function testTheScriptsTheTemplateNamesExist()
    {
        preg_match_all( "/'(javascript\\/[\\w.-]+\\.js)'\\|ezdesign\\( 'no' \\)/", self::code(), $scripts );
        $this->assertNotEmpty( $scripts[1] );
        foreach ( $scripts[1] as $script )
            $this->assertFileExists( self::design() . '/' . $script );
        // the editors are loaded on first use, not with the page
        preg_match_all( "/\\{ezscript_require\\( '([^']+)' \\)\\}/", self::code(), $required );
        $this->assertSame( array( 'expimageeditor.js' ), $required[1] );
    }

    /** ID-05 */
    public function testTheScriptsBuildNoInlineCodeOrStyle()
    {
        foreach ( self::SCRIPTS as $script )
        {
            $js = self::read( "javascript/$script" );
            // comments are not code
            $code = preg_replace( array( '#/\*.*?\*/#s', '#(^|[^:\\\\])//[^\n]*#' ), array( '', '$1' ), $js );
            $this->assertDoesNotMatchRegularExpression( '/\beval\s*\(|new\s+Function\b|set(?:Timeout|Interval)\s*\(\s*[\'"]/', $code, "$script: code from a string" );
            $this->assertDoesNotMatchRegularExpression( '/setAttribute\(\s*[\'"]style[\'"]/', $code, "$script: a style attribute" );
            $this->assertDoesNotMatchRegularExpression( '/<[a-z][^>\'"]*\sstyle\s*=/i', $code, "$script: style=\"\" in markup" );
            $this->assertDoesNotMatchRegularExpression( '/\.on[a-z]+\s*=\s*[\'"]|setAttribute\(\s*[\'"]on/', $code, "$script: an event handler from a string" );
            $this->assertDoesNotMatchRegularExpression( '/document\.write/', $code, "$script: document.write" );
            preg_match_all( '/\.(?:innerHTML|outerHTML)\s*=|insertAdjacentHTML/', $code, $html );
            // the frame parses the save view's fragment (the server's own template) into a <template>; nothing else
            $this->assertSame( $script === 'expimageeditor.js' ? 1 : 0, count( $html[0] ), "$script: markup from a string" );
        }
    }

    /** ID-06: on a phone the dialog fits the visible window, the targets are 44px and the fields do not zoom */
    public function testTheDialogIsUsableOnAPhone()
    {
        $css = preg_replace( '#/\*.*?\*/#s', '', self::read( 'stylesheets/expimageeditor.css' ) );
        $this->assertMatchesRegularExpression( '/@media \(max-width: 760px\) \{\s*dialog\.expimageeditor \{[^}]*height: 100dvh;/', $css,
                                               'iOS: 100vh is the height with the bars collapsed, the footer slid under them' );
        $this->assertMatchesRegularExpression( '/@media \(max-width: 760px\) \{[^@]*\.expimageeditor-toolbar \{ flex-wrap: nowrap; overflow-x: auto;/', $css,
                                               'one sideways row of tools on a phone, not three wrapped rows' );
        $this->assertMatchesRegularExpression( '/@media \(max-width: 760px\), \(pointer: coarse\) \{\s*dialog\.expimageeditor \{ --a4-control-h: 44px; \}/', $css );
        // the dialog is a child of <body>: admin4's 16px rule for #page does not reach it
        $this->assertMatchesRegularExpression( '/@media \(pointer: coarse\) \{\s*dialog\.expimageeditor :is\([^{]*select, textarea\) \{ font-size: var\(--a4-text-l, 16px\); \}/', $css );
    }
}
