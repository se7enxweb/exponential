<?php
/**
 * The contentfinder/action view (doc/features/6.0/content-finder.md, "Actions"): POST only. The result page posts
 * Action (trash, delete, purge, removeversion), ObjectID and, for a version, VersionID; this view answers with the
 * confirmation page, which shows exactly what will happen (expContentFinderAction::plan()) and posts back with
 * ConfirmButton, the confirmation token and, for what cannot be undone, ConfirmPermanent. Then the action runs and
 * the browser goes back to the finder with the outcome.
 *
 * Needs contentfinder/search and contentfinder/delete (the module's view functions) and the content policies of
 * each action; refused without the form tokens, with another method than POST (405), and on a siteaccess that is
 * not an administration one (404). Every refusal and every action is recorded (content.finder.action).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Contentfinder
{

class Action extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $module = $Params['Module'];
        if ( !\expContentFinder::isAvailableHere() )
            return $module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? (string)$_SERVER['REQUEST_METHOD'] : '';
        $decision = self::decide( $method, $_POST );
        $http = \eZHTTPTool::instance();

        switch ( $decision['step'] )
        {
            case 'refused':
                \expContentFinderAction::auditRefusal( self::emptyPlan( $decision ), $decision['reason'] );
                if ( !headers_sent() )
                {
                    http_response_code( $decision['reason'] === 'method' ? 405 : 403 );
                    if ( $decision['reason'] === 'method' )
                        header( 'Allow: POST' );
                }
                return $this->page( 'design:contentfinder/refused.tpl', array( 'reason' => $decision['reason'] ) );

            case 'cancel':
                return $module->redirectTo( self::searchURI( $decision['object_id'] ) );

            case 'execute':
                $outcome = \expContentFinderAction::execute( $decision['action'], $decision['object_id'], $decision['version_id'],
                                                             isset( $_POST[\expContentFinderAction::CONFIRM_FIELD] ) ? $_POST[\expContentFinderAction::CONFIRM_FIELD] : '' );
                if ( !$outcome['done'] )
                    \expContentFinderAction::auditRefusal( $outcome['plan'], $outcome['reason'] );
                $http->setSessionVariable( Search::FEEDBACK, array( 'action' => $decision['action'], 'done' => $outcome['done'],
                                                                    'reason' => $outcome['reason'], 'object_id' => $decision['object_id'],
                                                                    'name' => (string)$outcome['plan']['name'],
                                                                    'version' => $outcome['plan']['version'] ? (int)$outcome['plan']['version']['version'] : 0 ) );
                $gone = $outcome['done'] && in_array( $decision['action'], array( 'delete', 'purge' ), true );
                return $module->redirectTo( $gone ? '/contentfinder/search' : self::searchURI( $decision['object_id'] ) );

            case 'confirm':
            default:
                $plan = \expContentFinderAction::plan( $decision['action'], $decision['object_id'], $decision['version_id'] );
                if ( !$plan['allowed'] )
                    \expContentFinderAction::auditRefusal( $plan, $plan['reason'] );
                return $this->page( 'design:contentfinder/confirm.tpl',
                                    array( 'plan' => $plan, 'reason' => $decision['reason'],
                                           'confirm_token' => $plan['allowed'] ? \expContentFinderAction::confirmToken( $plan ) : '',
                                           'confirm_field' => \expContentFinderAction::CONFIRM_FIELD,
                                           'form_token' => \expContentFinderAction::formToken(),
                                           'token_field' => \expContentFinderAction::TOKEN_FIELD,
                                           'restore_allowed' => \expContentFinder::hasPolicy( 'content', 'restore' ) ) );
        }
    }

    /**
     * What a request to the action view leads to, from its method and its posted fields only.
     *
     * @param string $method the request method
     * @param array $post the posted fields
     * @return array array( 'step' => refused | cancel | confirm | execute, 'reason' => '' | method | token |
     *               no_policy | unknown_action | not_found | confirm_permanent, 'action', 'object_id', 'version_id' )
     */
    public static function decide( $method, array $post )
    {
        $decision = array( 'step' => 'refused', 'reason' => '', 'action' => '', 'object_id' => 0, 'version_id' => 0 );
        if ( strtoupper( (string)$method ) !== 'POST' )
            return array( 'reason' => 'method' ) + $decision;
        if ( !\expContentFinderAction::checkEzFormToken( $post )
             || !\expContentFinderAction::checkFormToken( isset( $post[\expContentFinderAction::TOKEN_FIELD] ) ? $post[\expContentFinderAction::TOKEN_FIELD] : null ) )
            return array( 'reason' => 'token' ) + $decision;
        if ( !\expContentFinder::hasPolicy( 'contentfinder', 'delete' ) )
            return array( 'reason' => 'no_policy' ) + $decision;
        $action = isset( $post['Action'] ) && is_string( $post['Action'] ) ? $post['Action'] : '';
        if ( !in_array( $action, \expContentFinderAction::ACTIONS, true ) )
            return array( 'reason' => 'unknown_action' ) + $decision;
        $objectID = \expContentFinderInput::id( isset( $post['ObjectID'] ) ? $post['ObjectID'] : '' );
        if ( $objectID === false )
            return array( 'reason' => 'not_found', 'action' => $action ) + $decision;
        $versionID = \expContentFinderInput::id( isset( $post['VersionID'] ) ? $post['VersionID'] : '' );
        $decision = array( 'action' => $action, 'object_id' => $objectID, 'version_id' => $versionID === false ? 0 : $versionID ) + $decision;
        if ( isset( $post['CancelButton'] ) )
            return array( 'step' => 'cancel' ) + $decision;
        if ( isset( $post['ConfirmButton'] ) )
        {
            if ( in_array( $action, \expContentFinderAction::PERMANENT, true ) && empty( $post['ConfirmPermanent'] ) )
                return array( 'step' => 'confirm', 'reason' => 'confirm_permanent' ) + $decision;
            return array( 'step' => 'execute' ) + $decision;
        }
        return array( 'step' => 'confirm' ) + $decision;
    }

    /**
     * @param array $decision
     * @return array the plan of a refusal that never got to plan(), for the audit
     */
    protected static function emptyPlan( array $decision )
    {
        return array( 'action' => (string)$decision['action'], 'object_id' => (int)$decision['object_id'], 'nodes' => array(),
                      'trash' => array(), 'version' => null );
    }

    /**
     * @param int $objectID
     * @return string
     */
    public static function searchURI( $objectID )
    {
        return '/contentfinder/search?SearchType=object&SearchText=' . (int)$objectID;
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
                      'path' => array( array( 'text' => \ezpI18n::tr( 'kernel/contentfinder', 'Setup' ), 'url' => 'setup/menu' ),
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentfinder', 'Content finder' ), 'url' => 'contentfinder/search' ),
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentfinder', 'Confirm' ), 'url' => false ) ) );
    }
}

}
