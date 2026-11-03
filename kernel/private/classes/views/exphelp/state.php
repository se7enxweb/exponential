<?php
/**
 * exphelp/state/<help_id> and exphelp/state/manual/<remote_id> (doc/eng-us/features/6.0/help-content.md, "The review
 * workflow"): one step of Draft -> Review -> Released for a help topic or manual, from the buttons of exphelp/topic and
 * exphelp/manual. POST only, with the state to go to in HelpState (review, released, draft); the form token of the
 * kernel protects it like every POST.
 *
 * A step back (Review from Released, which hides the page from readers; Draft from Review) is not taken at once: the
 * view shows a confirmation page (design:exphelp/state_confirm.tpl) whose form posts the same HelpState again with
 * HelpStateConfirm, or HelpStateCancel to go back unchanged. decide() is that rule, without a database.
 *
 * The step goes through expHelpContentWorkflow::assign(): the user's state/assign policies decide (the roles "Help
 * editor" and "Help release"), and the author of the current version does not release it. Back to the topic with
 * (step)/<state> when it was done, (step)/refused-author or (step)/refused when not. A topic the user may not see is
 * a 404, as in exphelp/topic.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class State extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expHelp::isEnabled() || !\expHelpContentProvider::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $id = \expHelp::requestedId( $Params );
        $provider = \expHelpContentProvider::instance();
        $isManual = strpos( $id, 'manual/' ) === 0;
        $item = $isManual ? $provider->manual( substr( $id, 7 ) ) : ( $id !== '' ? $provider->topic( $id ) : null );
        if ( $item === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $back = $isManual ? 'exphelp/manual/' . $item['remote_id'] : 'exphelp/topic/' . $item['help_id'];

        $http = \eZHTTPTool::instance();
        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string)$_SERVER['REQUEST_METHOD'] ) : 'GET';
        $object = $method === 'POST' ? $provider->repository()->object( (int)$item['object_id'] ) : null;
        if ( $method === 'POST' && !$object instanceof \eZContentObject )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $target = $http->hasPostVariable( 'HelpState' ) ? (string)$http->postVariable( 'HelpState' ) : '';
        $current = $object instanceof \eZContentObject ? \expHelpContentWorkflow::stateOf( $object ) : '';
        $decision = self::decide( $method, $target, $current, $http->hasPostVariable( 'HelpStateConfirm' ), $http->hasPostVariable( 'HelpStateCancel' ) );
        if ( $decision === 'back' )
            return $Module->redirectTo( $back );

        if ( $decision === 'confirm' )
        {
            $title = $item['title'] !== '' ? $item['title'] : ( $isManual ? $item['remote_id'] : $item['help_id'] );
            $tpl = \eZTemplate::factory();
            $tpl->setVariable( 'help_title', $title );
            $tpl->setVariable( 'help_current', $current );
            $tpl->setVariable( 'help_target', $target );
            $tpl->setVariable( 'help_hides', \expHelpContentWorkflow::hidesFromReaders( $current, $target ) );
            $tpl->setVariable( 'help_state_url', 'exphelp/state/' . $id );
            $tpl->setVariable( 'help_back_url', $back );
            $Result = array();
            $Result['content'] = $tpl->fetch( 'design:exphelp/state_confirm.tpl' );
            $Result['path'] = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ),
                                     array( 'url' => $back, 'text' => $title ),
                                     array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Review and release' ) ) );
            return $Result;
        }

        $result = \expHelpContentWorkflow::assign( $object, $target );
        \expHelpContentProvider::resetInstance();
        $step = $result === '' ? $target : ( $result === 'author' ? 'refused-author' : 'refused' );
        return $Module->redirectTo( $back . '/(step)/' . $step );
    }

    /**
     * What a request to exphelp/state does (pure, for the tests): back to the page unchanged (no POST, no state, or
     * Cancel), show the confirmation (a step back not yet confirmed), or assign
     *
     * @param string $method the request method
     * @param string $target HelpState ('' none)
     * @param string $current the object's state identifier
     * @param bool $confirmed HelpStateConfirm was posted
     * @param bool $cancelled HelpStateCancel was posted
     * @return string back, confirm or assign
     */
    public static function decide( $method, $target, $current, $confirmed, $cancelled )
    {
        if ( strtoupper( (string)$method ) !== 'POST' || (string)$target === '' || $cancelled )
            return 'back';
        if ( \expHelpContentWorkflow::needsConfirmation( $current, $target ) && !$confirmed )
            return 'confirm';
        return 'assign';
    }
}

}
