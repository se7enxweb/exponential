<?php
/**
 * @description Change a user account: e-mail address, login, first and last name, groups added or removed, the main group (a dry run unless --apply)
 * @alias exp:user:edit
 *
 * File containing the exp:user:edit command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuseredit extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:edit';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Change a user account\n" .
                             "Changes the e-mail address and the login (checked as the administration checks them: valid, not\n" .
                             "another account's), the first and last name (a new version, published with its workflows), and\n" .
                             "the groups: --add-group adds a location in a group, --remove-group removes one (an account keeps\n" .
                             "at least one), --main-group chooses the main one. Without --apply nothing is changed.\n" .
                             "\n" .
                             "./console exp:user:edit jane --email=jane.doe@example.com --apply\n" .
                             "./console exp:user:edit jane --add-group=Editors --remove-group=Members --main-group=Editors --apply\n" .
                             "./console exp:user:edit 1234 --first-name=Janet --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[email:][login:][first-name:][last-name:][add-group:*][remove-group:*][main-group:][apply][dry-run][as:][format:]', '[user]', array(
            'email' => 'The e-mail address (create: of the new account; edit: the new address)',
            'login' => 'The login name (create: of the new account; edit: the new login name)',
            'first-name' => 'The first name (the attribute first_name of the user class)',
            'last-name' => 'The last name (the attribute last_name of the user class)',
            'add-group' => 'Add the account to this user group (node id or name; repeat for more)',
            'remove-group' => 'Remove the account from this user group (node id or name; repeat for more); an account keeps at least one',
            'main-group' => 'Make this group (node id or name, one the account is or becomes a member of) its main group',
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
        $value = function ( $name ) use ( $options ) {
            return isset( $options[$name] ) && $options[$name] !== false && $options[$name] !== null ? (string)$options[$name] : null;
        };
        $spec = array( 'email' => $value( 'email' ), 'login' => $value( 'login' ), 'first_name' => $value( 'first-name' ), 'last_name' => $value( 'last-name' ),
                       'add_groups' => self::listOption( isset( $options['add-group'] ) ? $options['add-group'] : array() ),
                       'remove_groups' => self::listOption( isset( $options['remove-group'] ) ? $options['remove-group'] : array() ),
                       'main_group' => (string)$value( 'main-group' ) );
        $result = \expUserManager::edit( $user, $spec, $apply );
        if ( !$result['changes'] && !$result['errors'] )
            $result['warnings'][] = 'nothing to change: name what to change (--email, --login, --first-name, --last-name, --add-group, --remove-group, --main-group)';
        $this->finish( self::actionReport( 'edit', $result, $user, $apply ), $format );
    }
}

}
