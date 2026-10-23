<?php
/**
 * The small page scripts of admin4 that run on plain JavaScript: none of them uses jQuery again, so they keep working
 * on a page that loads no jQuery, and each one still binds what its template renders. The same for the frame's,
 * the tree's, the node view's, the edit pages', queued publishing's and the dashboard's scripts (OTHER_SCRIPTS),
 * which also evaluate no string and write no inline handler (CSP-10), the shared helpers they use (a4_dom.js, loaded
 * by the head), and the templates that no longer ask for jQuery for them.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminPageScriptsWithoutJQueryTest.php
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminPageScriptsWithoutJQueryTest extends PHPUnit\Framework\TestCase
{
    /** Page script => an id or class it binds, which must still be in the code */
    const SCRIPTS = array(
        'node_details.js' => 'tab-details-set-states',
        'node_ordering.js' => 'ezasi-sort-set',
        'node_locations.js' => 'tab-locations-list-set-main',
        'translations_main.js' => 'tab-translations-list-set-main',
        'translations_always_available.js' => 'tab-translations-alwaysavailable-btn',
        'content_edit_attribute.js' => 'ezcca-collapsed',
        'content_edit_languages.js' => 'ezcoeditlanguages-sourcets',
        'user_register.js' => 'PublishButton',
        'content_urlalias.js' => 'aliasform',
        'template_look_edit.js' => 'editform',
        'class_edit.js' => 'ezcca-edit-list',
        'content_multiedit.js' => 'MultiStoreButton',
        'children_detailed.js' => 'subitems-config',
        'content_ezobjectrelation_ajaxuploader.js' => 'simple-relation-upload-new',
        'content_ezobjectrelationlist_ajaxuploader.js' => 'relation-upload-new',
    );

    /**
     * The other scripts admin4 runs on plain JavaScript (the frame, the tree, the node view, content and class edit,
     * queued publishing, the dashboard), relative to the installation => an id or class each binds. Where a copy in
     * design/admin is the same file, it is checked too.
     */
    const OTHER_SCRIPTS = array(
        'design/admin4/javascript/a4_dom.js' => 'ezxform_token_js',
        'design/admin4/javascript/ezadmin_menubar.js' => 'sidebar-control',
        'design/admin4/javascript/contentstructuremenu_dynamic.js' => 'nodeicon',
        'design/admin/javascript/contentstructuremenu_dynamic.js' => 'nodeicon',
        'design/admin4/javascript/node_tabs.js' => 'show-hide-tabs',
        'design/admin4/javascript/ezajaxsubitems_expdatatable.js' => 'content-sub-items-list',
        'design/admin/javascript/ezajaxsubitems_expdatatable.js' => 'content-sub-items-list',
        'design/admin4/javascript/subitems_priority_drag.js' => 'a4-content-drag-grip',
        'design/admin4/javascript/ezajaxrelations_jquery.js' => 'ezobject-relation-search-btn',
        'design/admin/javascript/ezajaxrelations_jquery.js' => 'ezobject-relation-search-btn',
        'design/admin4/javascript/expajaxuploader.js' => 'AjaxUploadHandlerData',
        'design/admin/javascript/expajaxuploader.js' => 'AjaxUploadHandlerData',
        'design/standard/javascript/ezasynchronouspublishing.js' => 'ezap-message-publishing',
        'design/standard/javascript/dashboard_community_activity.js' => 'community-activity-config',
    );

    /** These keep a jQuery plugin for the older designs' templates, reached only through Exp.$ where a page has it */
    const PLUGIN_SCRIPTS = array( 'design/admin4/javascript/expajaxuploader.js', 'design/admin/javascript/expajaxuploader.js' );

    /** Scripts that call a4Dom (a4_dom.js): each says so when it is missing instead of failing on it */
    const A4DOM_USERS = array( 'design/admin4/javascript/node_tabs.js', 'design/admin4/javascript/page/class_edit.js' );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** The source without its comments, so a comment cannot satisfy or fail an assertion on code */
    private static function code( $name )
    {
        return self::codeOf( 'design/admin4/javascript/page/' . $name );
    }

    /** The same for a path relative to the installation */
    private static function codeOf( $path )
    {
        $source = AdminFiles::read( self::root() . '/' . $path );
        return preg_replace( array( '#/\*.*?\*/#s', '#(?<![:\'"\\\\])//[^\n]*#' ), '', $source );
    }

    public static function otherScripts()
    {
        $cases = array();
        foreach ( self::OTHER_SCRIPTS as $path => $bound )
            $cases[$path] = array( $path, $bound );
        return $cases;
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'otherScripts' )]
    public function testTheScriptUsesNoJQuery( $path, $bound )
    {
        $code = self::codeOf( $path );
        $this->assertDoesNotMatchRegularExpression( '/\bjQuery\b/', $code, "$path names jQuery" );
        $this->assertDoesNotMatchRegularExpression( '/(?<![\w.$])\$\s*[(.]/', $code, "$path calls \$() or \$.something" );
        $this->assertDoesNotMatchRegularExpression( '/\$\.(?:ajax|get|post|getJSON|ez|each|extend|inArray|makeArray|contains)\b/', $code, "$path calls a jQuery function" );
        if ( !in_array( $path, self::PLUGIN_SCRIPTS, true ) )
            $this->assertDoesNotMatchRegularExpression( '/\b(?:window|Exp)\.\$/', $code, "$path reaches jQuery through window.\$ or Exp.\$" );
        $this->assertStringContainsString( $bound, $code, "$path no longer binds $bound" );
    }

    /** CSP-10's rule for the scripts themselves: no eval, no string timers, no handlers written as markup */
    #[PHPUnit\Framework\Attributes\DataProvider( 'otherScripts' )]
    public function testTheScriptNeedsNoUnsafeEvalOrInlineHandlers( $path, $bound )
    {
        $code = self::codeOf( $path );
        $this->assertDoesNotMatchRegularExpression( '/\beval\s*\(|\bnew\s+Function\s*\(|\bset(?:Timeout|Interval)\s*\(\s*[\'"]/', $code, "$path evaluates a string" );
        $this->assertDoesNotMatchRegularExpression( '/[\'"<\s]on(?:click|load|error|change|submit|keydown|mouse\w+)\s*=/i', $code, "$path writes an inline handler" );
    }

    public static function a4DomUsers()
    {
        $cases = array();
        foreach ( self::A4DOM_USERS as $path )
            $cases[$path] = array( $path );
        return $cases;
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'a4DomUsers' )]
    public function testAScriptOnA4DomChecksForIt( $path )
    {
        $code = self::codeOf( $path );
        $this->assertStringContainsString( 'window.a4Dom', $code );
        $this->assertStringContainsString( "if ( !window.a4Dom )", $code, "$path does not check that a4_dom.js is on the page" );
    }

    /** a4_dom.js loads in the head's script list right after a4_url.js, in both branches, before any page script */
    public function testTheHeadLoadsA4DomBeforeThePageScripts()
    {
        $tpl = AdminFiles::read( self::root() . '/design/admin4/templates/page_head_script.tpl' );
        $code = preg_replace( '#\{\*.*?\*\}#s', '', $tpl );
        $this->assertSame( 2, substr_count( $code, "'ezjsc::jqueryio', 'a4_url.js', 'a4_dom.js'" ), 'both lists of page_head_script.tpl load a4_dom.js after a4_url.js' );
        $dom = self::codeOf( 'design/admin4/javascript/a4_dom.js' );
        foreach ( array( 'ready', 'each', 'on', 'formToken', 'headers', 'post', 'getJSON' ) as $name )
            $this->assertMatchesRegularExpression( '/\b' . $name . ':\s*' . $name . '\b/', $dom, "a4Dom.$name" );
        // the form token, the CSRF header and X-Requested-With go with every POST
        $this->assertStringContainsString( "'X-Requested-With': 'XMLHttpRequest'", $dom );
        $this->assertStringContainsString( "out['X-CSRF-Token']", $dom );
        $this->assertStringContainsString( "data.append( 'ezxform_token', token );", $dom );
        $this->assertStringContainsString( "credentials: 'same-origin'", $dom );
    }

    /** The templates of the converted scripts no longer ask for jQuery for them */
    public function testTheTemplatesOfTheConvertedScriptsRequireNoJQuery()
    {
        foreach ( array( 'design/admin4/templates/content/queued.tpl', 'design/admin4/templates/content/datatype/edit/ezobjectrelation_ajax_search.tpl' ) as $path )
        {
            $code = preg_replace( '#\{\*.*?\*\}#s', '', AdminFiles::read( self::root() . '/' . $path ) );
            $this->assertStringNotContainsString( 'ezjsc::jquery', $code, $path );
            $this->assertStringContainsString( "'exp::core::shared', 'exp::io'", $code, "$path loads what its script runs on" );
        }
        // the Online Editor: jQuery only for the built-in TinyMCE 3 (an engine without a template of its own)
        $ezoe = preg_replace( '#\{\*.*?\*\}#s', '', AdminFiles::read( self::root() . '/extension/ezoe/design/standard/templates/content/datatype/edit/ezxmltext_ezoe.tpl' ) );
        $this->assertMatchesRegularExpression( "#\{if \\\$input_handler\.engine\.template\|not\}\s*\{ezscript_require\( 'ezjsc::jquery' \)\}\s*\{/if\}#", $ezoe );
    }

    /** Text from an answer goes in as text: the publishing queue's error, the relation search's box id as a selector */
    public function testAnswersAreNoMarkupOrSelectors()
    {
        $this->assertStringContainsString( 'failed(function (element) { element.textContent = text; });',
                                           self::codeOf( 'design/standard/javascript/ezasynchronouspublishing.js' ) );
        foreach ( array( 'design/admin', 'design/admin4' ) as $design )
            $this->assertStringContainsString( 'window.CSS.escape( callbackID )', self::codeOf( "$design/javascript/ezajaxrelations_jquery.js" ), $design );
    }

    /** The dashboard's community posts come from another site: built as elements and text, links http(s) only */
    public function testTheCommunityBlockWritesThePostsAsText()
    {
        $code = self::codeOf( 'design/standard/javascript/dashboard_community_activity.js' );
        $this->assertStringNotContainsString( 'innerHTML', $code );
        $this->assertStringNotContainsString( 'insertAdjacentHTML', $code );
        $this->assertStringContainsString( "a.textContent = title;", $code );
        $this->assertStringContainsString( "url.protocol !== 'http:' && url.protocol !== 'https:'", $code );
        // a failure says so instead of loading for ever
        $this->assertStringContainsString( 'xhr.onerror = failed;', $code );
        $this->assertStringContainsString( 'data-failed-text=', AdminFiles::read( self::root() . '/design/standard/templates/dashboard/community_activity.tpl' ) );
    }

    public static function scripts()
    {
        $cases = array();
        foreach ( self::SCRIPTS as $name => $bound )
            $cases[$name] = array( $name, $bound );
        return $cases;
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'scripts' )]
    public function testThePageScriptUsesNoJQuery( $name, $bound )
    {
        $code = self::code( $name );
        $this->assertDoesNotMatchRegularExpression( '/\bjQuery\b/', $code, "$name names jQuery" );
        $this->assertDoesNotMatchRegularExpression( '/(?<![\w.$])\$\s*[(.]/', $code, "$name calls \$() or \$.something" );
        $this->assertDoesNotMatchRegularExpression( '/\b(?:window|Exp)\.\$/', $code, "$name reaches jQuery through window.\$ or Exp.\$" );
        $this->assertStringContainsString( $bound, $code, "$name no longer binds $bound" );
    }

    public function testTheClassicAdministrationLoadsTheSameRegistrationScript()
    {
        $this->assertFileEquals( self::root() . '/design/admin4/javascript/page/user_register.js',
                                 self::root() . '/design/admin/javascript/page/user_register.js' );
    }
}
