<?php
/**
 * The datatypes of the installation (class/datatype), read only, the companion of class/grouplist:
 *
 *   /class/datatype/list[/(filter)/used|unused|orphans][/(group)/<kernel|extension|orphans>][/(refresh)/1]
 *   /class/datatype/view/<datatype string>[/(sort)/modified|published|name|class][/(order)/asc|desc][/(offset)/<n>][/(limit)/10|25|50|100][/(class)/<id>]
 *
 * The list shows every datatype content.ini registers, by provider (the kernel, each extension), with its class,
 * file, flags and how much the content model uses it, and the datatypes the database uses that nothing registers.
 * The detail page shows one datatype: its meta data, the settings columns it reads, its templates in the
 * administration and the default site, the classes and attributes that use it, its tables and its
 * help page, and every main node of the content using it, a page at a time (sorted, by class). The data is built by
 * expDataTypeInventory; the counts are kept five minutes.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\ContentClass
{

class Datatype extends \Exponential\Runnable\ModuleView
{
    /**
     * The view parameters of the list, checked.
     *
     * @param array $userParameters
     * @param array $groupKeys the groups there are
     * @return array hash( filter, group, refresh )
     */
    public static function listParameters( array $userParameters, array $groupKeys )
    {
        $filter = isset( $userParameters['filter'] ) ? (string)$userParameters['filter'] : 'all';
        $group = isset( $userParameters['group'] ) ? (string)$userParameters['group'] : '';
        return array( 'filter' => in_array( $filter, \expDataTypeInventory::FILTERS, true ) ? $filter : 'all',
                      'group' => in_array( $group, $groupKeys, true ) ? $group : '',
                      'refresh' => !empty( $userParameters['refresh'] ) );
    }

    /**
     * The URL of the list with a filter and a group.
     *
     * @param string $filter
     * @param string $group
     * @return string
     */
    public static function listURL( $filter, $group )
    {
        return 'class/datatype/list' . ( $filter !== 'all' && $filter !== '' ? '/(filter)/' . $filter : '' ) . ( $group !== '' ? '/(group)/' . $group : '' );
    }

    public function run( array $scope )
    {
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $action = isset( $Params['Action'] ) && $Params['Action'] !== '' ? (string)$Params['Action'] : 'list';
        $userParameters = isset( $Params['UserParameters'] ) && is_array( $Params['UserParameters'] ) ? $Params['UserParameters'] : array();

        if ( $action === 'view' )
            return $this->viewResult( null, $this->detail( $Module, isset( $Params['DataTypeString'] ) ? (string)$Params['DataTypeString'] : '', $userParameters ) );
        if ( $action !== 'list' )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        $registry = \expDataTypeInventory::registry();
        $usage = \expDataTypeInventory::usage( !empty( $userParameters['refresh'] ) );
        $built = \expDataTypeInventory::build( $registry, $usage['class'], $usage['object'] );
        $params = self::listParameters( $userParameters, array_map( 'strval', array_keys( $built['groups'] ) ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'module', $Module );
        $tpl->setVariable( 'datatype_groups', \expDataTypeInventory::filter( $built['groups'], $params['filter'], $params['group'] ) );
        $tpl->setVariable( 'datatype_all_groups', $built['groups'] );
        $tpl->setVariable( 'datatype_summary', $built['summary'] );
        $tpl->setVariable( 'datatype_filter', $params['filter'] );
        $tpl->setVariable( 'datatype_group', $params['group'] );
        $tpl->setVariable( 'datatype_counted', (int)$usage['counted'] );
        $tpl->setVariable( 'datatype_cache_ttl', \expDataTypeInventory::CACHE_TTL );
        $tpl->setVariable( 'view_parameters', array( 'filter' => $params['filter'], 'group' => $params['group'] ) );

        $Module->setTitle( \ezpI18n::tr( 'kernel/class', 'Datatypes' ) );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:class/datatype_list.tpl' );
        $Result['path'] = array( array( 'url' => '/class/grouplist/', 'text' => \ezpI18n::tr( 'kernel/class', 'Class groups' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/class', 'Datatypes' ) ) );
        return $this->viewResult( $Result, null );
    }

    /**
     * The page of one datatype.
     *
     * @param \eZModule $Module
     * @param string $string
     * @param array $userParameters
     * @return array the view's result
     */
    protected function detail( $Module, $string, array $userParameters )
    {
        if ( !preg_match( '/^[A-Za-z0-9_]{1,50}$/', $string ) )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $registry = \expDataTypeInventory::registry();
        $usage = \expDataTypeInventory::usage( !empty( $userParameters['refresh'] ) );
        $built = \expDataTypeInventory::build( $registry, $usage['class'], $usage['object'] );
        $row = false;
        foreach ( $built['groups'] as $group )
            foreach ( $group['rows'] as $candidate )
                if ( $candidate['string'] === $string )
                    $row = $candidate;
        if ( !$row )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        $source = \expDataTypeInventory::readSource( \expDataTypeInventory::sourceOf( $row['file'] ) );
        $attributeUsage = \expDataTypeInventory::attributeUsageCached( !empty( $userParameters['refresh'] ) );
        $attributes = \expDataTypeInventory::classAttributes( $string, $attributeUsage['attributes'], $source['settings'] );
        $classIDs = array_map( 'intval', array_keys( $attributes['classes'] ) );

        // the filter form of the content list (POST): answered with a 303 to the same choice as view parameters,
        // from the first page; nothing is read from the query string
        if ( \expViewParameters::redirectsPost( $_POST, array( 'DataTypeNodeFilterButton' ) ) )
        {
            $posted = array();
            foreach ( array( 'sort', 'order', 'limit', 'class' ) as $key )
                $posted[$key] = \expViewParameters::posted( $_POST, $key );
            \expViewParameters::redirect( \expDataTypeInventory::nodeListURL( $string, \expDataTypeInventory::nodeParameters( $posted, $classIDs ) ) );
        }
        $nodeParams = \expDataTypeInventory::nodeParameters( $userParameters, $classIDs );
        $nodePage = \expDataTypeInventory::nodePage( $classIDs, $nodeParams, !empty( $userParameters['refresh'] ) );
        $nodeParams['offset'] = $nodePage['offset'];

        $frontAccess = (string)\eZINI::instance()->variable( 'SiteSettings', 'DefaultAccess' );
        $templates = array( array( 'siteaccess' => '', 'templates' => \expDataTypeInventory::templates( $string, false ) ) );
        if ( $frontAccess !== '' && $frontAccess !== ( isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : '' ) )
            $templates[] = array( 'siteaccess' => $frontAccess, 'templates' => \expDataTypeInventory::templates( $string, $frontAccess ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'module', $Module );
        $tpl->setVariable( 'datatype', $row );
        $tpl->setVariable( 'datatype_source', $source );
        $tpl->setVariable( 'datatype_tables', \expDataTypeInventory::existingTables( $source['tables'] ) );
        $tpl->setVariable( 'datatype_attributes', $attributes['attributes'] );
        $tpl->setVariable( 'datatype_classes', $attributes['classes'] );
        $tpl->setVariable( 'datatype_templates', $templates );
        $tpl->setVariable( 'datatype_nodes', $nodePage['rows'] );
        $tpl->setVariable( 'datatype_node_total', $nodePage['total'] );
        $tpl->setVariable( 'datatype_node_params', $nodeParams );
        $tpl->setVariable( 'datatype_node_sort_links', \expDataTypeInventory::nodeSortLinks( $string, $nodeParams ) );
        $tpl->setVariable( 'datatype_node_sizes', \expDataTypeInventory::NODE_PAGE_SIZES );
        $tpl->setVariable( 'datatype_node_size_links', array_combine( \expDataTypeInventory::NODE_PAGE_SIZES, array_map( function ( $size ) use ( $string, $nodeParams )
        {
            return \expDataTypeInventory::nodeListURL( $string, $nodeParams, array( 'limit' => $size ) );
        }, \expDataTypeInventory::NODE_PAGE_SIZES ) ) );
        $tpl->setVariable( 'datatype_node_page_uri', '/class/datatype/view/' . $string );
        $tpl->setVariable( 'view_parameters', array_merge( array( 'offset' => $nodePage['offset'] ), \expDataTypeInventory::nodeViewParameters( $nodeParams ) ) );
        $tpl->setVariable( 'datatype_help_page', \expDataTypeInventory::helpPage( $string ) );
        $tpl->setVariable( 'datatype_counted', (int)$usage['counted'] );
        $tpl->setVariable( 'datatype_current_access', isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '' );

        $Module->setTitle( \ezpI18n::tr( 'kernel/class', 'Datatype %string', null, array( '%string' => $string ) ) );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:class/datatype_view.tpl' );
        $Result['path'] = array( array( 'url' => '/class/grouplist/', 'text' => \ezpI18n::tr( 'kernel/class', 'Class groups' ) ),
                                 array( 'url' => '/class/datatype/list', 'text' => \ezpI18n::tr( 'kernel/class', 'Datatypes' ) ),
                                 array( 'url' => false, 'text' => $string ) );
        return $Result;
    }
}

}

// The other class views were Exponential\View\Kernel\Class\* until 6.0.15 and keep that name; this one answers to it
// as well, so every view of the module can be reached by the same prefix.
namespace
{
    class_alias( 'Exponential\\View\\Kernel\\ContentClass\\Datatype', 'Exponential\\View\\Kernel\\Class\\Datatype' );
}
