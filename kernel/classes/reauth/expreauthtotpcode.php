<?php
/**
 * File containing the expReauthTotpCode class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * "Verify it's you" with the code of an authenticator app set up in sevenx_authentication_2fa (when that extension
 * is active): the account's TOTP secret is read through the extension's helper, a code is accepted once per time step
 * (the same step never counts again, here or at sign-in) and wrong codes count towards the extension's lock
 * ([OtherSignInPaths] CodeMaxAttempts and CodeLockSeconds). Without the extension, or for an account whose second step
 * is off or sends codes by e-mail, there is no code to offer. Only the public methods of the extension's classes are
 * used, each checked before it is called, so a different version of the extension makes the code unavailable instead
 * of failing.
 */
class expReauthTotpCode implements expReauthCodeHandler
{
    /** @return object|null the extension's helper, when it is there and switched on */
    protected function helper()
    {
        if ( !class_exists( 'sevenxAuthentication2faHelper' ) || !method_exists( 'sevenxAuthentication2faHelper', 'instance' ) )
            return null;
        $helper = sevenxAuthentication2faHelper::instance();
        foreach ( array( 'isEnabled', 'userData', 'matchTotp', 'saveUserData' ) as $m )
            if ( !method_exists( $helper, $m ) )
                return null;
        return $helper->isEnabled() ? $helper : null;
    }

    /** @return object|null the account's two-step data when an authenticator app is set up */
    protected function totpData( $helper, eZUser $user )
    {
        $data = $helper->userData( (int)$user->attribute( 'contentobject_id' ) );
        if ( !is_object( $data ) )
            return null;
        foreach ( array( 'isActive', 'method', 'secret', 'lastStep', 'setLastStep' ) as $m )
            if ( !method_exists( $data, $m ) )
                return null;
        if ( !$data->isActive() || $data->method() !== 'totp' || (string)$data->secret() === '' )
            return null;
        return $data;
    }

    public function hasReauthCode( eZUser $user )
    {
        $helper = $this->helper();
        return $helper !== null && $this->totpData( $helper, $user ) !== null;
    }

    public function checkReauthCode( eZUser $user, $code )
    {
        $helper = $this->helper();
        $data = $helper ? $this->totpData( $helper, $user ) : null;
        $code = preg_replace( '/\s+/', '', (string)$code );
        if ( !$data || !preg_match( '/^\d{6,8}$/', $code ) )
            return false;
        $userID = (int)$user->attribute( 'contentobject_id' );
        $settings = method_exists( $helper, 'otherPathSettings' ) ? $helper->otherPathSettings() : array();
        $locks = class_exists( 'sevenxAuthentication2faPolicy' ) && method_exists( 'sevenxAuthentication2faPolicy', 'codeLocked' )
                 && method_exists( $data, 'codeFailures' ) && method_exists( $data, 'setCodeFailures' )
                 && isset( $settings['code_max_attempts'], $settings['code_lock_seconds'] );
        $now = time();
        if ( $locks && sevenxAuthentication2faPolicy::codeLocked( $data->codeFailures(), $settings['code_max_attempts'], $settings['code_lock_seconds'], $now ) )
            return false;
        $step = $helper->matchTotp( $data->secret(), $code, $data->lastStep() );
        if ( $step === false || $step === null )
        {
            if ( $locks )
            {
                $data->setCodeFailures( sevenxAuthentication2faPolicy::codeFailed( $data->codeFailures(), $settings['code_lock_seconds'], $now ) );
                $helper->saveUserData( $userID, $data );
            }
            return false;
        }
        $data->setLastStep( $step );
        if ( $locks )
            $data->setCodeFailures( null );
        $helper->saveUserData( $userID, $data );
        return true;
    }
}
