<?php
/**
 * The code of kernel/visual/menuconfig.php, moved into a class (#207 stage 1). The file kernel/visual/menuconfig.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/visual/menuconfig.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Visual
{

/**
 * Setup > Menus (classic): the menu.ini [SelectedMenu] settings of a siteaccess, which only the page layouts
 * of the classic designs read. Which siteaccesses read them is found by \expClassicMenuSiteAccessInspector, the
 * settings are read and written by \expClassicMenuSettings; this view joins the two and the request.
 *
 * Kept from before: the POST names CurrentSiteAccess, SelectCurrentSiteAccessButton, MenuType and StoreButton, the
 * session variable eZTemplateAdminCurrentSiteAccess (shared with the template editor) and the template variables
 * available_menu_array, current_menu, siteaccess_list and current_siteaccess. New: the unordered parameter
 * (siteaccess)/<name>, which opens the page for one siteaccess without changing the session.
 *
 * Guide: doc/guides/classic-menu-settings.md
 */
class Menuconfig extends \Exponential\Runnable\ModuleView
{
    /** The session variable that remembers the chosen siteaccess (the template editor uses it too) */
    const SESSION_SITEACCESS = 'eZTemplateAdminCurrentSiteAccess';

    /** The session variable that carries the result of a save to the page after the redirect */
    const SESSION_FEEDBACK = 'eZVisualMenuconfigFeedback';

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $module = $Params['Module'];
        $ini = \eZINI::instance();
        $tpl = \eZTemplate::factory();

        $siteAccessList = array_values( array_filter( (array)$ini->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' ),
                                                      array( '\expClassicMenuSettings', 'isSiteAccessName' ) ) );
        $inspected = \expClassicMenuSiteAccessInspector::inspectAll( $siteAccessList );

        // The siteaccess shown: the URL's (siteaccess)/<name>, a posted choice, the session, else the first one where
        // the settings take effect, else the first of the list
        $siteAccess = false;
        $fromUrl = isset( $Params['SiteAccess'] ) ? self::knownSiteAccess( $Params['SiteAccess'], $siteAccessList ) : false;
        if ( $module->isCurrentAction( 'SelectCurrentSiteAccess' ) && $http->hasPostVariable( 'CurrentSiteAccess' ) )
        {
            $posted = self::knownSiteAccess( $http->postVariable( 'CurrentSiteAccess' ), $siteAccessList );
            if ( $posted !== false )
            {
                $http->setSessionVariable( self::SESSION_SITEACCESS, $posted );
                $siteAccess = $posted;
            }
        }
        if ( $siteAccess === false && $module->isCurrentAction( 'Store' ) && $http->hasPostVariable( 'CurrentSiteAccess' ) )
            $siteAccess = self::knownSiteAccess( $http->postVariable( 'CurrentSiteAccess' ), $siteAccessList );
        if ( $siteAccess === false && $fromUrl !== false )
            $siteAccess = $fromUrl;
        if ( $siteAccess === false && $http->hasSessionVariable( self::SESSION_SITEACCESS ) )
            $siteAccess = self::knownSiteAccess( $http->sessionVariable( self::SESSION_SITEACCESS ), $siteAccessList );
        if ( $siteAccess === false )
            $siteAccess = self::defaultSiteAccess( $inspected, $siteAccessList );

        $menuINI = \eZSiteAccess::getIni( $siteAccess !== false ? $siteAccess : '', 'menu.ini' );
        $availableMenuArray = (array)$menuINI->variable( 'MenuSettings', 'AvailableMenuArray' );
        $groups = array();
        foreach ( $availableMenuArray as $menuType )
        {
            if ( is_string( $menuType ) && $menuType !== '' && $menuINI->hasGroup( $menuType ) )
                $groups[$menuType] = $menuINI->group( $menuType );
        }
        $choices = \expClassicMenuSettings::choices( $availableMenuArray, $groups );

        if ( $module->isCurrentAction( 'Store' ) && $siteAccess !== false )
        {
            $feedback = self::store( $http, $siteAccess, $siteAccessList, $choices );
            $http->setSessionVariable( self::SESSION_FEEDBACK, $feedback );
            return $this->viewResult( null, $module->redirectTo( '/visual/menuconfig/(siteaccess)/' . $siteAccess ) );
        }

