<?php
/**
 * The staticpublish/dashboard view: Setup > Static publishing. The targets the user may read, each with its
 * siteaccess, host, last publish, stale pages, a build not published yet, the size against the host's limit and the
 * job going on, with the buttons the user's policies allow (Build, Preview, History, Settings). Without a target an
 * invitation to the wizard and the tutorial. The switch of the feature as a whole (staticcache.ini
 * [StaticPublishSettings] Enabled, written through expIniEditor) for users with staticpublish/setup on every target; a
 * POST that asks "Verify it's you". Policy staticpublish/read.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Staticpublish
{

class Dashboard extends Page
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $messages = array();
        $canSetup = \expStaticAdmin::canDoAll( 'setup' );

        if ( self::isPost() && isset( $_POST['SwitchButton'] ) )
        {
            if ( !$canSetup )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            $on = isset( $_POST['SwitchTo'] ) && $_POST['SwitchTo'] === 'on';
            $problem = \expStaticAdmin::setEnabled( $on );
            $messages[] = $problem === ''
                ? array( 'kind' => 'ok', 'text' => $on ? self::t( 'Static publishing is switched on.' ) : self::t( 'Static publishing is switched off.' ) )
                : array( 'kind' => 'bad', 'text' => self::t( 'The setting could not be written: %problem', array( '%problem' => $problem ) ) );
        }

        $targets = array();
        $problem = '';
        try
        {
            foreach ( \expStaticAdmin::readableTargets() as $target )
                $targets[] = \expStaticAdmin::overview( $target );
        }
        catch ( \Throwable $e )
        {
            $problem = $e->getMessage();
        }

        return self::page( 'dashboard.tpl', array(
            'enabled' => \expStaticTarget::enabled(),
            'targets' => $targets,
            'can_setup' => $canSetup,
            'messages' => $messages,
            'problem' => $problem,
            'site_limit' => \expStaticAdmin::siteLimit(),
        ), self::path() );
    }
}

}
?>
