<?php
/**
 * File containing the expLibSQLResult class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The rows of a statement run on a libSQL server (expLibSQLConnection::query()), read as PHP's SQLite3Result is
 * read: fetchArray( SQLITE3_ASSOC | SQLITE3_NUM | SQLITE3_BOTH ) row by row, false after the last; numColumns(),
 * columnName(), reset(), finalize(). The server sends all rows in one answer, so they are all in memory.
 *
 * @since 6.0.15
 */
class expLibSQLResult
{
    /** @var string[] */
    protected $Columns;

    /** @var array list of rows, each a list of values */
    protected $Rows;

    /** @var int the next row */
    protected $Position = 0;

    /**
     * @param string[] $columns
     * @param array $rows
     */
    public function __construct( array $columns, array $rows )
    {
        $this->Columns = array_values( $columns );
        $this->Rows = array_values( $rows );
    }

    /**
     * The next row, false after the last.
     *
     * @param int $mode SQLITE3_ASSOC (1), SQLITE3_NUM (2) or SQLITE3_BOTH (3, the default)
     * @return array|false
     */
    public function fetchArray( $mode = 3 )
    {
        if ( !isset( $this->Rows[$this->Position] ) )
            return false;
        $values = array_values( (array)$this->Rows[$this->Position++] );
        $row = array();
        foreach ( $this->Columns as $n => $name )
        {
            $value = array_key_exists( $n, $values ) ? $values[$n] : null;
            if ( $mode & 2 )
                $row[$n] = $value;
            if ( $mode & 1 )
                $row[$name] = $value;   // a name twice: the last column wins, as in SQLite3Result
        }
        return $row;
    }

    /** @return int */
    public function numColumns()
    {
        return count( $this->Columns );
    }

    /**
     * @param int $column
     * @return string|false
     */
    public function columnName( $column )
    {
        return isset( $this->Columns[$column] ) ? $this->Columns[$column] : false;
    }

    /** @return int the rows of the result (not in SQLite3Result, which does not know them before the end) */
    public function numRows()
    {
        return count( $this->Rows );
    }

    /** @return bool */
    public function reset()
    {
        $this->Position = 0;
        return true;
    }

    /** @return bool */
    public function finalize()
    {
        $this->Rows = array();
        $this->Position = 0;
        return true;
    }
}
