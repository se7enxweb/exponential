<?php
/**
 * @description Approve a waiting registration (activate the account, or accept its approval item and publish it), or reject it with --reject, as Users > Unactivated and the approval item do (a dry run unless --apply)
 * @alias exp:user:approve
 *
 * File containing the exp:user:approve command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserapprove extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:approve';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Approve or reject a waiting registration\n" .
                             "A registration waits for the person to confirm the e-mail address, for an administrator to activate\n" .
                             "the account, or for an approval item (exp:user:pending lists them). Approving activates the account\n" .
                             "as Users > Unactivated does (the registration then completes: publish, the welcome mail), or accepts\n" .
                             "the approval item and continues its workflow at once. --reject denies the item and removes the\n" .
                             "account for good, as the reject link of the registration mail does; an active account is never\n" .
                             "removed here. Without --apply nothing is changed.\n" .
                             "\n" .
                             "./console exp:user:approve jane --apply\n" .
                             "./console exp:user:approve spam-account --reject --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[reject][apply][dry-run][as:][format:]', '[user]', array(
            'reject' => 'Reject the registration instead: deny its approval and remove the account for good',
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
        $reject = !empty( $options['reject'] );
        $result = \expUserManager::decideRegistration( $user, $reject, (int)$actor->attribute( 'contentobject_id' ), $apply );
        $report = self::actionReport( $reject ? 'reject' : 'approve', $result, $user, $apply );
        if ( $report['user'] === null )
            $report['user'] = $before;
        $this->finish( $report, $format );
    }
}

}
