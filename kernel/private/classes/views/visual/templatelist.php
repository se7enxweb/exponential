<?php
/**
 * The code of kernel/visual/templatelist.php, moved into a class (#207 stage 1). The file kernel/visual/templatelist.php is one call to it.
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/visual/templatelist.php:
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

class Templatelist extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $module = $Params['Module'];

        $offset = $Params['Offset'];

        $doFiltration = false;
        $filterString = '';

        if ( !is_numeric( $offset ) )
            $offset = 0;

        // The filter is the (filter) view parameter, its value written as expLogPermalink::encode() writes it ("~2F" for
        // "/", which template paths are full of). The form without scripts is sent with POST and answered 303 with
        // visual/templatelist/(filter)/<text>. A query string is never read.
        if ( \expViewParameters::redirectsPost( $_POST, array( 'filterString' ) ) )
        {
            $posted = trim( \expViewParameters::posted( $_POST, 'filterString' ) );
            \expViewParameters::redirect( 'visual/templatelist' . ( $posted !== '' ? '/(filter)/' . \expLogPermalink::encode( substr( $posted, 0, 200 ) ) : '' ) );
        }
        $filterParameter = \expViewParameters::get( $Params, 'filter', 'Filter' );
        if ( $filterParameter !== false )
        {
            $filterString = trim( \expLogPermalink::decode( $filterParameter ) );
            if ( strlen( $filterString ) > 0 )
                $doFiltration = true;
        }


        $ini = \eZINI::instance();
        $tpl = \eZTemplate::factory();

        $siteAccess = $http->sessionVariable( 'eZTemplateAdminCurrentSiteAccess' );

        $overrideArray = \eZTemplateDesignResource::overrideArray( $siteAccess );

        $mostUsedOverrideArray = array();
        $filteredOverrideArray = array();
        $mostUsedMatchArray = array( 'node/view/', 'content/view/embed', 'pagelayout.tpl', 'search.tpl', 'basket' );
        foreach ( array_keys( $overrideArray ) as $overrideKey )
        {
            foreach ( $mostUsedMatchArray as $mostUsedMatch )
            {
                if ( strpos( $overrideArray[$overrideKey]['template'], $mostUsedMatch ) !== false )
                {
                    $mostUsedOverrideArray[$overrideKey] = $overrideArray[$overrideKey];
                }
            }
            if ( $doFiltration ) {
                if ( strpos( $overrideArray[$overrideKey]['template'], $filterString ) !== false )
                {
                    $filteredOverrideArray[$overrideKey] = $overrideArray[$overrideKey];
                }
            }
        }

        $tpl->setVariable( 'filterString', $filterString );
        // the pager keeps the filter: "/(filter)/<encoded text>", '' without one
        $tpl->setVariable( 'filter_suffix', $doFiltration ? '/(filter)/' . \expLogPermalink::encode( $filterString ) : '' );

        if ( $doFiltration )
        {
            $tpl->setVariable( 'template_array', $filteredOverrideArray );
            $tpl->setVariable( 'template_count', count( $filteredOverrideArray ) );
        }
        else
        {
            $tpl->setVariable( 'template_array', $overrideArray );
            $tpl->setVariable( 'template_count', count( $overrideArray ) );
        }

        $tpl->setVariable( 'most_used_template_array', $mostUsedOverrideArray );
        $viewParameters = array( 'offset' => $offset );
        $tpl->setVariable( 'view_parameters', $viewParameters );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:visual/templatelist.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/design', 'Template list' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