        $feedback = false;
        if ( $http->hasSessionVariable( self::SESSION_FEEDBACK ) )
        {
            $stored = $http->sessionVariable( self::SESSION_FEEDBACK );
            $http->removeSessionVariable( self::SESSION_FEEDBACK );
            if ( is_array( $stored ) && isset( $stored['siteaccess'] ) && $stored['siteaccess'] === $siteAccess )
                $feedback = $stored;
        }

        $current = $siteAccess !== false ? \expClassicMenuSettings::read( $siteAccess ) : false;
        $directory = $siteAccess !== false ? \expClassicMenuSettings::siteAccessDirectory( $siteAccess, $siteAccessList ) : false;
        $targetFile = $directory !== false ? $directory . '/' . \expClassicMenuSettings::FILE_NAME : '';
        $currentChoice = $current ? \expClassicMenuSettings::findChoice( $current['current_menu'], $choices ) : false;

        $planned = array();
        foreach ( $choices as $choice )
            $planned[$choice['type']] = \expClassicMenuSettings::plannedLines( \expClassicMenuSettings::selection( $choice['type'], $choices ) );

        $siteAccessInfo = array();
        $relevantCount = 0;
        foreach ( $inspected as $name => $info )
        {
            $info['siteaccess'] = (string)$name;
            $info['selected'] = ( (string)$name === $siteAccess );
            $read = ( (string)$name === $siteAccess && $current ) ? $current : \expClassicMenuSettings::read( (string)$name );
            $choice = \expClassicMenuSettings::findChoice( $read['current_menu'], $choices );
            $info['current_menu'] = $read['current_menu'];
            $info['current_title'] = $choice ? $choice['title'] : $read['current_menu'];
            $info['overridden'] = $read['overridden'];
            $siteAccessInfo[] = $info;
            if ( $info['relevant'] )
                $relevantCount++;
        }

        $layoutsModule = \eZModule::exists( 'explayouts_ui' );
        $user = \eZUser::currentUser();
        $layoutsAccess = $layoutsModule && $user->hasAccessTo( 'explayouts_ui', 'read' )['accessWord'] !== 'no';

        // kept for templates written for the page before 6.0.15
        $tpl->setVariable( 'available_menu_array', array_map( function ( $choice ) use ( $groups ) {
            return array( 'type' => $choice['type'], 'settings' => $groups[$choice['type']] );
        }, $choices ) );
        $tpl->setVariable( 'current_menu', $current ? $current['current_menu'] : '' );
        $tpl->setVariable( 'siteaccess_list', $siteAccessList );
        $tpl->setVariable( 'current_siteaccess', $siteAccess );

        $tpl->setVariable( 'menu_choices', $choices );
        $tpl->setVariable( 'menu_current', $current );
        $tpl->setVariable( 'menu_current_choice', $currentChoice );
        $tpl->setVariable( 'menu_planned_lines', $planned );
        $tpl->setVariable( 'menu_planned_text', array_map( function ( $lines ) { return implode( "\n", $lines ); }, $planned ) );
        $tpl->setVariable( 'menu_target_file', $targetFile );
        $tpl->setVariable( 'menu_target_exists', $targetFile !== '' && file_exists( $targetFile ) );
        $tpl->setVariable( 'menu_target_writable', $directory !== false && ( file_exists( $targetFile ) ? is_writable( $targetFile ) : is_writable( $directory ) ) );
        $tpl->setVariable( 'menu_siteaccesses', $siteAccessInfo );
        $tpl->setVariable( 'menu_selected_info', $siteAccess !== false && isset( $inspected[$siteAccess] ) ? $inspected[$siteAccess] : false );
        $tpl->setVariable( 'menu_relevant_count', $relevantCount );
        $tpl->setVariable( 'menu_feedback', $feedback );
        $tpl->setVariable( 'menu_layouts_url', $layoutsAccess ? 'explayouts_ui/dashboard' : false );
        $tpl->setVariable( 'menu_velocity', defined( 'QBIX_WEBSERVER' ) || defined( 'QBIX_SERVER_VERSION' ) || isset( $_SERVER['QBIX_WORKER'] ) || isset( $_SERVER['VELOCITY'] ) );

