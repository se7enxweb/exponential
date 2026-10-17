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
        // the node from the filter form (GET NodeID), else from the address. No redirect to an address with the node:
        // the kernel carries the query of a request over every module redirect, so the NodeID of the form came back
        // with the redirect and sent it to the same address again, until the browser gave up (too many redirects)
        // (expContentCheckScope: the form's node, else the address's, else the user's last choice, which it becomes)
        $input['NodeID'] = \expContentCheckScope::resolve( isset( $Params['NodeID'] ) ? $Params['NodeID'] : null,
                                                          $http->hasGetVariable( 'NodeID' ) ? $http->getVariable( 'NodeID' ) : null );
        // "Select all" / "Select none" of the states and the references (KindChoice, RelationChoice); none ticked
        // checks every one, as no box ticked does
        $none = array();
        foreach ( array( 'Kind' => \expContentCheckBrokenRelations::KINDS, 'Relation' => \expContentCheckBrokenRelations::RELATIONS ) as $group => $all )
        {
            $choice = $http->hasGetVariable( $group . 'Choice' ) ? $http->getVariable( $group . 'Choice' ) : '';
            if ( $choice === 'all' )
                $input[$group] = $all;
            else if ( $choice === 'none' )
            {
                unset( $input[$group] );
                $none[] = $group;
            }
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
        $tpl->setVariable( 'scope', \expContentCheckScope::forTemplate( (int)$params['node_id'], 'brokenrelations' ) );
        $tpl->setVariable( 'kinds', \expContentCheckBrokenRelations::KINDS );
        $tpl->setVariable( 'kinds_shown', in_array( 'Kind', $none, true ) ? array() : $params['kinds'] );
        $tpl->setVariable( 'relations_shown', in_array( 'Relation', $none, true ) ? array() : $params['relations'] );
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
     * @return string starting with ?
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
        return '?' . implode( '&', $query );
    }
}

}
