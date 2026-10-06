<?php
/**
 * File containing the expUserActivation class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What follows the enabling of an account, for user/activate (the link the user clicks) and the Activate button of
 * user/unactivated.
 *
 * A registration with e-mail verification runs the operation user/register up to its activation check and stops
 * there: the operation keeps a memento (keyed by the user id) and the user gets the activation mail. When the account
 * is enabled, running user/register again resumes that operation after the check: it publishes the user object, sends
 * the "registration approved" mail (sendUserNotification(), switched by site.ini [UserSettings]
 * EmailRegistrationInfo) and runs the post_register trigger.
 *
 * An account without such a memento (made by a script, an import, or before the memento was cleaned up) has no
 * registration to resume: running user/register then starts it afresh, which sends a new activation mail and disables
 * the account again. finish() tells the two apart and, without a pending registration, makes the same steps by hand:
 * it publishes the user object and sends the approval mail.
 */
class expUserActivation
{
    /**
     * Whether user/register stopped for this user at its activation check and can be resumed.
     *
     * @param int $userID
     * @return bool
     */
    public static function hasPendingRegistration( $userID )
    {
        $memento = eZOperationMemento::fetchMain( array( 'user_id' => (int)$userID ) );
        if ( !$memento instanceof eZOperationMemento )
            return false;
        return self::isRegisterMemento( $memento->data() );
    }

    /**
     * Whether the data of a memento is that of the operation user/register.
     *
     * @param mixed $data
     * @return bool
     */
    public static function isRegisterMemento( $data )
    {
        return is_array( $data ) && isset( $data['module_name'], $data['operation_name'] )
               && $data['module_name'] === 'user' && $data['operation_name'] === 'register';
    }

    /**
     * What finish() does for an account: 'resume' the pending registration, or 'steps' (publish and the approval mail
     * by hand); and whether the approval mail goes out.
     *
     * @param bool $pending hasPendingRegistration()
     * @param bool $byAdministrator activated from user/unactivated
     * @param string $emailRegistrationInfo site.ini [UserSettings] EmailRegistrationInfo
     * @param string $adminSetting site.ini [UserSettings] ActivationByAdministratorSendsApprovalMail
     * @return array way ('resume'|'steps'), mail (bool)
     */
    public static function plan( $pending, $byAdministrator, $emailRegistrationInfo, $adminSetting )
    {
        $mail = $emailRegistrationInfo !== 'disabled';
        if ( $byAdministrator && $adminSetting === 'disabled' )
            $mail = false;
        return array( 'way' => $pending ? 'resume' : 'steps', 'mail' => $mail );
    }

    /** @var bool while set, sendUserNotification() sends nothing (see finish()) */
    public static $holdApprovalMail = false;

    /**
     * Completes the registration of an account that was just enabled: resumes user/register when it is pending, else
     * publishes the user object and sends the approval mail by hand. Never starts user/register afresh, so it never
     * sends a new activation mail or disables the account.
     *
     * @param int $userID
     * @param bool $byAdministrator from user/unactivated: the approval mail then also follows
     *        site.ini [UserSettings] ActivationByAdministratorSendsApprovalMail
     * @return array status (the operation status: eZModuleOperationInfo::STATUS_*), way, mail
     */
    public static function finish( $userID, $byAdministrator = false )
    {
        $userID = (int)$userID;
        $ini = eZINI::instance();
        $plan = self::plan( self::hasPendingRegistration( $userID ), $byAdministrator,
                            $ini->variable( 'UserSettings', 'EmailRegistrationInfo' ),
                            $ini->hasVariable( 'UserSettings', 'ActivationByAdministratorSendsApprovalMail' )
                                ? $ini->variable( 'UserSettings', 'ActivationByAdministratorSendsApprovalMail' ) : 'enabled' );
        if ( $plan['way'] === 'resume' )
        {
            self::$holdApprovalMail = !$plan['mail'];
            try
            {
                $result = eZOperationHandler::execute( 'user', 'register', array( 'user_id' => $userID ) );
            }
            finally
            {
                self::$holdApprovalMail = false;
            }
            $status = is_array( $result ) && isset( $result['status'] ) ? $result['status'] : eZModuleOperationInfo::STATUS_CONTINUE;
            return array( 'status' => $status, 'way' => 'resume', 'mail' => $plan['mail'] );
        }

        $result = eZUserOperationCollection::publishUserContentObject( $userID );
        $status = is_array( $result ) && isset( $result['status'] ) ? $result['status'] : eZModuleOperationInfo::STATUS_CONTINUE;
        // as the operation: the mail follows a publication that went through, not one waiting for approval
        if ( $plan['mail'] && $status === eZModuleOperationInfo::STATUS_CONTINUE )
            eZUserOperationCollection::sendUserNotification( $userID );
        return array( 'status' => $status, 'way' => 'steps', 'mail' => $plan['mail'] );
    }
}
