<?php
/**
 * File containing the expDataTableWriter class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Writes rows into a persistent class's table, the one way every import, sync and tool puts rows into a data table
 * (doc/eng-us/features/6.0/data-tables.md, "Writing rows"): checked against the class's definition, on the class's
 * connection, matched by a unique key, a transaction per chunk, never raw SQL.
 *
 *   $writer = new expDataTableWriter( 'myArchiveRow', array( 'keys' => array( 'remote_id' ), 'mode' => 'upsert' ) );
 *   $result = $writer->write( $rows );     // inserted, updated, unchanged, skipped, failed, errors, ids
 *
 * Options:
 *   keys       the fields a row is matched by (the class's keys when none are given; the counted key is left out)
 *   mode       insert (only new rows), update (only existing rows), upsert (both, the default), replace (an existing
 *              row is removed and written again)
 *   dry_run    check and count, write nothing
 *   chunk      rows per transaction (default 200)
 *   unknown    what an unknown field does: refuse (the row fails, the default) or ignore
 *
 * A value of a relation column (object, node, user) may be a reference instead of an id: @object:<remote id>,
 * @node:<remote id> or @path:<url alias> (expDataTableRelation). A row that cannot be written fails alone; its error
 * names the row's number and the field, never a value of a field whose name says it is a secret.
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataTableWriter
{
    const MODES = array( 'insert', 'update', 'upsert', 'replace' );

    /** @var array the catalogue entry */
    protected $entry;

    /** @var array the definition */
    protected $definition;

    /** @var array */
    protected $options;

    /** @var array column => relation */
    protected $relations = array();

    /**
     * @param string $class a persistent class of the catalogue
     * @param array $options keys, mode, dry_run, chunk, unknown
     * @throws InvalidArgumentException when the class is not a persistent class with a table
     */
    public function __construct( $class, array $options = array() )
    {
        $entry = expDataTableCatalogue::instance()->entry( (string)$class );
        // a class the catalogue has not seen yet (made after it was built): from its own definition
        if ( $entry === null && class_exists( (string)$class ) && is_subclass_of( (string)$class, 'eZPersistentObject' ) )
            $entry = expDataTableCatalogue::entryFromDefinition( (string)$class, call_user_func( array( (string)$class, 'definition' ) ) );
        if ( $entry === null || $entry['table'] === '' || $entry['error'] !== '' || !class_exists( $entry['class'] ) )
            throw new InvalidArgumentException( "'$class' is not a persistent class with a table" );
        $this->entry = $entry;
        $this->definition = call_user_func( array( $entry['class'], 'definition' ) );
        $keys = isset( $options['keys'] ) && $options['keys'] ? array_values( array_map( 'strval', (array)$options['keys'] ) ) : $entry['keys'];
        if ( $entry['increment_key'] !== '' && count( $keys ) > 1 )
            $keys = array_values( array_diff( $keys, array( $entry['increment_key'] ) ) );
        foreach ( $keys as $key )
            if ( !isset( $entry['fields'][$key] ) )
                throw new InvalidArgumentException( "'$key' is not a field of $class" );
        $mode = isset( $options['mode'] ) && in_array( $options['mode'], self::MODES, true ) ? $options['mode'] : 'upsert';
        $this->options = array( 'keys' => $keys, 'mode' => $mode, 'dry_run' => !empty( $options['dry_run'] ),
                                'chunk' => isset( $options['chunk'] ) ? max( 1, min( 5000, (int)$options['chunk'] ) ) : 200,
                                'unknown' => isset( $options['unknown'] ) && $options['unknown'] === 'ignore' ? 'ignore' : 'refuse' );
        foreach ( $entry['relations'] as $relation )
            $this->relations[$relation['column']] = $relation;
    }

    /** @return array the options in use */
    public function options()
    {
        return $this->options;
    }

    /**
     * Writes rows.
     *
     * @param array $rows list of field => value
     * @return array inserted, updated, unchanged, skipped, failed, errors (list of strings), ids (row number => key)
     */
    public function write( array $rows )
    {
        $result = array( 'inserted' => 0, 'updated' => 0, 'unchanged' => 0, 'skipped' => 0, 'failed' => 0, 'errors' => array(), 'ids' => array() );
        $db = eZPersistentObject::dbFor( $this->definition );
        // a failing statement throws instead of ending the request; the chunk is then rolled back and written again
        // row by row, so a bad row fails alone
        $before = expAuditIndexSchema::errorHandling( $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            foreach ( array_chunk( $rows, $this->options['chunk'], true ) as $chunk )
            {
                if ( $this->options['dry_run'] )
                {
                    $this->writeRows( $chunk, $result, false );
                    continue;
                }
                $chunkResult = self::emptyResult();
                $db->begin();
                try
                {
                    $this->writeRows( $chunk, $chunkResult, true );
                    $db->commit();
                }
                catch ( Throwable $e )
                {
                    // the driver rolled back already when it reported the error
                    if ( $db->transactionCounter() > 0 )
                        $db->rollback();
                    $chunkResult = self::emptyResult();
                    $this->writeRows( $chunk, $chunkResult, false, $db );
                }
                foreach ( array( 'inserted', 'updated', 'unchanged', 'skipped', 'failed' ) as $key )
                    $result[$key] += $chunkResult[$key];
                $result['errors'] = array_merge( $result['errors'], $chunkResult['errors'] );
                $result['ids'] += $chunkResult['ids'];
            }
        }
        finally
        {
            $db->setErrorHandling( $before );
        }
        return $result;
    }

    /** @return array an empty result */
    protected static function emptyResult()
    {
        return array( 'inserted' => 0, 'updated' => 0, 'unchanged' => 0, 'skipped' => 0, 'failed' => 0, 'errors' => array(), 'ids' => array() );
    }

    /**
     * @param array $rows
     * @param array $result
     * @param bool $inTransaction a database error is thrown on (the chunk is redone row by row)
     * @param eZDBInterface|null $db row by row: each row in a transaction of its own, so that the driver reports a
     *                               failed statement as an exception (outside a transaction it only logs it)
     * @throws Throwable a database error while $inTransaction
     */
    protected function writeRows( array $rows, array &$result, $inTransaction, $db = null )
    {
        foreach ( $rows as $number => $row )
        {
            $rowResult = self::emptyResult();
            try
            {
                if ( $db )
                    $db->begin();
                $this->writeRow( (int)$number, is_array( $row ) ? $row : array(), $rowResult );
                if ( $db )
                    $db->commit();
            }
            catch ( Throwable $e )
            {
                if ( $db && $db->transactionCounter() > 0 )
                    $db->rollback();
                if ( $inTransaction && $e instanceof eZDBException )
                    throw $e;
                $rowResult = self::emptyResult();
                $rowResult['failed'] = 1;
                $rowResult['errors'][] = 'row ' . ( (int)$number + 1 ) . ': ' . mb_substr( $e->getMessage(), 0, 200 );
            }
            foreach ( array( 'inserted', 'updated', 'unchanged', 'skipped', 'failed' ) as $key )
                $result[$key] += $rowResult[$key];
            $result['errors'] = array_merge( $result['errors'], $rowResult['errors'] );
            $result['ids'] += $rowResult['ids'];
        }
    }

    /**
     * A row checked and typed against the definition (pure apart from references).
     *
     * @param array $row
     * @param string[] $errors
     * @return array|null null when the row cannot be written
     */
    public function prepare( array $row, &$errors = array() )
    {
        $clean = array();
        foreach ( $row as $field => $value )
        {
            if ( !isset( $this->entry['fields'][$field] ) )
            {
                if ( $this->options['unknown'] === 'refuse' )
                    $errors[] = "unknown field '$field'";
                continue;
            }
            if ( expDataTableRelation::isReference( $value ) && isset( $this->relations[$field] ) )
            {
                $resolved = expDataTableRelation::resolveReference( $value, $this->relations[$field]['kind'] );
                if ( $resolved === null )
                {
                    $errors[] = "$field: nothing has the reference $value";
                    continue;
                }
                $value = $resolved;
            }
            $clean[$field] = self::cast( $this->entry['fields'][$field]['datatype'], $value );
        }
        foreach ( $this->options['keys'] as $key )
            if ( !isset( $clean[$key] ) || $clean[$key] === '' )
                $errors[] = "the key field '$key' is empty";
        return $errors ? null : $clean;
    }

    /**
     * A value as the field's type wants it (pure).
     *
     * @param string $datatype
     * @param mixed $value
     * @return mixed
     */
    public static function cast( $datatype, $value )
    {
        if ( is_bool( $value ) )
            $value = $value ? 1 : 0;
        if ( $value === null )
            return null;
        switch ( strtolower( (string)$datatype ) )
        {
            case 'integer':
            case 'int':
                if ( is_string( $value ) && preg_match( '/^\d{4}-\d\d-\d\d/', $value ) && ( $time = strtotime( $value ) ) !== false )
                    return $time;
                return is_numeric( $value ) ? (int)$value : 0;
            case 'float':
            case 'double':
            case 'decimal':
                return is_numeric( $value ) ? (float)$value : 0.0;
        }
        return is_scalar( $value ) ? (string)$value : json_encode( $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
    }

    /**
     * @param int $number
     * @param array $row
     * @param array $result
     */
    protected function writeRow( $number, array $row, array &$result )
    {
        $errors = array();
        $clean = $this->prepare( $row, $errors );
        if ( $clean === null )
        {
            $result['failed']++;
            $result['errors'][] = 'row ' . ( $number + 1 ) . ': ' . implode( '; ', $errors );
            return;
        }
        $conditions = array();
        foreach ( $this->options['keys'] as $key )
            $conditions[$key] = $clean[$key];
        $existing = $conditions ? eZPersistentObject::fetchObject( $this->definition, null, $conditions ) : null;
        $mode = $this->options['mode'];
        if ( $existing && $mode === 'insert' || !$existing && $mode === 'update' )
        {
            $result['skipped']++;
            return;
        }
        if ( $existing )
        {
            $changed = false;
            foreach ( $clean as $field => $value )
            {
                if ( (string)$existing->attribute( $field ) !== (string)$value )
                {
                    $changed = true;
                    break;
                }
            }
            if ( !$changed && $mode !== 'replace' )
            {
                $result['unchanged']++;
                $result['ids'][$number] = $this->keyOf( $existing );
                return;
            }
            if ( !$this->options['dry_run'] )
            {
                if ( $mode === 'replace' )
                {
                    $existing->remove();
                    $object = $this->newObject( $clean );
                    $object->store();
                    $existing = $object;
                }
                else
                {
                    foreach ( $clean as $field => $value )
                        $existing->setAttribute( $field, $value );
                    $existing->store();
                }
            }
            $result['updated']++;
            $result['ids'][$number] = $this->keyOf( $existing );
            return;
        }
        $object = $this->newObject( $clean );
        if ( !$this->options['dry_run'] )
            $object->store();
        $result['inserted']++;
        $result['ids'][$number] = $this->keyOf( $object );
    }

    /**
     * A new object with the definition's defaults and the row's values.
     *
     * @param array $clean
     * @return eZPersistentObject
     */
    protected function newObject( array $clean )
    {
        $row = array();
        foreach ( $this->entry['fields'] as $name => $field )
            $row[$name] = array_key_exists( $name, $clean ) ? $clean[$name] : $field['default'];
        if ( $this->entry['increment_key'] !== '' && !isset( $clean[$this->entry['increment_key']] ) )
            $row[$this->entry['increment_key']] = null;
        $class = $this->entry['class'];
        return new $class( $row );
    }

    /**
     * @param eZPersistentObject $object
     * @return array key => value
     */
    protected function keyOf( $object )
    {
        $key = array();
        foreach ( $this->entry['keys'] as $field )
            $key[$field] = $object->attribute( $field );
        return $key;
    }
}
?>
