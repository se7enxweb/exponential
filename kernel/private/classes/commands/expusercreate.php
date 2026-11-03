<?php
/**
 * @description Create a user account through the content publish, as the administration does (its workflows and the approval of new accounts run): login, e-mail, names, groups, and a password from standard input, a generated one or the activation mail (a dry run unless --apply)
 * @alias exp:user:create
 *
 * File containing the exp:user:create command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expusercreate extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:create';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Create a user account\n" .
                             "Creates the account as a content object of a user class in its first group and publishes it with\n" .
                             "the content/publish operation, exactly as the administration does: its workflows run, so where\n" .
                             "new accounts need approval the account waits until it is approved. The other groups are added as\n" .
                             "locations. Choose one way to the password: --password-stdin (one line on standard input, never\n" .
                             "on the command line), --generate-password (printed once) or --send-activation (the account starts\n" .
                             "disabled and gets the activation mail). Without --apply nothing is created: the command lists what\n" .
                             "it would do and which checks fail.\n" .
                             "\n" .
                             "./console exp:user:create --login=jane --email=jane@example.com --first-name=Jane --last-name=Doe \\\n" .
                             "    --group=Editors --generate-password --apply\n" .
                             "printf '%s\\n' \"\$PASSWORD\" | ./console exp:user:create --login=ci-bot --email=ci@example.com --group=12 --password-stdin --apply\n" .
                             "./console exp:user:create --login=member1 --email=member1@example.com --group=Members --send-activation --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[login:][email:][first-name:][last-name:][group:*][class:][password-stdin][generate-password][send-activation][disabled][apply][dry-run][as:][format:]', '', array(
            'login' => 'The login name (create: of the new account; edit: the new login name)',
            'email' => 'The e-mail address (create: of the new account; edit: the new address)',
            'first-name' => 'The first name (the attribute first_name of the user class)',
            'last-name' => 'The last name (the attribute last_name of the user class)',
            'group' => 'A user group by node id or name: list, export and bulk take only its members (below it, at any depth); create and import: the group of the new accounts (repeat for more, the first is the main group; default [UserSettings] DefaultUserPlacement)',
            'class' => 'The user class of the new account (default the class of [UserSettings] UserClassID)',
            'password-stdin' => 'Read the password from standard input (one line), never from the command line (create and reset-password: it must pass the password rules)',
            'generate-password' => 'Generate a password that passes the password rules and print it once',
            'send-activation' => 'Create the account disabled and send the activation mail; the person sets a password with "Forgot password"',
            'disabled' => 'Create the account disabled (enable it later with exp:user:enable)',
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
        foreach ( array( 'login', 'email' ) as $required )
            if ( !isset( $options[$required] ) || trim( (string)$options[$required] ) === '' )
                $this->usage( "--$required is required" );
        $this->database( $format );
        $this->actor( $options, $format );
        $spec = array( 'login' => (string)$options['login'], 'email' => (string)$options['email'],
                       'first_name' => isset( $options['first-name'] ) && $options['first-name'] !== false ? (string)$options['first-name'] : '',
                       'last_name' => isset( $options['last-name'] ) && $options['last-name'] !== false ? (string)$options['last-name'] : '',
                       'groups' => self::listOption( isset( $options['group'] ) ? $options['group'] : array() ),
                       'class' => isset( $options['class'] ) && $options['class'] !== false ? (string)$options['class'] : '',
                       'password' => !empty( $options['password-stdin'] ) ? $this->stdinPassword() : null,
                       'generate' => !empty( $options['generate-password'] ), 'send_activation' => !empty( $options['send-activation'] ),
                       'disabled' => !empty( $options['disabled'] ) );
        $result = \expUserManager::create( $spec, $apply );
        $user = isset( $result['user_id'] ) ? \eZUser::fetch( (int)$result['user_id'] ) : null;
        $this->finish( self::actionReport( 'create', $result, $user, $apply ), $format );
    }
}

}
