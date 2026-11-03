<?php
/**
 * @description Require a new password at the account's next sign-in, or remove that mark with --clear, as the account's edit form does (a dry run unless --apply)
 * @alias exp:user:require-password-change
 *
 * File containing the exp:user:require-password-change command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserrequirepasswordchange extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:require-password-change';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Require a new password at the next sign-in\n" .
                             "Marks the account, as \"Change password at next sign-in\" of its edit form does: after the next\n" .
                             "sign-in the person must choose a new password before anything else. --clear removes the mark. The\n" .
                             "audit records access.user.password.change_required.set or .clear. Without --apply nothing is\n" .
                             "changed.\n" .
                             "\n" .
                             "./console exp:user:require-password-change jane --apply\n" .
                             "./console exp:user:require-password-change jane --clear --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[clear][apply][dry-run][as:][format:]', '[user]', array(
            'clear' => 'Remove the mark instead: no new password is required at the next sign-in',
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
        $result = \expUserManager::markPasswordChange( $user, empty( $options['clear'] ), $apply );
        $this->finish( self::actionReport( 'require-password-change', $result, $user, $apply ), $format );
    }
}

}
