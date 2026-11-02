<?php
/**
 * The migration/remotes view: the remotes this installation deploys its migrations to (migration.ini
 * [RemoteSettings] RemoteList[], one [Remote_<id>] each) with their environment, transport, address, groups and
 * credential (its kind and name, never a value), what is wrong with a remote's settings, and "Test connection" (a POST
 * of TestButton with TestRemoteID): the remote's migration status read once (expMigrationRemoteAdmin::testConnection()),
 * nothing changed. Policy migration/read; testing, adding and changing need migration/remotes.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Migration
{

class Remotes extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Module = $scope['Params']['Module'];
        $http = \eZHTTPTool::instance();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        $canEdit = \expMigrationAdmin::canDo( 'remotes' );
        $remotes = \expMigrationRemote::all();
        $test = null;
        $tested = '';
        $messages = array();
        if ( $isPost && $http->hasPostVariable( 'TestButton' ) )
        {
            $tested = trim( (string)$http->postVariable( 'TestRemoteID', '' ) );
            if ( !$canEdit )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            if ( !isset( $remotes[$tested] ) )
                $messages[] = array( 'kind' => 'bad', 'text' => \expMigrationRemoteAdmin::tr( 'Choose the remote to test.' ) );
            else
                $test = \expMigrationRemoteAdmin::testConnection( $remotes[$tested] );
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'remotes', \expMigrationRemoteAdmin::overview( $remotes ) );
        $tpl->setVariable( 'groups', \expMigrationRemoteAdmin::groups( $remotes ) );
        $tpl->setVariable( 'settings', \expMigrationRemote::settings() );
        $tpl->setVariable( 'test', $test );
        $tpl->setVariable( 'tested', $tested );
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'can_edit', $canEdit );
        $tpl->setVariable( 'can_deploy', \expMigrationAdmin::canDo( 'deploy' ) );
        $tpl->setVariable( 'can_run', \expMigrationAdmin::canDo( 'run' ) );
        $tpl->setVariable( 'can_rollback', \expMigrationAdmin::canDo( 'rollback' ) );
        $tpl->setVariable( 'view', 'remotes' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:migration/remotes.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Setup' ) ),
                                 array( 'url' => 'migration/status', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Migrations' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/migration', 'Remotes' ) ) );
        return $Result;
    }
}

}
?>
