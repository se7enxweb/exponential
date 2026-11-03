<?php
/**
 * File containing the shared code of the exp:user commands.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

/**
 * What every exp:user command (user management from the console) shares: the options, the text and JSON output, the
 * exit codes, finding the account and the user who acts. The work is in expUserManager (kernel/classes/user/); these
 * commands only parse, call and print.
 *
 * Every command that changes something is a dry run unless --apply is given (--dry-run is accepted and means the
 * same; both together are a usage error). A password is never read from the command line, where the process list
 * and the shell history would keep it: --password-stdin reads it from standard input. A generated password is printed
 * once and never again; no output ever holds a password hash.
 *
 * The names, options, arguments, exit codes and the exp-user/1 output are a permanent contract (contract() and
 * arguments() are the snapshot tests/tests/kernel/classes/user/expUserCommandContractTest.php compares against):
 * options and keys may be added, never renamed or removed. The exit codes are exp:upgrade's: 0 done, 1 failed or
 * refused, 2 usage, 3 the database unreachable, 4 internal error.
 */
abstract class ExpuserBase extends \Exponential\Runnable\Command
{
    /**
     * Every command with its option configuration (permanent: only additions).
     *
     * @return array command => option config
     */
    public static function contract()
    {
        return array(
            'exp:user:list' => '[state:][group:][search:][days:][sort:][order:][limit:][offset:][format:]',
            'exp:user:show' => '[as:][format:]',
            'exp:user:create' => '[login:][email:][first-name:][last-name:][group:*][class:][password-stdin][generate-password][send-activation][disabled][apply][dry-run][as:][format:]',
            'exp:user:edit' => '[email:][login:][first-name:][last-name:][add-group:*][remove-group:*][main-group:][apply][dry-run][as:][format:]',
            'exp:user:delete' => '[apply][dry-run][as:][format:]',
            'exp:user:disable' => '[apply][dry-run][as:][format:]',
            'exp:user:enable' => '[apply][dry-run][as:][format:]',
            'exp:user:reset-password' => '[send-link][generate][password-stdin][require-change][apply][dry-run][as:][format:]',
            'exp:user:approve' => '[reject][apply][dry-run][as:][format:]',
            'exp:user:pending' => '[search:][limit:][offset:][format:]',
            'exp:user:groups' => '[parent:][name:][to:][apply][dry-run][as:][format:]',
            'exp:user:roles' => '[subtree:][section:][apply][dry-run][as:][format:]',
            'exp:user:sessions' => '[end][session:][apply][dry-run][as:][format:]',
            'exp:user:unlock' => '[apply][dry-run][as:][format:]',
            'exp:user:2fa' => '[reset][remove-passkeys][apply][dry-run][as:][format:]',
            'exp:user:require-password-change' => '[clear][apply][dry-run][as:][format:]',
            'exp:user:resend-activation' => '[apply][dry-run][as:][format:]',
            'exp:user:export' => '[state:][group:][search:][days:][type:][output:][format:]',
            'exp:user:import' => '[type:][group:*][send-activation][disabled][apply][dry-run][as:][format:]',
            'exp:user:bulk' => '[state:][group:][search:][days:][limit:][confirm:][apply][dry-run][as:][format:]',
            'exp:user:audit' => '[event:][result:][from:][to:][limit:][offset:][format:]',
            'exp:user:hygiene' => '[days:][format:]',
            'exp:user:whoami' => '[as:][format:]',
            'exp:user:check-login' => '[password-stdin][format:]',
        );
    }

    /**
     * The arguments of the commands that take one (permanent).
     *
     * @return array command => argument config
     */
    public static function arguments()
    {
        return array(
            'exp:user:show' => '[user]',
            'exp:user:edit' => '[user]',
            'exp:user:delete' => '[user]',
            'exp:user:disable' => '[user]',
            'exp:user:enable' => '[user]',
            'exp:user:reset-password' => '[user]',
            'exp:user:approve' => '[user]',
            'exp:user:groups' => '[action][group]',
            'exp:user:roles' => '[action][role][target]',
            'exp:user:sessions' => '[user]',
            'exp:user:unlock' => '[user]',
            'exp:user:2fa' => '[user]',
            'exp:user:require-password-change' => '[user]',
            'exp:user:resend-activation' => '[user]',
            'exp:user:import' => '[file]',
            'exp:user:bulk' => '[action]',
            'exp:user:audit' => '[user]',
            'exp:user:check-login' => '[user]',
        );
    }

