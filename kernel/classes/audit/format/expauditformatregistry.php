<?php
/**
 * The archive format registry: [AuditArchiveSettings] FormatHandlers[<name>]=<class implementing
 * expAuditFormatHandler> (RAD survey registry "auditformats"). A handler whose PHP extension or binary is
 * missing reports a problem() and the archive run falls back to gzip; the manifest names the handler used.
 *
 *   $handler = expAuditFormatRegistry::handler( 'zstd' );            // or gzip when zstd cannot work here
 *   $handler->compress( $live, $archive, expAuditFormatRegistry::level( 'zstd' ) );
 *   $stream = $handler->open( $archive ); ...; expAuditFormatBase::close( $stream );
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditFormatRegistry
{
    /** @return array name => class, as configured (the shipped five when nothing is configured) */
    public static function classes()
    {
        $classes = expAuditConfig::hash( 'AuditArchiveSettings', 'FormatHandlers' );
        if ( !$classes )
            $classes = array( 'gzip' => 'expAuditGzipFormat', 'bzip2' => 'expAuditBzip2Format', 'xz' => 'expAuditXzFormat',
                              'zstd' => 'expAuditZstdFormat', 'zip' => 'expAuditZipFormat' );
        return $classes;
    }

    /**
     * A handler by name, or null when it is not registered or its class does not implement the interface.
     *
     * @param string $name
     * @return expAuditFormatHandler|null
     */
    public static function get( $name )
    {
        $classes = self::classes();
        if ( !isset( $classes[$name] ) || !class_exists( $classes[$name] ) )
            return null;
        $handler = new $classes[$name]();
        return $handler instanceof expAuditFormatHandler ? $handler : null;
    }

    /**
     * The handler to use for a name: itself when it works here, else gzip.
     *
     * @param string $name
     * @return expAuditFormatHandler
     */
    public static function handler( $name )
    {
        $handler = self::get( $name );
        if ( $handler && $handler->problem() === '' )
            return $handler;
        return new expAuditGzipFormat();
    }

    /**
     * The handler that wrote an archive, by its file name's extension.
     *
     * @param string $archive
     * @return expAuditFormatHandler|null
     */
    public static function forArchive( $archive )
    {
        foreach ( array_keys( self::classes() ) as $name )
        {
            $h = self::get( $name );
            if ( $h && substr( $archive, -strlen( $h->extension() ) ) === $h->extension() )
                return $h;
        }
        return null;
    }

    /**
     * Every registered handler with its problem ('' = available).
     *
     * @return array name => array( class, problem, extension )
     */
    public static function status()
    {
        $out = array();
        foreach ( self::classes() as $name => $class )
        {
            $h = self::get( $name );
            $out[$name] = array( 'class' => $class, 'extension' => $h ? $h->extension() : '',
                                 'problem' => $h ? $h->problem() : "the class $class does not exist or does not implement expAuditFormatHandler" );
        }
        return $out;
    }

    /** @return int The configured compression level of a handler */
    public static function level( $name )
    {
        $levels = expAuditConfig::hash( 'AuditArchiveSettings', 'Level' );
        $defaults = array( 'gzip' => 9, 'bzip2' => 9, 'xz' => 6, 'zstd' => 19, 'zip' => 9 );
        if ( isset( $levels[$name] ) && is_numeric( $levels[$name] ) )
            return (int)$levels[$name];
        return isset( $defaults[$name] ) ? $defaults[$name] : 6;
    }
}
