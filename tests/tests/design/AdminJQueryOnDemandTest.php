<?php
/**
 * jQuery only on the admin pages that need it. admin4 and admin4l load neither jQuery (ezjsc::jquery) nor Exponential
 * UI (exp::*) on every page: a template whose scripts need them requests them itself with ezscript_require, outside
 * every cache-block, and the head's ezscript_load prints them before the page scripts.
 *
 * - JQ-01: admin4's page_head_script.tpl loads neither ezjsc::jquery nor ezjsc::jqueryio on every page; a4_url.js and
 *          a4_dom.js stay first
 * - JQ-02: no BackendJavaScriptList[] in settings/ or in an extension shipped in this repository names jQuery or an
 *          exp:: module
 * - JQ-03: the scripts those lists name (found through the admin design chain) do not call jQuery, $ or Exp; a
 *          guarded optional read such as window.jQuery && window.jQuery.ez is allowed
 * - JQ-04: every admin template of this repository (the copy the admin design chain finds first) that loads a script
 *          calling jQuery, has an inline script calling it or requests exp::compat also requests jQuery
 *          (ezjsc::jquery, or exp::core, which brings its own)
 * - JQ-05: no admin template (packages included) requests jQuery or an exp:: module inside a {cache-block}: on a
 *          cache hit the request would not run
 * - JQ-06: the page layouts of admin4 and admin4l include page_head_script.tpl outside every cache-block, so the
 *          head prints what the page requested on every request, and a request made later in the page can print
 *          its scripts
 * - JQ-07: the older admin designs (admin, admin3) still load jQuery and, after BackendJavaScriptList, the new
 *          ClassicBackendJavaScriptList on every page
 * - JQ-08: reduced motion reaches a jQuery that a page loads after admin4.js
 * - JQ-09: admin4's node tabs (node_tabs.js) run without jQuery, and the node view requests only them
 * - JQ-10: admin4's sub-items table runs on Exponential UI without jQuery, and the node view's templates request
 *          the modules it uses, no jQuery
 * - JQ-11: admin4's content edit, class edit and queued publishing pages request no jQuery, only the exp:: modules
 *          their scripts use, and those scripts run without jQuery
 * - JQ-12: the dashboard's community activity block runs without jQuery
 * - JQ-13: the installed packages (extensions with a composer.json, installed by Composer): once no every-page list
 *          brings jQuery any more, none of their admin templates may load a script that needs jQuery without
 *          requesting it. While a package still puts ezjsc::jquery into BackendJavaScriptList (eztags up to 2.4.24),
 *          jQuery is still on every page and the test is skipped, naming what is pending.
 *
 * The packages are not part of this repository; their checks run only where they are installed.
 *
 * DB-less. Run: php vendor/bin/phpunit tests/tests/design/AdminJQueryOnDemandTest.php
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminJQueryOnDemandTest extends PHPUnit\Framework\TestCase
{
    /** The admin siteaccesses' design chain, first match wins */
    const DESIGNS = array( 'admin4l', 'admin4', 'admin3', 'admin2', 'admin', 'standard' );

    /**
     * Template directories left out of JQ-04 and JQ-13: the online editor's popups (ezoe/*) are pages of their own,
     * drawn by ezoe/popup_pagelayout.tpl, which requests jQuery itself
     */
    const OWN_PAGE_LAYOUT = array( 'ezoe/' );

    /** JQ-11: the scripts of the edit pages and of queued publishing, which run without jQuery */
    const NO_JQUERY_SCRIPTS = array(
        'design/admin4/javascript/expajaxuploader.js',
        'design/admin4/javascript/ezajaxrelations_jquery.js',
        'design/admin4/javascript/page/content_ezobjectrelation_ajaxuploader.js',
        'design/admin4/javascript/page/content_ezobjectrelationlist_ajaxuploader.js',
        'design/admin4/javascript/page/content_datepicker.js',
        'design/admin4/javascript/page/class_edit.js',
        'design/standard/javascript/ezasynchronouspublishing.js',
        'design/standard/javascript/content_queued.js',
    );

    /** JQ-11: their templates, which request no jQuery */
    const NO_JQUERY_TEMPLATES = array(
        'design/admin4/templates/content/edit.tpl',
        'design/admin4/templates/class/edit.tpl',
        'design/admin4/templates/content/queued.tpl',
        'design/admin4/templates/content/datatype/edit/ezdate.tpl',
        'design/admin4/templates/content/datatype/edit/ezdatetime.tpl',
        'design/admin4/templates/content/datatype/edit/ezobjectrelation_ajaxuploader.tpl',
        'design/admin4/templates/content/datatype/edit/ezobjectrelationlist_ajaxuploader.tpl',
        'design/admin4/templates/content/datatype/edit/ezobjectrelation_ajax_search.tpl',
    );

    /**
     * A call of jQuery in code without comments: jQuery( / jQuery. / $( / $. / Exp.$ (Exponential UI's jQuery). A $
     * right after ] or \ is a character of a regular expression, not a call: AdminNeo's adminneo/main.js (sevenx_dse)
     * escapes with /[\\.+*?\[^\]$(){}=!<>|:]/g.
     */
    const JQUERY_CALL = '/(?<![\w$.\]\\\\])(?:jQuery|\$)\s*[(.]|\bExp\.\$\s*[(.]/';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return AdminFiles::read( self::root() . '/' . $path );
    }

    private static function relative( $file )
    {
        return substr( $file, strlen( self::root() ) + 1 );
    }

    /** A template without its {* comments *} */
    private static function template( $path )
    {
        return preg_replace( '/\{\*.*?\*\}/s', '', self::read( $path ) );
    }

    /** A script without its comments */
    private static function script( $path )
    {
        return preg_replace( array( '#/\*.*?\*/#s', '#(?<![:\'"\\\\])//[^\n]*#' ), '', self::read( $path ) );
    }

    /**
     * Whether code calls jQuery. A guarded optional read of the page's jQuery is allowed (window.jQuery &&
     * window.jQuery.ez; Exp.$ behind if (Exp.$ ...) or Exp.$ &&, a plugin only where the page has jQuery), and so is a
     * $ the code defines itself (function $( id ) { return document.getElementById( id ); }). In a minified file
     * ($minified) a $ is a variable of its own: only jQuery itself counts.
     */
    private static function callsJQuery( $code, $minified = false )
    {
        // $ taken from the page's jQuery (var $ = window.jQuery;, a wrapper's (function ($) { ... }(jQuery))) is jQuery
        if ( preg_match( '/\b(?:var|let|const)\s+\$\s*=\s*(?:window\.)?jQuery\b|\}\s*\)?\s*\(\s*(?:window\.)?jQuery\s*\)/', $code ) )
            return true;
        $code = preg_replace( '/window\.jQuery\b/', 'window.pageJQuery', $code );
        if ( $minified )
            return preg_match( '/(?<![\w$.])jQuery\s*[(.]/', $code ) === 1;
        if ( preg_match( '/\bExp\.\$\s*&&|\bif\s*\(\s*!?\s*Exp\.\$/', $code ) )
            $code = preg_replace( '/\bExp\.\$/', 'Exp.pageJQuery', $code );
        if ( preg_match( '/\bfunction\s+\$\s*\(|\b(?:var|let|const)\s+\$\s*=/', $code ) )
            $code = preg_replace( '/(?<![\w$.])\$\s*[(.]/', 'own(', $code );
        return preg_match( self::JQUERY_CALL, $code ) === 1;
    }

    /** An extension installed by Composer (it has a composer.json), not shipped in this repository */
    private static function isPackage( $extensionDir )
    {
        return is_file( "$extensionDir/composer.json" );
    }

    /** The extension directories: shipped in this repository (false) or installed packages (true) */
    private static function extensions( $packages )
    {
        $dirs = array();
        foreach ( glob( self::root() . '/extension/*', GLOB_ONLYDIR ) ?: array() as $dir )
        {
            if ( self::isPackage( $dir ) === $packages )
                $dirs[] = $dir;
        }
        return $dirs;
    }

    /** The settings files that can carry BackendJavaScriptList[]: settings/ and the given extension directories */
    private static function designSettings( array $extensionDirs, $withSettings = true )
    {
        $root = self::root();
        $files = array();
        if ( $withSettings )
        {
            $files = array_merge( glob( "$root/settings/design.ini*" ) ?: array(),
                                  glob( "$root/settings/{override,siteaccess/*}/design.ini*", GLOB_BRACE ) ?: array() );
        }
        foreach ( $extensionDirs as $dir )
        {
            $files = array_merge( $files, glob( "$dir/settings/design.ini*" ) ?: array(),
                                  glob( "$dir/settings/{override,siteaccess/*}/design.ini*", GLOB_BRACE ) ?: array() );
        }
        return array_map( array( __CLASS__, 'relative' ), $files );
    }

    /** The BackendJavaScriptList[] entries of settings files: [ file, entry ] */
    private static function backendEntries( array $files )
    {
        $entries = array();
        foreach ( $files as $path )
        {
            preg_match_all( '/^\s*BackendJavaScriptList\[\]=(\S+)\s*$/m', self::read( $path ), $m );
            foreach ( $m[1] as $entry )
                $entries[] = array( $path, $entry );
        }
        return $entries;
    }

    /** The file the admin design chain finds for a script name (extensions' copies before the design's own), or false */
    private static function findScript( $name )
    {
        $root = self::root();
        foreach ( self::DESIGNS as $design )
        {
            $found = glob( "$root/extension/*/design/$design/javascript/$name" ) ?: array();
            if ( is_file( "$root/design/$design/javascript/$name" ) )
                $found[] = "$root/design/$design/javascript/$name";
            if ( $found )
                return self::relative( $found[0] );
        }
        return false;
    }

    /**
     * The admin templates below the given base directories: for each template name the file the admin design chain
     * finds first (per design the extensions' copy before the design's own), so a hidden copy of an older design is
     * not held to these rules. $bases: design => list of templates directories, already in that order.
     */
    private static function adminTemplates( array $extensionDirs, $withKernel = true )
    {
        $root = self::root();
        $files = array();
        foreach ( self::DESIGNS as $design )
        {
            $bases = array();
            foreach ( $extensionDirs as $dir )
                $bases[] = "$dir/design/$design/templates";
            if ( $withKernel )
                $bases[] = "$root/design/$design/templates";
            foreach ( $bases as $dir )
            {
                if ( !is_dir( $dir ) )
                    continue;
                $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
                foreach ( $it as $file )
                {
                    $name = substr( $file->getPathname(), strlen( $dir ) + 1 );
                    if ( substr( $name, -4 ) === '.tpl' && !isset( $files[$name] ) )
                        $files[$name] = self::relative( $file->getPathname() );
                }
            }
        }
        ksort( $files );
        return $files;
    }

    /** The {ezscript...( ... )} calls of a template: [ offset, call ] */
    private static function scriptCalls( $tpl )
    {
        preg_match_all( '/\{ezscript(?:_require|_load)?\(.*?\)\s*\}/s', $tpl, $m, PREG_OFFSET_CAPTURE );
        $calls = array();
        foreach ( $m[0] as $match )
            $calls[] = array( $match[1], $match[0] );
        return $calls;
    }

    /** Whether a call names jQuery itself: ezjsc::jquery, or exp::core (not ::shared), which brings its own */
    private static function namesJQuery( $call )
    {
        return strpos( $call, "'ezjsc::jquery'" ) !== false || strpos( $call, "'exp::core'" ) !== false;
    }

    /** How many cache-blocks are open at an offset of a template */
    private static function cacheDepth( $tpl, $offset )
    {
        $before = substr( $tpl, 0, $offset );
        return preg_match_all( '/\{cache-block\b/', $before ) - substr_count( $before, '{/cache-block}' );
    }

    /**
     * What of a template needs jQuery: the scripts it loads (ezscript calls, <script src={'javascript/...'|ezdesign}>)
     * that call jQuery, an inline script that calls it, exp::compat. Empty when nothing does.
     */
    private static function jQueryNeeds( $tpl )
    {
        $needs = array();
        $names = array();
        foreach ( self::scriptCalls( $tpl ) as $call )
        {
            preg_match_all( '/[\'"]([^\'"]+\.js)[\'"]/', $call[1], $m );
            $names = array_merge( $names, $m[1] );
            if ( strpos( $call[1], "'exp::compat'" ) !== false )
                $needs[] = 'exp::compat';
        }
        preg_match_all( '#[\'"]javascript/([^\'"]+\.js)[\'"]\s*\|\s*ezdesign#', $tpl, $m );
        $names = array_merge( $names, $m[1] );
        foreach ( array_unique( $names ) as $name )
        {
            $file = self::findScript( $name );
            if ( $file !== false && self::callsJQuery( self::script( $file ), substr( $file, -7 ) === '.min.js' ) )
                $needs[] = $name;
        }
        preg_match_all( '#<script\b(?![^>]*\bsrc=)(?![^>]*type="(?:application/json|text/template)")[^>]*>(.*?)</script>#s', $tpl, $m );
        foreach ( $m[1] as $inline )
        {
            if ( self::callsJQuery( preg_replace( array( '#/\*.*?\*/#s', '#(?<![:\'"\\\\])//[^\n]*#' ), '', $inline ) ) )
                $needs[] = 'an inline script';
        }
        return array_values( array_unique( $needs ) );
    }

    /** Whether a template requests jQuery itself */
    private static function requestsJQuery( $tpl )
    {
        foreach ( self::scriptCalls( $tpl ) as $call )
        {
            if ( self::namesJQuery( $call[1] ) )
                return true;
        }
        return false;
    }

    /** Whether a template path belongs to a page with a page layout of its own (OWN_PAGE_LAYOUT) */
    private static function ownPageLayout( $name )
    {
        foreach ( self::OWN_PAGE_LAYOUT as $prefix )
        {
            if ( strpos( $name, $prefix ) === 0 )
                return true;
        }
        return false;
    }

    /** JQ-01 */
    public function testTheHeadDoesNotLoadJQueryOnEveryPage()
    {
        $head = self::template( 'design/admin4/templates/page_head_script.tpl' );
        $this->assertStringNotContainsString( 'ezjsc::jquery', $head );
        $this->assertStringContainsString( "{ezscript_load( ezini( 'JavaScriptSettings', 'BackendJavaScriptList', 'design.ini' )|prepend( 'a4_url.js', 'a4_dom.js' ) )}", $head );
        $this->assertStringContainsString( "{ezscript_load( array( 'a4_url.js', 'a4_dom.js' ) )}", $head );
    }

    /** JQ-02 */
    public function testNoBackendScriptListNamesJQueryOrExponentialUI()
    {
        $entries = self::backendEntries( self::designSettings( self::extensions( false ) ) );
        $this->assertNotEmpty( $entries, 'no BackendJavaScriptList[] entry found' );
        foreach ( $entries as $entry )
            $this->assertDoesNotMatchRegularExpression( '/^(ezjsc::jquery|exp::)/i', $entry[1], "$entry[0]: BackendJavaScriptList[]=$entry[1] loads jQuery or Exponential UI on every page" );
    }

    /** JQ-03 */
    public function testTheScriptsOfEveryPageRunWithoutJQuery()
    {
        $checked = 0;
        foreach ( self::backendEntries( self::designSettings( self::extensions( false ) ) ) as $entry )
        {
            if ( strpos( $entry[1], '::' ) !== false )
                continue;
            $file = self::findScript( $entry[1] );
            $this->assertNotFalse( $file, "$entry[0]: BackendJavaScriptList[]=$entry[1] is in no admin design" );
            $checked++;
            $js = self::script( $file );
            $this->assertFalse( self::callsJQuery( $js ), "$file (BackendJavaScriptList, on every page) uses jQuery" );
            $this->assertDoesNotMatchRegularExpression( '/(?<![\w$.])Exp\.(?!\w*\s*=)/', $js, "$file (BackendJavaScriptList, on every page) uses Exponential UI" );
        }
        $this->assertGreaterThan( 0, $checked );
    }

    /** JQ-04 */
    public function testEveryTemplateThatNeedsJQueryRequestsIt()
    {
        $templates = self::adminTemplates( self::extensions( false ) );
        $this->assertNotEmpty( $templates );
        $scanned = 0;
        $missing = array();
        foreach ( $templates as $name => $path )
        {
            if ( self::ownPageLayout( $name ) )
                continue;
            $tpl = self::template( $path );
            if ( self::scriptCalls( $tpl ) )
                $scanned++;
            $needs = self::jQueryNeeds( $tpl );
            if ( $needs && !self::requestsJQuery( $tpl ) )
                $missing[] = "$path loads " . implode( ', ', $needs ) . ' but does not request ezjsc::jquery';
        }
        $this->assertSame( array(), $missing );
        $this->assertGreaterThan( 50, $scanned, 'hardly a template loads a script: the scan found nothing to check' );
    }

    /** JQ-05 */
    public function testNoJQueryRequestSitsInACacheBlock()
    {
        $cached = array();
        $templates = array_merge( self::adminTemplates( self::extensions( false ) ), self::adminTemplates( self::extensions( true ), false ) );
        foreach ( $templates as $path )
        {
            $tpl = self::template( $path );
            foreach ( self::scriptCalls( $tpl ) as $call )
            {
                if ( strpos( $call[1], '{ezscript_require' ) !== 0 )
                    continue;
                if ( !self::namesJQuery( $call[1] ) && strpos( $call[1], "'exp::" ) === false )
                    continue;
                if ( self::cacheDepth( $tpl, $call[0] ) !== 0 )
                    $cached[] = "$path: $call[1] is inside a cache-block";
            }
        }
        $this->assertSame( array(), $cached );
    }

    /** JQ-06 */
    public function testThePageLayoutsPrintTheHeadScriptsOutsideTheCacheBlocks()
    {
        foreach ( array( 'admin4l', 'admin4' ) as $design )
        {
            $path = "design/$design/templates/pagelayout.tpl";
            $tpl = self::template( $path );
            $at = strpos( $tpl, "{include uri='design:page_head_script.tpl'}" );
            $this->assertNotFalse( $at, $path );
            $this->assertSame( 0, self::cacheDepth( $tpl, $at ), "$path: page_head_script.tpl is inside a cache-block" );
            // the head's styles stay cached, before it
            $style = strpos( $tpl, "{include uri='design:page_head_style.tpl'}" );
            $this->assertNotFalse( $style, $path );
            $this->assertLessThan( $at, $style, $path );
            $this->assertSame( 1, self::cacheDepth( $tpl, $style ), "$path: page_head_style.tpl left the cache-block" );
        }
    }

    /** JQ-07 */
    public function testTheOlderAdminDesignsStillLoadTheirEveryPageScripts()
    {
        foreach ( array( 'design/admin/templates/page_head_script.tpl', 'design/admin3/templates/page_head_script.tpl' ) as $path )
        {
            $tpl = self::template( $path );
            $this->assertStringContainsString( "ezini( 'JavaScriptSettings', 'ClassicBackendJavaScriptList', 'design.ini' )", $tpl, $path );
            $this->assertStringContainsString( "'ezjsc::jquery'", $tpl, $path );
        }
        $this->assertStringContainsString( "\nClassicBackendJavaScriptList[]\n", self::read( 'settings/design.ini' ) );
        $this->assertStringNotContainsString( 'ClassicBackendJavaScriptList', self::template( 'design/admin4/templates/page_head_script.tpl' ) );
    }

    /** JQ-08 */
    public function testReducedMotionReachesAJQueryLoadedAfterAdmin4()
    {
        $js = self::read( 'design/admin4/javascript/admin4.js' );
        $this->assertStringContainsString( "window.jQuery.fx.off = true", $js );
        $this->assertStringContainsString( "document.addEventListener('DOMContentLoaded', fxOff);", $js );
        $this->assertStringContainsString( "window.addEventListener('load', fxOff);", $js );
    }

    /** JQ-09 */
    public function testTheNodeTabsRunWithoutJQuery()
    {
        $this->assertFalse( self::callsJQuery( self::script( 'design/admin4/javascript/node_tabs.js' ) ), 'node_tabs.js uses jQuery' );
        foreach ( array( 'design/admin4/templates/window_controls.tpl', 'design/admin4/templates/node/top_node_full.tpl' ) as $path )
        {
            $tpl = self::template( $path );
            $this->assertMatchesRegularExpression( "/\{ezscript_require\( (?:array\( )?'node_tabs\.js'/", $tpl, $path );
            $this->assertStringNotContainsString( 'ezjsc::jquery', $tpl, $path );
        }
    }

    /** JQ-10 */
    public function testTheSubItemsTableRunsWithoutJQuery()
    {
        foreach ( array( 'design/admin4/javascript/ezajaxsubitems_expdatatable.js', 'design/admin4/javascript/subitems_priority_drag.js',
                         'design/admin4/javascript/page/children_detailed.js' ) as $path )
        {
            $js = self::script( $path );
            $this->assertFalse( self::callsJQuery( $js ), "$path uses jQuery" );
            $this->assertDoesNotMatchRegularExpression( '/expDataTable/', $js, "$path uses Exponential UI's jQuery plugin" );
        }
        $this->assertStringContainsString( "{ezscript_require( array( 'exp::core::shared', 'exp::io', 'exp::datatable', 'subitems_priority_drag.js', 'ezajaxsubitems_expdatatable.js' ) )}",
                                           self::template( 'design/admin4/templates/children.tpl' ) );
        $this->assertStringContainsString( "{ezscript_require( array( 'exp::core::shared', 'exp::io', 'exp::datatable' ) )}",
                                           self::template( 'design/admin4/templates/children_detailed.tpl' ) );
        foreach ( array( 'design/admin4/templates/children.tpl', 'design/admin4/templates/children_detailed.tpl',
                         'design/admin4/templates/window_controls.tpl', 'design/admin4/templates/node/top_node_full.tpl' ) as $path )
        {
            foreach ( self::scriptCalls( self::template( $path ) ) as $call )
                $this->assertFalse( self::namesJQuery( $call[1] ), "$path: $call[1] requests jQuery" );
        }
    }

    /** JQ-11 */
    public function testTheEditPagesRunWithoutJQuery()
    {
        foreach ( self::NO_JQUERY_SCRIPTS as $path )
            $this->assertFalse( self::callsJQuery( self::script( $path ) ), "$path uses jQuery" );
        foreach ( self::NO_JQUERY_TEMPLATES as $path )
        {
            $tpl = self::template( $path );
            $this->assertStringNotContainsString( 'ezjsc::jquery', $tpl, "$path requests jQuery" );
            foreach ( self::scriptCalls( $tpl ) as $call )
            {
                $this->assertFalse( self::namesJQuery( $call[1] ), "$path: $call[1] requests jQuery" );
                $this->assertStringNotContainsString( "'exp::compat'", $call[1], "$path: $call[1] needs jQuery" );
            }
        }
        // the templates request Exponential UI's core without jQuery in front of it, and what their scripts use
        foreach ( array( 'content/edit.tpl', 'class/edit.tpl' ) as $name )
            $this->assertStringContainsString( "{ezscript_require( array( 'exp::core::shared', 'exp::sticky' ) )}", self::template( "design/admin4/templates/$name" ), $name );
        $this->assertStringContainsString( "{ezscript_require( array( 'exp::core::shared', 'exp::io', 'ezasynchronouspublishing.js' ) )}", self::template( 'design/admin4/templates/content/queued.tpl' ) );
        $this->assertStringContainsString( "{ezscript_require( array( 'exp::core::shared', 'exp::io', 'ezajaxrelations_jquery.js' ) )}",
                                           self::template( 'design/admin4/templates/content/datatype/edit/ezobjectrelation_ajax_search.tpl' ) );
        foreach ( array( 'ezdate.tpl', 'ezdatetime.tpl' ) as $name )
            $this->assertStringContainsString( "{ezscript_require( array( 'exp::core::shared', 'exp::datepicker' ) )}",
                                               self::template( "design/admin4/templates/content/datatype/edit/$name" ), $name );
        foreach ( array( 'ezobjectrelation_ajaxuploader.tpl', 'ezobjectrelationlist_ajaxuploader.tpl' ) as $name )
            $this->assertStringContainsString( "{ezscript_require( array( 'exp::core::shared', 'exp::io', 'exp::dialog', 'exp::upload', 'expajaxuploader.js' ) )}",
                                               self::template( "design/admin4/templates/content/datatype/edit/$name" ), $name );
    }

    /** JQ-12 */
    public function testTheCommunityActivityBlockRunsWithoutJQuery()
    {
        $this->assertFalse( self::callsJQuery( self::script( 'design/standard/javascript/dashboard_community_activity.js' ) ) );
        $tpl = self::template( 'design/standard/templates/dashboard/community_activity.tpl' );
        $this->assertStringContainsString( "{ezscript( array( 'dashboard_community_activity.js' ) )}", $tpl );
        $this->assertStringNotContainsString( 'ezjsc::jquery', $tpl );
    }

    /** JQ-13 */
    public function testInstalledPackagesRequestTheJQueryTheyNeed()
    {
        $packages = self::extensions( true );
        if ( !$packages )
            $this->markTestSkipped( 'no package (an extension with a composer.json) is installed here' );

        // jQuery on every page, brought by a package's BackendJavaScriptList[]
        $everyPage = array();
        $pending = array();
        foreach ( self::backendEntries( self::designSettings( $packages, false ) ) as $entry )
        {
            $extension = explode( '/', $entry[0] )[1];
            if ( preg_match( '/^ezjsc::jquery$/i', $entry[1] ) )
                $everyPage[] = $extension;
            elseif ( strpos( $entry[1], '::' ) === false && ( $file = self::findScript( $entry[1] ) ) !== false
                     && self::callsJQuery( self::script( $file ) ) )
                $pending[] = "$extension: $entry[1] (BackendJavaScriptList) uses jQuery";
        }

        foreach ( self::adminTemplates( $packages, false ) as $name => $path )
        {
            if ( self::ownPageLayout( $name ) )
                continue;
            $tpl = self::template( $path );
            $needs = self::jQueryNeeds( $tpl );
            if ( $needs && !self::requestsJQuery( $tpl ) )
                $pending[] = "$path loads " . implode( ', ', $needs ) . ' but does not request ezjsc::jquery';
        }

        if ( $everyPage )
            $this->markTestSkipped( 'jQuery is still on every admin page through the BackendJavaScriptList[] of '
                                    . implode( ', ', array_unique( $everyPage ) ) . ' (a release that leaves it to the templates is pending); '
                                    . ( $pending ? 'until then these rely on it: ' . implode( '; ', $pending ) : 'nothing else relies on it' ) );
        $this->assertSame( array(), $pending, 'installed packages that need jQuery on an admin page without requesting it' );
    }
}
