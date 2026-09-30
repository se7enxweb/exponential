<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * A word level difference of two texts, for the package comparison viewer (package/compare,
 * eZPackageComparison): which words were removed from the first text and which were added in the
 * second. Myers' O(ND) algorithm over word, space and punctuation tokens, after the common
 * beginning and end are cut off, so that a long text with a small change is cheap. A difference too
 * big to trace word by word is traced line by line, and one too big for that is shown as the whole
 * of the first text removed and the whole of the second added.
 *
 * Plain PHP, no database and no templates; the result is escaped by whoever outputs it.
 */
class eZPackageComparisonDiff
{
    /** Above this many edits between the token lists the trace is given up at that granularity. */
    const MAX_EDITS = 1000;

    /** Above this many tokens on the two sides together a word trace is not tried (line trace instead). */
    const MAX_WORD_TOKENS = 40000;

    /**
     * The difference of $old and $new as a list of runs: array( array( 'op' => 'equal'|'delete'|'insert',
     * 'text' => string ), ... ), neighbouring runs of the same kind merged. Concatenating the 'equal'
     * and 'delete' runs gives $old back, 'equal' and 'insert' gives $new.
     */
    static function words( $old, $new )
    {
        $old = (string)$old;
        $new = (string)$new;
        if ( $old === $new )
            return $old === '' ? array() : array( array( 'op' => 'equal', 'text' => $old ) );

        // Several lines: matched line by line first, then word by word inside each changed stretch,
        // so a line only one side has is shown whole instead of spliced into its neighbours
        if ( strpos( $old, "\n" ) !== false || strpos( $new, "\n" ) !== false )
        {
            $lineOps = self::tokenDiff( self::lines( $old ), self::lines( $new ) );
            if ( $lineOps !== null )
            {
                $ops = array();
                $deleted = '';
                $inserted = '';
                $lineOps[] = array( 'op' => 'equal', 'text' => '' ); // flushes the last stretch
                foreach ( $lineOps as $op )
                {
                    if ( $op['op'] === 'delete' )
                        $deleted .= $op['text'];
                    elseif ( $op['op'] === 'insert' )
                        $inserted .= $op['text'];
                    else
                    {
                        if ( $deleted !== '' && $inserted !== '' )
                        {
                            $inner = self::tokenDiff( self::tokens( $deleted ), self::tokens( $inserted ) );
                            if ( $inner === null )
                                $inner = array( array( 'op' => 'delete', 'text' => $deleted ), array( 'op' => 'insert', 'text' => $inserted ) );
                            foreach ( $inner as $innerOp )
                                $ops[] = $innerOp;
                        }
                        elseif ( $deleted !== '' )
                            $ops[] = array( 'op' => 'delete', 'text' => $deleted );
                        elseif ( $inserted !== '' )
                            $ops[] = array( 'op' => 'insert', 'text' => $inserted );
                        $deleted = '';
                        $inserted = '';
                        if ( $op['text'] !== '' )
                            $ops[] = $op;
                    }
                }
                return self::merge( $ops );
            }
        }

        $a = self::tokens( $old );
        $b = self::tokens( $new );
        $ops = null;
        if ( count( $a ) + count( $b ) <= self::MAX_WORD_TOKENS )
            $ops = self::tokenDiff( $a, $b );
        if ( $ops === null )
        {
            // Too different to trace word by word: line by line, and word by word inside a changed
            // line pair only where that pair is small enough
            $ops = self::tokenDiff( self::lines( $old ), self::lines( $new ) );
            if ( $ops === null )
                $ops = array( array( 'op' => 'delete', 'text' => $old ), array( 'op' => 'insert', 'text' => $new ) );
        }
        return self::merge( $ops );
    }

    /**
     * The runs split for a side by side view: array( 'old' => runs of $old ('equal'/'delete'),
     * 'new' => runs of $new ('equal'/'insert'), 'changed' => bool ).
     */
    static function sides( $old, $new )
    {
        $runs = self::words( $old, $new );
        $oldRuns = array();
        $newRuns = array();
        foreach ( $runs as $run )
        {
            if ( $run['op'] !== 'insert' )
                $oldRuns[] = $run;
            if ( $run['op'] !== 'delete' )
                $newRuns[] = $run;
        }
        return array( 'old' => $oldRuns, 'new' => $newRuns, 'changed' => (string)$old !== (string)$new );
    }

