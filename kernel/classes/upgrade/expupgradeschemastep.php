<?php
/**
 * File containing the expUpgradeSchemaStep class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A database step made of tables, columns and indexes of the kernel schema (share/db_schema.dba): the same
 * definitions a new installation is created from, so an upgraded database ends up as a new one. The SQL is written
 * by each engine's schema handler (MySQL/MariaDB, PostgreSQL, SQLite, Oracle through ezoracle; MongoDB gets the
 * collections, columns and indexes do not apply to it).
 *
 * A subclass names what it needs:
 *
 *   tables()  array( 'expbookmark_folder' )                         created when missing
 *   fields()  array( 'ezcontentbrowsebookmark' => array( 'folder_id', 'priority' ) )   added when missing
 *   indexes() array( 'ezcontentbrowsebookmark' => array( 'ezcontentbrowsebookmark_folder' ) )   by their columns
 *
 * Detection asks the catalog, so a part applied by hand (the SQL update file) counts, and a half-applied step is
 * completed: only what is missing is created. Columns and indexes of a table that does not exist (an optional
 * feature's table, e.g. ezprest_authcode without the REST extension's schema) are not applicable.
 *
 * @since 6.0.15
 * @package kernel
 */
abstract class expUpgradeSchemaStep extends expUpgradeBaseStep
{
    public function stage()
    {
        return 'database';
    }

    /** @return string[] tables to create when missing */
    public function tables()
    {
        return array();
    }

    /** @return array table => column names to add when missing */
    public function fields()
    {
        return array();
    }

    /** @return array table => index names (of the kernel schema) to add when missing */
    public function indexes()
    {
        return array();
    }

    /**
     * What is missing: array( 'tables' => [], 'fields' => [table => [field]], 'indexes' => [table => [name]],
     * 'unknown' => [what], 'skipped' => [what] ).
     *
     * @param expUpgradeContext $context
     * @return array
     */
    public function missing( expUpgradeContext $context )
    {
        $db = $context->db();
        $mongo = $context->engine() === 'mongodb';
        $result = array( 'tables' => array(), 'fields' => array(), 'indexes' => array(), 'unknown' => array(), 'skipped' => array() );
        foreach ( $this->tables() as $table )
        {
            $exists = expKernelTableSchema::exists( $table, $db, true );
            if ( $exists === null )
                $result['unknown'][] = "table $table";
            else if ( !$exists )
                $result['tables'][] = $table;
        }
        if ( $mongo )
            return $result;
        foreach ( $this->fields() as $table => $fields )
        {
            if ( in_array( $table, $result['tables'], true ) )
                continue;
            $columns = expUpgradeCatalog::columns( $db, $table );
            if ( $columns === null )
            {
                if ( expKernelTableSchema::exists( $table, $db, true ) === false )
                    $result['skipped'][] = "table $table does not exist";
                else
                    $result['unknown'][] = "columns of $table";
                continue;
            }
            foreach ( $fields as $field )
                if ( !in_array( strtolower( $field ), $columns, true ) )
                    $result['fields'][$table][] = $field;
        }
        foreach ( $this->indexes() as $table => $names )
        {
            if ( in_array( $table, $result['tables'], true ) )
                continue;
            if ( expKernelTableSchema::exists( $table, $db, true ) !== true )
                continue;
            $definition = $context->table( $table );
            foreach ( $names as $name )
            {
                if ( !isset( $definition['indexes'][$name] ) )
                {
                    $result['unknown'][] = "index $name (not in the kernel schema)";
                    continue;
                }
                $def = $definition['indexes'][$name];
                // a column this step adds in the same run: the index is missing too
                if ( !empty( $result['fields'][$table] ) && array_intersect( self::indexFields( $def ), $result['fields'][$table] ) )
                {
                    $result['indexes'][$table][] = $name;
                    continue;
                }
                $has = expUpgradeCatalog::hasIndex( $db, $table, self::indexFields( $def ), $def['type'] === 'unique' );
                if ( $has === null )
                    $result['unknown'][] = "indexes of $table";
                else if ( !$has )
                    $result['indexes'][$table][] = $name;
            }
        }
        return $result;
    }

    public function detect( expUpgradeContext $context )
    {
        $missing = $this->missing( $context );
        $parts = self::describe( $missing );
        if ( $parts )
            return self::effect( self::MISSING, 'missing: ' . implode( ', ', $parts ) );
        if ( $missing['unknown'] )
            return self::effect( self::UNKNOWN, 'cannot tell: ' . implode( ', ', $missing['unknown'] ) );
        if ( $missing['skipped'] && count( $missing['skipped'] ) >= count( $this->fields() ) && !$this->tables() )
            return self::effect( self::NOT_APPLICABLE, implode( ', ', $missing['skipped'] ) );
        return self::effect( self::PRESENT, $this->presentDetail() );
    }

    /** @return string what detect() says when everything is there */
    protected function presentDetail()
    {
        $what = array();
        foreach ( $this->tables() as $t )
            $what[] = $t;
        foreach ( $this->fields() as $t => $fields )
            foreach ( $fields as $f )
                $what[] = "$t.$f";
        return 'in place: ' . implode( ', ', $what );
    }

