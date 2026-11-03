<?php
/**
 * @description Remove a user account for good, as the administration does; refuses the anonymous user, the administrator accounts, the user who acts and the last administrator (a dry run unless --apply)
 * @alias exp:user:delete
 *
 * File containing the exp:user:delete command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserdelete extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:delete';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Remove a user account for good\n" .
                             "Removes the account's locations as the administration does; accounts are never moved to the\n" .
                             "trash (the administration refuses that too), so the account, its settings and its sessions go.\n" .
                             "The content the person created stays. Refused for the anonymous user, the user of\n" .
                             "[UserSettings] UserCreatorID, the administrator account, the user who acts and the last enabled\n" .
                             "administrator. To keep the account but stop its use, see exp:user:disable. Without --apply\n" .
                             "nothing is removed.\n" .
                             "\n" .
                             "./console exp:user:delete jane\n" .
                             "./console exp:user:delete jane --apply",
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
        $before = \expUserManager::summary( $user );
        $result = \expUserManager::delete( $user, (int)$actor->attribute( 'contentobject_id' ), $apply );
        $report = self::actionReport( 'delete', $result, $user, $apply );
        if ( $report['user'] === null )
            $report['user'] = $before;
        $this->finish( $report, $format );
    }
}

}
