<?php
/**
 * The migration/deploy view: the form that deploys this installation's migrations to a selection of remotes (ticked
 * one by one, or by group), and the history of the deploys from their stored reports
 * (var/<site>/migration/deploy/reports/, expMigrationRemoteDeploy::history()).
 *
 * Preflight (PreflightButton) starts a dry run in the background: the preflight on every selected remote and the plan,
 * nothing sent or changed. Deploy (DeployButton, "Yes, deploy now" ticked, "Verify it's you") starts the deploy: all at
 * once, or staged (the first stage, then a gate before each next one), or transfer only (the files sent and verified,
 * nothing run). Each runs as the signed-in user in a console process of its own (expMigrationJob kind deploy:
 * exp:migration:deploy) and opens its page, migration/deployment/<deploy id>, which follows it live. Policy
 * migration/read for the page; starting needs migration/deploy.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Migration
{

class Deploy extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Module = $scope['Params']['Module'];
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        $post = $isPost ? $_POST : array();
        $canDeploy = \expMigrationAdmin::canDo( 'deploy' );
        $remotes = \expMigrationRemote::all();
        $messages = array();
        $form = array( 'remotes' => array(), 'groups' => array(), 'mode' => 'apply', 'staged' => false, 'only' => '' );
        $login = (string)\eZUser::currentUser()->attribute( 'login' );

        if ( $isPost && ( isset( $post['PreflightButton'] ) || isset( $post['DeployButton'] ) ) )
        {
            if ( !$canDeploy )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            $preflight = isset( $post['PreflightButton'] );
            $errors = array();
            $options = \expMigrationRemoteAdmin::deployOptions( $post, $remotes, $preflight, $errors );
            $form = array( 'remotes' => isset( $post['DeployRemote'] ) ? array_map( 'strval', (array)$post['DeployRemote'] ) : array(),
                           'groups' => isset( $post['DeployGroup'] ) ? array_map( 'strval', (array)$post['DeployGroup'] ) : array(),
                           'mode' => $options['mode'], 'staged' => $options['staged_choice'], 'only' => implode( ', ', $options['only'] ) );
            foreach ( array_unique( $errors ) as $code )
                $messages[] = array( 'kind' => 'bad', 'text' => self::formError( $code ) );
            if ( !$errors )
            {
                $started = \expMigrationJob::start( 'deploy', array( 'remotes' => $options['remotes'], 'only' => $options['only'], 'apply' => $options['apply'],
                                                                     'transfer-only' => $options['transfer-only'], 'staged' => $options['staged'] ), $login );
                if ( $started['ok'] )
                    return $Module->redirectTo( '/migration/deployment/' . $started['id'] );
                $messages[] = array( 'kind' => 'bad', 'text' => Run::refusal( $started ) );
            }
        }

        $running = \expMigrationJob::running();
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'remotes', \expMigrationRemoteAdmin::overview( $remotes ) );
        $tpl->setVariable( 'groups', \expMigrationRemoteAdmin::groups( $remotes ) );
        $history = array();
        foreach ( \expMigrationRemoteDeploy::history( 30 ) as $h )
            $history[] = $h + array( 'kind' => \expMigrationRemoteAdmin::stateKind( $h['state'] ), 'started_ts' => (int)strtotime( (string)$h['started_at'] ), 'out_of_sync' => $h['in_sync'] === false );
        $tpl->setVariable( 'history', $history );
        $tpl->setVariable( 'form', $form );
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'job', $running );
        $tpl->setVariable( 'login', $login );
        $tpl->setVariable( 'can_deploy', $canDeploy );
        $tpl->setVariable( 'can_edit', \expMigrationAdmin::canDo( 'remotes' ) );
        $tpl->setVariable( 'can_run', \expMigrationAdmin::canDo( 'run' ) );
        $tpl->setVariable( 'can_rollback', \expMigrationAdmin::canDo( 'rollback' ) );
        $tpl->setVariable( 'view', 'deploy' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:migration/deploy.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Setup' ) ),
                                 array( 'url' => 'migration/status', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Migrations' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/migration', 'Deploys' ) ) );
        return $Result;
    }

    /**
     * The message of a problem with the form.
     *
     * @param string $code expMigrationRemoteAdmin::deployOptions()
     * @return string
     */
    public static function formError( $code )
    {
        switch ( $code )
        {
            case 'remotes':
                return \expMigrationRemoteAdmin::tr( 'Choose at least one remote or group.' );
            case 'unknown':
                return \expMigrationRemoteAdmin::tr( 'A chosen remote is no longer in the settings. Reload the page.' );
            case 'confirm':
                return \expMigrationRemoteAdmin::tr( 'Tick "Yes, deploy now" first.' );
            case 'ftp':
                return \expMigrationRemoteAdmin::tr( 'An FTP remote cannot run migrations: choose "Transfer only", or leave it out.' );
            case 'only':
                return \expMigrationRemoteAdmin::tr( 'The migrations to deploy are migration ids, separated by commas.' );
        }
        return \ezpI18n::tr( 'design/admin/migration', 'The form could not be read.' );
    }
}

}
?>
