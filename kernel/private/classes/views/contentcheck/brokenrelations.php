<?php
/**
 * The contentcheck/brokenrelations view (doc/features/6.0/content-checks.md, "Broken relations"): visible,
 * published content below a node (contentcheck/brokenrelations/<node id>, the content tree's top node without one) that refers
 * to deleted, trashed, hidden or unpublished content, page by page. Read only. Needs the policy
 * contentcheck/brokenrelations; answers on administration siteaccesses only.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Contentcheck
{

class Brokenrelations extends \Exponential\Runnable\ModuleView
{
    /** The preference that holds the chosen page size (its position in the sizes) */
    const LIMIT_PREFERENCE = 'admin_contentcheck_broken_limit';

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $module = $Params['Module'];
        if ( !\expContentCheck::isAvailableHere() )
            return $module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $http = \eZHTTPTool::instance();
        list( $limit, $limitChoice, $limitChoices ) = \expAdminPagination::chosen( 'contentcheck/brokenrelations', self::LIMIT_PREFERENCE );
        $input = array();
        foreach ( array( 'Exclude', 'Kind', 'Relation', 'After' ) as $name )
        {
            if ( $http->hasGetVariable( $name ) )
                $input[$name] = $http->getVariable( $name );
        }
        // the node from the address, or from the filter form (GET NodeID), which then becomes the address
        $input['NodeID'] = isset( $Params['NodeID'] ) ? $Params['NodeID'] : null;
        if ( $http->hasGetVariable( 'NodeID' ) )
        {
            $params = \expContentCheckBrokenRelations::parameters( array_merge( $input, array( 'NodeID' => $http->getVariable( 'NodeID' ) ) ), Unreferenced::rootNodeID() );
            return $module->redirectTo( '/contentcheck/brokenrelations/' . (int)$params['node_id'] . self::querySuffix( $params ) );
        }
        $input['Limit'] = $limit;
        // without a node: the content tree's top node, which a user with a limited content/read may read as well
        $params = \expContentCheckBrokenRelations::parameters( $input, Unreferenced::rootNodeID() );

        $result = \expContentCheckBrokenRelations::scan( $params );
        if ( $result['error'] === 'not_found' )
            return $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'result', $result );
        $tpl->setVariable( 'params', $params );
        $tpl->setVariable( 'kinds', \expContentCheckBrokenRelations::KINDS );
        $tpl->setVariable( 'relations', \expContentCheckBrokenRelations::RELATIONS );
        $tpl->setVariable( 'page_uri', '/contentcheck/brokenrelations/' . (int)$params['node_id'] );
        $tpl->setVariable( 'filter_suffix', self::querySuffix( $params ) );
        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'limit_choices', $limitChoices );
        $tpl->setVariable( 'limit_preference', self::LIMIT_PREFERENCE );

        return array( 'content' => $tpl->fetch( 'design:contentcheck/brokenrelations.tpl' ),
                      'path' => array( array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Setup' ), 'url' => 'setup/menu' ),
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Broken relations' ), 'url' => false ) ) );
    }

    /**
     * The query string of the filters, without the position (After), which the pages add.
     *
     * @param array $params from expContentCheckBrokenRelations::parameters()
     * @return string empty without a filter, else starting with ?
     */
    public static function querySuffix( array $params )
    {
        $query = array();
        if ( $params['exclude'] )
            $query[] = 'Exclude=' . rawurlencode( implode( ',', $params['exclude'] ) );
        if ( count( $params['kinds'] ) < count( \expContentCheckBrokenRelations::KINDS ) )
        {
            foreach ( $params['kinds'] as $kind )
                $query[] = 'Kind%5B%5D=' . rawurlencode( $kind );
        }
        if ( count( $params['relations'] ) < count( \expContentCheckBrokenRelations::RELATIONS ) )
        {
            foreach ( $params['relations'] as $relation )
                $query[] = 'Relation%5B%5D=' . rawurlencode( $relation );
        }
        // no filter, no bare "?" in the links of the page (admin4 review 15.10.2026)
        return $query ? '?' . implode( '&', $query ) : '';
    }
}

}
