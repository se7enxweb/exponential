<?php
/**
 * The code of kernel/content/trash.php, moved into a class (#207 stage 1). The file kernel/content/trash.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/trash.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

class Trash extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $Offset = $Params['Offset'];
        if ( isset( $Params['UserParameters'] ) )
        {
            $UserParameters = $Params['UserParameters'];
        }
        else
        {
            $UserParameters = array();
        }
        $viewParameters = array( 'offset' => $Offset, 'namefilter' => false );
        $viewParameters = array_merge( $viewParameters, $UserParameters );

        $http = \eZHTTPTool::instance();

        // The trash service is shared with bin/php/trashpurge.php and cronjobs/trashpurge.php. Loaded by path:
        // a server whose workers kept an autoload array from before the class existed (Velocity) still renders.
        require_once 'kernel/private/classes/services/trash.php';

        // What the list shows about each item (Exponential\Service\TrashList, doc/bc/6.0/trash.md). Loaded by path
        // like the trash service; without it the template falls back to its own fetch.
        if ( !class_exists( '\Exponential\Service\TrashRecord' ) )
            require_once 'kernel/private/classes/services/trashrecord.php';
        if ( !class_exists( '\Exponential\Service\TrashList' ) )
            require_once 'kernel/private/classes/services/trashlist.php';

        $user = \eZUser::currentUser();
        $userID = $user->id();

        if ( $http->hasPostVariable( 'RemoveButton' )  )
        {
            if ( $http->hasPostVariable( 'DeleteIDArray' ) )
            {
                if ( \Exponential\Service\Trash::canEmpty( $user ) )
                {
                    \Exponential\Service\Trash::purgeObjects( $http->postVariable( 'DeleteIDArray' ) );
                }
                else
                {
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
                }
            }
        }
        else if ( $http->hasPostVariable( 'EmptyButton' )  )
        {
            if ( \Exponential\Service\Trash::canEmpty( $user ) )
            {
                // as the command does: 100 at a time, each batch in a transaction of its own, a pause between them
                \Exponential\Service\Trash::emptyTrash( 100, 1 );
            }
            else
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
            }
        }

        // Filters (who trashed it, class, date range): posted, then carried in the URL as view parameters
        if ( $http->hasPostVariable( 'FilterButton' ) || $http->hasPostVariable( 'ResetFilterButton' ) )
        {
            $uri = '/content/trash';
            if ( $http->hasPostVariable( 'FilterButton' ) )
            {
                $posted = array();
                foreach ( array( 'trashed_by' => 'FilterTrashedBy', 'class' => 'FilterClassID',
                                 'from' => 'FilterTrashedFrom', 'to' => 'FilterTrashedTo' ) as $key => $name )
                {
                    if ( $http->hasPostVariable( $name ) )
                        $posted[$key] = trim( (string)$http->postVariable( $name ) );
                }
                $uri .= \Exponential\Service\TrashList::filterURI( \Exponential\Service\TrashList::filters( $posted ) );
            }
            foreach ( array( 'sort_field', 'sort_order' ) as $key )
            {
                if ( isset( $viewParameters[$key] ) && preg_match( '/^[a-z_0-9]+$/', (string)$viewParameters[$key] ) )
                    $uri .= '/(' . $key . ')/' . $viewParameters[$key];
            }
            return $this->viewResult( null, $Module->redirectTo( $uri ) );
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'view_parameters', $viewParameters );

        $filters = \Exponential\Service\TrashList::filters( $viewParameters );
        $context = \Exponential\Service\TrashList::context();

        $limitPreference = (int)\eZPreferences::value( 'admin_list_limit' );
        $limits = array( 1 => 10, 2 => 25, 3 => 50 );
        $limit = isset( $limits[$limitPreference] ) ? $limits[$limitPreference] : 10;
        $sortField = isset( $viewParameters['sort_field'] ) && in_array( $viewParameters['sort_field'], array( 'name', 'class_name', 'section', 'trashed' ) )
                   ? $viewParameters['sort_field'] : 'trashed';
        $sortOrder = isset( $viewParameters['sort_order'] ) && (string)$viewParameters['sort_order'] === '1' ? '1' : '0';

        $listParams = array_merge( \Exponential\Service\TrashList::listParams( $filters, $context ),
                                   array( 'ObjectNameFilter' => $viewParameters['namefilter'],
                                          'AttributeFilter' => false ) );
        $trashCount = (int)\eZContentObjectTrashNode::trashListCount( $listParams );
        $trashNodes = \eZContentObjectTrashNode::trashList( array_merge( $listParams,
                                                            array( 'Limit' => $limit,
                                                                   'Offset' => (int)$Offset,
                                                                   'SortBy' => array( $sortField, $sortOrder ) ) ) );

        $tpl->setVariable( 'trash_items', \Exponential\Service\TrashList::describe( is_array( $trashNodes ) ? $trashNodes : array(), $context ) );
        $tpl->setVariable( 'trash_count', $trashCount );
        $tpl->setVariable( 'trash_summary', \Exponential\Service\TrashList::summary( $context ) );
        $tpl->setVariable( 'trash_filters', $filters );
        $tpl->setVariable( 'trash_filtered', \Exponential\Service\TrashList::isFiltered( $filters ) );
        $tpl->setVariable( 'trash_filter_uri', \Exponential\Service\TrashList::filterURI( $filters ) );
        $tpl->setVariable( 'trash_user_options', \Exponential\Service\TrashList::userOptions( $context ) );
        $tpl->setVariable( 'trash_class_options', \Exponential\Service\TrashList::classOptions() );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/trash.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'Trash' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
