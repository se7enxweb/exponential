<?php
/**
 * @description Two-step sign-in and passkeys of an account: show them; reset the second step for someone who lost the phone, remove the passkeys of a lost device (changes are a dry run unless --apply)
 * @alias exp:user:2fa
 *
 * File containing the exp:user:2fa command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuser2fa extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:2fa';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Two-step sign-in and passkeys of an account\n" .
                             "Shows whether the account has a second sign-in step (its method) and how many passkeys. --reset\n" .
                             "removes the second step, for someone who lost the phone, and resets the failed sign-ins; where\n" .
                             "two-step sign-in is enforced, the next sign-in asks for a new setup. It needs the two-step\n" .
                             "sign-in extension (sevenx_authentication_2fa). --remove-passkeys removes every passkey of the\n" .
                             "account, as an administrator does for a lost or stolen device. The audit records\n" .
                             "access.user.two_factor.remove and access.passkey.revoke. Without --apply nothing is changed.\n" .
                             "\n" .
                             "./console exp:user:2fa jane\n" .
                             "./console exp:user:2fa jane --reset --apply\n" .
                             "./console exp:user:2fa jane --remove-passkeys --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[reset][remove-passkeys][apply][dry-run][as:][format:]', '[user]', array(
            'reset' => 'Remove the second sign-in step (a lost phone) and reset the failed sign-ins; with enforced two-step sign-in the next sign-in asks for a new setup',
            'remove-passkeys' => 'Remove every passkey of the account (a lost or stolen device)',
            'apply' => 'Make the change; without it the command only lists what it would change (a dry run)',
            'dry-run' => 'The default, kept for scripts that pass it: list what would change, change nothing',
            'as' => 'Act as this user (login, e-mail or id; default the user of [UserSettings] UserCreatorID); the audit records it as who did it',
            'format' => 'text (default) or json (the machine output exp-user/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $this->database( $format );
        $this->actor( $options, $format );
        $user = $this->account( $options, $format );
        $report = array( 'mode' => '2fa', 'user' => \expUserManager::summary( $user ), 'two_factor' => \expUserManager::twoFactor( $user ),
                         'errors' => array(), 'warnings' => array() );
        if ( !empty( $options['reset'] ) || !empty( $options['remove-passkeys'] ) )
        {
            $result = \expUserManager::resetTwoFactor( $user, !empty( $options['reset'] ), !empty( $options['remove-passkeys'] ), $apply );
            $report = array( 'dry_run' => !$apply ) + $result + $report;
            if ( $apply && $result['applied'] )
                $report['two_factor'] = \expUserManager::twoFactor( \eZUser::fetch( (int)$user->attribute( 'contentobject_id' ) ) );
        }
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $t = $report['two_factor'];
        $lines = array( sprintf( 'Account %s (id %d)', $report['user']['login'], $report['user']['id'] ),
                        '  Two-step sign-in: ' . ( !$t['available'] ? 'not available (the extension is not active, or the class has no field for it)'
                                                                     : ( $t['active'] ? 'on (' . $t['method'] . ')' : 'off' ) ),
                        '  Passkeys:         ' . ( $t['passkeys'] === null ? 'not available' : $t['passkeys'] ) );
        $report['user'] = null;
        return array_merge( $lines, self::actionLines( $report ) );
    }
}

}
