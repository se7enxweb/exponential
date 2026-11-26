<?php
/**
 * File containing the expDFSSQLServerResult class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The result of one statement of the SQL Server DFS backend (expDFSFileHandlerSQLServerBackend): its rows, read one
 * at a time as the backend reads them, and the number of rows it changed.
 *
 * @package kernel
 */
class expDFSSQLServerResult
{
    /** @var array[] rows, column => value (strings or null) */
    private $rows;

    /** @var int */
    private $affected;

    /** @var int the next row */
    private $position = 0;

    /**
     * @param array[] $rows
     * @param int $affected rows changed by an INSERT, UPDATE, DELETE or MERGE
     */
    public function __construct( array $rows, $affected )
    {
        $this->rows = array_values( $rows );
        $this->affected = (int)$affected;
    }

    /**
     * The next row by column name, or false after the last one.
     *
     * @return array|false
     */
    public function fetchAssoc()
    {
        return isset( $this->rows[$this->position] ) ? $this->rows[$this->position++] : false;
    }

    /**
     * The next row by position, or false after the last one.
     *
     * @return array|false
     */
    public function fetchNum()
    {
        $row = $this->fetchAssoc();
        return $row === false ? false : array_values( $row );
    }

    /**
     * Rows a change touched; for a SELECT, the rows it returned.
     *
     * @return int
     */
    public function affected()
    {
        return $this->rows && !$this->affected ? count( $this->rows ) : $this->affected;
    }
}
