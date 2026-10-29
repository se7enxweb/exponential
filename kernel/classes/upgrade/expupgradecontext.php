<?php
/**
 * File containing the expUpgradeContext class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What a step works on: the database connection, its engine, the kernel schema (share/db_schema.dba), the options of
 * the run and whether it writes. Steps use $context->db(), never eZDB::instance() of their own, so a test or a tool
 * can run them on another connection.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeContext
{
    /** @var eZDBInterface */
    protected $db;

    /** @var bool */
    protected $apply;

    /** @var array */
    protected $options;

    /** @var array|null name => definition */
    protected $schema;

    /** @var string|null */
    protected $schemaFile;

    /**
     * @param eZDBInterface|null $db the connection (eZDB::instance() when null)
     * @param bool $apply whether the run writes
     * @param array $options the run's options (only, skip, force-order ...)
     * @param string|null $schemaFile the .dba the schema steps read (share/db_schema.dba when null)
     */
    public function __construct( $db = null, $apply = false, array $options = array(), $schemaFile = null )
    {
        $this->db = $db ?: eZDB::instance();
        $this->apply = (bool)$apply;
        $this->options = $options;
        $this->schemaFile = $schemaFile;
    }

    /** @return eZDBInterface */
    public function db()
    {
        return $this->db;
    }

    /**
     * The engine: mysql, postgresql, sqlite, oracle, mongodb (expKernelTableSchema::type()).
     *
     * @return string
     */
    public function engine()
    {
        return expKernelTableSchema::type( $this->db );
    }

    /** @return bool */
    public function isApply()
    {
        return $this->apply;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function option( $name, $default = null )
    {
        return array_key_exists( $name, $this->options ) ? $this->options[$name] : $default;
    }

    /**
     * Sets an option for the steps that follow (expUpgrade sets finish-needed before the finish stage).
     *
     * @param string $name
     * @param mixed $value
     */
    public function setOption( $name, $value )
    {
        $this->options[$name] = $value;
    }

    /**
     * The kernel schema, read once.
     *
     * @return array name => definition
     */
    public function schema()
    {
        if ( $this->schema === null )
            $this->schema = expKernelTableSchema::read( $this->schemaFile );
        return $this->schema;
    }

    /**
     * The definition of one table of the kernel schema, or null.
     *
     * @param string $table
     * @return array|null
     */
    public function table( $table )
    {
        $schema = $this->schema();
        return isset( $schema[$table] ) ? $schema[$table] : null;
    }
}
