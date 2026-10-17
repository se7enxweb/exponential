<?php
/**
 * The contentcheck/storageaction view (doc/features/6.0/content-checks.md, "Storage files"): POST only, from the
 * forms of contentcheck/storage.
 *
 *   ScanButton / ContinueButton       one step of the scan (contentcheck/storage), then the progress page or the list
 *   Action=quarantine|restore|delete|permissions with Items[]
 *                                     the plan (the dry run: what would happen to each file, nothing changed) and a
 *                                     confirmation; with ConfirmButton and the confirmation token the action is done,
 *                                     each file checked again (expContentCheckQuarantine), and the browser goes back
 *                                     to the list with the outcome. Deleting from the quarantine also needs the box
 *                                     ConfirmDelete ticked.
 *
 * Refused without the form tokens (403), with another method than POST (405), without the action's policy (403:
 * quarantine, restore and delete need contentcheck/quarantine, permissions contentcheck/permissions; everything
 * contentcheck/storage), and on a siteaccess that is not an administration one (404).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Contentcheck
{

class Storageaction extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $module = $Params['Module'];
        if ( !\expContentCheck::isAvailableHere() )
            return $module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? (string)$_SERVER['REQUEST_METHOD'] : '';
        $decision = self::decide( $method, $_POST, function ( $function ) { return \expContentCheck::hasPolicy( 'contentcheck', $function ); } );
        $back = '/contentcheck/storage/' . (int)$decision['node_id'] . '?Show=' . rawurlencode( $decision['show'] );

        switch ( $decision['step'] )
        {
            case 'refused':
                if ( !headers_sent() )
                {
                    http_response_code( $decision['reason'] === 'method' ? 405 : 403 );
                    if ( $decision['reason'] === 'method' )
                        header( 'Allow: POST' );
                }
                if ( class_exists( 'expAudit' ) && $decision['reason'] === 'policy' )
                    \expAudit::event( 'content.file.orphan.refused', array( 'object' => array( 'type' => 'view', 'id' => 'contentcheck/storageaction' ),
                                                                           'reason' => 'policy', 'after' => array( 'action' => $decision['action'] ),
                                                                           'result' => 'denied' ) );
                return $this->page( 'design:contentcheck/storage_refused.tpl', array( 'reason' => $decision['reason'], 'node_id' => (int)$decision['node_id'] ) );

            case 'cancel':
                return $module->redirectTo( $back );

            case 'scan':
                $state = \expContentCheckStorage::lastScan();
                if ( $decision['restart'] || !$state || $state['phase'] === 'done' )
                    $state = null;
                $state = \expContentCheckStorage::scanStep( $state, (float)\expContentCheck::intSetting( 'StorageSettings', 'StepSeconds', 4, 1, 25 ) );
                if ( $state['phase'] === 'done' )
                    return $module->redirectTo( $back );
                return $this->page( 'design:contentcheck/storage_progress.tpl',
                                    array( 'state' => $state, 'percent' => \expContentCheckStorage::percent( $state ), 'node_id' => (int)$decision['node_id'],
                                           'show' => $decision['show'], 'form_token' => \expContentCheck::formToken(),
                                           'token_field' => \expContentCheck::TOKEN_FIELD ) );

            case 'execute':
                $outcome = \expContentCheckQuarantine::execute( $decision['action'], $decision['items'],
                                                                isset( $_POST[\expContentCheck::CONFIRM_FIELD] ) ? $_POST[\expContentCheck::CONFIRM_FIELD] : '' );
                \eZHTTPTool::instance()->setSessionVariable( Storage::FEEDBACK,
                                                             array( 'action' => $decision['action'], 'done' => $outcome['done'], 'reason' => $outcome['reason'],
                                                                    'count' => $outcome['count'], 'bytes' => $outcome['bytes'], 'failed' => $outcome['failed'] ) );
                return $module->redirectTo( $back );

            case 'confirm':
            default:
                $plan = \expContentCheckQuarantine::plan( $decision['action'], $decision['items'] );
                return $this->page( 'design:contentcheck/storage_confirm.tpl',
                                    array( 'plan' => $plan, 'node_id' => (int)$decision['node_id'], 'show' => $decision['show'],
                                           'nothing_selected' => !$decision['items'], 'needs_delete_box' => $decision['needs_delete_box'],
                                           'confirm_token' => $plan['allowed'] ? \expContentCheck::confirmToken( $plan['fingerprint'] ) : '',
                                           'confirm_field' => \expContentCheck::CONFIRM_FIELD,
                                           'form_token' => \expContentCheck::formToken(), 'token_field' => \expContentCheck::TOKEN_FIELD,
                                           'batch_limit' => \expContentCheckQuarantine::batchLimit(),
                                           'quarantine_root' => \expContentCheckQuarantine::root() ) );
        }
    }

    /**
     * What a request leads to, from its method, its posted fields and the user's policies only.
     *
     * @param string $method
     * @param array $post
     * @param callable $hasPolicy function( string $function ): bool for the module contentcheck
     * @return array array( 'step' => refused|cancel|scan|confirm|execute, 'reason' => '' | method | token | policy,
     *               'action' => string, 'items' => string[], 'node_id' => int, 'show' => string, 'restart' => bool,
     *               'needs_delete_box' => bool )
     */
    public static function decide( $method, array $post, $hasPolicy )
    {
        $show = isset( $post['Show'] ) && in_array( $post['Show'], Storage::SHOWS, true ) ? $post['Show'] : 'orphans';
        $decision = array( 'step' => 'refused', 'reason' => '', 'action' => '', 'items' => array(), 'restart' => false,
                           'node_id' => \expContentCheck::id( isset( $post['NodeID'] ) ? $post['NodeID'] : null, \expContentCheckScope::rootNodeID() ),
                           'show' => $show, 'needs_delete_box' => false );
        if ( strtoupper( (string)$method ) !== 'POST' )
            return array_merge( $decision, array( 'reason' => 'method' ) );
        if ( !\expContentCheck::matches( isset( $post[\expContentCheck::TOKEN_FIELD] ) ? $post[\expContentCheck::TOKEN_FIELD] : null, \expContentCheck::formToken() )
             || !\expContentCheck::checkEzFormToken( $post ) )
            return array_merge( $decision, array( 'reason' => 'token' ) );
        if ( !call_user_func( $hasPolicy, 'storage' ) )
            return array_merge( $decision, array( 'reason' => 'policy' ) );
        if ( isset( $post['CancelButton'] ) )
            return array_merge( $decision, array( 'step' => 'cancel' ) );
        if ( isset( $post['ScanButton'] ) || isset( $post['ContinueButton'] ) || isset( $post['RestartButton'] ) )
            return array_merge( $decision, array( 'step' => 'scan', 'restart' => isset( $post['ScanButton'] ) || isset( $post['RestartButton'] ) ) );

        $action = isset( $post['Action'] ) && is_string( $post['Action'] ) && in_array( $post['Action'], \expContentCheckQuarantine::ACTIONS, true ) ? $post['Action'] : '';
        if ( $action === '' )
            return array_merge( $decision, array( 'step' => 'cancel' ) );
        $decision['action'] = $action;
        if ( !call_user_func( $hasPolicy, \expContentCheckQuarantine::POLICIES[$action] ) )
            return array_merge( $decision, array( 'reason' => 'policy' ) );
        $decision['items'] = \expContentCheckQuarantine::cleanList( $action, isset( $post['Items'] ) ? $post['Items'] : array() );
        $decision['needs_delete_box'] = $action === 'delete';
        $confirmed = isset( $post['ConfirmButton'] ) && $decision['items']
                     && ( $action !== 'delete' || ( isset( $post['ConfirmDelete'] ) && $post['ConfirmDelete'] === '1' ) );
        $decision['step'] = $confirmed ? 'execute' : 'confirm';
        return $decision;
    }

    /**
     * @param string $template
     * @param array $variables
     * @return array the view's result
     */
    protected function page( $template, array $variables )
    {
        $tpl = \eZTemplate::factory();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        return array( 'content' => $tpl->fetch( $template ),
                      'path' => array( array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Setup' ), 'url' => 'setup/menu' ),
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Storage files' ), 'url' => 'contentcheck/storage' ),
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Confirm' ), 'url' => false ) ) );
    }
}

}
