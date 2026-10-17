<?php
/**
 * admin4 has its own templates for the copy step of content/copy and content/copysubtree, the asynchronous publishing
 * page (content/queued) and the locked class (class/edit while a cronjob syncs its objects), which used to be the
 * unstyled ones of design/standard. Checked on the files alone (no database):
 *
 * - each template is in design/admin4 and posts exactly the form names the view and its module.php read, to the
 *   view's own URL;
 * - every variable a template reads is one its view sets (or one the template defines itself);
 * - the publishing page keeps the ids and classes ezasynchronouspublishing.js and content_queued.js look for;
 * - no inline script, no event handler attribute, no javascript: URL, no style="" and no <style> (the administration
 *   runs under a Content Security Policy without 'unsafe-inline'), and every a4- class is one admin4's stylesheets
 *   draw, so the pages follow the theme, dark mode included;
 * - admin4's image edit template, which hides the copy the image editor extension (ezie) ships in design/standard,
 *   includes content/datatype/edit/ezimage_editor.tpl beside a stored image; that draws the editor's scripts and
 *   Edit button only when ezmodule( 'ezie/prepare' ) allows it.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminCopyQueuedLockedTemplatesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminCopyQueuedLockedTemplatesTest extends PHPUnit\Framework\TestCase
{
    /** template => [ view source, the variables the template defines itself ] */
    private const TEMPLATES = array(
        'content/copy.tpl' => array( 'kernel/private/classes/views/content/copy.php', array( 'copy_version_count' ) ),
        'content/copy_subtree.tpl' => array( 'kernel/private/classes/views/content/copysubtree.php', array() ),
        'content/queued.tpl' => array( 'kernel/private/classes/views/content/queued.php', array() ),
        'class/edit_locked.tpl' => array( 'kernel/private/classes/views/class/edit.php', array() ),
    );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        $file = self::root() . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    /** The template without its comments {* ... *} */
    private static function template( $name )
    {
        return preg_replace( '/\{\*.*?\*\}/s', '', self::read( 'design/admin4/templates/' . $name ) );
    }

    /** The source with every run of white space as one space */
    private static function flat( $source )
    {
        return preg_replace( '/\s+/', ' ', $source );
    }

    /** name => list of value attributes of the form controls (input, button, select, textarea) */
    private static function controls( $source )
    {
        $controls = array();
        preg_match_all( '/<(?:input|button|select|textarea)\b[^>]*>/i', $source, $tags );
        foreach ( $tags[0] as $tag )
        {
            if ( !preg_match( '/\bname="([^"]+)"/', $tag, $name ) )
                continue;
            $value = preg_match( '/\bvalue="([^"]*)"/', $tag, $v ) ? $v[1] : null;
            $controls[$name[1]][] = $value;
        }
        return $controls;
    }

    /** The view's entry of kernel/<module>/module.php: its array literal, read without running the file (it would
        open the database) */
    private static function viewDefinition( $module, $view )
    {
        $code = self::read( 'kernel/' . $module . '/module.php' );
        $start = strpos( $code, "\$ViewList['$view'] = array(" );
        self::assertNotFalse( $start, "kernel/$module/module.php defines $view" );
        $end = strpos( $code, ");\n", $start );
        $literal = substr( $code, $start + strlen( "\$ViewList['$view'] = " ), $end + 1 - $start - strlen( "\$ViewList['$view'] = " ) );
        self::assertMatchesRegularExpression( '/^array\((?:[\s,()]|array|\'[^\']*\'|=>)*\)$/', $literal, 'a literal of strings and arrays' );
        return eval( 'return ' . $literal . ';' );
    }

    public function testTheTemplatesAreAdmin4sOwn()
    {
        foreach ( array_keys( self::TEMPLATES ) as $name )
        {
            $this->assertFileExists( self::root() . '/design/admin4/templates/' . $name, "admin4 has its own $name" );
            // the view fetches exactly this template
            $this->assertStringContainsString( "'design:$name'", self::read( self::TEMPLATES[$name][0] ) );
        }
    }

    public function testTheCopyFormPostsWhatTheViewReads()
    {
        $source = self::template( 'content/copy.tpl' );
        $view = self::viewDefinition( 'content', 'copy' );
        $controls = self::controls( $source );

        $this->assertMatchesRegularExpression( '#<form action=\{concat\( \'/content/copy/\', \$object\.id, \'/\' \)\|ezurl\} method="post"#', $source );
        foreach ( array_keys( $view['single_post_actions'] ) as $button )
            $this->assertArrayHasKey( $button, $controls, "content/copy reads $button" );
        $this->assertSame( array( 'VersionChoice' ), array_keys( $view['post_action_parameters']['Copy'] ) );
        $this->assertSame( array( '1', '2' ), $controls['VersionChoice'], 'all versions (1) or the current one (2)' );
        $this->assertMatchesRegularExpression( '/name="VersionChoice" value="2" checked="checked"/', $source, 'the current version is preselected' );
        $this->assertSame( array( '{$selected_node_id|wash}' ), $controls['SelectedNodeID'] );
        $this->assertStringContainsString( "postVariable( 'SelectedNodeID' )", self::read( self::TEMPLATES['content/copy.tpl'][0] ) );
        $this->assertEqualsCanonicalizing( array( 'CopyButton', 'CancelButton', 'VersionChoice', 'SelectedNodeID' ), array_keys( $controls ) );
    }

    public function testTheCopySubtreeFormPostsWhatTheViewReads()
    {
        $source = self::template( 'content/copy_subtree.tpl' );
        $view = self::viewDefinition( 'content', 'copysubtree' );
        $controls = self::controls( $source );

        $this->assertMatchesRegularExpression( '#<form action=\{concat\( \'/content/copysubtree/\', \$node\.node_id, \'/\' \)\|ezurl\} method="post"#', $source );
        foreach ( array_keys( $view['single_post_actions'] ) as $button )
            $this->assertArrayHasKey( $button, $controls, "content/copysubtree reads $button" );
        foreach ( array_keys( $view['post_action_parameters']['Copy'] ) as $choice )
        {
            $this->assertSame( array( '1', '2' ), $controls[$choice], "$choice: keep (1) or new (2)" );
            $this->assertMatchesRegularExpression( '/name="' . $choice . '" value="2" checked="checked"/', $source );
        }
        // each choice only when content.ini leaves it to the user
        $this->assertMatchesRegularExpression( '/\{if \$choose_versions\}\s*<div class="a4-field">\s*<ul class="a4-radios">\s*<li><label><input type="radio" name="VersionChoice"/', $source );
        $this->assertMatchesRegularExpression( '/\{if \$choose_creator\}\s*<div class="a4-field">\s*<ul class="a4-radios">\s*<li><label><input type="radio" name="CreatorChoice"/', $source );
        $this->assertMatchesRegularExpression( '/\{if \$choose_time\}\s*<div class="a4-field">\s*<ul class="a4-radios">\s*<li><label><input type="radio" name="TimeChoice"/', $source );
        // the run now or in the background choice of the browse page goes on with the options
        $this->assertStringContainsString( '{if and( is_set( $content_job_mode ), $content_job_mode )}<input type="hidden" name="ContentJobMode" value="{$content_job_mode|wash}" />{/if}', $source );
        $this->assertSame( array( '{$selected_node_id|wash}' ), $controls['SelectedNodeID'] );
        $this->assertEqualsCanonicalizing(
            array( 'CopyButton', 'CancelButton', 'VersionChoice', 'CreatorChoice', 'TimeChoice', 'ContentJobMode', 'SelectedNodeID' ),
            array_keys( $controls ) );
    }

    public function testTheLockedClassFormPostsWhatTheViewReads()
    {
        $source = self::template( 'class/edit_locked.tpl' );
        $controls = self::controls( $source );
        $view = self::read( self::TEMPLATES['class/edit_locked.tpl'][0] );

        $this->assertMatchesRegularExpression( '#<form action=\{concat\( \'class/edit/\', \$class\.id \)\|ezurl\} method="post" name="ClassEdit"#', $source );
        $this->assertSame( array( 'RetryButton', 'CancelConflictButton' ), array_keys( $controls ) );
        $this->assertStringContainsString( "hasPostVariable( 'CancelConflictButton' )", $view );
        // the way out stays: the script monitor and the command that forces the change
        $this->assertStringContainsString( "'scriptmonitor/list'|ezurl", $source );
        // the source text and most translations close the link after %urlend themselves: closed once, not twice
        $this->assertStringContainsString( "'%urlend', '</a>' ) )|explode( '</a></a>' )|implode( '</a>' )}", $source );
        $this->assertStringContainsString( 'syncobjectattributes.php -s {$access_type.name|wash} --classid={$class.id|int}', $source );
    }

    public function testThePublishingPageKeepsWhatItsScriptsLookFor()
    {
        $source = self::template( 'content/queued.tpl' );
        $script = self::read( 'design/standard/javascript/ezasynchronouspublishing.js' );
        $config = self::read( 'design/standard/javascript/content_queued.js' );

        foreach ( array( 'ezap-error', 'ezap-message-publishing', 'ezap-message-finished', 'ezap-message-deferred', 'ezap-contentview-uri', 'ezap-versionview-uri' ) as $id )
        {
            $this->assertStringContainsString( "#$id", $script );
            $this->assertMatchesRegularExpression( '/\bid="' . $id . '"/', $source, "#$id is on the page" );
        }
        foreach ( array( 'ezap-message-publishing', 'ezap-message-finished', 'ezap-message-deferred' ) as $id )
            $this->assertMatchesRegularExpression( '/<div class="ezap-placeholder[^"]*" id="' . $id . '"/', $source, "#$id is a placeholder" );
        // shown by the script only
        foreach ( array( 'ezap-error', 'ezap-message-finished', 'ezap-message-deferred' ) as $id )
            $this->assertMatchesRegularExpression( '/<div (?:id="' . $id . '" class="[^"]*\bezap-hidden\b|class="[^"]*\bezap-hidden\b[^"]*" id="' . $id . '")/', $source );
        $this->assertDoesNotMatchRegularExpression( '/class="[^"]*ezap-hidden[^"]*" id="ezap-message-publishing"/', $source );

        $this->assertStringContainsString( '<div id="ezap-config" hidden="hidden"', $source );
        preg_match_all( "/getAttribute\\( '(data-[a-z-]+)' \\)/", $config, $read );
        $this->assertNotEmpty( $read[1] );
        foreach ( $read[1] as $attribute )
            $this->assertStringContainsString( $attribute . '="', $source, "content_queued.js reads $attribute" );
        $this->assertStringContainsString( "ezscript_require( array( 'ezjsc::jquery', 'exp::core::shared', 'exp::io', 'ezasynchronouspublishing.js' ) )", $source );
        $this->assertStringContainsString( "<script type=\"text/javascript\" src={'javascript/content_queued.js'|ezdesign}></script>", $source );
        $this->assertStringContainsString( "{'stylesheets/content_queued.css'|ezdesign}", $source );
        $this->assertStringContainsString( '.ezap-hidden', self::read( 'design/standard/stylesheets/content_queued.css' ) );
    }

    public function testTheTemplatesReadOnlyWhatTheirViewsSet()
    {
        foreach ( self::TEMPLATES as $name => $spec )
        {
            list( $viewFile, $own ) = $spec;
            preg_match_all( "/setVariable\\(\\s*[\"']([A-Za-z_]+)[\"']/", self::read( $viewFile ), $set );
            $known = array_merge( $set[1], $own );
            // template variables: $name, not inside a quoted i18n string such as '%1'
            preg_match_all( '/\$([A-Za-z_][A-Za-z0-9_]*)/', self::template( $name ), $used );
            foreach ( array_unique( $used[1] ) as $variable )
                $this->assertContains( $variable, $known, "$name reads \$$variable, which its view does not set" );
        }
    }

    /** @return string[] the four templates and admin4's image edit template */
    private static function checkedTemplates()
    {
        return array_merge( array_keys( self::TEMPLATES ), array( 'content/datatype/edit/ezimage.tpl', 'content/datatype/edit/ezimage_editor.tpl' ) );
    }

    public function testTheTemplatesCarryNoInlineCode()
    {
        foreach ( self::checkedTemplates() as $name )
        {
            $source = self::template( $name );
            $this->assertDoesNotMatchRegularExpression( '/<script\b(?![^>]*\bsrc=)[^>]*>/i', $source, "$name: no inline <script>" );
            $this->assertDoesNotMatchRegularExpression( '/<[a-z][^>]*\son[a-z]+\s*=/i', $source, "$name: no event handler attribute" );
            $this->assertDoesNotMatchRegularExpression( '/javascript:/i', $source, "$name: no javascript: URL" );
            $this->assertDoesNotMatchRegularExpression( '/\sstyle\s*=/i', $source, "$name: no style attribute" );
            $this->assertDoesNotMatchRegularExpression( '/<style\b/i', $source, "$name: no <style>" );
        }
    }

    public function testEveryA4ClassIsDrawnByAdmin4()
    {
        $css = '';
        foreach ( glob( self::root() . '/design/admin4/stylesheets/{,*/}*.css', GLOB_BRACE ) as $file )
            $css .= file_get_contents( $file );
        $this->assertNotSame( '', $css );
        foreach ( array_keys( self::TEMPLATES ) as $name )
        {
            preg_match_all( '/\bclass="([^"]*)"/', self::template( $name ), $classes );
            foreach ( $classes[1] as $list )
                foreach ( preg_split( '/\s+/', trim( $list ) ) as $class )
                    if ( strpos( $class, 'a4-' ) === 0 )
                        $this->assertMatchesRegularExpression( '/\.' . preg_quote( $class, '/' ) . '(?![\w-])/', $css, "$name: .$class is drawn by admin4" );
        }
    }

    public function testTheImageEditorIsOfferedWhenItsModuleIsThere()
    {
        // admin4's image edit template includes the editor beside a stored image, and nothing else of it
        $image = self::template( 'content/datatype/edit/ezimage.tpl' );
        $this->assertStringContainsString( '{if $attribute_content.original.is_valid} {include uri=\'design:content/datatype/edit/ezimage_editor.tpl\'} <input class="button" type="submit" name="CustomActionButton[{$attribute.id}_delete_image]"',
            self::flat( $image ) );
        $this->assertStringNotContainsString( 'ezie', $image );

        $editor = trim( self::template( 'content/datatype/edit/ezimage_editor.tpl' ) );
        // all of it behind the module check: without ezie, or without access to it, the template draws nothing
        $this->assertStringStartsWith( "{if ezmodule( 'ezie/prepare' )}", $editor );
        $this->assertStringEndsWith( '{/if}', $editor );
        $this->assertSame( 1, substr_count( $editor, '{if ezmodule(' ) );
        // its scripts once per page, not again when its save view renders the template
        $this->assertStringContainsString( '{run-once} {if is_set( $ezie_ajax_response )|not()} {include uri=\'design:ezie/gui.tpl\' attribute=$attribute} {/if} {/run-once}',
            self::flat( $editor ) );
        // the Edit button the editor binds to, with the URL of its prepare view; a type="button" posts nothing
        $controls = self::controls( $editor );
        $this->assertSame( array( "ezieEdit[{array( 'ezie/prepare', \$attribute.contentobject_id, \$attribute.language_code, \$attribute.id, \$attribute.version )|implode( '/' )|ezurl( no )}]" ), array_keys( $controls ) );
        $this->assertMatchesRegularExpression( '/<input class="button ezieEdit ezieEditButton" type="button" name="ezieEdit\[/', $editor );
        $this->assertStringContainsString( 'id="ezieEdit_{$attribute.id}_{$attribute.version}_{$attribute.contentobject_id}"', $editor );
    }
}
