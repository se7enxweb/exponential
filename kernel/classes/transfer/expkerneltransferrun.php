<?php
/**
 * File containing the expKernelTransferRun class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One run of an import or export tool: a row of exp_transfer_run (doc/eng-us/features/6.0/transfer-runs.md).
 *
 * Every tool of the transfer registry writes one row per run through expKernelTransferHistory, whatever ran it
 * (expqueue, the kernel's content jobs, its own process): the tool (its registry id, transfer.xrowextract), the
 * direction and kind, what it covered (scope, scope_ref), who ran it, when, how it ended, the counts, the bytes, the
 * format, a file reference relative to the tool (never a path), the engine and its job (job_ref), the tool's own
 * job or run (tool_ref) and the link to the tool's own page of the run (detail_url). The tools keep their detailed
 * records (rows, logs, files) themselves; this row is the summary every page can list and count.
 *
 * normalize() is pure: it is what every write goes through, so the values a row can hold are tested without a
 * database.
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelTransferRun extends eZPersistentObject
{
    const TABLE = 'exp_transfer_run';

    const STATE_QUEUED = 'queued';
    const STATE_RUNNING = 'running';
    const STATE_DONE = 'done';
    const STATE_FAILED = 'failed';
    const STATE_CANCELLED = 'cancelled';

    /** Every state; the last three end a run */
    const STATES = array( 'queued', 'running', 'done', 'failed', 'cancelled' );
    const FINAL_STATES = array( 'done', 'failed', 'cancelled' );

    /** The kinds of run: export and import are the usual ones, the others are runs a tool marks as such */
    const KINDS = array( 'export', 'import', 'dry_run', 'retry', 'undo' );

    const DIRECTIONS = array( 'import', 'export' );

    /** What ran it: expqueue, the kernel's content jobs, the tool's own process, or the request itself (inline) */
    const ENGINES = array( 'expqueue', 'contentjob', 'process', 'inline' );

    /** The text columns and their lengths (values are cut to them, by characters) */
    const LENGTHS = array( 'tool' => 64, 'direction' => 10, 'kind' => 20, 'title' => 255, 'state' => 16, 'format' => 32,
                           'scope' => 20, 'scope_ref' => 255, 'file_ref' => 255, 'engine' => 16, 'job_ref' => 64,
                           'tool_ref' => 64, 'detail_url' => 255, 'error' => 255, 'siteaccess' => 64 );

    /** The number columns */
    const NUMBERS = array( 'user_id', 'created', 'modified', 'started', 'finished', 'duration', 'rows_total', 'rows_done',
                           'rows_failed', 'bytes' );

    /** The most error messages detail keeps */
    const MAX_ERRORS = 20;

    public static function definition()
    {
        static $definition = null;
        if ( $definition === null )
        {
            $fields = array( 'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ) );
            foreach ( self::NUMBERS as $name )
                $fields[$name] = array( 'name' => $name, 'datatype' => 'integer', 'default' => 0, 'required' => true );
            foreach ( array_keys( self::LENGTHS ) as $name )
                $fields[$name] = array( 'name' => $name, 'datatype' => 'string', 'default' => '', 'required' => true );
            $fields['detail'] = array( 'name' => 'Detail', 'datatype' => 'text', 'default' => '', 'required' => false );
            $definition = array(
                'fields' => $fields,
                'keys' => array( 'id' ),
                'increment_key' => 'id',
                'function_attributes' => array(
                    'detail_array' => 'detailArray',
                    'is_final' => 'isFinal',
                    'outcome' => 'outcome',
                ),
                'sort' => array( 'started' => 'desc', 'id' => 'desc' ),
                'class_name' => 'expKernelTransferRun',
                'name' => self::TABLE,
            );
        }
        return $definition;
    }

    /**
     * The values of a write, cleaned: only known columns, numbers as int (never negative), texts cut to their
     * column, state, kind, direction and engine from their lists (an unknown one is left out), detail as JSON.
     *
     * @param array $info column => value; 'detail' may be an array; 'errors' (a list of messages) goes into detail
     * @return array
     */
    public static function normalize( array $info )
    {
        $out = array();
        foreach ( self::NUMBERS as $name )
            if ( array_key_exists( $name, $info ) && is_numeric( $info[$name] ) )
                $out[$name] = max( 0, (int)$info[$name] );
        foreach ( self::LENGTHS as $name => $length )
        {
            if ( !array_key_exists( $name, $info ) || !is_scalar( $info[$name] ) )
                continue;
            $value = trim( preg_replace( '/[\x00-\x1F\x7F]+/u', ' ', (string)$info[$name] ) ?? '' );
            $out[$name] = function_exists( 'mb_substr' ) ? mb_substr( $value, 0, $length, 'UTF-8' ) : substr( $value, 0, $length );
        }
        $lists = array( 'state' => self::STATES, 'kind' => self::KINDS, 'direction' => self::DIRECTIONS, 'engine' => self::ENGINES );
        foreach ( $lists as $name => $allowed )
            if ( isset( $out[$name] ) && !in_array( $out[$name], $allowed, true ) )
                unset( $out[$name] );
        if ( isset( $out['detail_url'] ) && !self::isRelativeUrl( $out['detail_url'] ) )
            unset( $out['detail_url'] );
        if ( isset( $out['file_ref'] ) && ( strpos( $out['file_ref'], '..' ) !== false || $out['file_ref'][0] === '/' ) )
            $out['file_ref'] = basename( $out['file_ref'] );
        $detail = null;
        if ( array_key_exists( 'detail', $info ) )
            $detail = is_array( $info['detail'] ) ? $info['detail'] : array();
        if ( isset( $info['errors'] ) && is_array( $info['errors'] ) )
        {
            $detail = (array)$detail;
            $detail['errors'] = array_slice( array_values( array_filter( array_map( function ( $m ) {
                return is_scalar( $m ) ? mb_substr( trim( (string)$m ), 0, 500 ) : '';
            }, $info['errors'] ), 'strlen' ) ), 0, self::MAX_ERRORS );
        }
        if ( $detail !== null )
        {
            $json = json_encode( $detail, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR );
            $out['detail'] = $json === false || $json === '[]' ? '' : $json;
        }
        return $out;
    }

    /**
     * Whether a link is a module URL of this installation (module/view/...), never an absolute or scheme URL.
     *
     * @param string $url
     * @return bool
     */
    public static function isRelativeUrl( $url )
    {
        $url = (string)$url;
        return $url === '' || ( preg_match( '#^/?[A-Za-z0-9_]+(/[^\s]*)?$#', $url ) === 1 && strpos( $url, '//' ) === false );
    }

    /** @return array the decoded detail */
    public function detailArray()
    {
        $detail = json_decode( (string)$this->attribute( 'detail' ), true );
        return is_array( $detail ) ? $detail : array();
    }

    /** @return bool */
    public function isFinal()
    {
        return in_array( $this->attribute( 'state' ), self::FINAL_STATES, true );
    }

    /**
     * How the run ended, in the words of the statistics: completed, completed_with_errors, failed, cancelled,
     * running (also for a queued run).
     *
     * @return string
     */
    public function outcome()
    {
        return self::outcomeOf( (string)$this->attribute( 'state' ), (int)$this->attribute( 'rows_failed' ) );
    }

    /**
     * @param string $state
     * @param int $failed
     * @return string
     */
    public static function outcomeOf( $state, $failed )
    {
        switch ( $state )
        {
            case self::STATE_DONE: return (int)$failed > 0 ? 'completed_with_errors' : 'completed';
            case self::STATE_FAILED: return 'failed';
            case self::STATE_CANCELLED: return 'cancelled';
            default: return 'running';
        }
    }

    /**
     * The row as a plain array (every column, detail decoded, the outcome).
     *
     * @return array
     */
    public function toArray()
    {
        $out = array( 'id' => (int)$this->attribute( 'id' ) );
        foreach ( self::NUMBERS as $name )
            $out[$name] = (int)$this->attribute( $name );
        foreach ( array_keys( self::LENGTHS ) as $name )
            $out[$name] = (string)$this->attribute( $name );
        $out['detail'] = $this->detailArray();
        $out['outcome'] = $this->outcome();
        $out['final'] = $this->isFinal();
        return $out;
    }

    /**
     * @param int $id
     * @return expKernelTransferRun|null
     */
    public static function fetch( $id )
    {
        $row = eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)$id ) );
        return $row instanceof self ? $row : null;
    }
}