    public function plan( expUpgradeContext $context )
    {
        $lines = array();
        foreach ( $this->statements( $context, $this->missing( $context ) ) as $item )
            $lines[] = $item['what'] . ( $item['sql'] !== '' ? ': ' . preg_replace( '/\s+/', ' ', trim( $item['sql'] ) ) : '' );
        return $lines;
    }

    public function apply( expUpgradeContext $context )
    {
        $db = $context->db();
        $missing = $this->missing( $context );
        if ( $missing['unknown'] )
            throw new RuntimeException( 'cannot tell what is missing: ' . implode( ', ', $missing['unknown'] ) );
        $done = array();
        if ( $missing['tables'] )
        {
            $messages = array();
            $ok = expKernelTableSchema::install( $missing['tables'], $db, $context->schema(), $messages );
            if ( !$ok )
                throw new RuntimeException( implode( '; ', $messages ) );
            foreach ( $missing['tables'] as $table )
                $done[] = "created table $table";
        }
        foreach ( $this->statements( $context, array( 'tables' => array() ) + $missing ) as $item )
        {
            if ( $item['sql'] === '' )
                throw new RuntimeException( 'no SQL for ' . $item['what'] . ' on ' . $context->engine() );
            expUpgradeCatalog::execute( $db, $item['sql'] );
            $done[] = $item['what'];
        }
        expKernelTableSchema::reset();
        $this->afterApply( $context );
        return $done;
    }

    /** Called after apply() changed the schema: refresh what the kernel cached about it. */
    protected function afterApply( expUpgradeContext $context )
    {
    }

    /**
     * The changes for what is missing, each array( 'what' => text, 'sql' => SQL text ).
     *
     * @param expUpgradeContext $context
     * @param array $missing as missing() returns
     * @return array[]
     */
    protected function statements( expUpgradeContext $context, array $missing )
    {
        $items = array();
        $engine = $context->engine();
        if ( $missing['tables'] )
        {
            $schema = array();
            foreach ( $missing['tables'] as $table )
                if ( $context->table( $table ) )
                    $schema[$table] = $context->table( $table );
            $items[] = array( 'what' => 'create table ' . implode( ', ', $missing['tables'] ),
                              'sql' => $engine === 'mongodb' ? '' : expKernelTableSchema::sql( $schema, $engine ) );
        }
        $handler = null;
        foreach ( array( 'fields', 'indexes' ) as $kind )
        {
            foreach ( $missing[$kind] as $table => $names )
            {
                $definition = $context->table( $table );
                foreach ( $names as $name )
                {
                    if ( $handler === null )
                        $handler = self::handler( $engine, $context->schema() );
                    $def = $kind === 'fields' ? ( isset( $definition['fields'][$name] ) ? $definition['fields'][$name] : null )
                                              : ( isset( $definition['indexes'][$name] ) ? $definition['indexes'][$name] : null );
                    $sql = '';
                    if ( $handler && $def )
                        $sql = $kind === 'fields' ? self::addFieldSql( $handler, $engine, $table, $name, $def )
                                                  : (string)$handler->generateAddIndexSql( $table, $name, $def, self::params( $engine ) );
                    $items[] = array( 'what' => ( $kind === 'fields' ? "add column $table.$name" : "add index $name on $table" ),
                                      'sql' => $sql );
                }
            }
        }
        return $items;
    }

    /**
     * The statement that adds a column. PostgreSQL gets the default and NOT NULL in the same statement, so rows that
     * exist are filled (the handler's own upgrade SQL sets NOT NULL afterwards, which fails on a table with rows).
     */
    protected static function addFieldSql( $handler, $engine, $table, $field, array $def )
    {
        if ( $engine === 'postgresql' )
            return "ALTER TABLE $table ADD COLUMN " . $handler->generateFieldDef( $table, $field, $def, true, self::params( $engine ) ) . ";\n";
        return (string)$handler->generateAddFieldSql( $table, $field, $def, self::params( $engine ) );
    }

    /**
     * A schema handler that writes SQL for an engine without a connection.
     *
     * @param string $engine
     * @param array $schema
     * @return eZDBSchemaInterface|false
     */
    protected static function handler( $engine, array $schema )
    {
        return eZDbSchema::instance( array( 'type' => $engine, 'instance' => expKernelTableSchema::nullDB(), 'schema' => $schema ) );
    }

    /** @return array the handler options */
    protected static function params( $engine )
    {
        return array( 'schema' => true, 'data' => false, 'allow_multi_insert' => false, 'diff_friendly' => false,
                      'table_type' => $engine === 'mysql' ? 'innodb' : '' );
    }

    /**
     * The column names of an index definition (a field may be array( 'name' => ..., 'mysql:length' => ... )).
     *
     * @param array $def
     * @return string[]
     */
    protected static function indexFields( array $def )
    {
        $fields = array();
        foreach ( $def['fields'] as $field )
            $fields[] = is_array( $field ) ? $field['name'] : $field;
        return $fields;
    }

    /**
     * @param array $missing
     * @return string[]
     */
    protected static function describe( array $missing )
    {
        $parts = array();
        foreach ( $missing['tables'] as $t )
            $parts[] = "table $t";
        foreach ( $missing['fields'] as $t => $fields )
            foreach ( $fields as $f )
                $parts[] = "column $t.$f";
        foreach ( $missing['indexes'] as $t => $names )
            foreach ( $names as $n )
                $parts[] = "index $n";
        return $parts;
    }
}
