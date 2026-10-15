<?php
/**
 * The contentcheck/trash view (doc/features/6.0/content-checks.md, "Moving to the trash"): POST only. The list of
 * unreferenced objects posts ObjectIDList[] and NodeID; this view answers with the confirmation page, which shows
 * what will happen to each object (expContentCheckTrash::plan()) and posts back with ConfirmButton and the
 * confirmation token. Then the objects go to the trash through the kernel's removal and the browser goes back to
 * the list with the outcome.
 *
 * Needs contentcheck/unreferenced and contentcheck/trash (the module's view functions) and content/remove on every
 * object; refused without the form tokens (403), with another method than POST (405), and on a siteaccess that is
 * not an administration one (404). Each object moved is recorded in the audit by the kernel's removal
 * (content.node.remove.trash), as any removal to the trash.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Contentcheck
{

class Trash extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $module = $Params['Module'];
        if ( !\expContentCheck::isAvailableHere() )
            return $module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? (string)$_SERVER['REQUEST_METHOD'] : '';
        $decision = self::decide( $method, $_POST );
        $back = '/contentcheck/unreferenced/' . (int)$decision['node_id'];

        switch ( $decision['step'] )
        {
            case 'refused':
                if ( !headers_sent() )
                {
                    http_response_code( $decision['reason'] === 'method' ? 405 : 403 );
                    if ( $decision['reason'] === 'method' )
                        header( 'Allow: POST' );
                }
                return $this->page( 'design:contentcheck/refused.tpl', array( 'reason' => $decision['reason'] ) );

            case 'cancel':
                return $module->redirectTo( $back );

            case 'execute':
                $outcome = \expContentCheckTrash::execute( $decision['object_ids'],
                                                           isset( $_POST[\expContentCheck::CONFIRM_FIELD] ) ? $_POST[\expContentCheck::CONFIRM_FIELD] : '' );
                \eZHTTPTool::instance()->setSessionVariable( Unreferenced::FEEDBACK,
                                                             array( 'done' => $outcome['done'], 'reason' => $outcome['reason'],
                                                                    'moved' => count( $outcome['moved'] ), 'failed' => count( $outcome['failed'] ) ) );
                return $module->redirectTo( $back );

            case 'confirm':
            default:
                $plan = \expContentCheckTrash::plan( $decision['object_ids'] );
                return $this->page( 'design:contentcheck/confirm.tpl',
                                    array( 'plan' => $plan, 'node_id' => (int)$decision['node_id'],
                                           'nothing_selected' => !$decision['object_ids'],
                                           'confirm_token' => $plan['allowed'] ? \expContentCheck::confirmToken( $plan['fingerprint'] ) : '',
                                           'confirm_field' => \expContentCheck::CONFIRM_FIELD,
                                           'form_token' => \expContentCheck::formToken(),
                                           'token_field' => \expContentCheck::TOKEN_FIELD,
                                           'batch_limit' => \expContentCheckTrash::batchLimit() ) );
        }
    }

    /**
     * What a request to the view leads to, from its method and its posted fields only.
     *
     * @param string $method the request method
     * @param array $post the posted fields
     * @return array array( 'step' => refused|cancel|confirm|execute, 'reason' => '' | method | token,
     *               'object_ids' => int[], 'node_id' => int )
     */
    public static function decide( $method, array $post )
    {
        $decision = array( 'step' => 'refused', 'reason' => '', 'object_ids' => array(),
                           'node_id' => \expContentCheck::id( isset( $post['NodeID'] ) ? $post['NodeID'] : null, Unreferenced::rootNodeID() ) );
        if ( strtoupper( (string)$method ) !== 'POST' )
            return array_merge( $decision, array( 'reason' => 'method' ) );
        if ( !\expContentCheck::matches( isset( $post[\expContentCheck::TOKEN_FIELD] ) ? $post[\expContentCheck::TOKEN_FIELD] : null, \expContentCheck::formToken() )
             || !\expContentCheck::checkEzFormToken( $post ) )
            return array_merge( $decision, array( 'reason' => 'token' ) );
        if ( isset( $post['CancelButton'] ) )
            return array_merge( $decision, array( 'step' => 'cancel' ) );
        $decision['object_ids'] = \expContentCheck::idList( isset( $post['ObjectIDList'] ) && is_array( $post['ObjectIDList'] ) ? $post['ObjectIDList'] : array(),
                                                           \expContentCheckTrash::batchLimit() + 1 );
        $decision['step'] = isset( $post['ConfirmButton'] ) && $decision['object_ids'] ? 'execute' : 'confirm';
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
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Unreferenced objects' ), 'url' => 'contentcheck/unreferenced' ),
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Move to the trash' ), 'url' => false ) ) );
    }
}

}
