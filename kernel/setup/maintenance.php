<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/*
 Setup > Maintenance: take the site offline for a maintenance window and back,
 the same as bin/php/maintenance.php on|off. The administration stays reachable
 while it is on (and, when ticked, the address it was switched on from), or the page
 that switches it off again could not be reached.
*/

$module = $Params['Module'];
$http = eZHTTPTool::instance();
$root = eZSys::rootDir();
$feedback = false;

if ( $module->isCurrentAction( 'SwitchOn' ) )
{
    $until = 0;
    $minutes = (int)$http->postVariable( 'MaintenanceMinutes', 0 );
    if ( $minutes > 0 )
        $until = time() + min( $minutes, 7 * 24 * 60 ) * 60;
    $ips = array();
    foreach ( preg_split( '/[\s,]+/', (string)$http->postVariable( 'MaintenanceAllowIPs', '' ) ) as $ip )
        if ( filter_var( $ip, FILTER_VALIDATE_IP ) )
            $ips[] = $ip;
    // Only when asked for: switched on here, the public site must show the
    // maintenance page to the one who switched it on too, or it looks as if
    // nothing happened. /admin stays reachable either way.
    if ( $http->hasPostVariable( 'MaintenanceAllowMe' ) )
        $ips[] = eZSys::clientIP();
    $ok = expMaintenance::enable( $root, array(
        'reason' => 'manual',
        'message' => trim( mb_substr( (string)$http->postVariable( 'MaintenanceMessage', '' ), 0, 500 ) ),
        'until' => $until,
        'allow_ips' => array_values( array_unique( array_filter( $ips ) ) ),
        'allow_paths' => array( '/admin' ),
        'by' => eZUser::currentUser()->attribute( 'login' ),
    ) );
    $feedback = $ok ? array( true, 'Maintenance is on: visitors see the maintenance page.' )
                    : array( false, 'Maintenance could not be switched on: ' . expMaintenance::MARKER . ' is not writable.' );
    eZDebug::writeNotice( $feedback[1], 'setup/maintenance' );
}
elseif ( $module->isCurrentAction( 'SwitchOff' ) )
{
    $ok = expMaintenance::disable( $root );
    if ( $ok )
        expMaintenance::clearResponseCaches( $root );
    $feedback = $ok ? array( true, 'Maintenance is off: the site answers again.' )
                    : array( false, 'Maintenance was not on.' );
    eZDebug::writeNotice( $feedback[1], 'setup/maintenance' );
}

$state = expMaintenance::state( $root );
$tpl = eZTemplate::factory();
$tpl->setVariable( 'maintenance_on', $state !== false );
$tpl->setVariable( 'maintenance', $state ? $state : array() );
$tpl->setVariable( 'feedback', $feedback );
$tpl->setVariable( 'client_ip', eZSys::clientIP() );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/maintenance.tpl' );
$Result['path'] = array( array( 'url' => false, 'text' => ezpI18n::tr( 'kernel/setup', 'Maintenance' ) ) );
?>