        // The examples: what each arrangement lists, from this installation's content, the template that draws it
        // in the shown siteaccess's designs, and the recipes with their exact lines
        $sources = array();
        $templateFiles = array();
        foreach ( $choices as $choice )
        {
            $sources[$choice['type']] = \expClassicMenuSettings::menuSources( $choice );
            foreach ( array( $choice['top'], $choice['left'] ) as $template )
            {
                if ( $template !== '' && !isset( $templateFiles[$template] ) && $siteAccess !== false )
                    $templateFiles[$template] = \expClassicMenuSiteAccessInspector::templateFile( $siteAccess, 'menu/' . $template . '.tpl' );
            }
        }
        // the two siteaccesses of the per-siteaccess recipe: two that read these settings, else example names
        $layoutsSiteAccesses = array();
        $pair = array();
        foreach ( $siteAccessInfo as $info )
        {
            if ( $info['status'] === 'layouts' || $info['status'] === 'mixed' )
                $layoutsSiteAccesses[] = $info['siteaccess'];
            if ( $info['relevant'] && count( $pair ) < 2 )
                $pair[] = $info['siteaccess'];
        }
        if ( count( $pair ) < 2 )
            $pair = array( 'mysite_en', 'mysite_de' );
        $tpl->setVariable( 'menu_sources', $sources );
        $tpl->setVariable( 'menu_template_files', $templateFiles );
        $tpl->setVariable( 'menu_preview', $siteAccess !== false && $current ? self::previewItems( $siteAccess, $current ) : false );
        // the lines as text: a template loop cannot put a line break between them
        $recipes = $siteAccess !== false ? \expClassicMenuSettings::recipes( $choices, $siteAccess, $pair[0], $pair[1] ) : array();
        foreach ( $recipes as $id => $recipe )
            $recipes[$id]['files'] = array_map( function ( $lines ) { return implode( "\n", $lines ); }, $recipe['files'] );
        $tpl->setVariable( 'menu_recipes', $recipes );
        $tpl->setVariable( 'menu_recipe_pair', $pair );
        $tpl->setVariable( 'menu_layouts_siteaccesses', $layoutsSiteAccesses );
        $tpl->setVariable( 'menu_design_survey', \expClassicMenuSiteAccessInspector::designSurvey() );
        $tpl->setVariable( 'menu_template_examples', \expClassicMenuSettings::templateExamples() );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:visual/menuconfig.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'design/standard/menuconfig', 'Menus (classic)' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * The name, when it is one of the offered siteaccesses, else false.
     *
     * @param mixed $name
     * @param array $siteAccessList
     * @return string|false
     */
    public static function knownSiteAccess( $name, array $siteAccessList )
    {
        return \expClassicMenuSettings::isSiteAccessName( $name ) && in_array( $name, $siteAccessList, true ) ? $name : false;
    }

    /**
     * The siteaccess the page opens with when none was chosen: the first one whose pages read the settings, else the
     * first of the list.
     *
     * @param array $inspected siteaccess => inspector result, in the inspector's order
     * @param array $siteAccessList
     * @return string|false
     */
    public static function defaultSiteAccess( array $inspected, array $siteAccessList )
    {
        foreach ( $inspected as $name => $info )
        {
            if ( !empty( $info['relevant'] ) && in_array( (string)$name, $siteAccessList, true ) )
                return (string)$name;
        }
        return $siteAccessList ? $siteAccessList[0] : false;
    }

    /**
     * What the classic menus would list in this installation, read the way the menu templates fetch it: the children
     * of the start page (content.ini [NodeSettings] RootNode of the siteaccess) of the top and left menu classes, and
     * for the first of them (the page a visitor is taken to be in) its children of the same classes. Hidden pages are
     * left out, as for visitors. Read with the viewer's rights, at most 8 per list; false when nothing can be read.
     *
     * @param string $siteAccess
     * @param array $current from \expClassicMenuSettings::read()
     * @return array|false root, top, left_root, section, second, left, deeper
     */
    protected static function previewItems( $siteAccess, array $current )
    {
        try
        {
            $rootID = (int)\eZSiteAccess::getIni( $siteAccess, 'content.ini' )->variable( 'NodeSettings', 'RootNode' );
            $root = $rootID > 0 ? \eZContentObjectTreeNode::fetch( $rootID ) : null;
            if ( !$root instanceof \eZContentObjectTreeNode )
                return false;
            $top = self::children( $root, $current['top_classes'] );
            $leftRoot = self::children( $root, $current['left_classes'] );
            // the page the visitor is taken to be in: the first top menu page with pages of its own
            $empty = array( 'items' => array(), 'more' => false );
            $candidates = $top['items'] ? $top['items'] : $leftRoot['items'];
            $sectionItem = $candidates ? $candidates[0] : false;
            $section = null;
            $second = $empty;
            $left = $empty;
            foreach ( $candidates as $candidate )
            {
                $node = \eZContentObjectTreeNode::fetch( $candidate['node_id'] );
                if ( !$node instanceof \eZContentObjectTreeNode )
                    continue;
                $candidateLeft = self::children( $node, $current['left_classes'] );
                $candidateSecond = self::children( $node, $current['top_classes'] );
                if ( $section === null || $candidateLeft['items'] || $candidateSecond['items'] )
                {
                    $section = $node;
                    $sectionItem = $candidate;
                    $left = $candidateLeft;
                    $second = $candidateSecond;
                }
                if ( $candidateLeft['items'] || $candidateSecond['items'] )
                    break;
            }
            $deeperOf = $left['items'] ? \eZContentObjectTreeNode::fetch( $left['items'][0]['node_id'] ) : null;
            return array(
                'root' => array( 'node_id' => $rootID, 'name' => (string)$root->attribute( 'name' ) ),
                'top' => $top,
                'left_root' => $leftRoot,
                'section' => $sectionItem,
                'second' => $second,
                'left' => $left,
                'deeper_of' => $left['items'] ? $left['items'][0] : false,
                'deeper' => $deeperOf ? self::children( $deeperOf, $current['left_classes'] ) : $empty,
            );
        }
        catch ( \Throwable $e )
        {
            \eZDebug::writeWarning( 'No menu preview: ' . $e->getMessage(), __METHOD__ );
            return false;
        }
    }

