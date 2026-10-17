<?php
/**
 * The contentcheck/unreferenced view (doc/features/6.0/content-checks.md, "Unreferenced objects"): the published
 * objects below a node (contentcheck/unreferenced/<node id>) that nothing refers to, filtered by class, sorted and
 * paged, with a form that posts the ticked ones to contentcheck/trash. Needs the policy contentcheck/unreferenced;
 * answers on administration siteaccesses only.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Contentcheck
{

class Unreferenced extends \Exponential\Runnable\ModuleView
{
    /** The session variable the trash view leaves its outcome in */
    const FEEDBACK = 'ContentCheckTrashFeedback';

    /** The preference that holds the chosen page size (its position in the sizes) */
    const LIMIT_PREFERENCE = 'admin_contentcheck_list_limit';

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $module = $Params['Module'];
        if ( !\expContentCheck::isAvailableHere() )
            return $module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $http = \eZHTTPTool::instance();
        list( $limit, $limitChoice, $limitChoices ) = \expAdminPagination::chosen( 'contentcheck/unreferenced', self::LIMIT_PREFERENCE );
        $input = array();
        foreach ( array( 'ClassID', 'Sort', 'Order', 'Containers' ) as $name )
        {
            if ( $http->hasGetVariable( $name ) )
                $input[$name] = $http->getVariable( $name );
        }
        // the form's node, else the address's, else the user's last choice, which it becomes (expContentCheckScope)
        $input['NodeID'] = \expContentCheckScope::resolve( isset( $Params['NodeID'] ) ? $Params['NodeID'] : null,
                                                          $http->hasGetVariable( 'NodeID' ) ? $http->getVariable( 'NodeID' ) : null );
        // only the asset classes can be listed, so only they are offered as a filter
        $classNames = array();
        foreach ( \expContentCheckUnreferenced::assetClasses() as $identifier )
        {
            $class = \eZContentClass::fetchByIdentifier( $identifier );
            if ( $class instanceof \eZContentClass )
                $classNames[(int)$class->attribute( 'id' )] = (string)$class->attribute( 'name' );
        }
        asort( $classNames, SORT_NATURAL | SORT_FLAG_CASE );
        $classOptions = array();
        foreach ( $classNames as $id => $name )
            $classOptions[] = array( 'id' => $id, 'name' => $name );

        // "Select all" and "Select none" are submit buttons of the filter form (ClassChoice): no JavaScript needed
        $choice = $http->hasGetVariable( 'ClassChoice' ) ? $http->getVariable( 'ClassChoice' ) : '';
        $input['ClassID'] = self::classChoice( $input, array_keys( $classNames ), $choice === 'all', $choice === 'none' );
        $input['Offset'] = \expAdminPagination::offset( $Params );
        $input['Limit'] = $limit;
        $params = \expContentCheckUnreferenced::parameters( $input, self::rootNodeID() );

        $feedback = null;
        if ( $http->hasSessionVariable( self::FEEDBACK ) )
        {
            $feedback = $http->sessionVariable( self::FEEDBACK );
            $http->removeSessionVariable( self::FEEDBACK );
            if ( !is_array( $feedback ) )
                $feedback = null;
        }

        $result = \expContentCheckUnreferenced::fetch( $params );
        if ( $result['error'] === 'not_found' )
            return $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'result', $result );
        $tpl->setVariable( 'params', $params );
        $tpl->setVariable( 'scope', \expContentCheckScope::forTemplate( (int)$params['node_id'], 'unreferenced' ) );
        $tpl->setVariable( 'class_options', $classOptions );
        $tpl->setVariable( 'sorts', array_keys( \expContentCheckUnreferenced::SORTS ) );
        $tpl->setVariable( 'page_uri', '/contentcheck/unreferenced/' . (int)$params['node_id'] );
        $tpl->setVariable( 'page_uri_suffix', self::querySuffix( $params ) );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $params['offset'] ) );
        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'limit_choices', $limitChoices );
        $tpl->setVariable( 'limit_preference', self::LIMIT_PREFERENCE );
        $tpl->setVariable( 'feedback', $feedback );
        $tpl->setVariable( 'select_all', $http->hasGetVariable( 'SelectAll' ) && $http->getVariable( 'SelectAll' ) === '1' );
        $tpl->setVariable( 'can_trash', \expContentCheck::hasPolicy( 'contentcheck', 'trash' ) );
        $tpl->setVariable( 'batch_limit', \expContentCheckTrash::batchLimit() );
        $tpl->setVariable( 'form_token', \expContentCheck::formToken() );
        $tpl->setVariable( 'token_field', \expContentCheck::TOKEN_FIELD );

        return array( 'content' => $tpl->fetch( 'design:contentcheck/unreferenced.tpl' ),
                      'path' => array( array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Setup' ), 'url' => 'setup/menu' ),
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Unreferenced objects' ), 'url' => false ) ) );
    }

    /**
     * The classes asked for: every listed class with "Select all", none with "Select none", else the form's ClassID[].
     *
     * @param array $input the request's fields (ClassID)
     * @param int[] $allClassIDs the classes the filter offers
     * @param bool $all the button "Select all" was pressed (ClassChoice=all)
     * @param bool $none the button "Select none" was pressed (ClassChoice=none)
     * @return array
     */
    public static function classChoice( array $input, array $allClassIDs, $all, $none )
    {
        if ( $none )
            return array();
        if ( $all )
            return array_values( array_map( 'intval', $allClassIDs ) );
        return isset( $input['ClassID'] ) ? $input['ClassID'] : array();
    }

    /**
     * The node a list starts from when the address names none: the top node of the content tree.
     *
     * @return int
     */
    public static function rootNodeID()
    {
        $ini = \eZINI::instance( 'content.ini' );
        $root = $ini->hasVariable( 'NodeSettings', 'RootNode' ) ? (int)$ini->variable( 'NodeSettings', 'RootNode' ) : 2;
        return $root > 0 ? $root : 2;
    }

    /**
     * The query string that keeps the filters and the order on the pages of the list.
     *
     * @param array $params from expContentCheckUnreferenced::parameters()
     * @return string empty, or starting with ?
     */
    public static function querySuffix( array $params )
    {
        $query = array();
        foreach ( $params['class_ids'] as $classID )
            $query[] = 'ClassID%5B%5D=' . (int)$classID;
        $query[] = 'Sort=' . rawurlencode( $params['sort'] );
        $query[] = 'Order=' . rawurlencode( $params['order'] );
        if ( $params['containers'] )
            $query[] = 'Containers=1';
        return '?' . implode( '&', $query );
    }
}

}
