<?php
/**
 * The contentfinder/search view (doc/features/6.0/content-finder.md): one search field and a type selector, read
 * from the query string (SearchText, SearchType), and one result block per object found, with what the user may
 * see of it. Needs the policy contentfinder/search; answers on administration siteaccesses only. Every search is
 * recorded in the audit (content.finder.search).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Contentfinder
{

class Search extends \Exponential\Runnable\ModuleView
{
    /** The session variable the action view leaves its outcome in */
    const FEEDBACK = 'ContentFinderFeedback';

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $module = $Params['Module'];
        if ( !\expContentFinder::isAvailableHere() )
            return $module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $http = \eZHTTPTool::instance();
        $text = $http->hasGetVariable( 'SearchText' ) ? $http->getVariable( 'SearchText' ) : '';
        $type = \expContentFinderInput::type( $http->hasGetVariable( 'SearchType' ) ? $http->getVariable( 'SearchType' ) : 'auto' );
        $text = is_scalar( $text ) ? (string)$text : '';

        $feedback = null;
        if ( $http->hasSessionVariable( self::FEEDBACK ) )
        {
            $feedback = $http->sessionVariable( self::FEEDBACK );
            $http->removeSessionVariable( self::FEEDBACK );
            if ( !is_array( $feedback ) )
                $feedback = null;
        }

        $found = null;
        if ( trim( $text ) !== '' )
        {
            $finder = new \expContentFinder();
            $found = $finder->search( $text, $type );
            self::audit( $found );
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'search_text', $found !== null ? $found['input']['text'] : '' );
        $tpl->setVariable( 'search_type', $type );
        $tpl->setVariable( 'types', \expContentFinderInput::types() );
        $tpl->setVariable( 'found', $found );
        $tpl->setVariable( 'feedback', $feedback );
        $tpl->setVariable( 'form_token', \expContentFinderAction::formToken() );
        $tpl->setVariable( 'token_field', \expContentFinderAction::TOKEN_FIELD );
        $tpl->setVariable( 'can_act', \expContentFinder::hasPolicy( 'contentfinder', 'delete' ) );

        return array( 'content' => $tpl->fetch( 'design:contentfinder/search.tpl' ),
                      'path' => array( array( 'text' => \ezpI18n::tr( 'kernel/contentfinder', 'Setup' ), 'url' => 'setup/menu' ),
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentfinder', 'Content finder' ), 'url' => false ) ) );
    }

    /**
     * Records a search: the type asked and recognised, the text (at most 128 characters), how many objects were
     * shown and how many were found but not shown.
     *
     * @param array $found from expContentFinder::search()
     */
    public static function audit( array $found )
    {
        if ( !class_exists( 'expAuditHook' ) )
            return;
        \expAuditHook::emit( 'content.finder.search', function () use ( $found ) {
            $value = (string)$found['input']['text'];
            if ( function_exists( 'mb_strlen' ) ? mb_strlen( $value ) > 128 : strlen( $value ) > 128 )
                $value = ( function_exists( 'mb_substr' ) ? mb_substr( $value, 0, 128 ) : substr( $value, 0, 128 ) ) . '…';
            $types = array();
            foreach ( $found['input']['queries'] as $query )
                $types[] = $query['type'];
            return array( 'object' => array( 'type' => 'view', 'id' => 'contentfinder/search' ),
                          'result' => $found['input']['error'] === '' ? 'success' : 'refused',
                          'reason' => $found['input']['error'] === '' ? null : $found['input']['error'],
                          'after' => array( 'type' => $found['input']['type'], 'as' => array_values( array_unique( $types ) ),
                                            'value' => $value, 'results' => (int)$found['count'], 'hidden' => (int)$found['hidden'],
                                            'truncated' => (bool)$found['truncated'] ) );
        } );
    }
}

}
