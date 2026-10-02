<?php
/**
 * The code of kernel/ezinfo/copyright.php, moved into a class (#207 stage 1). The file kernel/ezinfo/copyright.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/ezinfo/copyright.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Ezinfo
{

class Copyright extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];

        // The parts of the notice: the plain notice below is made from them, and a design can word
        // them in the interface language (design:ezinfo/copyright.tpl gets them as copyright_info)
        $copyrightInfo = array( 'years'           => '1998-2026',
                                'holder'          => '7x / Exponential Foundation',
                                'original_years'  => '1999-2014',
                                'original_holder' => 'eZ Systems AS',
                                'license'         => 'GNU General Public License',
                                'license_version' => '2',
                                'license_url'     => 'https://www.gnu.org/licenses/old-licenses/gpl-2.0.html',
                                'version'         => \ExponentialSDK::version( true ) );

        $text = <<<COPYRIGHT
        <p>Copyright (C) {$copyrightInfo['years']} {$copyrightInfo['holder']}. All rights reserved.</p>

        <p>Exponential is free software: you may redistribute it and/or modify it
        under the terms of the "GNU General Public License" version 2 as published
        by the Free Software Foundation and appearing in the file LICENSE included
        in the packaging of this software.</p>

        <p>Exponential is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
        THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
        PURPOSE.</p>

        <p>Exponential was originally developed by eZ Systems as eZ Publish (Legacy):
        Copyright (C) {$copyrightInfo['original_years']} {$copyrightInfo['original_holder']}. All rights reserved. The copyright
        notices of the original authors and of the third-party software included
        with Exponential are kept in the source files and in the LICENSE file, as
        the license requires.</p>

        <p>The "GNU General Public License" (GPL) is available at
        <a href="{$copyrightInfo['license_url']}">{$copyrightInfo['license_url']}</a>
        and in the file LICENSE included in the packaging of this software.</p>
        COPYRIGHT;

        // The notice is rendered through design:ezinfo/copyright.tpl, so a design can present it;
        // Without a result, the plain notice is shown.
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'copyright_notice', $text );
        $tpl->setVariable( 'copyright_info', $copyrightInfo );
        $content = $tpl->fetch( 'design:ezinfo/copyright.tpl' );

        $Result = array();
        $Result['content'] = ( is_string( $content ) && trim( $content ) !== '' ) ? $content : $text;
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/ezinfo', 'Info' ) ),
                                 array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/ezinfo', 'Copyright' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
