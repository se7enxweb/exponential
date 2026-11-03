<?php
/**
 * @description Send the activation mail again, with a new key, to an account that never activated, as Users > Unactivated does (a dry run unless --apply)
 * @alias exp:user:resend-activation
 *
 * File containing the exp:user:resend-activation command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserresendactivation extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:resend-activation';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Send the activation mail again\n" .
                             "For an account that never activated (disabled, with its activation key): a new key replaces the\n" .
                             "old one, so an older mail's link stops working, and the activation mail goes to the account's\n" .
                             "address, as Resend in Users > Unactivated does. To activate the account without the person, use\n" .
                             "exp:user:approve. Without --apply no mail is sent.\n" .
                             "\n" .
                             "./console exp:user:resend-activation member1\n" .
                             "./console exp:user:resend-activation member1 --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][as:][format:]', '[user]', array(
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
        $result = \expUserManager::resendActivation( $user, $apply );
        $this->finish( self::actionReport( 'resend-activation', $result, $user, $apply ), $format );
    }
}

}
