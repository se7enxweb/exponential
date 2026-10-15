<?php
/**
 * The pages of the reverse relation check (doc/features/6.0/reverse-relation-check.md), drawn by the real template
 * engine in admin4, without a database.
 *
 *  RR-01 - the removal page lists the referring objects per selected item: link, class, kind, field, target
 *  RR-02 - with block the OK button is disabled and says why; with warn it stays; a refused POST says nothing was
 *          removed
 *  RR-03 - without the check (disabled) the removal page is the one before, with its hint
 *  RR-04 - objects the user may not read: counted per item ("2 references you cannot see"), never named; names
 *          are escaped; a Layouts block links to the layout editor
 *  RR-05 - the hide confirmation lists references and further locations and posts the confirmation back
 *  RR-06 - every design that has the pages includes the list (standard's removal page too); the list lives in
 *          standard (admin4 needs no other)
 *  RR-07 - the strings are translated into German and American English, and listed for translators
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\Service\ReverseRelationCheck;

class AdminReverseRelationCheckTest extends PHPUnit\Framework\TestCase
{
    const CONTEXT = 'design/admin/content/reverserelations';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    /**
     * A template of admin4 drawn with the given variables.
     */
    public static function render( $template, array $variables )
    {
        $root = self::root();
        $site = eZINI::instance( 'site.ini' );
        $savedDesigns = $site->hasVariable( 'DesignSettings', 'AdditionalSiteDesignList' ) ? array( $site->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) ) : null;
        $savedGlobals = array();
        foreach ( array( 'eZSiteBasics', 'eZOverrideTemplateCacheMap', 'eZTemplateDesignSetting', 'eZTemplateDesignResourceBases', 'eZCurrentAccess' ) as $name )
            $savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $handler = set_exception_handler( null );
        restore_exception_handler();
        $overrides = new ReflectionProperty( 'eZTemplateDesignResource', 'overrideArrayCache' );
        // the class icons of the page read the web directory, which a test process has not set
        $sys = eZSys::instance();
        $wwwDir = $sys->WWWDir;
        try
        {
            if ( $wwwDir === null )
                $sys->WWWDir = '';
            $site->setVariable( 'DesignSettings', 'AdditionalSiteDesignList', array() );
            $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'reverse_relation_test', 'type' => 1 );
            unset( $GLOBALS['eZOverrideTemplateCacheMap'], $GLOBALS['eZTemplateDesignResourceBases'] );
            $GLOBALS['eZTemplateDesignSetting'] = array( 'site' => 'admin4' );
            $overrides->setValue( null, null );
            $tpl = eZTemplate::factory();
            foreach ( $variables as $name => $value )
                $tpl->setVariable( $name, $value );
            $html = $tpl->fetch( "$root/$template" );
            PHPUnit\Framework\Assert::assertSame( array(), $tpl->errorLog() );
            return $html;
        }
        finally
        {
            eZTemplate::resetInstance();
            $sys->WWWDir = $wwwDir;
            if ( $savedDesigns === null )
                $site->removeSetting( 'DesignSettings', 'AdditionalSiteDesignList' );
            else
                $site->setVariable( 'DesignSettings', 'AdditionalSiteDesignList', $savedDesigns[0] );
            $overrides->setValue( null, null );
            foreach ( $savedGlobals as $name => $value )
            {
                if ( $value === null )
                    unset( $GLOBALS[$name] );
                else
                    $GLOBALS[$name] = $value[0];
            }
            for ( $i = 0; $i < 5; $i++ )
            {
                $current = set_exception_handler( null );
                restore_exception_handler();
                if ( $current === $handler )
                    break;
                restore_exception_handler();
            }
        }
    }

    /** The check of two selected folders as ReverseRelationCheck::templateData() gives it */
    public static function check( $mode, $refused = false )
    {
        return ReverseRelationCheck::templateData( $mode, self::raw(), $refused );
    }

    /** The check of two selected folders as ReverseRelationCheck::forNodes() gives it: 9 references, 4 listed, 3 from objects the user may not read */
    public static function raw()
    {
        $relation = function ( $fromID, $fromName, $nodeID, $class, array $kinds, $field, $toID, $toName, $selected )
        {
            return array( 'from_id' => $fromID, 'from_name' => $fromName, 'from_node_id' => $nodeID, 'from_url' => '', 'from_class' => $class,
                          'kinds' => $kinds, 'field' => $field, 'to_id' => $toID, 'to_name' => $toName, 'is_selected' => $selected );
        };
        $items = array(
            array( 'node_id' => 61, 'object_id' => 160, 'name' => 'Press photos', 'path_string' => '/1/2/61/', 'locations' => array(),
                   'unreadable' => 2, 'unreadable_locations' => 0,
                   'relations' => array(
                       $relation( 201, 'Annual report <2026>', 211, 'Article', array( 'embed' ), '', 161, 'Team photo', false ),
                       $relation( 202, 'Team page', 212, 'Folder', array( 'attribute' ), 'Header image', 160, 'Press photos', true ),
                       $relation( 205, 'Gallery', 215, 'Article', array( 'link', 'embed' ), '', 162, '', false ),
                       array_merge( $relation( 0, 'Front page: Teaser <list>', 0, 'Layouts block', array( 'layout' ), '', 160, 'Press photos', true ),
                                    array( 'from_url' => '/explayouts_ui_api/app#layout/19' ) ) ) ),
            array( 'node_id' => 62, 'object_id' => 170, 'name' => 'Logos', 'path_string' => '/1/2/62/', 'locations' => array(),
                   'unreadable' => 1, 'unreadable_locations' => 0,
                   'relations' => array( $relation( 204, 'Imprint', 214, 'Page', array( 'common' ), '', 170, 'Logos', true ) ) ) );
        return array( 'items' => $items, 'total' => 9, 'listed' => 4, 'unreadable' => 3, 'location_count' => 0, 'failed' => false );
    }

    /** The variables content/removeobject gives node/removeobject.tpl for two folders the user may remove */
    public static function removeVariables( $check )
    {
        $item = function ( $id, $name )
        {
            return array( 'node' => array( 'node_id' => $id, 'name' => $name, 'path' => array( array( 'name' => 'Home' ) ) ),
                          'object' => array( 'class_name' => 'Folder' ), 'class' => array( 'identifier' => 'folder', 'name' => 'Folder' ),
                          'child_count' => 2, 'can_remove' => true );
        };
        return array( 'module' => array( 'functions' => array( 'removeobject' => array( 'uri' => '/content/removeobject' ) ) ),
                      'remove_info' => array( 'has_pending_object' => false, 'can_remove_all' => true ),
                      'remove_list' => array( $item( 61, 'Press photos' ), $item( 62, 'Logos' ) ),
                      'total_child_count' => 4, 'reverse_related' => 3, 'exceeded_limit' => false, 'delete_items_exist' => true,
                      'move_to_trash_allowed' => true, 'move_to_trash' => true, 'run_as_job' => false, 'job_mode' => null,
                      'job_summary' => null, 'reverse_relation_check' => $check );
    }

    /** The variables content/hide gives content/job_confirm.tpl for a node with references and a further location */
    public static function hideVariables()
    {
        $check = self::raw();
        $check['items'] = array( $check['items'][1] );
        $check['items'][0]['unreadable'] = 0;
        $check['items'][0]['unreadable_locations'] = 2;
        $check['unreadable'] = 0;
        $check['items'][0]['locations'] = array( array( 'node_id' => 90, 'path' => 'Home / Partners / Logos', 'is_hidden' => false, 'is_invisible' => false ),
                                                 array( 'node_id' => 91, 'path' => 'Home / Archive / Logos', 'is_hidden' => false, 'is_invisible' => true ) );
        $check['total'] = 1;
        $check['listed'] = 1;
        $check['location_count'] = 4;
        $check = ReverseRelationCheck::templateData( 'warn', $check );
        return array( 'title' => 'Hide Logos', 'text' => 'The node and everything below it are hidden from the site.',
                      'action_url' => '/content/hide/62', 'back_url' => '/content/view/full/62',
                      'hidden' => array( 'RedirectURI' => '/content/view/full/2', 'ReverseRelationCheckConfirmed' => '1' ),
                      'operation' => 'hide', 'job_mode' => null, 'job_summary' => null, 'reverse_relation_check' => $check );
    }

    private static function confirmButton( $html )
    {
        PHPUnit\Framework\Assert::assertSame( 1, preg_match( '#<input[^>]*name="ConfirmButton"[^>]*/>#', $html, $m ), 'one OK button' );
        return $m[0];
    }

    /** RR-01 */
    public function testTheRemovalPageListsTheReferencesPerItem()
    {
        $html = self::render( 'design/admin4/templates/node/removeobject.tpl', self::removeVariables( self::check( 'warn' ) ) );
        $this->assertStringContainsString( 'class="message-warning reverse-relation-check" id="reverse-relation-check" role="status"', $html );
        $this->assertStringContainsString( '<h3 id="reverse-relation-item-61">Press photos</h3>', $html );
        $this->assertStringContainsString( '<h3 id="reverse-relation-item-62">Logos</h3>', $html );
        $this->assertStringContainsString( 'aria-labelledby="reverse-relation-item-61"', $html );
        $this->assertMatchesRegularExpression( '#<a href="[^"]*/content/view/full/212">Team page</a></td>\s*<td>Folder</td>\s*<td>Relation field \(Header image\)</td>\s*<td>This item</td>#', $html );
        $this->assertMatchesRegularExpression( '#<td>Embedded in rich text</td>\s*<td>Team photo</td>#', $html );
        $this->assertMatchesRegularExpression( '#<td>Object relation</td>\s*<td>This item</td>#', $html );
        $this->assertStringContainsString( '<p>Further references are not listed.</p>', $html );
        // the list replaces the old hint to look it up in the menu
        $this->assertStringNotContainsString( 'Reverse related for subtree', $html );
    }

    /** RR-02 */
    public function testBlockDisablesTheOKButton()
    {
        $warn = self::confirmButton( self::render( 'design/admin4/templates/node/removeobject.tpl', self::removeVariables( self::check( 'warn' ) ) ) );
        $this->assertStringNotContainsString( 'disabled', $warn );

        $html = self::render( 'design/admin4/templates/node/removeobject.tpl', self::removeVariables( self::check( 'block' ) ) );
        $button = self::confirmButton( $html );
        $this->assertStringContainsString( 'disabled="disabled"', $button );
        $this->assertStringContainsString( 'aria-describedby="reverse-relation-check"', $button );
        $this->assertStringContainsString( 'You cannot continue while other objects use the items', $button );
        $this->assertStringContainsString( 'class="message-error reverse-relation-check" id="reverse-relation-check" role="alert"', $html );
        $this->assertStringContainsString( 'The removal is blocked: other objects use the items', $html );
        $this->assertStringNotContainsString( 'Nothing was removed.', $html );
        $this->assertStringContainsString( 'name="CancelButton"', $html );

        $refused = self::render( 'design/admin4/templates/node/removeobject.tpl', self::removeVariables( self::check( 'block', true ) ) );
        $this->assertStringContainsString( '<strong>Nothing was removed.</strong>', $refused );
    }

    /** RR-03 */
    public function testWithoutTheCheckThePageIsTheOneBefore()
    {
        $html = self::render( 'design/admin4/templates/node/removeobject.tpl', self::removeVariables( null ) );
        $this->assertStringNotContainsString( 'reverse-relation-check', $html );
        $this->assertStringContainsString( 'Reverse related for subtree', $html );
        $this->assertStringNotContainsString( 'disabled', self::confirmButton( $html ) );
        // a view of an older release sets no variable at all
        $variables = self::removeVariables( null );
        unset( $variables['reverse_relation_check'] );
        $this->assertStringNotContainsString( 'disabled', self::confirmButton( self::render( 'design/admin4/templates/node/removeobject.tpl', $variables ) ) );
    }

    /** RR-04 */
    public function testObjectsTheUserMayNotReadAreOnlyCountedAndNamesAreEscaped()
    {
        $html = self::render( 'design/admin4/templates/node/removeobject.tpl', self::removeVariables( self::check( 'warn' ) ) );
        // counted per item, never named
        $this->assertStringContainsString( '<p class="reverse-relation-unreadable">2 references you cannot see</p>', $html );
        $this->assertStringContainsString( '<p class="reverse-relation-unreadable">1 reference you cannot see</p>', $html );
        $this->assertMatchesRegularExpression( '#<a href="[^"]*/explayouts_ui_api/app\#layout/19">Front page: Teaser &lt;list&gt;</a></td>\s*<td>Layouts block</td>\s*<td>Layouts block</td>\s*<td>This item</td>#', $html );
        $this->assertMatchesRegularExpression( '#<td>Linked in rich text, Embedded in rich text</td>\s*<td><em>An object you may not read</em></td>#', $html,
                                               'a target the user may not read is not named' );
        $this->assertStringContainsString( 'Annual report &lt;2026&gt;', $html );
        $this->assertStringNotContainsString( 'Annual report <2026>', $html );
        // with block the removal is refused although the user sees only some of the references
        $block = self::render( 'design/admin4/templates/node/removeobject.tpl', self::removeVariables( self::check( 'block' ) ) );
        $this->assertStringContainsString( 'disabled="disabled"', self::confirmButton( $block ) );
    }

    /** RR-05 */
    public function testTheHideConfirmationListsReferencesAndLocations()
    {
        $html = self::render( 'design/admin4/templates/content/job_confirm.tpl', self::hideVariables() );
        $this->assertStringContainsString( 'Check before hiding', $html );
        $this->assertStringContainsString( 'Other objects point at what you are about to hide.', $html );
        $this->assertStringContainsString( 'The object has further locations. Hiding this one leaves them as they are:', $html );
        $this->assertMatchesRegularExpression( '#<a href="[^"]*/content/view/full/90">Home / Partners / Logos</a></li>#', $html );
        $this->assertMatchesRegularExpression( '#/content/view/full/91">Home / Archive / Logos</a> \(hidden by a parent\)</li>#', $html );
        $this->assertStringContainsString( '<p class="reverse-relation-unreadable">2 further locations you cannot see</p>', $html );
        $this->assertStringContainsString( '<input type="hidden" name="ReverseRelationCheckConfirmed" value="1" />', $html );
        $this->assertStringContainsString( 'name="ConfirmJobButton"', $html );
        // an operation without the check draws the page as before
        $plain = self::hideVariables();
        $plain['reverse_relation_check'] = null;
        $this->assertStringNotContainsString( 'reverse-relation-check', self::render( 'design/admin4/templates/content/job_confirm.tpl', $plain ) );
        unset( $plain['reverse_relation_check'] );
        $this->assertStringNotContainsString( 'reverse-relation-check', self::render( 'design/admin4/templates/content/job_confirm.tpl', $plain ) );
    }

    /** RR-06 */
    public function testEveryDesignIncludesTheList()
    {
        $this->assertFileExists( self::root() . '/design/standard/templates/content/reverse_relation_check.tpl' );
        foreach ( array( 'admin', 'admin4', 'standard' ) as $design )
            $this->assertStringContainsString( "{include uri='design:content/reverse_relation_check.tpl' check=\$reverse_relation_check operation=\$operation}",
                                               self::read( "design/$design/templates/content/job_confirm.tpl" ), $design );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $template = self::read( "design/$design/templates/node/removeobject.tpl" );
            $this->assertStringContainsString( "{include uri='design:content/reverse_relation_check.tpl' check=\$rr_check operation='remove'}", $template, $design );
            $this->assertStringContainsString( '$rr_blocked|not )}', $template, $design );
        }
        $standard = self::read( 'design/standard/templates/node/removeobject.tpl' );
        $this->assertStringContainsString( "{include uri='design:content/reverse_relation_check.tpl' check=\$reverse_relation_check operation='remove'}", $standard );
        $this->assertStringContainsString( 'disabled=or( $exceeded_limit, $rr_blocked )}', $standard );
    }

    /** RR-07 */
    public function testTheStringsAreTranslated()
    {
        preg_match_all( "/'((?:[^'\\\\]|\\\\.)+)'\\|i18n\\( '" . preg_quote( self::CONTEXT, '/' ) . "'/", self::read( 'design/standard/templates/content/reverse_relation_check.tpl' )
                        . self::read( 'design/admin4/templates/node/removeobject.tpl' ), $m );
        preg_match_all( "/ezpI18n::tr\\( '" . preg_quote( self::CONTEXT, '/' ) . "', '([^']+)'/", self::read( 'kernel/private/classes/views/content/action.php' ), $php );
        $sources = array_unique( array_merge( $m[1], $php[1] ) );
        $this->assertGreaterThan( 20, count( $sources ) );
        foreach ( array( 'ger-DE', 'eng-US', 'untranslated' ) as $language )
        {
            $ts = self::read( "share/translations/$language/translation.ts" );
            $start = strpos( $ts, '<name>' . self::CONTEXT . '</name>' );
            $this->assertNotFalse( $start, $language );
            $context = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
            foreach ( $sources as $source )
            {
                $this->assertStringContainsString( '<source>' . htmlspecialchars( $source, ENT_NOQUOTES ) . '</source>', $context, "$language: $source" );
                if ( $language !== 'untranslated' )
                    $this->assertDoesNotMatchRegularExpression( '#<source>' . preg_quote( htmlspecialchars( $source, ENT_NOQUOTES ), '#' ) . '</source>\s*<translation type="unfinished">#', $context, "$language: $source" );
            }
        }
        $this->assertStringContainsString( "<source>Check before hiding</source>\n        <translation>Vor dem Verbergen prüfen</translation>",
                                           self::read( 'share/translations/ger-DE/translation.ts' ) );
        $this->assertStringContainsString( "<source>%count references you cannot see</source>\n        <translation>%count Verweise, die Sie nicht sehen dürfen</translation>",
                                           self::read( 'share/translations/ger-DE/translation.ts' ) );
    }
}
