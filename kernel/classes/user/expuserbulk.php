<?php
/**
 * File containing the expUserBulk class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Many accounts at once, from the console (exp:user:export, exp:user:import, exp:user:bulk). Each account goes through
 * expUserManager exactly as one account does: an import creates every account as exp:user:create does, a bulk action
 * is the single action repeated, with the same refusals. Nothing here knows a password: an export never holds one or
 * its hash, and an import file with a password column is refused.
 *
 * Guide: doc/eng-us/guides/user-management-from-the-console.md
 */
class expUserBulk
{
    /** The columns of an export, in order */
    const EXPORT_COLUMNS = array( 'id', 'login', 'email', 'first_name', 'last_name', 'name', 'enabled', 'states', 'groups', 'main_group', 'last_visit',
                                  'login_count' );

    /** The columns an import reads; login and email are required */
    const IMPORT_COLUMNS = array( 'login', 'email', 'first_name', 'last_name', 'groups' );

    /** The actions of exp:user:bulk */
    const ACTIONS = array( 'disable', 'enable', 'delete', 'approve', 'reject', 'unlock' );

    /** The types of an export or import file */
    const TYPES = array( 'csv', 'json' );

    /** The most accounts one bulk run changes */
    const MAX_BULK = 5000;

    /** The most rows one import file holds */
    const MAX_IMPORT = 10000;

    // ---- export -----------------------------------------------------------------------------------------------------

    /**
     * One account as an export row: never a password or its hash. Pure.
     *
     * @param array $account see expUserHygiene::enrich()
     * @param array $names first_name, last_name
     * @return array the columns of EXPORT_COLUMNS
     */
    public static function exportRow( array $account, array $names = array() )
    {
        $groups = array();
        foreach ( isset( $account['groups'] ) ? $account['groups'] : array() as $group )
            $groups[] = (int)$group['node_id'];
        return array( 'id' => (int)$account['id'], 'login' => (string)$account['login'], 'email' => (string)$account['email'],
                      'first_name' => isset( $names['first_name'] ) ? (string)$names['first_name'] : '',
                      'last_name' => isset( $names['last_name'] ) ? (string)$names['last_name'] : '',
                      'name' => (string)$account['name'], 'enabled' => !empty( $account['enabled'] ),
                      'states' => implode( '|', isset( $account['states'] ) ? (array)$account['states'] : array() ),
                      'groups' => implode( '|', $groups ), 'main_group' => $groups ? $groups[0] : 0,
                      'last_visit' => !empty( $account['last_visit'] ) ? gmdate( 'Y-m-d\TH:i:s\Z', (int)$account['last_visit'] ) : '',
                      'login_count' => (int)$account['login_count'] );
    }

    /**
     * The first and last name of the accounts (the attributes first_name and last_name, where the class has them).
     *
     * @param int[] $ids
     * @return array id => first_name, last_name
     */
    protected static function names( array $ids )
    {
        $out = array();
        foreach ( $ids as $id )
        {
            $object = eZContentObject::fetch( (int)$id );
            $names = array( 'first_name' => '', 'last_name' => '' );
            if ( $object instanceof eZContentObject )
            {
                $map = $object->attribute( 'data_map' );
                foreach ( array_keys( $names ) as $field )
                    if ( isset( $map[$field] ) )
                        $names[$field] = (string)$map[$field]->toString();
            }
            $out[(int)$id] = $names;
            eZContentObject::clearCache( array( (int)$id ) );
        }
        return $out;
    }

