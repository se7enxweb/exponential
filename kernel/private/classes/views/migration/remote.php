<?php
/**
 * The migration/remote view (/migration/remote/new and /migration/remote/<id>): the wizard that adds or changes a
 * remote, in steps on one page:
 *
 *   1. the remote: its id (new only), environment and groups
 *   2. the connection: transport (rsync, ssh with scp, sftp, ftp, ftps, local), host, port, user, the installation's
 *      path there, the PHP command line there, whether its console scripts run as root
 *   3. the credential: none (the host's SSH agent and keys), an SSH key or a password, named in Setup > Secrets or as
 *      an environment variable (env:VARIABLE); never a value
 *   4. the host key: the fingerprint the host must show (optional)
 *   5. review: Review checks the settings (expMigrationRemoteAdmin::check()) and shows the lines that will be written
 *      to settings/override/migration.ini.append.php; Save writes them through expIniEditor (backup, audit
 *      system.migration.remote) and asks "Verify it's you"
 *
 * Test connection (TestButton) tries the settings as entered, without saving them. Remove (RemoveButton, a saved
 * remote, ConfirmRemove ticked) takes the remote out of the override, and asks "Verify it's you". Policy
 * migration/remotes.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Migration
{

class Remote extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        $post = $isPost ? $_POST : array();
        $param = isset( $Params['RemoteID'] ) ? trim( rawurldecode( (string)$Params['RemoteID'] ) ) : 'new';
        $isNew = $param === '' || $param === 'new';
        $all = \expMigrationRemote::all();
        if ( !$isNew && !isset( $all[$param] ) )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $id = $isNew ? '' : $param;
        $settings = $isNew ? \expMigrationRemoteAdmin::newSettings() : \expMigrationRemoteAdmin::settingsOf( $all[$id] );

        $errors = array();
        $warnings = array();
        $messages = array();
        $review = false;
        $diff = '';
        $test = null;
        $read = function () use ( $post, $isNew, &$id, &$settings ) {
            if ( $isNew )
                $id = isset( $post['RemoteIdentifier'] ) && is_scalar( $post['RemoteIdentifier'] ) ? trim( (string)$post['RemoteIdentifier'] ) : '';
            $settings = \expMigrationRemoteAdmin::settingsFromPost( $post );
        };

        if ( $isPost && isset( $post['RemoveButton'] ) && !$isNew )
        {
            if ( empty( $post['ConfirmRemove'] ) )
                $messages[] = array( 'kind' => 'bad', 'text' => \expMigrationRemoteAdmin::tr( 'Tick "Yes, remove this remote" first.' ) );
            else
            {
                $problem = \expMigrationRemoteAdmin::remove( $id );
                if ( $problem === '' )
                    return $Module->redirectTo( '/migration/remotes' );
                $messages[] = array( 'kind' => 'bad', 'text' => \expMigrationRemoteAdmin::tr( 'The remote could not be removed: %problem', array( '%problem' => $problem ) ) );
            }
        }
        else if ( $isPost && isset( $post['EditButton'] ) )
            $read();
        else if ( $isPost && isset( $post['TestButton'] ) )
        {
            $read();
            $check = \expMigrationRemoteAdmin::check( $id !== '' ? $id : 'test', $settings, array_keys( $all ), false );
            $errors = $check['errors'];
            if ( !$errors )
                $test = \expMigrationRemoteAdmin::testConnection( new \expMigrationRemote( $id !== '' ? $id : 'test', \expMigrationRemoteAdmin::iniSettings( $settings ) ) );
        }
        else if ( $isPost && ( isset( $post['ReviewButton'] ) || isset( $post['SaveButton'] ) ) )
        {
            $read();
            $check = \expMigrationRemoteAdmin::check( $id, $settings, array_map( 'strval', array_keys( $all ) ), $isNew );
            $errors = $check['errors'];
            $warnings = $check['warnings'];
            if ( !$errors && isset( $post['SaveButton'] ) )
            {
                $problem = \expMigrationRemoteAdmin::save( $id, $settings, $isNew );
                if ( $problem === '' )
                    return $Module->redirectTo( '/migration/remotes' );
                $messages[] = array( 'kind' => 'bad', 'text' => \expMigrationRemoteAdmin::tr( 'The remote could not be saved: %problem', array( '%problem' => $problem ) ) );
            }
            else if ( !$errors )
            {
                $review = true;
                try
                {
                    $diff = \expMigrationRemoteAdmin::editor( $id, $settings )->diff();
                }
                catch ( \Throwable $e )
                {
                    $messages[] = array( 'kind' => 'bad', 'text' => \expMigrationRemoteAdmin::tr( 'The settings file cannot be read: %problem', array( '%problem' => $e->getMessage() ) ) );
                    $review = false;
                }
            }
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'is_new', $isNew );
        $tpl->setVariable( 'id', $id );
        $tpl->setVariable( 'settings', $settings );
        $tpl->setVariable( 'groups_text', implode( ', ', $settings['Groups'] ) );
        $tpl->setVariable( 'credential', \expMigrationRemoteAdmin::credentialChoice( $settings['Credential'] ) );
        $tpl->setVariable( 'transports', array( 'rsync', 'ssh', 'sftp', 'ftp', 'ftps', 'local' ) );
        $tpl->setVariable( 'environments', \expMigrationRemoteAdmin::ENVIRONMENTS );
        $tpl->setVariable( 'secret_names', \expMigrationRemoteAdmin::secretNames() );
        $tpl->setVariable( 'errors', $errors );
        $tpl->setVariable( 'warnings', $warnings );
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'review', $review );
        $tpl->setVariable( 'diff', $diff );
        $tpl->setVariable( 'test', $test );
        $tpl->setVariable( 'can_run', \expMigrationAdmin::canDo( 'run' ) );
        $tpl->setVariable( 'can_rollback', \expMigrationAdmin::canDo( 'rollback' ) );
        $tpl->setVariable( 'view', 'remotes' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:migration/remote.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Setup' ) ),
                                 array( 'url' => 'migration/status', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Migrations' ) ),
                                 array( 'url' => 'migration/remotes', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Remotes' ) ),
                                 array( 'url' => false, 'text' => $isNew ? \ezpI18n::tr( 'design/admin/migration', 'New remote' ) : $id ) );
        return $Result;
    }
}

}
?>
