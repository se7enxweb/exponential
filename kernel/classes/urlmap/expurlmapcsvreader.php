<?php
/**
 * File containing the expUrlMapCsvReader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads CSV (RFC 4180) URL maps: the delimiter (",", ";" or a tab) is found from the first lines unless the option
 * "delimiter" gives it; quoted fields may hold delimiters, doubled quotes and line breaks; a UTF-8 byte order mark,
 * UTF-16 and CRLF line ends are handled; the first row is a header when it names the columns (expUrlMapTable).
 * Each row keeps the line number its record starts on. Lines whose first cell starts with "#" are comments.
 */
class expUrlMapCsvReader extends expUrlMapReader
{
    /**
     * @param string $data
     * @return expUrlMapRow[]
     */
    function readString( $data )
    {
        $text = self::utf8Text( $data );
        if ( trim( $text ) === '' )
            return array();
        $delimiter = isset( $this->options['delimiter'] ) ? (string)$this->options['delimiter'] : self::sniffDelimiter( $text );
        $problems = array();
        $table = self::parse( $text, $delimiter, $problems );
        $rows = expUrlMapTable::rowsFromTable( $table );
        foreach ( $rows as $row )
        {
            if ( isset( $problems[$row->line()] ) )
                $row->addProblem( $problems[$row->line()] );
        }
        return $rows;
    }

    /**
     * The delimiter that splits the first (up to 20) non-empty lines into the same number of fields most often;
     * "," when none does.
     *
     * @param string $text
     * @return string
     */
    static function sniffDelimiter( $text )
    {
        $lines = array();
        foreach ( explode( "\n", $text ) as $line )
        {
            if ( trim( $line ) === '' || ltrim( $line )[0] === '#' )
                continue;
            // quoted parts do not count
            $lines[] = preg_replace( '/"[^"]*"/', '""', $line );
            if ( count( $lines ) >= 20 )
                break;
        }
        $best = ',';
        $bestScore = 0;
        foreach ( array( "\t", ';', ',' ) as $candidate )
        {
            $counts = array();
            foreach ( $lines as $line )
                $counts[] = substr_count( $line, $candidate );
            if ( !$counts || max( $counts ) === 0 )
                continue;
            $frequency = array_count_values( $counts );
            arsort( $frequency );
            $common = key( $frequency );
            $score = $common > 0 ? current( $frequency ) * 100 + $common : 0;
            if ( $score > $bestScore )
            {
                $best = $candidate;
                $bestScore = $score;
            }
        }
        return $best;
    }

    /**
     * Splits CSV text into records.
     *
     * @param string $text UTF-8 with "\n" line ends
     * @param string $delimiter
     * @param array $problems line => problem, for records that are not well formed
     * @return array line number of the record's start => list of fields
     */
    static function parse( $text, $delimiter, array &$problems = array() )
    {
        $table = array();
        $length = strlen( $text );
        $pos = 0;
        $line = 1;
        while ( $pos < $length )
        {
            $start = $line;
            $fields = array();
            while ( true )
            {
                if ( $pos < $length && $text[$pos] === '"' )
                {
                    // quoted field
                    $value = '';
                    $pos++;
                    $closed = false;
                    while ( $pos < $length )
                    {
                        $next = strpos( $text, '"', $pos );
                        if ( $next === false )
                        {
                            $value .= substr( $text, $pos );
                            $line += substr_count( $text, "\n", $pos );
                            $pos = $length;
                            break;
                        }
                        $chunk = substr( $text, $pos, $next - $pos );
                        $line += substr_count( $chunk, "\n" );
                        $value .= $chunk;
                        if ( $next + 1 < $length && $text[$next + 1] === '"' )
                        {
                            $value .= '"';
                            $pos = $next + 2;
                            continue;
                        }
                        $pos = $next + 1;
                        $closed = true;
                        break;
                    }
                    if ( !$closed )
                        $problems[$start] = 'a quoted field that starts on line ' . $start . ' is not closed';
                    // anything up to the next delimiter after the closing quote belongs to the field
                    $rest = strcspn( $text, $delimiter . "\n", $pos );
                    if ( $rest > 0 )
                    {
                        $value .= substr( $text, $pos, $rest );
                        $pos += $rest;
                        if ( !isset( $problems[$start] ) )
                            $problems[$start] = 'text after a closing quote on line ' . $line;
                    }
                }
                else
                {
                    $span = strcspn( $text, $delimiter . "\n", $pos );
                    $value = substr( $text, $pos, $span );
                    $pos += $span;
                }
                $fields[] = $value;
                if ( $pos >= $length )
                    break;
                if ( $text[$pos] === "\n" )
                {
                    $pos++;
                    $line++;
                    break;
                }
                // a delimiter
                $pos += strlen( $delimiter );
                if ( $pos >= $length )
                {
                    $fields[] = '';
                    break;
                }
            }
            $table[$start] = $fields;
        }
        return $table;
    }
}