    /** @return array option => help text for every option of the family */
    public static function optionHelp()
    {
        return array(
            'state' => 'Only accounts in this state: all (default), enabled, disabled, unactivated, pending, locked, never, inactive, orphaned, duplicate_login or duplicate_email',
            'group' => 'A user group by node id or name: list, export and bulk take only its members (below it, at any depth); create and import: the group of the new accounts (repeat for more, the first is the main group; default [UserSettings] DefaultUserPlacement)',
            'search' => 'Only accounts whose login, name or e-mail address contains this text',
            'days' => 'With --state=inactive: not signed in for this many days (default 180)',
            'sort' => 'The order: name (default), login, email, last_visit, login_count or id',
            'order' => 'asc (default) or desc',
            'limit' => 'How many to list (default 25, at most 500); bulk: the most accounts one run may change (default 500, at most 5000)',
            'offset' => 'Skip this many first (the next page: --offset=25)',
            'format' => 'text (default) or json (the machine output exp-user/1)',
            'as' => 'Act as this user (login, e-mail or id; default the user of [UserSettings] UserCreatorID); the audit records it as who did it',
            'login' => 'The login name (create: of the new account; edit: the new login name)',
            'email' => 'The e-mail address (create: of the new account; edit: the new address)',
            'first-name' => 'The first name (the attribute first_name of the user class)',
            'last-name' => 'The last name (the attribute last_name of the user class)',
            'class' => 'The user class of the new account (default the class of [UserSettings] UserClassID)',
            'password-stdin' => 'Read the password from standard input (one line), never from the command line (create and reset-password: it must pass the password rules)',
            'generate-password' => 'Generate a password that passes the password rules and print it once',
            'send-activation' => 'Create the account disabled and send the activation mail; the person sets a password with "Forgot password"',
            'disabled' => 'Create the account disabled (enable it later with exp:user:enable)',
            'apply' => 'Make the change; without it the command only lists what it would change (a dry run)',
            'dry-run' => 'The default, kept for scripts that pass it: list what would change, change nothing',
            'add-group' => 'Add the account to this user group (node id or name; repeat for more)',
            'remove-group' => 'Remove the account from this user group (node id or name; repeat for more); an account keeps at least one',
            'main-group' => 'Make this group (node id or name, one the account is or becomes a member of) its main group',
            'send-link' => 'Send the "reset your password" mail with its one-time link (the password stays until the link is used)',
            'generate' => 'Set a generated password that passes the password rules, print it once and end the account\'s sessions',
            'require-change' => 'The account must choose a new password at its next sign-in',
            'reject' => 'Reject the registration instead: deny its approval and remove the account for good',
            'parent' => 'groups create: the group the new group goes below (node id or name; default the users root, content.ini [NodeSettings] UserRootNode)',
            'name' => 'groups create and rename: the name of the group',
            'to' => 'groups move: the group to move it below (node id or name); audit: only events on or before this day (YYYY-MM-DD)',
            'subtree' => 'roles assign and unassign: the assignment limited to the subtree of this node (node id)',
            'section' => 'roles assign and unassign: the assignment limited to this section (id or identifier)',
            'end' => 'End every tracked session of the account (Your devices)',
            'session' => 'End only the session with this id (from the list)',
            'reset' => 'Remove the second sign-in step (a lost phone) and reset the failed sign-ins; with enforced two-step sign-in the next sign-in asks for a new setup',
            'remove-passkeys' => 'Remove every passkey of the account (a lost or stolen device)',
            'clear' => 'Remove the mark instead: no new password is required at the next sign-in',
            'type' => 'The file type: csv (default) or json; import reads it from the file name when not given',
            'output' => 'Write the export to this file (new, mode 0600) instead of standard output',
            'confirm' => 'With --apply: the number of accounts the filter selects, as the dry run showed it; the action runs only when it still matches',
            'event' => 'Only events with this name or a name below it (access.user.password)',
            'result' => 'Only events with this result: success, refused or failed',
            'from' => 'Only events on or after this day (YYYY-MM-DD)',
        );
    }

