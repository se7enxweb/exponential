<?php
/**
 * The layout editor of Layouts (explayouts_ui_api, app.tpl) loads no jQuery: its app bundle runs on a small DOM
 * layer of its own (the bundle's module "jquery"), with its own drag and drop instead of jQuery UI.
 *
 * - LE-01: app.tpl requests no jQuery: outside its comments it names neither ezjsc::jquery, ezjsc::jqueryUI,
 *          ezjsc::jqueryio nor any other jQuery script, and calls no ezscript function that could bring one
 * - LE-02: app.tpl loads exactly the scripts of EDITOR_SCRIPTS from the package's design (a new script has to be
 *          added there and is then checked with the others), and each of them exists
 * - LE-03: none of those scripts uses jQuery: outside comments no jQuery, window.jQuery, window.$ or $( / $. (the
 *          app bundle's own module variables aside, see BUNDLE_ALLOWED)
 * - LE-04: the app bundle carries neither jQuery nor jQuery UI (their license headers), and its module "jquery" is
 *          the editor's DOM layer, not the library
 * - LE-05: no explayouts_ui_api.ini in settings/ or extension/x/settings/ adds a jQuery script to the page through
 *          [App] Javascripts[]
 *
 * LE-01 to LE-05 need the package installed in extension/explayouts_ui_api (composer install) in a version that
 * runs without jQuery, 1.3.30 or later (composer.json requires it); without it they are skipped, with the reason.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminLayoutEditorWithoutJQueryTest.php
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminLayoutEditorWithoutJQueryTest extends PHPUnit\Framework\TestCase
{
    /** The scripts app.tpl loads, below extension/explayouts_ui_api/design/standard/javascript/ */
    const EDITOR_SCRIPTS = array(
        'explayouts-app-head.js',
        'explayouts-tiptap.js',
        'netgen-layouts.js',
        'explayouts-mobile.js',
        'explayouts-app.js',
    );

    /**
     * Code of the app bundle that names jQuery and is allowed: Backbone's fallback for a page without a module system
     * (window.jQuery || Zepto || ender || $). The bundle never takes it: it has a module system and hands Backbone
     * require("jquery"), the editor's DOM layer.
     */
    const BUNDLE_ALLOWED = array(
        'netgen-layouts.js' => array( 'r.Backbone=i(r,{},r._,r.jQuery||r.Zepto||r.ender||r.$)' ),
    );

    /** The first version of explayouts_ui_api whose layout editor loads no jQuery */
    const FIRST_VERSION_WITHOUT_JQUERY = '1.3.30';

    /** Built bundles: minified, so a $ in them is a variable of their own; LE-03 checks them for jQuery only */
    const BUILT_SCRIPTS = array( 'explayouts-tiptap.js', 'netgen-layouts.js' );

    /**
     * A request of jQuery in an INI value: ezjscore's jQuery packs, a jQuery library file, exp::compat, and scripts
     * known to need jQuery (ezajaxrelations_jquery.js is plain JavaScript despite its name and does not match)
     */
    const JQUERY_REQUEST = '/ezjsc::jquery|(?<![\w-])jquery[\w.-]*\.js|\bjqueryUI\b|\bjqueryio\b|exp::compat|ezstarrating_jquery\.js|(?<![\w-])ezie\.js/i';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function editorDir()
    {
        return self::root() . '/extension/explayouts_ui_api/design/standard';
    }

    /** A template without its {* comments *} */
    private static function templateCode( $source )
    {
        return preg_replace( '#\{\*.*?\*\}#s', '', $source );
    }

    /** JavaScript without its comments, so a comment cannot satisfy or fail an assertion on code */
    private static function scriptCode( $source )
    {
        return preg_replace( array( '#/\*.*?\*/#s', '#(?<![:\'"\\\\])//[^\n]*#' ), '', $source );
    }

    /** The editor's checks need the package, in a version without jQuery */
    private function requireEditorPackage()
    {
        $xml = self::root() . '/extension/explayouts_ui_api/extension.xml';
        if ( !is_file( $xml ) )
            $this->markTestSkipped( 'explayouts_ui_api is not installed in extension/ (composer install)' );
        $version = preg_match( '#<version>([^<]+)</version>#', file_get_contents( $xml ), $m ) ? trim( $m[1] ) : '0';
        if ( version_compare( $version, self::FIRST_VERSION_WITHOUT_JQUERY, '<' ) )
            $this->markTestSkipped( "explayouts_ui_api $version still loads jQuery; " . self::FIRST_VERSION_WITHOUT_JQUERY . ' does not' );
    }

    private static function appTemplate()
    {
        return AdminFiles::read( self::editorDir() . '/templates/explayouts_ui_api/app.tpl' );
    }

    public function testTheEditorPageRequestsNoJQuery()
    {
        $this->requireEditorPackage();
        $code = self::templateCode( self::appTemplate() );
        $this->assertDoesNotMatchRegularExpression( '/jquery/i', $code, 'app.tpl names jQuery outside its comments' );
        $this->assertDoesNotMatchRegularExpression( '/\bezscript(?:_require|_load)?\s*\(/', $code,
            'app.tpl calls ezscript: an ezjscore pack can bring jQuery' );
    }

    public function testTheEditorPageLoadsOnlyTheCheckedScripts()
    {
        $this->requireEditorPackage();
        $code = self::templateCode( self::appTemplate() );
        preg_match_all( '#<script\b[^>]*\bsrc="\{\'javascript/([^\']+)\'\|ezdesign#', $code, $matches );
        $this->assertSame( self::EDITOR_SCRIPTS, $matches[1],
            'app.tpl loads other scripts than EDITOR_SCRIPTS (in this order): add a new one there, so it is checked' );
        foreach ( self::EDITOR_SCRIPTS as $name )
            $this->assertFileExists( self::editorDir() . '/javascript/' . $name );

        // the other script tags: the Google Maps API, and the [App] Javascripts[] of LE-05
        preg_match_all( '#<script\b[^>]*\bsrc="([^"]*)"#', $code, $all );
        $others = array_values( array_filter( $all[1], function ( $src ) {
            return strpos( $src, "{'javascript/" ) !== 0;
        } ) );
        $this->assertSame( array( 'https://maps.googleapis.com/maps/api/js?key={$google_maps_api_key|urlencode()}&loading=async&callback=initMap', '{$javascript|wash()}' ),
            $others, 'app.tpl loads a script that is neither the package\'s nor the Maps API nor [App] Javascripts[]' );
    }

    public static function scripts()
    {
        $cases = array();
        foreach ( self::EDITOR_SCRIPTS as $name )
            $cases[$name] = array( $name );
        return $cases;
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'scripts' )]
    public function testTheEditorScriptUsesNoJQuery( $name )
    {
        $this->requireEditorPackage();
        $code = self::scriptCode( AdminFiles::read( self::editorDir() . '/javascript/' . $name ) );
        foreach ( isset( self::BUNDLE_ALLOWED[$name] ) ? self::BUNDLE_ALLOWED[$name] : array() as $allowed )
        {
            $this->assertSame( 1, substr_count( $code, $allowed ), "$name: the allowed code changed: $allowed" );
            $code = str_replace( $allowed, '', $code );
        }
        self::assertNoMatch( '/\bjQuery\b/', $code, "$name names jQuery" );
        self::assertNoMatch( '/\bwindow\s*(?:\.\s*\$|\[\s*[\'"](?:\$|jQuery)[\'"]\s*\])/', $code, "$name reaches jQuery through window.\$" );
        if ( !in_array( $name, self::BUILT_SCRIPTS, true ) )
            self::assertNoMatch( '/(?<![\w.$])\$\s*[(.]/', $code, "$name calls \$() or \$.something" );
    }

    /** Fails with the code around the match, not with the whole file (the app bundle is 700 KB) */
    private static function assertNoMatch( $pattern, $code, $message )
    {
        $found = preg_match( $pattern, $code, $match, PREG_OFFSET_CAPTURE );
        self::assertSame( 0, $found, $found ? $message . ': ...' . substr( $code, max( 0, $match[0][1] - 60 ), 140 ) . '...' : $message );
    }

    public function testTheAppBundleCarriesNeitherJQueryNorJQueryUI()
    {
        $this->requireEditorPackage();
        $bundle = AdminFiles::read( self::editorDir() . '/javascript/netgen-layouts.js' );
        foreach ( array( 'jQuery JavaScript Library', 'jquery.org/license', 'jQuery UI - v', 'jqueryui.com',
                         'jquery.com/license' ) as $signature )
            $this->assertStringNotContainsStringIgnoringCase( $signature, $bundle, "the bundle carries $signature" );

        // module "jquery" (140) is the editor's DOM layer, the one the bundle hands Backbone and Core
        $layer = AdminFiles::read( self::root() . '/extension/explayouts_ui_api/src/app-patches/modules/140-jquery.js' );
        $this->assertStringContainsString( "The layout editor's DOM layer", $layer );
        $this->assertStringContainsString( trim( $layer ), $bundle, 'the bundle is not built from module 140 (apply.mjs)' );
        $this->assertStringContainsString( 'jquery:140', $bundle );
    }

    public function testNoSettingAddsJQueryToTheEditorPage()
    {
        $this->requireEditorPackage();
        $files = array_merge(
            glob( self::root() . '/settings/{,override/,siteaccess/*/}explayouts_ui_api.ini*', GLOB_BRACE ) ?: array(),
            glob( self::root() . '/extension/*/settings/{,override/,siteaccess/*/}explayouts_ui_api.ini*', GLOB_BRACE ) ?: array()
        );
        foreach ( $files as $file )
        {
            foreach ( preg_split( '/\R/', AdminFiles::read( $file ) ) as $line )
            {
                if ( preg_match( '/^\s*Javascripts\[\]\s*=(.*)$/', $line, $match ) )
                    $this->assertDoesNotMatchRegularExpression( self::JQUERY_REQUEST, $match[1], "$file adds jQuery to the layout editor" );
            }
        }
        // the package's own settings carry no [App] Javascripts[] at all
        $this->assertFileExists( self::root() . '/extension/explayouts_ui_api/settings' );
    }
}
