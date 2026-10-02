<?php
/**
 * The code of kernel/content/urlalias_wildcard.php, moved into a class (#207 stage 1). The file kernel/content/urlalias_wildcard.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/urlalias_wildcard.php:
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

class UrlaliasWildcard extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module =& $Params['Module'];
        $http = \eZHTTPTool::instance();

        $Offset = $Params['Offset'];
        if ( $Module->hasActionParameter( 'Offset' ) )
        {
            $Offset = $Module->actionParameter( 'Offset' );
        }

        $tpl = \eZTemplate::factory();
        $limit = 20;

        $infoCode = 'no-errors'; // This will be modified if info/warning is given to user.
        $infoData = array(); // Extra parameters can be added to this array
        $wildcardSrcText = false;
        $wildcardDstText = false;
        $wildcardType = false;

        if ( $Module->isCurrentAction( 'RemoveAllWildcards' ) )
        {
            \eZURLWildcard::removeAll();

            \eZURLWildcard::expireCache();

            $infoCode = "feedback-wildcard-removed-all";
        }
        else if ( $Module->isCurrentAction( 'RemoveWildcard' ) )
        {
            if ( $http->hasPostVariable( 'WildcardIDList' ) )
            {
                $wildcardIDs = $http->postVariable( 'WildcardIDList' );

                \eZURLWildcard::removeByIDs( $wildcardIDs );

                \eZURLWildcard::expireCache();

                $infoCode = "feedback-wildcard-removed";
            }
        }
        else if ( $Module->isCurrentAction( 'NewWildcard' ) )
        {
            $wildcardSrcText = trim( $Module->actionParameter( 'WildcardSourceText' ) );
            $wildcardSrcText = ltrim( $wildcardSrcText, '/' );
            $wildcardDstText = trim( $Module->actionParameter( 'WildcardDestinationText' ) );
            $wildcardType = $http->hasPostVariable( 'WildcardType' ) && strlen( trim( $http->postVariable( 'WildcardType' ) ) ) > 0;

            if ( strlen( $wildcardSrcText ) == 0 )
            {
                $infoCode = "error-no-wildcard-text";
            }
            else if ( strlen( $wildcardDstText ) == 0 )
            {
                $infoCode = "error-no-wildcard-destination-text";
            }
            else
            {
                $wildcard = \eZURLWildcard::fetchBySourceURL( $wildcardSrcText, false );
                if ( $wildcard )
                {
                    $infoCode = "feedback-wildcard-exists";

                    $infoData['wildcard_src_url'] = $wildcardSrcText;
                    $infoData['wildcard_dst_url'] = $wildcard['destination_url'];
                }
                else
                {
                    $row = array(
                        'source_url' => $wildcardSrcText,
                        'destination_url' => $wildcardDstText,
                        'type' => $wildcardType ? \eZURLWildcard::TYPE_FORWARD : \eZURLWildcard::TYPE_DIRECT );

                    $wildcard = new \eZURLWildcard( $row );
                    $wildcard->store();

                    \eZURLWildcard::expireCache();

                    $infoData['wildcard_src_url'] = $wildcardSrcText;
                    $infoData['wildcard_dst_url'] = $wildcardDstText;

                    $wildcardSrcText = false;
                    $wildcardDstText = false;
                    $wildcardType = false;

                    $infoCode = "feedback-wildcard-created";
                }
            }
        }

        // Audit (doc/bc/6.0/audit.md, content.urlalias.change): an alias or wildcard added or removed
        if ( class_exists( 'expAuditHook' ) && in_array( $infoCode, array( 'feedback-removed-all', 'feedback-removed', 'feedback-alias-created',
                                                                           'feedback-alias-cleanup', 'feedback-wildcard-removed-all',
                                                                           'feedback-wildcard-removed', 'feedback-wildcard-created' ), true ) )
            \expAuditHook::emit( 'content.urlalias.change', function () use ( $infoCode, $infoData, $Module ) {
                $removed = strpos( $infoCode, 'removed' ) !== false;
                $alias = isset( $infoData['wildcard_src_url'] ) ? (string)$infoData['wildcard_src_url'] : null;
                return array( 'object' => array( 'type' => 'wildcard', 'id' => $alias !== null ? $alias : $infoCode ),
                              'target' => null,
                              'verb' => $removed ? 'remove' : 'add',
                              'before' => $removed ? array( 'removed' => $infoCode === 'feedback-removed-all' || $infoCode === 'feedback-wildcard-removed-all' ? 'all' : 'selected' ) : null,
                              'after' => $removed ? null : array( 'alias' => $alias,
                                                                  'cleaned_from' => isset( $infoData['orig_alias'] ) ? (string)$infoData['orig_alias'] : null ) );
            } );

        // User preferences
        $limitList = array( array( 'id'    => 1,
                                   'value' => 10 ),
                            array( 'id'    => 2,
                                   'value' => 25 ),
                            array( 'id'    => 3,
                                   'value' => 50 ),
                            array( 'id'    => 4,
                                   'value' => 100 ) );
        $limitID = \eZPreferences::value( 'admin_urlwildcard_list_limit' );
        $limitValues = array();
        foreach ( $limitList as $limitEntry )
        {
            $limitIDs[]                     = $limitEntry['id'];
            $limitValues[$limitEntry['id']] = $limitEntry['value'];
        }
        if ( !in_array( $limitID, $limitIDs ) )
        {
            $limitID = 2;
        }

        // Fetch wildcads
        $wildcardsLimit = $limitValues[$limitID];
        $wildcardsCount = \eZURLWildcard::fetchListCount();
        // check offset, it can be out of range if some wildcards were removed.
        if ( $Offset >= $wildcardsCount )
        {
            $Offset = 0;
        }
        $wildcardList = \eZURLWildcard::fetchList( $Offset, $wildcardsLimit );

        $viewParameters = array( 'offset' => $Offset );


        $path = array();
        $path[] = array( 'url'  => false,
                         'text' => \ezpI18n::tr( 'kernel/content/urlalias_wildcard', 'URL wildcard aliases' ) );

        $tpl->setVariable( 'wildcard_list', $wildcardList );
        $tpl->setVariable( 'wildcards_limit', $wildcardsLimit );
        $tpl->setVariable( 'wildcards_count', $wildcardsCount );
        $tpl->setVariable( 'info_code', $infoCode );
        $tpl->setVariable( 'info_data', $infoData );
        $tpl->setVariable( 'wildcardSourceText', $wildcardSrcText );
        $tpl->setVariable( 'wildcardDestinationText', $wildcardDstText );
        $tpl->setVariable( 'wildcardType', $wildcardType );
        $tpl->setVariable( 'limitList', $limitList );
        $tpl->setVariable( 'limitID', $limitID );
        $tpl->setVariable( 'view_parameters', $viewParameters );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/urlalias_wildcard.tpl' );
        $Result['path'] = $path;

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
