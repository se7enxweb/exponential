<?php
/**
 * @description Export the accounts the list filters select as CSV or JSON: login, e-mail, names, state, groups, last visit (never a password or its hash)
 * @alias exp:user:export
 *
 * File containing the exp:user:export command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserexport extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:export';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Export accounts\n" .
                             "Writes the accounts the filters of exp:user:list select, as CSV (a header line, cells that a\n" .
                             "spreadsheet would run as formulas made safe) or JSON (an array of objects): id, login, email,\n" .
                             "first_name, last_name, name, enabled, states, groups (node ids separated by |), main_group,\n" .
                             "last_visit, login_count. Never a password or a password hash. To standard output, or with --output\n" .
                             "to a new file of mode 0600 (then the command prints a report). The file holds personal data:\n" .
                             "keep it as such. exp:user:import reads the same columns.\n" .
                             "\n" .
                             "./console exp:user:export > users.csv\n" .
                             "./console exp:user:export --group=Members --state=enabled --output=var/members.csv\n" .
                             "./console exp:user:export --state=inactive --days=365 --type=json --output=var/inactive.json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[state:][group:][search:][days:][type:][output:][format:]', '', array(
            'state' => 'Only accounts in this state: all (default), enabled, disabled, unactivated, pending, locked, never, inactive, orphaned, duplicate_login or duplicate_email',
            'group' => 'A user group by node id or name: list, export and bulk take only its members (below it, at any depth); create and import: the group of the new accounts (repeat for more, the first is the main group; default [UserSettings] DefaultUserPlacement)',
            'search' => 'Only accounts whose login, name or e-mail address contains this text',
            'days' => 'With --state=inactive: not signed in for this many days (default 180)',
            'type' => 'The file type: csv (default) or json; import reads it from the file name when not given',
            'output' => 'Write the export to this file (new, mode 0600) instead of standard output',
            'format' => 'text (default) or json (the machine output exp-user/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $type = isset( $options['type'] ) && $options['type'] !== false ? strtolower( trim( (string)$options['type'] ) ) : 'csv';
        if ( !in_array( $type, \expUserBulk::TYPES, true ) )
            $this->usage( "--type must be csv or json, not $type" );
        $checked = \expUserManager::listFilters( $options );
        if ( $checked['errors'] )
            $this->usage( implode( '; ', $checked['errors'] ) );
        $output = isset( $options['output'] ) && $options['output'] !== false ? trim( (string)$options['output'] ) : '';
        if ( $output !== '' && ( file_exists( $output ) || !is_dir( dirname( $output ) ) ) )
            $this->usage( "--output must name a new file in a folder that exists, not $output" );
        $this->database( $format );
        if ( $output === '' )
        {
            // the data itself is the output: no report around it, the exit code says whether it worked
            $result = \expUserBulk::export( STDOUT, $checked['filters'], $type );
            if ( $result['error'] !== '' )
                $this->error( 'FAIL: ' . $result['error'] );
            $this->shutdown( $result['error'] !== '' ? \expUpgrade::EXIT_FAILED : \expUpgrade::EXIT_OK );
        }
        // a new file, readable by its owner only before anything is written to it (personal data)
        $handle = fopen( $output, 'x' );
        if ( !$handle )
            $this->usage( "--output: $output cannot be created" );
        chmod( $output, \eZFile::fileMode( 0600 ) );
        $result = \expUserBulk::export( $handle, $checked['filters'], $type );
        fclose( $handle );
        $this->finish( array( 'mode' => 'export', 'type' => $type, 'output' => $output, 'count' => $result['count'],
                              'errors' => $result['error'] !== '' ? array( $result['error'] ) : array(), 'warnings' => array() ), $format );
    }

    protected function lines( array $report )
    {
        return isset( $report['count'] ) ? array( sprintf( '%d account(s) written to %s (%s, mode 0600; it holds personal data)', $report['count'], $report['output'], $report['type'] ) )
                                         : array();
    }
}

}
