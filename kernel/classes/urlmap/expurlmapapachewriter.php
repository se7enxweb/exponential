<?php
/**
 * File containing the expUrlMapApacheWriter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Writes the redirects of a URL map as Apache mod_rewrite rules, for a <VirtualHost> or the .htaccess of the document
 * root: one "RewriteRule ^/?old/?$ /new [R=301,L]" per redirect and wildcard (forward), R= the row's status, with
 * "RewriteCond %{HTTP_HOST}" for an old address with a host and "RewriteCond %{QUERY_STRING}" for one with a query.
 *
 * Aliases and wildcards (direct) are served by Exponential without a redirect, and a target node:<id> is only known
 * to Exponential, so those are written as "#exp:<kind> <old> <new>" comments, which expUrlMapApacheReader reads back.
 * Rows that cannot be imported are left out with a comment. A note, siteaccess and language go in a comment above.
 */
class expUrlMapApacheWriter extends expUrlMapWriter
{
    /**
     * @param expUrlMapRow[] $rows
     * @return string
     */
    function writeString( array $rows )
    {
        $out = "# URL map for Apache (mod_rewrite), written by Exponential.\n"
             . "# Place it inside <VirtualHost> or in the .htaccess file of the document root.\n"
             . "# Redirects answer with the status of their row (301 unless it says otherwise).\n"
             . "# Lines \"#exp:alias\" and \"#exp:wildcard-direct\" are internal: Exponential\n"
             . "# serves the new page at the old address itself, without a redirect, so Apache has nothing to do for them.\n"
             . "RewriteEngine On\n";
        foreach ( $rows as $row )
        {
            $out .= "\n";
            if ( !$row->isImportable() )
            {
                $problems = $row->problems();
                $out .= '# not written: ' . self::comment( $row->oldUrl() . ' (' . $problems[0] . ')' ) . "\n";
                continue;
            }
            $comment = self::rowComment( $row );
            if ( $comment !== '' )
                $out .= '# ' . $comment . "\n";
            if ( !$row->isForward() || strpos( $row->newUrl(), 'node:' ) === 0 )
            {
                $out .= '#exp:' . $row->effectiveKind() . ' ' . self::argument( $row->oldUrl() ) . ' ' . self::argument( $row->newUrl() )
                      . ( $row->status() !== '' ? ' ' . $row->status() : '' ) . "\n";
                continue;
            }
            if ( $row->oldHost() !== '' )
                $out .= 'RewriteCond %{HTTP_HOST} ' . self::argument( '^' . expUrlMapRegex::quote( $row->oldHost() ) . '$' ) . " [NC]\n";
            if ( $row->oldQuery() !== '' )
                $out .= 'RewriteCond %{QUERY_STRING} ' . self::argument( '^' . expUrlMapRegex::quote( $row->oldQuery() ) . '$' ) . "\n";
            $pattern = expUrlMapRegex::fromWildcard( self::decoded( $row->oldPath() ), true, true );
            $target = expUrlMapRegex::fromPlaceholders( self::decoded( $row->newUrl() ), true );
            if ( $row->oldQuery() !== '' && strpos( $target, '?' ) === false )
                $target .= '?';
            $out .= 'RewriteRule ' . self::argument( $pattern ) . ' ' . self::argument( $target ) . ' [R=' . $row->redirectStatus() . ",L]\n";
        }
        return $out;
    }

    /**
     * The note, siteaccess and language of a row as one comment line.
     *
     * @param expUrlMapRow $row
     * @return string
     */
    static function rowComment( expUrlMapRow $row )
    {
        $parts = array();
        if ( $row->note() !== '' )
            $parts[] = $row->note();
        if ( $row->siteaccess() !== '' )
            $parts[] = 'siteaccess ' . $row->siteaccess();
        if ( $row->language() !== '' )
            $parts[] = 'language ' . $row->language();
        return self::comment( implode( '; ', $parts ) );
    }

    /**
     * @param string $text
     * @return string one line
     */
    static function comment( $text )
    {
        return trim( preg_replace( '/\s+/', ' ', (string)$text ) );
    }

    /**
     * Percent-encoded text decoded as the web server compares it (but %2F, %3F, %23 and %25 stay encoded).
     *
     * @param string $text
     * @return string
     */
    static function decoded( $text )
    {
        return preg_replace_callback( '/%(?!2[Ff]|3[Ff]|23|25)[0-9A-Fa-f]{2}/', function( $m )
        {
            return rawurldecode( $m[0] );
        }, (string)$text );
    }

    /**
     * An argument, in quotes when it has white space or a quote.
     *
     * @param string $value
     * @return string
     */
    private static function argument( $value )
    {
        if ( $value === '' || preg_match( '/[\s"\']/', $value ) )
            return '"' . str_replace( '"', '\\"', $value ) . '"';
        return $value;
    }
}
