<?php
/**
 * The code of kernel/content/keyword.php, moved into a class (#207 stage 1). The file kernel/content/keyword.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/keyword.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

class Keyword extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $Alphabet = rawurldecode( $Params['Alphabet'] );

        $Offset = $Params['Offset'];
        $ClassID = $Params['ClassID'];
        $viewParameters = array( 'offset' => $Offset, 'classid' => $ClassID );

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'alphabet', $Alphabet );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/keyword.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'Keywords' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
