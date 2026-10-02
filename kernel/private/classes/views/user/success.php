<?php
/**
 * The code of kernel/user/success.php, moved into a class (#207 stage 1). The file kernel/user/success.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/user/success.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\User
{

class Success extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $Module->setTitle( "Successful registration" );
        // Template handling

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( "module", $Module );
        $ini = \eZINI::instance();

        $tpl->setVariable( "verify_user_email", $ini->variable( 'UserSettings', 'VerifyUserType' ) === "email" );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:user/success.tpl" );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/user', 'User' ),
                                        'url' => false ),
                                 array( 'text' => \ezpI18n::tr( 'kernel/user', 'Success' ),
                                        'url' => false ) );
        if ( $ini->variable( 'SiteSettings', 'LoginPage' ) == 'custom' )
            $Result['pagelayout'] = 'loginpagelayout.tpl';

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