    /** The script settings every command shares besides its description. */
    const SCRIPT = array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true );

    /** @return string the command's name, e.g. exp:user:list */
    abstract protected function name();

    /**
     * Starts the script: $this->script( array( 'description' => "...", ... ) ) and $this->startup( '<config>',
     * '<arguments>', <help> ), written out literally in each command, so ./console exp:help:examples reads the help
     * text and the options from the code. The option configuration must equal contract() and every help text
     * optionHelp() (expUserCommandContractTest checks both).
     *
     * @return array the options
     */
    abstract protected function begin();

    /**
     * The command's work: parse with begin(), call expUserManager, print with finish(). Every command overrides it;
     * this one only starts the script and ends with a usage error.
     */
    public function run()
    {
        $this->begin();
        $this->usage( $this->name() . ' has no work of its own' );
    }

    /**
     * The output format; after an error (not text or json) the command ends with exit 2.
     *
     * @param array $options
     * @return string
     */
    protected function format( array $options )
    {
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'text';
        if ( !in_array( $format, array( 'text', 'json' ), true ) )
            $this->usage( "--format must be text or json, not $format" );
        return $format;
    }

    /**
     * Whether to write: --apply; a usage error with --dry-run too.
     *
     * @param array $options
     * @return bool
     */
    protected function apply( array $options )
    {
        $apply = self::applyRequested( $options );
        if ( $apply === null )
            $this->usage( '--apply and --dry-run exclude each other; without --apply nothing is changed' );
        return $apply;
    }

    /**
     * A usage error: the message and exit 2.
     *
     * @param string $message
     */
    protected function usage( $message )
    {
        $this->error( 'FAIL: ' . $message );
        $this->shutdown( \expUpgrade::EXIT_USAGE );
    }

    /**
     * Ends with exit 3 when the database is not reachable.
     *
     * @param string $format
     * @return \eZDBInterface
     */
    protected function database( $format )
    {
        $problem = '';
        try
        {
            $db = \eZDB::instance();
        }
        catch ( \Throwable $e )
        {
            $db = null;
            $problem = ': ' . $e->getMessage();
        }
        if ( !$db || !$db->isConnected() )
            $this->finish( array( 'mode' => 'unreachable', 'errors' => array( 'the database is not reachable' . $problem ), 'warnings' => array(),
                                  'exit' => \expUpgrade::EXIT_UNREACHABLE ), $format );
        return $db;
    }

    /**
     * The user who acts (--as, else the user of [UserSettings] UserCreatorID), signed in for this process; exit 1 when
     * there is none.
     *
     * @param array $options
     * @param string $format
     * @return \eZUser
     */
    protected function actor( array $options, $format )
    {
        $found = \expUserManager::actAs( isset( $options['as'] ) && $options['as'] !== false ? (string)$options['as'] : '' );
        if ( !$found['user'] )
            $this->finish( array( 'mode' => 'actor', 'errors' => array( $found['error'] ), 'warnings' => array() ), $format );
        return $found['user'];
    }

    /**
     * The account named by the first argument; a usage error without one, exit 1 when no account has it.
     *
     * @param array $options
     * @param string $format
     * @return \eZUser
     */
    protected function account( array $options, $format )
    {
        $ref = isset( $options['arguments'][0] ) ? trim( (string)$options['arguments'][0] ) : '';
        if ( $ref === '' )
            $this->usage( 'name the account: ' . $this->name() . ' <login|e-mail|id>' );
        $found = \expUserManager::find( $ref );
        if ( !$found['user'] )
            $this->finish( array( 'mode' => 'find', 'user' => null, 'errors' => array( $found['error'] ), 'warnings' => array() ), $format );
        return $found['user'];
    }

    /**
     * One line of standard input, without its line end: the password of --password-stdin. A usage error when there
     * is none.
     *
     * @return string
     */
    protected function stdinPassword()
    {
        $line = fgets( STDIN );
        $password = $line === false ? '' : rtrim( $line, "\r\n" );
        if ( $password === '' )
            $this->usage( '--password-stdin: standard input holds no password (one line, e.g. from a password manager or a file of mode 0600)' );
        return $password;
    }

    /**
     * A list option (a comma list or repeated) as its values.
     *
     * @param mixed $value
     * @return string[]
     */
    protected static function listOption( $value )
    {
        $out = array();
        foreach ( (array)$value as $item )
            foreach ( explode( ',', (string)$item ) as $part )
                if ( trim( $part ) !== '' )
                    $out[] = trim( $part );
        return array_values( array_unique( $out ) );
    }

    /**
     * The report of an action of expUserManager, with the mode, the account and whether it was a dry run.
     *
     * @param string $mode
     * @param array $result what expUserManager returned
     * @param \eZUser|null $user the account after the action (null when removed)
     * @param bool $apply
     * @return array
     */
    protected static function actionReport( $mode, array $result, $user, $apply )
    {
        $user = $user instanceof \eZUser ? \eZUser::fetch( (int)$user->attribute( 'contentobject_id' ) ) : null;
        return array( 'mode' => $mode, 'dry_run' => !$apply, 'user' => $user instanceof \eZUser ? \expUserManager::summary( $user ) : null ) + $result;
    }

    /**
     * Prints a report and ends with its exit code.
     *
     * @param array $report
     * @param string $format
     */
    protected function finish( array $report, $format )
    {
        $report = array( 'schema' => \expUserManager::REPORT, 'command' => $this->name() ) + $report;
        if ( !isset( $report['exit'] ) )
            $report['exit'] = !empty( $report['errors'] ) ? \expUpgrade::EXIT_FAILED : \expUpgrade::EXIT_OK;
        if ( $format === 'json' )
            $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        else
            $this->printText( $report );
        $this->shutdown( (int)$report['exit'] );
    }

    /**
     * A report as text: the command's own lines (lines()), then warnings, errors and PASS or FAIL.
     *
     * @param array $report
     */
    protected function printText( array $report )
    {
        foreach ( $this->lines( $report ) as $line )
            $this->output( $line );
        foreach ( isset( $report['warnings'] ) ? $report['warnings'] : array() as $w )
            $this->output( 'WARN ' . $w );
        foreach ( isset( $report['errors'] ) ? $report['errors'] : array() as $e )
            $this->error( 'FAIL: ' . $e );
        $this->output( (int)$report['exit'] === \expUpgrade::EXIT_OK ? 'PASS' : 'FAIL (exit ' . (int)$report['exit'] . ')' );
    }

    /**
     * The command's lines of a report: by default those of an action (actionLines()).
     *
     * @param array $report
     * @return string[]
     */
    protected function lines( array $report )
    {
        return self::actionLines( $report );
    }

    /**
     * The lines of an action report: the account, what is (or would be) changed, the generated password once. Pure.
     *
     * @param array $report
     * @return string[]
     */
    public static function actionLines( array $report )
    {
        $lines = array();
        if ( !empty( $report['user'] ) )
            $lines[] = sprintf( 'Account %s (id %d, %s)', $report['user']['login'], $report['user']['id'], $report['user']['enabled'] ? 'enabled' : 'disabled' );
        if ( !empty( $report['changes'] ) )
        {
            $dry = !empty( $report['dry_run'] );
            $lines[] = $dry ? 'Would change (dry run, nothing changed; --apply makes the change):' : ( !empty( $report['applied'] ) ? 'Changed:' : 'Not changed:' );
            foreach ( $report['changes'] as $change )
                $lines[] = '  ' . $change;
        }
        if ( isset( $report['state_before'], $report['state'] ) && empty( $report['dry_run'] ) )
            $lines[] = sprintf( 'Registration: %s -> %s', $report['state_before'], $report['state'] );
        if ( isset( $report['sessions_ended'] ) )
            $lines[] = sprintf( 'Sessions ended: %d', $report['sessions_ended'] );
        if ( isset( $report['generated_password'] ) )
        {
            $lines[] = 'Generated password (shown this once, not stored anywhere readable):';
            $lines[] = '  ' . $report['generated_password'];
        }
        return $lines;
    }

    /**
     * The lines of a list of accounts. Pure.
     *
     * @param array $accounts see expUserManager::listAccounts()
     * @param int $total
     * @param int $offset
     * @return string[]
     */
    public static function listLines( array $accounts, $total, $offset )
    {
        $lines = array( sprintf( '%d account(s)%s', $total, $accounts ? sprintf( ', %d to %d listed', $offset + 1, $offset + count( $accounts ) ) : '' ) );
        if ( !$accounts )
            return $lines;
        $lines[] = sprintf( '  %-7s %-24s %-32s %-11s %-10s %s', 'Id', 'Login', 'E-mail', 'Last visit', 'State', 'Main group' );
        foreach ( $accounts as $a )
            $lines[] = sprintf( '  %-7d %-24s %-32s %-11s %-10s %s', $a['id'], self::cut( $a['login'], 24 ), self::cut( $a['email'], 32 ),
                                $a['last_visit'] ? date( 'Y-m-d', (int)$a['last_visit'] ) : 'never',
                                isset( $a['registration'] ) ? $a['registration'] : implode( ',', $a['states'] ),
                                !empty( $a['groups'] ) ? $a['groups'][0]['name'] : '-' );
        return $lines;
    }

    /** @return string the text cut to a width with an ellipsis */
    protected static function cut( $text, $width )
    {
        $text = (string)$text;
        return mb_strlen( $text ) > $width ? mb_substr( $text, 0, $width - 1 ) . '…' : $text;
    }
}

}
