<?php
/**
 * @description Unlock an account locked after too many wrong passwords: its failed sign-ins back to 0, as Users > Hygiene does (a dry run unless --apply)
 * @alias exp:user:unlock
 *
 * File containing the exp:user:unlock command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserunlock extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:unlock';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Unlock an account\n" .
                             "An account is locked after more wrong passwords than [UserSettings] MaxNumberOfFailedLogin. This\n" .
                             "sets its failed sign-ins back to 0, as Users > Hygiene does, so the person can sign in again;\n" .
                             "the password stays. The audit records access.user.unlock. Without --apply nothing is changed.\n" .
                             "\n" .
                             "./console exp:user:unlock jane\n" .
                             "./console exp:user:unlock jane --apply",
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
        $result = \expUserManager::unlock( $user, $apply );
        $this->finish( self::actionReport( 'unlock', $result, $user, $apply ), $format );
    }
}

}
