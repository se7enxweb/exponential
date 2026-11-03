<?php
/**
 * @description Check whether a password read from standard input signs an account in (right password, enabled, not locked), for scripts and health checks; never counts as a failed sign-in
 * @alias exp:user:check-login
 *
 * File containing the exp:user:check-login command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserchecklogin extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:check-login';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Check a sign-in\n" .
                             "Reads a password from standard input (--password-stdin, never from the command line) and says\n" .
                             "whether it signs the account in: the right password, the account enabled and not locked. Exit 0\n" .
                             "when it does, 1 when not, with the reason (wrong_password, disabled, locked, no_object). The check\n" .
                             "does not sign in, opens no session, never counts as a failed sign-in and never prints the\n" .
                             "password. Made for scripts and health checks of service accounts; a second sign-in step is not\n" .
                             "checked.\n" .
                             "\n" .
                             "./console exp:user:check-login ci-bot --password-stdin < /root/secrets/ci-bot-password\n" .
                             "printf '%s\\n' \"\$PASSWORD\" | ./console exp:user:check-login ci-bot --password-stdin --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[password-stdin][format:]', '[user]', array(
            'password-stdin' => 'Read the password from standard input (one line), never from the command line (create and reset-password: it must pass the password rules)',
            'format' => 'text (default) or json (the machine output exp-user/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        if ( empty( $options['password-stdin'] ) )
            $this->usage( 'give the password on standard input with --password-stdin (never on the command line)' );
        $this->database( $format );
        $user = $this->account( $options, $format );
        $check = \expUserManager::checkLogin( $user, $this->stdinPassword() );
        $this->finish( array( 'mode' => 'check-login', 'user' => \expUserManager::summary( $user ), 'valid' => $check['valid'], 'reason' => $check['reason'],
                              'errors' => $check['valid'] ? array() : array( 'the password does not sign the account in: ' . $check['reason'] ),
                              'warnings' => array() ), $format );
    }

    protected function lines( array $report )
    {
        return array( sprintf( 'Account %s (id %d): %s', $report['user']['login'], $report['user']['id'],
                               $report['valid'] ? 'the password signs it in' : 'no sign-in (' . $report['reason'] . ')' ) );
    }
}

}