    /** Words (letters, digits, underscore and joining marks), runs of white space, and single other characters. */
    static function tokens( $text )
    {
        if ( $text === '' )
            return array();
        $parts = preg_split( '/([\p{L}\p{N}\p{M}_]+|\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
        if ( $parts === false ) // not valid UTF-8: byte-wise words and spaces then
            $parts = preg_split( '/(\w+|\s+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
        return $parts === false ? array( $text ) : $parts;
    }

    /** Lines, each keeping its own line break. */
    static function lines( $text )
    {
        if ( $text === '' )
            return array();
        $parts = preg_split( '/(?<=\n)/', $text, -1, PREG_SPLIT_NO_EMPTY );
        return $parts === false ? array( $text ) : $parts;
    }

    /**
     * Edit script of token lists $a -> $b as runs (one token per run, merged later), or null when it
     * would take more than MAX_EDITS edits.
     */
    static function tokenDiff( array $a, array $b )
    {
        $n = count( $a );
        $m = count( $b );
        // Common beginning and end
        $start = 0;
        while ( $start < $n && $start < $m && $a[$start] === $b[$start] )
            ++$start;
        $endA = $n;
        $endB = $m;
        while ( $endA > $start && $endB > $start && $a[$endA - 1] === $b[$endB - 1] )
        {
            --$endA;
            --$endB;
        }
        $ops = array();
        for ( $i = 0; $i < $start; ++$i )
            $ops[] = array( 'op' => 'equal', 'text' => $a[$i] );

        $midA = array_slice( $a, $start, $endA - $start );
        $midB = array_slice( $b, $start, $endB - $start );
        $middle = self::myers( $midA, $midB );
        if ( $middle === null )
            return null;
        foreach ( $middle as $op )
            $ops[] = $op;

        for ( $i = $endA; $i < $n; ++$i )
            $ops[] = array( 'op' => 'equal', 'text' => $a[$i] );
        return $ops;
    }

    /**
     * Myers' greedy shortest edit script, with the V arrays of every step kept for the backtrack
     * (memory O(D^2), bounded by MAX_EDITS). Returns one run per token, or null past MAX_EDITS.
     */
    static function myers( array $a, array $b )
    {
        $n = count( $a );
        $m = count( $b );
        if ( $n === 0 && $m === 0 )
            return array();
        if ( $n === 0 )
            return array_map( function ( $t ) { return array( 'op' => 'insert', 'text' => $t ); }, $b );
        if ( $m === 0 )
            return array_map( function ( $t ) { return array( 'op' => 'delete', 'text' => $t ); }, $a );

        $max = min( $n + $m, self::MAX_EDITS );
        $v = array( 1 => 0 );
        $trace = array();
        $found = false;
        for ( $d = 0; $d <= $max; ++$d )
        {
            $trace[] = $v;
            for ( $k = -$d; $k <= $d; $k += 2 )
            {
                if ( $k === -$d || ( $k !== $d && ( isset( $v[$k - 1] ) ? $v[$k - 1] : -1 ) < ( isset( $v[$k + 1] ) ? $v[$k + 1] : -1 ) ) )
                    $x = isset( $v[$k + 1] ) ? $v[$k + 1] : 0;
                else
                    $x = ( isset( $v[$k - 1] ) ? $v[$k - 1] : 0 ) + 1;
                $y = $x - $k;
                while ( $x < $n && $y < $m && $a[$x] === $b[$y] )
                {
                    ++$x;
                    ++$y;
                }
                $v[$k] = $x;
                if ( $x >= $n && $y >= $m )
                {
                    $found = true;
                    break 2;
                }
            }
        }
        if ( !$found )
            return null;

        // Backtrack from (n, m) through the kept V arrays
        $ops = array();
        $x = $n;
        $y = $m;
        for ( $d = count( $trace ) - 1; $d >= 0; --$d )
        {
            $v = $trace[$d];
            $k = $x - $y;
            if ( $k === -$d || ( $k !== $d && ( isset( $v[$k - 1] ) ? $v[$k - 1] : -1 ) < ( isset( $v[$k + 1] ) ? $v[$k + 1] : -1 ) ) )
                $prevK = $k + 1;
            else
                $prevK = $k - 1;
            $prevX = isset( $v[$prevK] ) ? $v[$prevK] : 0;
            $prevY = $prevX - $prevK;
            while ( $x > $prevX && $y > $prevY )
            {
                $ops[] = array( 'op' => 'equal', 'text' => $a[$x - 1] );
                --$x;
                --$y;
            }
            if ( $d > 0 )
            {
                if ( $x === $prevX )
                    $ops[] = array( 'op' => 'insert', 'text' => $b[$y - 1] );
                else
                    $ops[] = array( 'op' => 'delete', 'text' => $a[$x - 1] );
            }
            $x = $prevX;
            $y = $prevY;
        }
        return array_reverse( $ops );
    }

    /**
     * Neighbouring runs of the same kind joined; and in a changed stretch every deletion put before
     * every insertion, so a replaced phrase reads as one removed and one added block, not as a
     * zipper of single words.
     */
    static function merge( array $ops )
    {
        $out = array();
        $pendingDelete = '';
        $pendingInsert = '';
        $flush = function () use ( &$out, &$pendingDelete, &$pendingInsert )
        {
            if ( $pendingDelete !== '' )
                $out[] = array( 'op' => 'delete', 'text' => $pendingDelete );
            if ( $pendingInsert !== '' )
                $out[] = array( 'op' => 'insert', 'text' => $pendingInsert );
            $pendingDelete = '';
            $pendingInsert = '';
        };
        $count = count( $ops );
        for ( $i = 0; $i < $count; ++$i )
        {
            $op = $ops[$i];
            if ( $op['op'] === 'equal' )
            {
                // A lone space between two changes belongs to the change, both sides
                $isGlue = $pendingDelete !== '' && $pendingInsert !== '' && trim( $op['text'] ) === ''
                          && isset( $ops[$i + 1] ) && $ops[$i + 1]['op'] !== 'equal';
                if ( $isGlue )
                {
                    $pendingDelete .= $op['text'];
                    $pendingInsert .= $op['text'];
                    continue;
                }
                $flush();
                $last = count( $out ) - 1;
                if ( $last >= 0 && $out[$last]['op'] === 'equal' )
                    $out[$last]['text'] .= $op['text'];
                else
                    $out[] = $op;
            }
            elseif ( $op['op'] === 'delete' )
                $pendingDelete .= $op['text'];
            else
                $pendingInsert .= $op['text'];
        }
        $flush();
        return $out;
    }
}

?>
