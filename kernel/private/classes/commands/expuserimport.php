<?php
/**
 * @description Import accounts from a CSV or JSON file, each created as exp:user:create creates it, with the activation mail or disabled; never a password from the file (a dry run that checks every row unless --apply)
 * @alias exp:user:import
 *
 * File containing the exp:user:import command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserimport extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:import';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Import accounts\n" .
                             "Reads a CSV file (a header line with the columns login, email, first_name, last_name, groups) or a\n" .
                             "JSON file (an array of objects with the same keys); groups are node ids or names separated by |,\n" .
                             "and a row without groups goes to --group. Each account is created exactly as exp:user:create\n" .
                             "creates it. Choose --send-activation (every person gets the activation mail and sets a password\n" .
                             "with \"Forgot password\") or --disabled (the accounts start disabled; reset their passwords later).\n" .
                             "A file with a password column is refused: an import never takes passwords. A login that exists\n" .
                             "already is skipped; a login twice in the file is refused. Without --apply every row is checked\n" .
                             "and nothing is created.\n" .
                             "\n" .
                             "./console exp:user:import members.csv --group=Members --send-activation\n" .
                             "./console exp:user:import members.csv --group=Members --send-activation --apply\n" .
                             "./console exp:user:import staff.json --disabled --apply --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[type:][group:*][send-activation][disabled][apply][dry-run][as:][format:]', '[file]', array(
            'type' => 'The file type: csv (default) or json; import reads it from the file name when not given',
            'group' => 'A user group by node id or name: list, export and bulk take only its members (below it, at any depth); create and import: the group of the new accounts (repeat for more, the first is the main group; default [UserSettings] DefaultUserPlacement)',
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
        $file = isset( $options['arguments'][0] ) ? (string)$options['arguments'][0] : '';
        if ( $file === '' || !is_file( $file ) || !is_readable( $file ) )
            $this->usage( 'name a readable CSV or JSON file: exp:user:import <file>' );
        $type = isset( $options['type'] ) && $options['type'] !== false ? strtolower( trim( (string)$options['type'] ) ) : \expUserBulk::typeOf( $file );
        if ( !in_array( $type, \expUserBulk::TYPES, true ) )
            $this->usage( '--type must be csv or json (the file name does not say)' );
        if ( empty( $options['send-activation'] ) === empty( $options['disabled'] ) )
            $this->usage( 'choose one of --send-activation and --disabled' );
        $this->database( $format );
        $this->actor( $options, $format );
        $parsed = \expUserBulk::parse( (string)file_get_contents( $file ), $type );
        if ( $parsed['error'] !== '' )
            $this->finish( array( 'mode' => 'import', 'file' => $file, 'errors' => array( $parsed['error'] ), 'warnings' => array() ), $format );
        $result = \expUserBulk::import( $parsed['rows'], !empty( $options['send-activation'] ) ? 'send-activation' : 'disabled',
                                        self::listOption( isset( $options['group'] ) ? $options['group'] : array() ), $apply );
        $errors = $result['refused'] ? array( $result['refused'] . ' row(s) refused: fix them and import the file again (created and existing logins are skipped then)' ) : array();
        $this->finish( array( 'mode' => 'import', 'dry_run' => !$apply, 'file' => $file, 'type' => $type ) + $result + array( 'errors' => $errors, 'warnings' => array() ),
                       $format );
    }

    protected function lines( array $report )
    {
        if ( !isset( $report['rows'] ) )
            return array();
        $lines = array( sprintf( '%s: %d %s, %d skipped, %d refused%s', $report['file'], $report['created'], !empty( $report['dry_run'] ) ? 'would be created' : 'created',
                                 $report['skipped'], $report['refused'], !empty( $report['dry_run'] ) ? ' (dry run, nothing created; --apply creates them)' : '' ) );
        foreach ( $report['rows'] as $row )
            $lines[] = sprintf( '  line %-5d %-24s %-12s %s', $row['line'], self::cut( $row['login'], 24 ), $row['result'], implode( '; ', $row['errors'] ) );
        return $lines;
    }
}

}
