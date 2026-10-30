<?php
/**
 * exphelp/state/<help_id> and exphelp/state/manual/<remote_id> (doc/eng-us/features/6.0/help-content.md, "The review
 * workflow"): one step of Draft -> Review -> Released for a help topic or manual, from the buttons of exphelp/topic and
 * exphelp/manual. POST only, with the state to go to in HelpState (review, released, draft); the form token of the
 * kernel protects it like every POST.
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
        if ( $method !== 'POST' || !$http->hasPostVariable( 'HelpState' ) )
            return $Module->redirectTo( $back );

        $object = $provider->repository()->object( (int)$item['object_id'] );
        if ( !$object instanceof \eZContentObject )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $target = (string)$http->postVariable( 'HelpState' );
        $result = \expHelpContentWorkflow::assign( $object, $target );
        \expHelpContentProvider::resetInstance();
        $step = $result === '' ? $target : ( $result === 'author' ? 'refused-author' : 'refused' );
        return $Module->redirectTo( $back . '/(step)/' . $step );
    }
}

}
