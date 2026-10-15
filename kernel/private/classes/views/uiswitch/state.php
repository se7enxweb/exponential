<?php
/**
 * The uiswitch/state view: JSON of what the signed-in user may choose on the current siteaccess, for scripts
 * (expUISwitch::toolbar()). GET; no cache.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Uiswitch
{

class State extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $data = array( 'siteaccess' => \expUISwitch::currentSiteAccess(),
                       'providers' => \expUISwitch::toolbar() );
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Cache-Control: private, no-store' );
        echo json_encode( $data );
        // no catch( Exception ) around this: under Velocity cleanExit() throws
        \eZExecution::cleanExit();
    }
}

}