    /**
     * Writes the accounts the list filters select to a stream, as CSV (a header line, every cell safe from being run as
     * a formula) or JSON (an array of objects), in batches of 500 by id.
     *
     * @param resource $out
     * @param array $filters see expUserManager::listFilters()
     * @param string $type csv or json
     * @return array count, error
     */
    public static function export( $out, array $filters, $type )
    {
        $selected = expUserManager::selection( $filters );
        if ( $selected['error'] !== '' )
            return array( 'count' => 0, 'error' => $selected['error'] );
        $hygiene = $selected['hygiene'];
        if ( $type === 'csv' )
            fputcsv( $out, self::EXPORT_COLUMNS, ',', '"', '' );
        else
            fwrite( $out, "[\n" );
        $count = 0;
        $after = 0;
        do
        {
            $accounts = $hygiene->batch( $selected['filters'], true, $selected['group_path'], $after, 500 );
            $names = self::names( array_column( $accounts, 'id' ) );
            foreach ( $accounts as $account )
            {
                $row = self::exportRow( $account, $names[(int)$account['id']] );
                if ( $type === 'csv' )
                    fputcsv( $out, array_map( function ( $v ) { return expUserAudit::csvCell( is_bool( $v ) ? ( $v ? '1' : '0' ) : $v ); }, array_values( $row ) ), ',', '"', '' );
                else
                    fwrite( $out, ( $count ? ",\n" : '' ) . '  ' . json_encode( $row, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
                $count++;
                $after = (int)$account['id'];
            }
        }
        while ( count( $accounts ) === 500 );
        if ( $type === 'json' )
            fwrite( $out, ( $count ? "\n" : '' ) . "]\n" );
        return array( 'count' => $count, 'error' => '' );
    }

    // ---- import -----------------------------------------------------------------------------------------------------

    /**
     * The type of a file by its name: csv or json, '' when neither. Pure.
     *
     * @param string $file
     * @return string
     */
    public static function typeOf( $file )
    {
        $ext = strtolower( pathinfo( (string)$file, PATHINFO_EXTENSION ) );
        return in_array( $ext, self::TYPES, true ) ? $ext : '';
    }

    /**
     * The rows of an import file's text: each with its line (CSV: the line number; JSON: the position, from 1) and
     * the columns of IMPORT_COLUMNS. A file with a column that holds a password (any column whose name contains
     * "password" or "hash"), an unknown column or no login or email column is refused as a whole. A cell that an
     * export protected from being run as a formula ('=...) gets its text back. Pure.
     *
     * @param string $text
     * @param string $type csv or json
     * @return array rows, error ('' when read)
     */
    public static function parse( $text, $type )
    {
        $text = preg_replace( '/^\xEF\xBB\xBF/', '', (string)$text );
        $records = array();
        if ( $type === 'json' )
        {
            $data = json_decode( $text, true );
            if ( !is_array( $data ) || ( $data && array_keys( $data ) !== range( 0, count( $data ) - 1 ) ) )
                return array( 'rows' => array(), 'error' => 'the JSON file must hold an array of objects' );
            foreach ( $data as $i => $item )
            {
                if ( !is_array( $item ) )
                    return array( 'rows' => array(), 'error' => 'entry ' . ( $i + 1 ) . ' is not an object' );
                $records[] = array( $i + 1, $item );
            }
        }
        else
        {
            $lines = preg_split( '/\r\n|\n|\r/', $text );
            $header = null;
            foreach ( $lines as $n => $line )
            {
                if ( trim( $line ) === '' )
                    continue;
                $cells = str_getcsv( $line, ',', '"', '' );
                if ( $header === null )
                {
                    $header = array_map( function ( $c ) { return strtolower( trim( (string)$c ) ); }, $cells );
                    continue;
                }
                $item = array();
                foreach ( $header as $k => $column )
                    $item[$column] = isset( $cells[$k] ) ? $cells[$k] : '';
                $records[] = array( $n + 1, $item );
            }
            if ( $header === null )
                return array( 'rows' => array(), 'error' => 'the CSV file is empty: it needs a header line (' . implode( ',', self::IMPORT_COLUMNS ) . ') and a line per account' );
        }
        // the columns of the file: a CSV file's header, even without a line below it
        $columns = isset( $header ) ? array_fill_keys( array_filter( $header, 'strlen' ), true ) : array();
        foreach ( $records as $record )
            $columns += array_fill_keys( array_map( 'strtolower', array_keys( $record[1] ) ), true );
        foreach ( array_keys( $columns ) as $column )
        {
            if ( strpos( $column, 'password' ) !== false || strpos( $column, 'hash' ) !== false )
                return array( 'rows' => array(), 'error' => "the file has a password column ($column): an import never takes passwords; remove the column and use --send-activation" );
            if ( !in_array( $column, self::IMPORT_COLUMNS, true ) )
                return array( 'rows' => array(), 'error' => "unknown column $column (the columns are " . implode( ', ', self::IMPORT_COLUMNS ) . ')' );
        }
        if ( $columns && ( !isset( $columns['login'] ) || !isset( $columns['email'] ) ) )
            return array( 'rows' => array(), 'error' => 'the file needs the columns login and email' );
        if ( count( $records ) > self::MAX_IMPORT )
            return array( 'rows' => array(), 'error' => count( $records ) . ' rows: at most ' . self::MAX_IMPORT . ' in one file' );
        $rows = array();
        foreach ( $records as $record )
        {
            $row = array( 'line' => $record[0] );
            $item = array_change_key_case( $record[1], CASE_LOWER );
            foreach ( self::IMPORT_COLUMNS as $column )
            {
                $value = isset( $item[$column] ) && is_scalar( $item[$column] ) ? trim( (string)$item[$column] ) : '';
                if ( strlen( $value ) > 1 && $value[0] === "'" && strpos( "=+-@", $value[1] ) !== false )
                    $value = substr( $value, 1 );
                $row[$column] = $value;
            }
            $rows[] = $row;
        }
        return array( 'rows' => $rows, 'error' => '' );
    }

    /**
     * Imports the rows: each account created as exp:user:create creates it (expUserManager::create()), either with
     * the activation mail (the person sets a password with "Forgot password") or disabled (with a random password
     * nobody knows, which you reset later). An existing login is skipped, a login twice in the file refused.
     *
     * @param array $rows see parse()
     * @param string $mode send-activation or disabled
     * @param string[] $defaultGroups the groups of a row without groups
     * @param bool $apply
     * @return array rows (line, login, result: created|would_create|skipped|refused, errors, user_id), created, skipped, refused
     */
    public static function import( array $rows, $mode, array $defaultGroups, $apply )
    {
        $out = array( 'rows' => array(), 'created' => 0, 'skipped' => 0, 'refused' => 0 );
        $seen = array();
        foreach ( $rows as $row )
        {
            $entry = array( 'line' => $row['line'], 'login' => $row['login'], 'result' => 'refused', 'errors' => array() );
            $key = mb_strtolower( $row['login'] );
            if ( $row['login'] !== '' && isset( $seen[$key] ) )
                $entry['errors'][] = 'the login is in the file twice (line ' . $seen[$key] . ')';
            else if ( $row['login'] !== '' && eZUser::fetchByName( $row['login'] ) instanceof eZUser )
            {
                $entry['result'] = 'skipped';
                $entry['errors'][] = 'an account has this login already';
            }
            if ( $row['login'] !== '' && !isset( $seen[$key] ) )
                $seen[$key] = $row['line'];
            if ( !$entry['errors'] )
            {
                $groups = $row['groups'] !== '' ? array_values( array_filter( array_map( 'trim', explode( '|', $row['groups'] ) ), 'strlen' ) ) : $defaultGroups;
                $spec = array( 'login' => $row['login'], 'email' => $row['email'], 'first_name' => $row['first_name'], 'last_name' => $row['last_name'],
                               'groups' => $groups );
                $spec += $mode === 'send-activation' ? array( 'send_activation' => true ) : array( 'generate' => true, 'disabled' => true );
                $r = expUserManager::create( $spec, $apply );
                unset( $r['generated_password'] );
                $entry['errors'] = $r['errors'];
                $entry['warnings'] = $r['warnings'];
                if ( !$r['errors'] )
                    $entry['result'] = $apply ? 'created' : 'would_create';
                if ( isset( $r['user_id'] ) )
                    $entry['user_id'] = $r['user_id'];
            }
            if ( $entry['result'] === 'skipped' )
                $out['skipped']++;
            else if ( $entry['result'] === 'refused' )
                $out['refused']++;
            else
                $out['created']++;
            $out['rows'][] = $entry;
        }
        return $out;
    }

    // ---- bulk -------------------------------------------------------------------------------------------------------

    /**
     * Whether --apply may go ahead: --confirm must be the number of accounts the filter selects now. Pure.
     *
     * @param int $matched
     * @param string|false|null $confirm
     * @return string '' when it may, else why not
     */
    public static function confirmProblem( $matched, $confirm )
    {
        $confirm = $confirm === null || $confirm === false ? '' : trim( (string)$confirm );
        if ( $confirm === '' )
            return "--apply needs --confirm=$matched: the number of accounts the filter selects (run without --apply to see them)";
        if ( !ctype_digit( $confirm ) || (int)$confirm !== (int)$matched )
            return "--confirm=$confirm does not match: the filter selects $matched account(s) now; check the list again";
        return '';
    }

    /**
     * Does one action to every account the filters select (at most $max), each through expUserManager as the
     * single command does it, with its refusals: a protected account or the last administrator is listed and never
     * changed.
     *
     * @param string $action one of ACTIONS
     * @param array $filters see expUserManager::listFilters()
     * @param int $max
     * @param int $actorID
     * @param bool $apply
     * @param string|null $confirm
     * @return array matched, accounts (id, login, result: done|would|refused|unchanged|failed, reason), done, refused, errors, warnings
     */
    public static function run( $action, array $filters, $max, $actorID, $apply, $confirm = null )
    {
        $out = array( 'action' => $action, 'matched' => 0, 'accounts' => array(), 'done' => 0, 'refused' => 0, 'errors' => array(), 'warnings' => array() );
        if ( !in_array( $action, self::ACTIONS, true ) )
        {
            $out['errors'][] = 'the action is one of ' . implode( ', ', self::ACTIONS );
            return $out;
        }
        $selected = expUserManager::selection( $filters );
        if ( $selected['error'] !== '' )
        {
            $out['errors'][] = $selected['error'];
            return $out;
        }
        $total = $selected['hygiene']->count( $selected['filters'], true, $selected['group_path'] );
        $out['matched'] = $total;
        if ( $total > $max )
        {
            $out['errors'][] = "the filter selects $total accounts, more than --limit=$max: narrow it, or raise --limit (at most " . self::MAX_BULK . ')';
            return $out;
        }
        if ( $apply )
        {
            $problem = self::confirmProblem( $total, $confirm );
            if ( $problem !== '' )
            {
                $out['errors'][] = $problem;
                return $out;
            }
        }
        $ids = array();
        $after = 0;
        do
        {
            $batch = $selected['hygiene']->batch( $selected['filters'], true, $selected['group_path'], $after, 500 );
            foreach ( $batch as $account )
            {
                $ids[] = (int)$account['id'];
                $after = (int)$account['id'];
            }
        }
        while ( count( $batch ) === 500 && count( $ids ) < $max );
        foreach ( $ids as $id )
        {
            $user = eZUser::fetch( $id );
            $entry = array( 'id' => $id, 'login' => $user instanceof eZUser ? (string)$user->attribute( 'login' ) : '', 'result' => 'failed', 'reason' => '' );
            if ( !$user instanceof eZUser )
                $entry['reason'] = 'the account is gone';
            else
            {
                $r = self::one( $action, $user, $actorID, $apply );
                if ( $r['errors'] )
                {
                    // refused while planning (nothing to do to it), or failed while doing what was planned
                    $entry['result'] = $apply && $r['changes'] ? 'failed' : 'refused';
                    $entry['reason'] = implode( '; ', $r['errors'] );
                }
                else if ( !$r['changes'] )
                {
                    $entry['result'] = 'unchanged';
                    $entry['reason'] = implode( '; ', $r['warnings'] );
                }
                else
                    $entry['result'] = $apply ? ( !empty( $r['applied'] ) ? 'done' : 'failed' ) : 'would';
            }
            if ( in_array( $entry['result'], array( 'done', 'would' ), true ) )
                $out['done']++;
            else if ( $entry['result'] === 'refused' )
                $out['refused']++;
            $out['accounts'][] = $entry;
            eZContentObject::clearCache( array( $id ) );
        }
        return $out;
    }

    /**
     * One action to one account.
     *
     * @param string $action
     * @param eZUser $user
     * @param int $actorID
     * @param bool $apply
     * @return array the result of expUserManager
     */
    protected static function one( $action, eZUser $user, $actorID, $apply )
    {
        switch ( $action )
        {
            case 'disable':
                return expUserManager::setEnabled( $user, false, $actorID, $apply );
            case 'enable':
                return expUserManager::setEnabled( $user, true, $actorID, $apply );
            case 'delete':
                return expUserManager::delete( $user, $actorID, $apply );
            case 'approve':
                return expUserManager::decideRegistration( $user, false, $actorID, $apply );
            case 'reject':
                return expUserManager::decideRegistration( $user, true, $actorID, $apply );
        }
        return expUserManager::unlock( $user, $apply );
    }
}
