<?php
/**
 * File containing the expUrlMapNginxWriter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Writes the redirects of a URL map as nginx configuration, for inclusion in the server block of the site: an exact
 * redirect is "location = /old { return 301 /new; }" (rows of the same path with a query each get an
 * "if ($args = ...)" inside it), a wildcard (forward) is "rewrite ^/old/(.*)$ /new/$1 permanent;". Rows whose old
 * address has a host go into a "server { server_name <host>; ... }" block of their own.
 *
 * Aliases and wildcards (direct) are served by Exponential without a redirect, and a target node:<id> is only known
 * to Exponential, so those are written as "#exp:<kind> <old> <new>" comments, which expUrlMapNginxReader reads back.
 * Rows that cannot be imported, and a second redirect of the same address, are left out with a comment.
 */
class expUrlMapNginxWriter extends expUrlMapWriter
{
    /**
     * @param expUrlMapRow[] $rows
     * @return string
     */
    function writeString( array $rows )
    {
        $out = "# URL map for nginx, written by Exponential.\n"
             . "# Include it in the server block of the site (the server blocks below are for other host names).\n"
             . "# Redirects answer with 301. Lines \"#exp:alias\" and \"#exp:wildcard-direct\" are internal: Exponential\n"
             . "# serves the new page at the old address itself, without a redirect, so nginx has nothing to do for them.\n";
        $byHost = array( '' => array() );
        foreach ( $rows as $row )
            $byHost[$row->isImportable() ? $row->oldHost() : ''][] = $row;
        foreach ( $byHost as $host => $hostRows )
        {
            if ( !$hostRows )
                continue;
            $indent = '';
            if ( $host !== '' )
            {
                $out .= "\nserver {\n    server_name " . self::word( preg_replace( '/:\d+$/', '', $host ) ) . ";\n";
                $indent = '    ';
            }
            $out .= $this->block( $hostRows, $indent );
            if ( $host !== '' )
                $out .= "}\n";
        }
        return $out;
    }

    /**
     * @param expUrlMapRow[] $rows
     * @param string $indent
     * @return string
     */
    private function block( array $rows, $indent )
    {
        // exact redirects grouped by path, written where the path first appears
        $exact = array();
        foreach ( $rows as $index => $row )
        {
            if ( $row->isImportable() && $row->isForward() && !$row->isWildcard() && strpos( $row->newUrl(), 'node:' ) !== 0 )
                $exact[$row->oldPath()][] = $index;
        }
        $out = '';
        $done = array();
        foreach ( $rows as $index => $row )
        {
            if ( isset( $done[$index] ) )
                continue;
            $out .= "\n";
            if ( !$row->isImportable() )
            {
                $problems = $row->problems();
                $out .= $indent . '# not written: ' . expUrlMapApacheWriter::comment( $row->oldUrl() . ' (' . $problems[0] . ')' ) . "\n";
                continue;
            }
            $comment = expUrlMapApacheWriter::rowComment( $row );
            if ( !$row->isForward() || strpos( $row->newUrl(), 'node:' ) === 0 )
            {
                if ( $comment !== '' )
                    $out .= $indent . '# ' . $comment . "\n";
                $out .= $indent . '#exp:' . $row->effectiveKind() . ' ' . self::word( $row->oldUrl() ) . ' ' . self::word( $row->newUrl() ) . "\n";
                continue;
            }
            if ( $row->isWildcard() )
            {
                if ( $comment !== '' )
                    $out .= $indent . '# ' . $comment . "\n";
                $regex = expUrlMapRegex::fromWildcard( expUrlMapApacheWriter::decoded( $row->oldPath() ), false, false );
                $out .= $indent . 'rewrite ' . self::word( $regex ) . ' '
                      . self::word( expUrlMapRegex::fromPlaceholders( $row->newUrl(), false ) . ( $row->oldQuery() !== '' ? '?' : '' ) ) . " permanent;\n";
                continue;
            }
            $plain = null;
            $conditions = '';
            $comments = '';
            $skipped = '';
            foreach ( $exact[$row->oldPath()] as $member )
            {
                $done[$member] = true;
                $memberRow = $rows[$member];
                $memberComment = expUrlMapApacheWriter::rowComment( $memberRow );
                if ( $memberComment !== '' )
                    $comments .= $indent . '# ' . $memberComment . "\n";
                $target = self::word( $memberRow->newUrl() );
                if ( $memberRow->oldQuery() !== '' )
                    $conditions .= $indent . '    if ($args = ' . self::word( $memberRow->oldQuery(), true ) . ') { return 301 ' . $target . "; }\n";
                elseif ( $plain === null )
                    $plain = $indent . '    return 301 ' . $target . ";\n";
                else
                    $skipped .= $indent . '# not written: a second redirect of ' . expUrlMapApacheWriter::comment( $memberRow->oldUrl() ) . ' to ' . expUrlMapApacheWriter::comment( $memberRow->newUrl() ) . "\n";
            }
            $out .= $comments . $indent . 'location = ' . self::word( expUrlMapApacheWriter::decoded( $row->oldPath() ) ) . " {\n"
                  . $conditions . ( $plain !== null ? $plain : '' ) . $indent . "}\n" . $skipped;
        }
        return $out;
    }

    /**
     * A word of the configuration, in double quotes when it has white space, a quote, ; { } or # (or when $always).
     *
     * @param string $value
     * @param bool $always
     * @return string
     */
    private static function word( $value, $always = false )
    {
        if ( $always || $value === '' || preg_match( '/[\s"\';{}#]/', $value ) )
            return '"' . str_replace( '"', '\\"', $value ) . '"';
        return $value;
    }
}
