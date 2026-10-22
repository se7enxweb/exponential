<?php
/**
 * File containing the expDataSync class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The synchronisation between content and data tables (doc/eng-us/features/6.0/data-tables.md, "Synchronisation"):
 *
 *   content_to_table  the objects of a content class below a node become rows (and stay rows: upsert by the key)
 *   table_to_content  the rows become objects below a node (updated when they are there already)
 *   two_way           both, by what changed since the last run; when both sides changed: the conflict rule
 *                     newest (the later modification wins), content, table, or ask (a conflict to resolve in the
 *                     administration)
 *
 * and how it runs: once (a run started from the administration or a tool), scheduled (the cronjob part datatable starts
 * a run when its interval has passed), live (each publish, move and removal of an object of the class below the node
 * is mirrored at once; scheduled runs are also made). Every run goes through the transfer runner and its history
 * (tool transfer.datatable, task expDataSyncTask).
 *
 * The map: column => source. A source is an attribute identifier of the content class, or one of
 *   @object_id @node_id @parent_node_id @remote_id @node_remote_id @name @published @modified @class @section @owner
 *   @path (the node's URL alias)
 * Only attribute sources are written to content; the @ sources are written to the table.
 *
 *   $sync = expDataSync::define( array( 'name' => ..., 'direction' => 'content_to_table', 'mode' => 'once',
 *                                       'class' => 'myArchiveRow', 'content_class' => 'article', 'subtree' => 2,
 *                                       'map' => array( 'contentobject_id' => '@object_id', 'title' => 'title' ),
 *                                       'keys' => array( 'contentobject_id' ), 'conflict' => 'newest' ), $problems );
 *   $run = expDataSync::start( $sync );        // the transfer runner's answer: engine, run_id, ...
 *   $counts = expDataSync::run( $sync );       // the same work in this process
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataSync
{
    /** The tool of the runs in the transfer registry */
    const TOOL = 'transfer.datatable';

    /** Objects or rows per chunk */
    const CHUNK = 100;

    /** The sources that are not attributes */
    const META = array( '@object_id', '@node_id', '@parent_node_id', '@remote_id', '@node_remote_id', '@name', '@published', '@modified',
                        '@class', '@section', '@owner', '@path' );

    /** @var bool content is being written by a synchronisation: the live mirror leaves its events alone */
    public static $applying = false;

    /** @var bool|null */
    protected static $installed = null;

    /**
     * Whether the three sync tables exist.
     *
     * @param bool $fresh
     * @return bool
     */
    public static function installed( $fresh = false )
    {
        if ( self::$installed !== null && !$fresh )
            return self::$installed;
        try
        {
            foreach ( array( expDataSyncDefinition::TABLE, expDataSyncLink::TABLE, expDataSyncConflict::TABLE ) as $table )
                if ( expKernelTableSchema::exists( $table, null, $fresh ) !== true )
                    return self::$installed = false;
        }
        catch ( Throwable $e )
        {
            return self::$installed = false;
        }
        return self::$installed = true;
    }

    /**
     * Creates the sync tables when they are missing.
     *
     * @param string[] $messages
     * @return bool
     */
    public static function install( &$messages = array() )
    {
        $ok = expKernelTableSchema::install( array( expDataSyncDefinition::TABLE, expDataSyncLink::TABLE, expDataSyncConflict::TABLE ), null, null, $messages );
        expKernelTableSchema::reset();
        self::$installed = null;
        return $ok;
    }

    /**
     * A definition checked and stored (a new one, or a change of an existing one).
     *
     * @param array $input name, direction, mode, class, content_class, subtree, map (array or "column = source" lines),
     *                     keys (array or comma list), conflict, interval (seconds), state, options (array)
     * @param string[] $problems
     * @param int $id the definition to change (0: a new one)
     * @return expDataSyncDefinition|null
     */
    public static function define( array $input, &$problems = array(), $id = 0 )
    {
        $data = self::normalize( $input );
        $problems = self::problems( $data );
        if ( $problems )
            return null;
        if ( !self::installed() )
        {
            $problems[] = 'The tables of the synchronisation are missing: create them on the Data tables page.';
            return null;
        }
        $now = time();
        $sync = $id ? expDataSyncDefinition::fetchByID( $id ) : null;
        if ( !$sync )
            $sync = new expDataSyncDefinition( array( 'created' => $now, 'creator_id' => (int)eZUser::currentUserID(), 'last_run' => 0, 'last_run_id' => 0 ) );
        foreach ( array( 'name' => 'name', 'direction' => 'direction', 'run_mode' => 'mode', 'class_name' => 'class', 'content_class' => 'content_class',
                         'subtree_node_id' => 'subtree', 'conflict_rule' => 'conflict', 'interval_seconds' => 'interval', 'state' => 'state' ) as $column => $key )
            $sync->setAttribute( $column, $data[$key] );
        $sync->setAttribute( 'map', json_encode( $data['map'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        $sync->setAttribute( 'key_columns', implode( ',', $data['keys'] ) );
        $sync->setAttribute( 'options', json_encode( $data['options'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        $sync->setAttribute( 'modified', $now );
        if ( $data['mode'] !== 'once' && !(int)$sync->attribute( 'next_run' ) )
            $sync->setAttribute( 'next_run', $now );
        $sync->store();
        return $sync;
    }

    /**
     * A definition's input with every key, typed (pure apart from nothing).
     *
     * @param array $input
     * @return array
     */
    public static function normalize( array $input )
    {
        $text = function ( $key, $default = '' ) use ( $input ) {
            return isset( $input[$key] ) && is_scalar( $input[$key] ) ? trim( (string)$input[$key] ) : $default;
        };
        $map = isset( $input['map'] ) ? $input['map'] : array();
        if ( is_string( $map ) )
            $map = self::parseMap( $map );
        $clean = array();
        foreach ( is_array( $map ) ? $map : array() as $column => $source )
            if ( is_string( $column ) && preg_match( '/^[A-Za-z_][A-Za-z0-9_]{0,63}$/', $column ) && is_scalar( $source ) && trim( (string)$source ) !== '' )
                $clean[$column] = trim( (string)$source );
        $keys = isset( $input['keys'] ) ? $input['keys'] : array();
        if ( is_string( $keys ) )
            $keys = explode( ',', $keys );
        $keys = array_values( array_filter( array_map( 'trim', (array)$keys ), 'strlen' ) );
        $direction = $text( 'direction', 'content_to_table' );
        $mode = $text( 'mode', 'once' );
        $conflict = $text( 'conflict', 'newest' );
        return array(
            'name' => mb_substr( $text( 'name' ), 0, 255 ),
            'direction' => in_array( $direction, expDataSyncDefinition::DIRECTIONS, true ) ? $direction : '',
            'mode' => in_array( $mode, expDataSyncDefinition::MODES, true ) ? $mode : '',
            'class' => $text( 'class' ),
            'content_class' => $text( 'content_class' ),
            'subtree' => (int)$text( 'subtree', '0' ),
            'map' => $clean,
            'keys' => $keys,
            'conflict' => in_array( $conflict, expDataSyncDefinition::RULES, true ) ? $conflict : '',
            'interval' => max( 0, (int)$text( 'interval', '3600' ) ),
            'state' => $text( 'state', 'enabled' ) === 'disabled' ? 'disabled' : 'enabled',
            'options' => isset( $input['options'] ) && is_array( $input['options'] ) ? array_intersect_key( $input['options'], array_flip( array( 'remove_missing', 'language' ) ) ) : array(),
        );
    }

    /**
     * The map from "column = source" lines (pure).
     *
     * @param string $text
     * @return array
     */
    public static function parseMap( $text )
    {
        $map = array();
        foreach ( preg_split( '/\R/', (string)$text ) as $line )
        {
            $line = trim( preg_replace( '/#.*$/', '', $line ) );
            if ( $line === '' || strpos( $line, '=' ) === false )
                continue;
            list( $column, $source ) = array_map( 'trim', explode( '=', $line, 2 ) );
            if ( $column !== '' && $source !== '' )
                $map[$column] = $source;
        }
        return $map;
    }

    /**
     * The map as "column = source" lines (pure).
     *
     * @param array $map
     * @return string
     */
    public static function mapText( array $map )
    {
        $lines = array();
        foreach ( $map as $column => $source )
            $lines[] = $column . ' = ' . $source;
        return implode( "\n", $lines );
    }

    /**
     * A map proposed for a persistent class and a content class: columns named like an attribute or a known source.
     *
     * @param string $class
     * @param string $contentClass
     * @return array
     */
    public static function proposeMap( $class, $contentClass )
    {
        $entry = expDataTableCatalogue::instance()->entry( (string)$class );
        $content = eZContentClass::fetchByIdentifier( (string)$contentClass );
        if ( !$entry || !$content )
            return array();
        $attributes = array();
        foreach ( $content->fetchAttributes() as $attribute )
            $attributes[(string)$attribute->attribute( 'identifier' )] = true;
        $known = array( 'contentobject_id' => '@object_id', 'object_id' => '@object_id', 'node_id' => '@node_id', 'main_node_id' => '@node_id',
                        'parent_node_id' => '@parent_node_id', 'remote_id' => '@remote_id', 'name' => '@name', 'published' => '@published',
                        'modified' => '@modified', 'section_id' => '@section', 'owner_id' => '@owner', 'path' => '@path' );
        $map = array();
        foreach ( array_keys( $entry['fields'] ) as $field )
        {
            if ( $field === $entry['increment_key'] )
                continue;
            if ( isset( $attributes[$field] ) )
                $map[$field] = $field;
            elseif ( strpos( $field, 'attr_' ) === 0 && isset( $attributes[substr( $field, 5 )] ) )
                $map[$field] = substr( $field, 5 );
            elseif ( isset( $known[$field] ) )
                $map[$field] = $known[$field];
        }
        return $map;
    }

    /**
     * What is wrong with a definition (normalize()d), as messages.
     *
     * @param array $d
     * @return string[]
     */
    public static function problems( array $d )
    {
        $problems = array();
        if ( $d['name'] === '' )
            $problems[] = 'Give the synchronisation a name.';
        if ( $d['direction'] === '' )
            $problems[] = 'Choose a direction.';
        if ( $d['mode'] === '' )
            $problems[] = 'Choose how it runs.';
        if ( $d['conflict'] === '' )
            $problems[] = 'Choose a conflict rule.';
        $entry = $d['class'] !== '' ? expDataTableCatalogue::instance()->entry( $d['class'] ) : null;
        if ( !$entry || $entry['error'] !== '' || $entry['table'] === '' )
            $problems[] = 'Choose a persistent class with a table.';
        $content = $d['content_class'] !== '' ? eZContentClass::fetchByIdentifier( $d['content_class'] ) : null;
        if ( !$content )
            $problems[] = 'Choose a content class.';
        if ( $d['subtree'] <= 0 || !eZContentObjectTreeNode::fetch( $d['subtree'] ) )
            $problems[] = 'Choose the node: the subtree to read (content to table) or the parent of new objects (table to content).';
        if ( !$d['map'] )
            $problems[] = 'Map at least one column.';
        if ( !$d['keys'] )
            $problems[] = 'Name the key column(s) a row is matched by.';
        if ( $entry )
        {
            foreach ( array_keys( $d['map'] ) as $column )
                if ( !isset( $entry['fields'][$column] ) )
                    $problems[] = "The class has no field '$column'.";
            foreach ( $d['keys'] as $key )
                if ( !isset( $d['map'][$key] ) )
                    $problems[] = "The key '$key' is not mapped.";
        }
        if ( $content )
        {
            $attributes = array();
            foreach ( $content->fetchAttributes() as $attribute )
                $attributes[(string)$attribute->attribute( 'identifier' )] = true;
            foreach ( $d['map'] as $column => $source )
                if ( $source[0] === '@' ? !in_array( $source, self::META, true ) : !isset( $attributes[$source] ) )
                    $problems[] = "'$source' is not an attribute of {$d['content_class']} nor one of " . implode( ' ', self::META ) . '.';
        }
        if ( $d['direction'] !== 'table_to_content' && $entry )
        {
            $connection = expDataConnections::get( $entry['connection'] );
            if ( $connection && $connection['read_only'] )
                $problems[] = 'The class is on a read-only connection: the table cannot be written.';
        }
        return array_values( array_unique( $problems ) );
    }

    /**
     * Starts a run through the transfer runner (expqueue, content jobs, or this process).
     *
     * @param expDataSyncDefinition $sync
     * @param array $params more parameters (object: one object only)
     * @return array the runner's answer: engine, run_id, job_ref, error, and counts when it ran here
     */
    public static function start( expDataSyncDefinition $sync, array $params = array() )
    {
        $params = array( 'sync' => (int)$sync->attribute( 'id' ) ) + $params;
        $run = expKernelTransferRunner::submit( self::TOOL, 'expDataSyncTask', $params, array(
            'title' => (string)$sync->attribute( 'name' ),
            'history' => array( 'direction' => $sync->attribute( 'direction' ) === 'table_to_content' ? 'import' : 'export', 'scope' => 'data',
                                'tool_ref' => (string)$sync->attribute( 'id' ), 'detail_url' => 'datatable/sync/' . (int)$sync->attribute( 'id' ) ) ) );
        if ( isset( $run['engine'] ) && $run['engine'] === 'process' )
        {
            // no queue and no content jobs here: the run is made in this request, recorded the same way
            $context = new expKernelTransferContext( 'inline', self::TOOL, isset( $run['run_id'] ) ? (int)$run['run_id'] : 0 );
            try
            {
                $run['counts'] = expKernelTransferRunner::execute( 'expDataSyncTask', $params, $context );
            }
            catch ( Throwable $e )
            {
                $run['error'] = $e->getMessage();
            }
        }
        $sync->setAttribute( 'last_run_id', isset( $run['run_id'] ) ? (int)$run['run_id'] : 0 );
        $sync->store();
        return $run;
    }

    /**
     * Runs a synchronisation in this process.
     *
     * @param expDataSyncDefinition $sync
     * @param expKernelTransferContext|null $context progress and cancel
     * @param int $objectID only this object (the live mirror)
     * @return array rows_written, objects_written, unchanged, conflicts, removed, failed, errors
     */
    public static function run( expDataSyncDefinition $sync, $context = null, $objectID = 0 )
    {
        $counts = array( 'rows_written' => 0, 'objects_written' => 0, 'unchanged' => 0, 'conflicts' => 0, 'removed' => 0, 'failed' => 0, 'errors' => array() );
        $direction = (string)$sync->attribute( 'direction' );
        $definition = self::classDefinition( $sync );
        if ( $direction !== 'table_to_content' )
            self::contentPass( $sync, $definition, $counts, $context, $objectID );
        if ( $direction !== 'content_to_table' && !$objectID )
            self::tablePass( $sync, $definition, $counts, $context );
        $now = time();
        $sync->setAttribute( 'last_run', $now );
        if ( $sync->attribute( 'run_mode' ) !== 'once' )
            $sync->setAttribute( 'next_run', $now + max( 60, (int)$sync->attribute( 'interval_seconds' ) ) );
        $sync->store();
        return $counts;
    }

    /**
     * The values a content object gives the mapped columns (with the object's main node).
     *
     * @param eZContentObject $object
     * @param array $map
     * @return array column => value
     */
    public static function contentValues( eZContentObject $object, array $map, $definition = null )
    {
        $node = $object->attribute( 'main_node' );
        $dataMap = $object->dataMap();
        $values = array();
        foreach ( $map as $column => $source )
        {
            switch ( $source )
            {
                case '@object_id':
                    $values[$column] = (int)$object->attribute( 'id' );
                    break;
                case '@node_id':
                    $values[$column] = $node ? (int)$node->attribute( 'node_id' ) : 0;
                    break;
                case '@parent_node_id':
                    $values[$column] = $node ? (int)$node->attribute( 'parent_node_id' ) : 0;
                    break;
                case '@remote_id':
                    $values[$column] = (string)$object->attribute( 'remote_id' );
                    break;
                case '@node_remote_id':
                    $values[$column] = $node ? (string)$node->attribute( 'remote_id' ) : '';
                    break;
                case '@name':
                    $values[$column] = (string)$object->attribute( 'name' );
                    break;
                case '@published':
                    $values[$column] = (int)$object->attribute( 'published' );
                    break;
                case '@modified':
                    $values[$column] = (int)$object->attribute( 'modified' );
                    break;
                case '@class':
                    $values[$column] = (string)$object->attribute( 'class_identifier' );
                    break;
                case '@section':
                    $values[$column] = (int)$object->attribute( 'section_id' );
                    break;
                case '@owner':
                    $values[$column] = (int)$object->attribute( 'owner_id' );
                    break;
                case '@path':
                    $values[$column] = $node ? (string)$node->attribute( 'url_alias' ) : '';
                    break;
                default:
                    $values[$column] = isset( $dataMap[$source] ) ? (string)$dataMap[$source]->toString() : '';
            }
        }
        return is_array( $definition ) ? self::typed( $values, $definition ) : $values;
    }

    /**
     * Values as the table's fields type them (an empty relation is 0 in an integer column, '' as an attribute's
     * string), so content and row compare equal when they hold the same thing (pure).
     *
     * @param array $values column => value
     * @param array $definition the persistent class's definition
     * @return array
     */
    public static function typed( array $values, array $definition )
    {
        foreach ( $values as $column => $value )
        {
            if ( !isset( $definition['fields'][$column] ) )
                continue;
            $field = $definition['fields'][$column];
            $datatype = is_array( $field ) && isset( $field['datatype'] ) ? $field['datatype'] : 'string';
            $values[$column] = expDataTableWriter::cast( $datatype, $value === null ? '' : $value );
        }
        return $values;
    }

    /**
     * The hash of the values of the columns mapped to attributes (what a side "changed" means) (pure).
     *
     * @param array $values column => value
     * @param array $map
     * @return string
     */
    public static function hash( array $values, array $map )
    {
        $data = array();
        foreach ( $map as $column => $source )
            if ( $source[0] !== '@' || in_array( $source, array( '@name', '@remote_id' ), true ) )
                $data[$column] = isset( $values[$column] ) ? (string)$values[$column] : '';
        ksort( $data );
        return md5( json_encode( $data, JSON_UNESCAPED_UNICODE ) );
    }

    /**
     * The key of a row (pure).
     *
     * @param array $values
     * @param string[] $keys
     * @return string '' when a key value is empty
     */
    public static function rowKey( array $values, array $keys )
    {
        $parts = array();
        foreach ( $keys as $key )
        {
            if ( !isset( $values[$key] ) || (string)$values[$key] === '' || ( is_numeric( $values[$key] ) && (int)$values[$key] === 0 && preg_match( '/_id$/', $key ) ) )
                return '';
            $parts[] = (string)$values[$key];
        }
        return mb_substr( implode( '|', $parts ), 0, 255 );
    }

    /**
     * Which side wins when both changed (pure): content, table, or ask.
     *
     * @param string $rule newest, content, table, ask
     * @param int $contentModified
     * @param int $rowModified 0 when the table has no time
     * @return string content, table or ask
     */
    public static function winner( $rule, $contentModified, $rowModified )
    {
        switch ( $rule )
        {
            case 'content':
            case 'table':
            case 'ask':
                return $rule;
        }
        // newest: a table without a time loses to content, the side that has one
        return (int)$rowModified > (int)$contentModified ? 'table' : 'content';
    }

    /**
     * Resolves an open conflict: takes the content's values into the table, or the row's into the content.
     *
     * @param expDataSyncConflict $conflict
     * @param string $take content or table
     * @return bool
     */
    public static function resolve( expDataSyncConflict $conflict, $take )
    {
        if ( $conflict->attribute( 'state' ) !== 'open' || !in_array( $take, array( 'content', 'table' ), true ) )
            return false;
        $sync = expDataSyncDefinition::fetchByID( (int)$conflict->attribute( 'sync_id' ) );
        $object = eZContentObject::fetch( (int)$conflict->attribute( 'object_id' ) );
        if ( !$sync || !$object )
            return false;
        $definition = self::classDefinition( $sync );
        $map = $sync->mapArray();
        $link = eZPersistentObject::fetchObject( expDataSyncLink::definition(), null, array( 'id' => (int)$conflict->attribute( 'link_id' ) ) );
        $counts = array( 'rows_written' => 0, 'objects_written' => 0, 'unchanged' => 0, 'conflicts' => 0, 'removed' => 0, 'failed' => 0, 'errors' => array() );
        $contentValues = self::contentValues( $object, $map, $definition );
        if ( $take === 'content' )
            self::writeRow( $sync, $definition, $contentValues, $counts );
        else
        {
            $row = self::fetchRow( $definition, $sync->keysArray(), (string)$conflict->attribute( 'row_key' ) );
            if ( $row )
                self::writeContent( $sync, $object, $row, $counts, $definition );
            $object = eZContentObject::fetch( (int)$conflict->attribute( 'object_id' ) );
            $contentValues = self::contentValues( $object, $map, $definition );
            self::writeMeta( $definition, $sync->keysArray(), (string)$conflict->attribute( 'row_key' ), $contentValues, $map );
        }
        if ( $counts['failed'] )
            return false;
        $row = self::fetchRow( $definition, $sync->keysArray(), (string)$conflict->attribute( 'row_key' ) );
        if ( $link instanceof expDataSyncLink )
            self::storeLink( $link, $sync, (string)$conflict->attribute( 'row_key' ), $object, $contentValues, $row ?: array(), $definition );
        $conflict->setAttribute( 'state', 'resolved' );
        $conflict->setAttribute( 'resolution', $take );
        $conflict->setAttribute( 'resolved', time() );
        $conflict->setAttribute( 'resolver_id', (int)eZUser::currentUserID() );
        $conflict->store();
        return true;
    }

    /**
     * The live mirror's work for one object: every live definition of its class whose subtree holds it.
     *
     * @param int $objectID
     * @param string $event publish, move or remove
     * @param expDataSyncDefinition[]|null $syncs these definitions (the workflow event's choice) instead of the live ones
     * @return int how many definitions did something
     */
    public static function mirror( $objectID, $event, $syncs = null )
    {
        if ( self::$applying || !self::installed() )
            return 0;
        $done = 0;
        if ( !is_array( $syncs ) )
            $syncs = expDataSyncDefinition::fetchList( array( 'run_mode' => 'live', 'state' => 'enabled' ) );
        foreach ( $syncs as $sync )
        {
            if ( $sync->attribute( 'direction' ) === 'table_to_content' )
                continue;
            try
            {
                if ( $event === 'remove' )
                {
                    $done += self::removeObject( $sync, (int)$objectID ) ? 1 : 0;
                    continue;
                }
                $object = eZContentObject::fetch( (int)$objectID );
                if ( !$object || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED || !$object->attribute( 'main_node_id' ) )
                {
                    // gone since it was noted (purged, in the trash, its last location removed)
                    $done += self::removeObject( $sync, (int)$objectID ) ? 1 : 0;
                    continue;
                }
                if ( $object->attribute( 'class_identifier' ) !== $sync->attribute( 'content_class' ) )
                    continue;
                if ( !self::inSubtree( $object, (int)$sync->attribute( 'subtree_node_id' ) ) )
                {
                    // moved out of the subtree: as removed, when the definition removes rows
                    if ( $event === 'move' )
                        $done += self::removeObject( $sync, (int)$objectID ) ? 1 : 0;
                    continue;
                }
                $counts = self::run( $sync, null, (int)$objectID );
                $done += ( $counts['rows_written'] + $counts['conflicts'] ) ? 1 : 0;
                if ( $counts['errors'] )
                    eZDebug::writeWarning( 'live mirror ' . $sync->attribute( 'name' ) . ': ' . implode( '; ', $counts['errors'] ), __METHOD__ );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeError( 'live mirror ' . $sync->attribute( 'name' ) . ': ' . $e->getMessage(), __METHOD__ );
            }
        }
        return $done;
    }

    /**
     * The definitions that are due (scheduled or live, enabled, next_run passed).
     *
     * @param int|null $now
     * @return expDataSyncDefinition[]
     */
    public static function due( $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $due = array();
        if ( !self::installed() )
            return $due;
        foreach ( expDataSyncDefinition::fetchList( array( 'state' => 'enabled' ) ) as $sync )
            if ( $sync->attribute( 'run_mode' ) !== 'once' && (int)$sync->attribute( 'next_run' ) <= $now )
                $due[] = $sync;
        return $due;
    }

    /**
     * The persistent class's definition of a synchronisation.
     *
     * @param expDataSyncDefinition $sync
     * @return array
     * @throws RuntimeException
     */
    protected static function classDefinition( expDataSyncDefinition $sync )
    {
        $class = (string)$sync->attribute( 'class_name' );
        if ( !class_exists( $class ) || !is_subclass_of( $class, 'eZPersistentObject' ) )
            throw new RuntimeException( "The persistent class $class is not available." );
        return call_user_func( array( $class, 'definition' ) );
    }

    /**
     * Content to table (and both ways, the content side).
     */
    protected static function contentPass( expDataSyncDefinition $sync, array $definition, array &$counts, $context, $objectID )
    {
        $map = $sync->mapArray();
        $keys = $sync->keysArray();
        $rule = (string)$sync->attribute( 'conflict_rule' );
        $twoWay = $sync->attribute( 'direction' ) === 'two_way';
        $options = $sync->optionsArray();
        $seen = array();
        $offset = 0;
        $total = null;
        do
        {
            if ( $objectID )
            {
                $object = eZContentObject::fetch( $objectID );
                $objects = $object ? array( $object ) : array();
                $last = true;
            }
            else
            {
                $params = array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( (string)$sync->attribute( 'content_class' ) ),
                                 'Limitation' => array(), 'IgnoreVisibility' => true, 'MainNodeOnly' => true, 'Offset' => $offset, 'Limit' => self::CHUNK,
                                 'SortBy' => array( 'node_id', true ) );
                if ( $total === null )
                    $total = (int)eZContentObjectTreeNode::subTreeCountByNodeID( $params, (int)$sync->attribute( 'subtree_node_id' ) );
                $nodes = eZContentObjectTreeNode::subTreeByNodeID( $params, (int)$sync->attribute( 'subtree_node_id' ) );
                $objects = array();
                foreach ( is_array( $nodes ) ? $nodes : array() as $node )
                    $objects[] = $node->attribute( 'object' );
                $last = count( $objects ) < self::CHUNK;
                $offset += self::CHUNK;
            }
            foreach ( $objects as $object )
            {
                if ( !$object instanceof eZContentObject )
                    continue;
                try
                {
                    $values = self::contentValues( $object, $map, $definition );
                    $key = self::rowKey( $values, $keys );
                    if ( $key === '' )
                    {
                        $counts['failed']++;
                        $counts['errors'][] = 'object ' . $object->attribute( 'id' ) . ': the key is empty';
                        continue;
                    }
                    $seen[(int)$object->attribute( 'id' )] = true;
                    $link = self::link( $sync, $key, (int)$object->attribute( 'id' ) );
                    $row = self::fetchRow( $definition, $keys, $key );
                    $contentHash = self::hash( $values, $map );
                    if ( !$twoWay || !$link )
                    {
                        if ( $twoWay && !$link && $row && self::hash( $row, $map ) !== $contentHash )
                        {
                            // both sides hold the item, never paired: the rule decides
                            $side = self::winner( $rule, (int)$object->attribute( 'modified' ), self::rowTime( $row, $map ) );
                            if ( $side === 'ask' )
                            {
                                $link = self::storeLink( $link ?: new expDataSyncLink( array() ), $sync, $key, $object, $values, $row, $definition, true );
                                self::conflict( $sync, $link, $object, $key, $values, $row, $counts );
                                continue;
                            }
                            if ( $side === 'table' )
                            {
                                self::writeContent( $sync, $object, $row, $counts, $definition );
                                $object = eZContentObject::fetch( (int)$object->attribute( 'id' ) );
                                $values = self::contentValues( $object, $map, $definition );
                                self::writeMeta( $definition, $keys, $key, $values, $map );
                                $row = self::fetchRow( $definition, $keys, $key ) ?: $row;
                                self::storeLink( $link ?: new expDataSyncLink( array() ), $sync, $key, $object, $values, $row, $definition );
                                continue;
                            }
                        }
                        if ( $row && self::hash( $row, $map ) === $contentHash && self::sameMeta( $row, $values, $map ) )
                            $counts['unchanged']++;
                        else
                            self::writeRow( $sync, $definition, $values, $counts );
                        $row = self::fetchRow( $definition, $keys, $key );
                        self::storeLink( $link ?: new expDataSyncLink( array() ), $sync, $key, $object, $values, $row ?: array(), $definition );
                        continue;
                    }
                    // two way, a pair known from the last run
                    $contentChanged = $link->attribute( 'content_hash' ) !== $contentHash;
                    $rowChanged = $row ? $link->attribute( 'row_hash' ) !== self::hash( $row, $map ) : false;
                    if ( !$row )
                    {
                        // the row was removed on the table's side: written again (content is not removed by a sync)
                        self::writeRow( $sync, $definition, $values, $counts );
                    }
                    elseif ( $contentChanged && $rowChanged )
                    {
                        $side = self::winner( $rule, (int)$object->attribute( 'modified' ), self::rowTime( $row, $map ) );
                        if ( $side === 'ask' )
                        {
                            self::conflict( $sync, $link, $object, $key, $values, $row, $counts );
                            continue;
                        }
                        if ( $side === 'content' )
                            self::writeRow( $sync, $definition, $values, $counts );
                        else
                            self::writeContent( $sync, $object, $row, $counts, $definition );
                    }
                    elseif ( $contentChanged || !self::sameMeta( $row, $values, $map ) )
                        self::writeRow( $sync, $definition, $values, $counts );
                    elseif ( $rowChanged )
                        self::writeContent( $sync, $object, $row, $counts, $definition );
                    else
                    {
                        $counts['unchanged']++;
                        continue;
                    }
                    $object = eZContentObject::fetch( (int)$object->attribute( 'id' ) );
                    $values = self::contentValues( $object, $map, $definition );
                    // a published change moved the object's own columns (modified, name): the row follows
                    self::writeMeta( $definition, $keys, $key, $values, $map );
                    $row = self::fetchRow( $definition, $keys, $key );
                    self::storeLink( $link, $sync, $key, $object, $values, $row ?: array(), $definition );
                }
                catch ( Throwable $e )
                {
                    $counts['failed']++;
                    $counts['errors'][] = 'object ' . $object->attribute( 'id' ) . ': ' . mb_substr( $e->getMessage(), 0, 200 );
                }
            }
            if ( $context instanceof expKernelTransferContext && !$objectID )
            {
                $context->progress( min( $offset, (int)$total ), $total );
                $context->throwIfCancelled();
            }
        }
        while ( !$last );

        // objects gone from the subtree: their rows go, when the definition says so
        if ( !$objectID && !empty( $options['remove_missing'] ) && $sync->attribute( 'direction' ) === 'content_to_table' )
        {
            foreach ( self::links( $sync ) as $link )
                if ( !isset( $seen[(int)$link->attribute( 'object_id' )] ) && self::removeObject( $sync, (int)$link->attribute( 'object_id' ) ) )
                    $counts['removed']++;
        }
    }

    /**
     * Table to content (and both ways, the rows no object answers yet).
     */
    protected static function tablePass( expDataSyncDefinition $sync, array $definition, array &$counts, $context )
    {
        $map = $sync->mapArray();
        $keys = $sync->keysArray();
        $twoWay = $sync->attribute( 'direction' ) === 'two_way';
        $offset = 0;
        $total = (int)eZPersistentObject::count( $definition );
        do
        {
            $rows = eZPersistentObject::fetchObjectList( $definition, null, null, array( $definition['keys'][0] => 'asc' ), array( 'offset' => $offset, 'length' => self::CHUNK ), false );
            $rows = is_array( $rows ) ? $rows : array();
            $offset += self::CHUNK;
            foreach ( $rows as $row )
            {
                $row = self::typed( (array)$row, $definition );
                $key = self::rowKey( $row, $keys );
                if ( $key === '' )
                    continue;
                try
                {
                    $link = self::linkByKey( $sync, $key );
                    $object = $link ? eZContentObject::fetch( (int)$link->attribute( 'object_id' ) ) : self::objectOfRow( $row, $map );
                    if ( $twoWay && $link && $object )
                        continue; // handled by the content pass
                    $rowHash = self::hash( $row, $map );
                    if ( $object && $link && $link->attribute( 'row_hash' ) === $rowHash )
                    {
                        $counts['unchanged']++;
                        continue;
                    }
                    $object = $object ? self::writeContent( $sync, $object, $row, $counts, $definition ) : self::createContent( $sync, $row, $counts );
                    if ( !$object )
                        continue;
                    $values = self::contentValues( $object, $map, $definition );
                    // the row learns its object and node (columns mapped to @object_id, @node_id ...)
                    self::writeMeta( $definition, $keys, $key, $values, $map );
                    $row = self::fetchRow( $definition, $keys, $key ) ?: $row;
                    self::storeLink( $link ?: new expDataSyncLink( array() ), $sync, $key, $object, $values, $row, $definition );
                }
                catch ( Throwable $e )
                {
                    $counts['failed']++;
                    $counts['errors'][] = 'row ' . $key . ': ' . mb_substr( $e->getMessage(), 0, 200 );
                }
            }
            if ( $context instanceof expKernelTransferContext )
            {
                $context->progress( min( $offset, $total ), $total );
                $context->throwIfCancelled();
            }
        }
        while ( count( $rows ) === self::CHUNK );
    }

    /**
     * Writes a row from content values (upsert by the keys) through expDataTableWriter.
     */
    protected static function writeRow( expDataSyncDefinition $sync, array $definition, array $values, array &$counts )
    {
        $writer = new expDataTableWriter( (string)$sync->attribute( 'class_name' ), array( 'keys' => $sync->keysArray(), 'mode' => 'upsert', 'unknown' => 'ignore' ) );
        $result = $writer->write( array( $values ) );
        $counts['rows_written'] += $result['inserted'] + $result['updated'];
        $counts['unchanged'] += $result['unchanged'];
        $counts['failed'] += $result['failed'];
        $counts['errors'] = array_merge( $counts['errors'], $result['errors'] );
    }

    /**
     * Updates an object from a row (the columns mapped to attributes); a new version is published.
     *
     * @return eZContentObject|null the object
     */
    protected static function writeContent( expDataSyncDefinition $sync, eZContentObject $object, array $row, array &$counts, array $definition = array() )
    {
        // only the attributes whose value differs: an attribute written again from its own string (an image, a
        // relation list) could lose what its string does not carry
        $current = self::contentValues( $object, $sync->mapArray(), $definition ?: null );
        $row = $definition ? self::typed( $row, $definition ) : $row;
        $attributes = array();
        foreach ( $sync->mapArray() as $column => $source )
            if ( $source[0] !== '@' && array_key_exists( $column, $row ) && (string)$row[$column] !== (string)( isset( $current[$column] ) ? $current[$column] : '' ) )
                $attributes[$source] = (string)$row[$column];
        if ( !$attributes )
        {
            $counts['unchanged']++;
            return $object;
        }
        self::$applying = true;
        try
        {
            $ok = eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => $attributes ), false );
        }
        finally
        {
            self::$applying = false;
        }
        if ( !$ok )
        {
            $counts['failed']++;
            $counts['errors'][] = 'object ' . $object->attribute( 'id' ) . ': the update was not published';
            return null;
        }
        $counts['objects_written']++;
        eZContentObject::clearCache( array( (int)$object->attribute( 'id' ) ) );
        return eZContentObject::fetch( (int)$object->attribute( 'id' ) );
    }

    /**
     * Creates an object below the definition's node from a row.
     *
     * @return eZContentObject|null
     */
    protected static function createContent( expDataSyncDefinition $sync, array $row, array &$counts )
    {
        $map = $sync->mapArray();
        $params = array( 'class_identifier' => (string)$sync->attribute( 'content_class' ), 'parent_node_id' => (int)$sync->attribute( 'subtree_node_id' ),
                         'attributes' => self::attributesOfRow( $row, $map ) );
        foreach ( $map as $column => $source )
            if ( $source === '@remote_id' && isset( $row[$column] ) && (string)$row[$column] !== '' )
                $params['remote_id'] = (string)$row[$column];
        $options = $sync->optionsArray();
        if ( !empty( $options['language'] ) )
            $params['language'] = (string)$options['language'];
        self::$applying = true;
        try
        {
            $object = eZContentFunctions::createAndPublishObject( $params );
        }
        finally
        {
            self::$applying = false;
        }
        if ( !$object instanceof eZContentObject )
        {
            $counts['failed']++;
            $counts['errors'][] = 'a row could not become an object of ' . $sync->attribute( 'content_class' );
            return null;
        }
        $counts['objects_written']++;
        return eZContentObject::fetch( (int)$object->attribute( 'id' ) );
    }

    /**
     * The attribute strings of a row (fromString() of each datatype reads them).
     *
     * @param array $row
     * @param array $map
     * @return array identifier => string
     */
    protected static function attributesOfRow( array $row, array $map )
    {
        $attributes = array();
        foreach ( $map as $column => $source )
            if ( $source[0] !== '@' && array_key_exists( $column, $row ) && (string)$row[$column] !== '' )
                $attributes[$source] = (string)$row[$column];
        return $attributes;
    }

    /**
     * Writes the @ columns of a row (object id, node id ...) after its object was made or changed.
     */
    protected static function writeMeta( array $definition, array $keys, $key, array $values, array $map )
    {
        $row = self::fetchRowObject( $definition, $keys, $key );
        if ( !$row )
            return;
        $changed = false;
        foreach ( $map as $column => $source )
        {
            if ( $source[0] === '@' && !in_array( $column, $keys, true ) && isset( $values[$column] ) && (string)$row->attribute( $column ) !== (string)$values[$column] )
            {
                $row->setAttribute( $column, $values[$column] );
                $changed = true;
            }
        }
        if ( $changed )
            $row->store();
    }

    /** @return bool whether the @ columns of a row hold the content's values */
    protected static function sameMeta( array $row, array $values, array $map )
    {
        foreach ( $map as $column => $source )
            if ( $source[0] === '@' && isset( $values[$column] ) && (string)( isset( $row[$column] ) ? $row[$column] : '' ) !== (string)$values[$column] )
                return false;
        return true;
    }

    /** @return int the time a row says it was changed (a column mapped to @modified, or one named modified) */
    protected static function rowTime( array $row, array $map )
    {
        foreach ( $map as $column => $source )
            if ( $source === '@modified' && isset( $row[$column] ) )
                return (int)$row[$column];
        return isset( $row['modified'] ) ? (int)$row['modified'] : 0;
    }

    /**
     * The object a row stands for, by the columns mapped to @object_id or @remote_id.
     *
     * @return eZContentObject|null
     */
    protected static function objectOfRow( array $row, array $map )
    {
        foreach ( $map as $column => $source )
        {
            if ( $source === '@object_id' && !empty( $row[$column] ) && ( $object = eZContentObject::fetch( (int)$row[$column] ) ) )
                return $object;
            if ( $source === '@remote_id' && !empty( $row[$column] ) && ( $object = eZContentObject::fetchByRemoteID( (string)$row[$column] ) ) )
                return $object;
        }
        return null;
    }

    /** @return array|null a row as values, by its key */
    protected static function fetchRow( array $definition, array $keys, $key )
    {
        $conditions = self::keyConditions( $keys, $key );
        $rows = $conditions ? eZPersistentObject::fetchObjectList( $definition, null, $conditions, null, array( 'offset' => 0, 'length' => 1 ), false ) : null;
        return is_array( $rows ) && $rows ? self::typed( (array)$rows[0], $definition ) : null;
    }

    /** @return eZPersistentObject|null a row as an object, by its key */
    protected static function fetchRowObject( array $definition, array $keys, $key )
    {
        $conditions = self::keyConditions( $keys, $key );
        return $conditions ? eZPersistentObject::fetchObject( $definition, null, $conditions ) : null;
    }

    /** @return array key column => value from a row key */
    protected static function keyConditions( array $keys, $key )
    {
        $parts = explode( '|', (string)$key );
        if ( count( $parts ) !== count( $keys ) )
            return array();
        return array_combine( $keys, $parts );
    }

    /** @return expDataSyncLink|null the pair of an object (or of a key) of a synchronisation */
    protected static function link( expDataSyncDefinition $sync, $key, $objectID )
    {
        $link = eZPersistentObject::fetchObject( expDataSyncLink::definition(), null, array( 'sync_id' => (int)$sync->attribute( 'id' ), 'object_id' => (int)$objectID ) );
        return $link ?: self::linkByKey( $sync, $key );
    }

    /** @return expDataSyncLink|null */
    protected static function linkByKey( expDataSyncDefinition $sync, $key )
    {
        return eZPersistentObject::fetchObject( expDataSyncLink::definition(), null, array( 'sync_id' => (int)$sync->attribute( 'id' ), 'row_key' => (string)$key ) );
    }

    /** @return expDataSyncLink[] */
    protected static function links( expDataSyncDefinition $sync )
    {
        $list = eZPersistentObject::fetchObjectList( expDataSyncLink::definition(), null, array( 'sync_id' => (int)$sync->attribute( 'id' ) ) );
        return is_array( $list ) ? $list : array();
    }

    /**
     * Stores a pair with both sides' hashes and times.
     *
     * @return expDataSyncLink
     */
    protected static function storeLink( expDataSyncLink $link, expDataSyncDefinition $sync, $key, eZContentObject $object, array $values, array $row, array $definition, $keepHashes = false )
    {
        $map = $sync->mapArray();
        $link->setAttribute( 'sync_id', (int)$sync->attribute( 'id' ) );
        $link->setAttribute( 'row_key', (string)$key );
        $link->setAttribute( 'object_id', (int)$object->attribute( 'id' ) );
        if ( !$keepHashes || !$link->attribute( 'id' ) )
        {
            $link->setAttribute( 'content_hash', $keepHashes ? '' : self::hash( $values, $map ) );
            $link->setAttribute( 'row_hash', $keepHashes ? '' : self::hash( $row, $map ) );
        }
        $link->setAttribute( 'content_modified', (int)$object->attribute( 'modified' ) );
        $link->setAttribute( 'row_modified', self::rowTime( $row, $map ) );
        $link->setAttribute( 'synced', time() );
        $link->store();
        return $link;
    }

    /**
     * Records a conflict (once per pair while it is open).
     */
    protected static function conflict( expDataSyncDefinition $sync, expDataSyncLink $link, eZContentObject $object, $key, array $values, array $row, array &$counts )
    {
        $open = eZPersistentObject::fetchObject( expDataSyncConflict::definition(), null, array( 'link_id' => (int)$link->attribute( 'id' ), 'state' => 'open' ) );
        $conflict = $open ?: new expDataSyncConflict( array( 'sync_id' => (int)$sync->attribute( 'id' ), 'link_id' => (int)$link->attribute( 'id' ),
                                                             'state' => 'open', 'resolution' => '', 'resolved' => 0, 'resolver_id' => 0 ) );
        $mapped = array();
        foreach ( array_keys( $sync->mapArray() ) as $column )
            $mapped[$column] = isset( $row[$column] ) ? $row[$column] : null;
        $conflict->setAttribute( 'object_id', (int)$object->attribute( 'id' ) );
        $conflict->setAttribute( 'row_key', (string)$key );
        $conflict->setAttribute( 'content_values', json_encode( $values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
        $conflict->setAttribute( 'row_values', json_encode( $mapped, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
        $conflict->setAttribute( 'detected', time() );
        $conflict->store();
        $counts['conflicts']++;
    }

    /**
     * Removes the row of an object (the live mirror's removal, remove_missing).
     *
     * @return bool whether a row went
     */
    protected static function removeObject( expDataSyncDefinition $sync, $objectID )
    {
        $options = $sync->optionsArray();
        if ( isset( $options['remove_missing'] ) && !$options['remove_missing'] )
            return false;
        $link = eZPersistentObject::fetchObject( expDataSyncLink::definition(), null, array( 'sync_id' => (int)$sync->attribute( 'id' ), 'object_id' => (int)$objectID ) );
        if ( !$link )
            return false;
        $definition = self::classDefinition( $sync );
        $conditions = self::keyConditions( $sync->keysArray(), (string)$link->attribute( 'row_key' ) );
        if ( $conditions )
            eZPersistentObject::removeObject( $definition, $conditions );
        $link->remove();
        return true;
    }

    /** @return bool whether an object's main node is in a subtree */
    protected static function inSubtree( eZContentObject $object, $subtreeNodeID )
    {
        $node = $object->attribute( 'main_node' );
        return $node && strpos( (string)$node->attribute( 'path_string' ), '/' . (int)$subtreeNodeID . '/' ) !== false;
    }
}
?>
