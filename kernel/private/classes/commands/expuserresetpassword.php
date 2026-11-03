<?php
/**
 * @description Reset a user's password: send the "reset your password" link, set a generated password shown once, or one read from standard input; optionally require a new password at the next sign-in (a dry run unless --apply)
 * @alias exp:user:reset-password
 *
 * File containing the exp:user:reset-password command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserresetpassword extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:reset-password';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Reset a user's password\n" .
                             "Choose one way: --send-link sends the mail of \"Forgot password\" with its one-time link (the\n" .
                             "password stays until the person uses the link); --generate sets a generated password that passes\n" .
                             "the password rules and prints it once; --password-stdin sets the password read from standard input\n" .
                             "(never from the command line). --generate and --password-stdin end the account's sessions;\n" .
                             "--require-change makes the person choose a new password at the next sign-in. The password is\n" .
                             "never written to a log or the audit. In demo mode an administrator's password is never changed.\n" .
                             "Without --apply nothing is changed and no mail is sent.\n" .
                             "\n" .
                             "./console exp:user:reset-password jane --send-link --apply\n" .
                             "./console exp:user:reset-password jane --generate --require-change --apply\n" .
                             "./console exp:user:reset-password ci-bot --password-stdin --apply < secret-file",
        ) + self::SCRIPT );
        return (array)$this->startup( '[send-link][generate][password-stdin][require-change][apply][dry-run][as:][format:]', '[user]', array(
            'send-link' => 'Send the "reset your password" mail with its one-time link (the password stays until the link is used)',
            'generate' => 'Set a generated password that passes the password rules, print it once and end the account\'s sessions',
            'password-stdin' => 'Read the password from standard input (one line), never from the command line (create and reset-password: it must pass the password rules)',
            'require-change' => 'The account must choose a new password at its next sign-in',
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
        $modes = array_filter( array( 'send-link' => !empty( $options['send-link'] ), 'generate' => !empty( $options['generate'] ),
                                      'stdin' => !empty( $options['password-stdin'] ) ) );
        if ( count( $modes ) !== 1 )
            $this->usage( 'choose exactly one of --send-link, --generate and --password-stdin' );
        $mode = key( $modes );
        $this->database( $format );
        $actor = $this->actor( $options, $format );
        $user = $this->account( $options, $format );
        $password = $mode === 'stdin' ? $this->stdinPassword() : null;
        $result = \expUserManager::resetPassword( $user, $mode, $password, !empty( $options['require-change'] ),
                                                  (int)$actor->attribute( 'contentobject_id' ), $apply );
        $this->finish( array( 'reset' => $mode ) + self::actionReport( 'reset-password', $result, $user, $apply ), $format );
    }
}

}
