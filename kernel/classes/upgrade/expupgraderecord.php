<?php
/**
 * File containing the expUpgradeRecord class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The record of what exp:upgrade applied: table expupgrade_step, one row per step id (the last result wins). The
 * record is information, not the truth: a step's state comes from detect(), the record only tells "done" (applied by
 * exp:upgrade) from "not-needed" (in place without it) and keeps the last failure's message.
 *
 * The table is created by the first database step (6.0.15-upgrade-record-table); until it exists nothing is read or
 * written and every step without a record counts as never run.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeRecord
{
    const TABLE = 'expupgrade_step';

    /**
     * Whether the table exists (true, false, null when it cannot be told).
     *
     * @param eZDBInterface $db
     * @return bool|null
     */
    public static function exists( $db )
    {
        return expKernelTableSchema::exists( self::TABLE, $db, true );
    }

    /**
     * Every row: step id => array( step_id, stage, version, state, applied_at, duration_ms, applied_by, engine,
     * checksum, message ).
     *
     * @param eZDBInterface $db
     * @return array
     */
    public static function all( $db )
    {
        if ( self::exists( $db ) !== true )
            return array();
        $rows = expKernelTableSchema::tryArrayQuery( $db, 'SELECT * FROM ' . self::TABLE );
        $all = array();
        foreach ( (array)$rows as $row )
        {
            $row = array_change_key_case( (array)$row, CASE_LOWER );
            if ( !isset( $row['step_id'] ) )
                continue;
            foreach ( array( 'applied_at', 'duration_ms' ) as $int )
                $row[$int] = isset( $row[$int] ) ? (int)$row[$int] : 0;
            $all[(string)$row['step_id']] = $row;
        }
        return $all;
    }

    /**
     * Writes the result of a step (replaces its row).
     *
     * @param eZDBInterface $db
     * @param expUpgradeStep $step
     * @param string $state done or failed
     * @param int $durationMs
     * @param string $message
     * @return bool false when the table does not exist
     */
    public static function write( $db, expUpgradeStep $step, $state, $durationMs, $message = '' )
    {
        if ( self::exists( $db ) !== true )
            return false;
        $values = array(
            'step_id' => $step->id(),
            'stage' => $step->stage(),
            'version' => $step->version(),
            'state' => (string)$state,
            'applied_at' => time(),
            'duration_ms' => (int)$durationMs,
            'applied_by' => self::by(),
            'engine' => expKernelTableSchema::type( $db ),
            'checksum' => self::checksum( $step ),
            'message' => mb_substr( (string)$message, 0, 1000 ),
        );
        $sqlValues = array();
        foreach ( $values as $name => $value )
            $sqlValues[] = is_int( $value ) ? (string)$value : "'" . $db->escapeString( (string)$value ) . "'";
        $db->begin();
        $db->query( 'DELETE FROM ' . self::TABLE . " WHERE step_id = '" . $db->escapeString( $step->id() ) . "'" );
        $db->query( 'INSERT INTO ' . self::TABLE . ' ( ' . implode( ', ', array_keys( $values ) ) . ' ) VALUES ( ' . implode( ', ', $sqlValues ) . ' )' );
        $db->commit();
        return true;
    }

    /**
     * Records a caption (expUpgradeCaption::take()): a row "caption:<id>" with the stage "caption", the kind as its
     * state (pre, post, safety, manual) and the label and source as its message.
     *
     * @param eZDBInterface $db
     * @param string $id
     * @param string $kind
     * @param string $label
     * @param string $source
     * @return bool false when the table does not exist
     */
    public static function writeCaption( $db, $id, $kind, $label, $source )
    {
        if ( self::exists( $db ) !== true )
            return false;
        $stepId = 'caption:' . $id;
        $values = array( 'step_id' => $stepId, 'stage' => 'caption', 'version' => '', 'state' => (string)$kind, 'applied_at' => time(),
                         'duration_ms' => 0, 'applied_by' => self::by(), 'engine' => expKernelTableSchema::type( $db ), 'checksum' => '',
                         'message' => mb_substr( "$label ($source)", 0, 1000 ) );
        $sql = array();
        foreach ( $values as $v )
            $sql[] = is_int( $v ) ? (string)$v : "'" . $db->escapeString( (string)$v ) . "'";
        $db->begin();
        $db->query( 'DELETE FROM ' . self::TABLE . " WHERE step_id = '" . $db->escapeString( $stepId ) . "'" );
        $db->query( 'INSERT INTO ' . self::TABLE . ' ( ' . implode( ', ', array_keys( $values ) ) . ' ) VALUES ( ' . implode( ', ', $sql ) . ' )' );
        $db->commit();
        return true;
    }

    /**
     * The SHA-256 of the file that defines the step's class, so a record says which code applied it.
     *
     * @param expUpgradeStep $step
     * @return string
     */
    public static function checksum( expUpgradeStep $step )
    {
        $file = ( new ReflectionClass( $step ) )->getFileName();
        return $file && is_readable( $file ) ? hash_file( 'sha256', $file ) : '';
    }

    /**
     * Who runs the upgrade: the system user and, in a web request, the signed-in login. Never a password.
     *
     * @return string
     */
    public static function by()
    {
        $who = function_exists( 'posix_geteuid' ) && function_exists( 'posix_getpwuid' ) ? posix_getpwuid( posix_geteuid() ) : false;
        $name = is_array( $who ) && isset( $who['name'] ) ? $who['name'] : (string)get_current_user();
        return php_sapi_name() === 'cli' ? 'cli ' . $name : 'web ' . $name;
    }
}