    /**
     * The visible children of a node of the given classes, sorted as the node sorts them, as a menu template fetches
     * them; at most $limit, and whether there are more.
     */
    protected static function children( \eZContentObjectTreeNode $parent, array $classes, $limit = 8 )
    {
        if ( !$classes )
            return array( 'items' => array(), 'more' => false );
        $nodes = \eZContentObjectTreeNode::subTreeByNodeID( array(
            'Depth' => 1, 'DepthOperator' => 'eq',
            'ClassFilterType' => 'include', 'ClassFilterArray' => array_values( $classes ),
            'SortBy' => $parent->sortArray(), 'Limit' => $limit + 1, 'IgnoreVisibility' => false,
            'LoadDataMap' => false,
        ), (int)$parent->attribute( 'node_id' ) );
        $items = array();
        foreach ( is_array( $nodes ) ? $nodes : array() as $node )
            $items[] = array( 'node_id' => (int)$node->attribute( 'node_id' ), 'name' => (string)$node->attribute( 'name' ),
                              'class' => (string)$node->attribute( 'class_identifier' ) );
        $more = count( $items ) > $limit;
        return array( 'items' => array_slice( $items, 0, $limit ), 'more' => $more );
    }

    /**
     * Saves the posted menu type for a siteaccess and says what happened, for the page after the redirect.
     *
     * @return array type (saved, unchanged, invalid, error), siteaccess, menu, file, error, cleared
     */
    protected static function store( \eZHTTPTool $http, $siteAccess, array $siteAccessList, array $choices )
    {
        $feedback = array( 'type' => 'error', 'siteaccess' => $siteAccess, 'menu' => '', 'file' => '', 'error' => '', 'cleared' => array() );
        $menuType = $http->hasPostVariable( 'MenuType' ) ? $http->postVariable( 'MenuType' ) : '';
        $selection = \expClassicMenuSettings::selection( $menuType, $choices );
        if ( $selection === false )
        {
            $feedback['type'] = 'invalid';
            return $feedback;
        }
        $choice = \expClassicMenuSettings::findChoice( $menuType, $choices );
        $feedback['menu'] = $choice['title'];

        $directory = \expClassicMenuSettings::siteAccessDirectory( $siteAccess, $siteAccessList );
        if ( $directory === false )
        {
            $feedback['error'] = 'no_directory';
            return $feedback;
        }

        $result = \expClassicMenuSettings::write( $siteAccess, $selection, $siteAccessList );
        $feedback['file'] = $result['file'] !== '' ? $result['file'] : $directory . '/' . \expClassicMenuSettings::FILE_NAME;
        if ( !$result['ok'] )
        {
            $feedback['error'] = $result['error'];
            \eZDebug::writeError( "Could not save the classic menu settings of $siteAccess: " . $result['error'], __METHOD__ );
            return $feedback;
        }
        if ( !$result['changed'] )
        {
            $feedback['type'] = 'unchanged';
            return $feedback;
        }
        $feedback['cleared'] = \expClassicMenuSettings::clearCaches( $directory );
        $feedback['type'] = 'saved';
        $feedback['created'] = $result['created'];
        $feedback['backup'] = $result['backup'];
        return $feedback;
    }
}

}
