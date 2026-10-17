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
        $input['NodeID'] = isset( $Params['NodeID'] ) ? $Params['NodeID'] : null;
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

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'result', $result );
        $tpl->setVariable( 'params', $params );
        $tpl->setVariable( 'class_options', $classOptions );
        $tpl->setVariable( 'sorts', array_keys( \expContentCheckUnreferenced::SORTS ) );
        $tpl->setVariable( 'page_uri', '/contentcheck/unreferenced/' . (int)$params['node_id'] );
        $tpl->setVariable( 'page_uri_suffix', self::querySuffix( $params ) );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $params['offset'] ) );
        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'limit_choices', $limitChoices );
        $tpl->setVariable( 'limit_preference', self::LIMIT_PREFERENCE );
        $tpl->setVariable( 'feedback', $feedback );
        $tpl->setVariable( 'can_trash', \expContentCheck::hasPolicy( 'contentcheck', 'trash' ) );
        $tpl->setVariable( 'batch_limit', \expContentCheckTrash::batchLimit() );
        $tpl->setVariable( 'form_token', \expContentCheck::formToken() );
        $tpl->setVariable( 'token_field', \expContentCheck::TOKEN_FIELD );

        return array( 'content' => $tpl->fetch( 'design:contentcheck/unreferenced.tpl' ),
                      'path' => array( array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Setup' ), 'url' => 'setup/menu' ),
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Unreferenced objects' ), 'url' => false ) ) );
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
     * @return string starting with ? (it always carries the order)
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
