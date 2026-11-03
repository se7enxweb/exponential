<?php
/**
 * @description Enable a disabled user account again, as Edit user settings does; an account that was never activated is approved with exp:user:approve instead (a dry run unless --apply)
 * @alias exp:user:enable
 *
 * File containing the exp:user:enable command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserenable extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:enable';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Enable a user account\n" .
                             "Enables a disabled account again through the setsettings operation of the user module, as Edit\n" .
                             "user settings does. An account that was never activated (it still has its activation key) is not\n" .
                             "enabled here: exp:user:approve finishes its registration, with the mails and steps that belong to\n" .
                             "it. Without --apply nothing is changed.\n" .
                             "\n" .
                             "./console exp:user:enable jane\n" .
                             "./console exp:user:enable jane --apply",
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
        $result = \expUserManager::setEnabled( $user, true, (int)$actor->attribute( 'contentobject_id' ), $apply );
        $this->finish( self::actionReport( 'enable', $result, $user, $apply ), $format );
    }
}

}
