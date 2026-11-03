<?php
/**
 * @description Disable a user account and end its sessions, as Edit user settings does; refuses the protected accounts and the last administrator (a dry run unless --apply)
 * @alias exp:user:disable
 *
 * File containing the exp:user:disable command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserdisable extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:disable';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Disable a user account\n" .
                             "Disables the account through the setsettings operation of the user module, as Edit user settings\n" .
                             "and Users > Hygiene do, and ends all its sessions: the person can no longer sign in, and nothing\n" .
                             "of the account or its content is removed. exp:user:enable undoes it. Refused for the anonymous\n" .
                             "user, the user of [UserSettings] UserCreatorID, the administrator account, the user who acts and\n" .
                             "the last enabled administrator. Without --apply nothing is changed.\n" .
                             "\n" .
                             "./console exp:user:disable jane\n" .
                             "./console exp:user:disable jane --apply",
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
        $actor = $this->actor( $options, $format );
        $user = $this->account( $options, $format );
        $result = \expUserManager::setEnabled( $user, false, (int)$actor->attribute( 'contentobject_id' ), $apply );
        $this->finish( self::actionReport( 'disable', $result, $user, $apply ), $format );
    }
}

}
