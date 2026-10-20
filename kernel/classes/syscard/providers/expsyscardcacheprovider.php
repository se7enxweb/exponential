<?php
/**
 * File containing the expSysCardCacheProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The caches: which are on (view cache, template compilation, the static cache), and for exp:info the size of the
 * cache directory and of each cache in it, measured in one walk that stops at the provider's deadline (a size cut
 * short is shown with "at least").
 */
class expSysCardCacheProvider extends expSysCardBaseProvider
{
    protected $id = 'caches';
    protected $section = 'site';
    protected $priority = 45;
    protected $budget = 3.0;

    /** How many cache directories exp:info lists by name (the largest) */
    const LISTED = 10;

    public function collect( expSysCardContext $context )
    {
        $on = array();
        $off = array();
        foreach ( array( 'view' => array( 'ContentSettings', 'ViewCaching', 'enabled' ), 'templates' => array( 'TemplateSettings', 'TemplateCompile', 'enabled' ),
                         'static' => array( 'ContentSettings', 'StaticCache', 'disabled' ) ) as $name => $setting )
        {
            if ( $context->iniEnabled( 'site.ini', $setting[0], $setting[1], $setting[2] ) )
                $on[] = $name;
            else
                $off[] = $name;
        }
        $viewOff = in_array( 'view', $off, true );
        $facts = array( $this->fact( 'state', 'Caches', ( empty( $on ) ? '' : implode( ', ', $on ) . ' on' ) . ( empty( $off ) ? '' : ( empty( $on ) ? '' : '; ' ) . implode( ', ', $off ) . ' off' ),
                                     $viewOff ? expSysCardFact::WARN : expSysCardFact::INFO,
                                     $viewOff ? 'The view cache is off (site.ini [ContentSettings] ViewCaching=disabled): every page is rendered again for every visitor.' : '' ) );
        if ( $context->mode !== expSysCardContext::MODE_INFO )
            return $facts;

        $dir = (string)$context->get( 'caches.dir', function () { return class_exists( 'eZSys' ) ? (string)eZSys::cacheDirectory() : ''; }, '' );
        if ( $dir === '' )
            return $facts;
        $dir = $context->path( $dir );
        $deadline = $context->deadline !== null ? $context->deadline - 0.1 : microtime( true ) + 2.0;
        $size = $context->get( 'caches.size:' . $dir, function () use ( $dir, $deadline ) {
            return class_exists( 'expSystemReport' ) ? expSystemReport::directorySize( $dir, $deadline, $dir ) : null;
        } );
        if ( !is_array( $size ) || $size['bytes'] === null )
            return $facts;
        $prefix = !empty( $size['complete'] ) ? '' : 'at least ';
        $facts[] = $this->fact( 'size', 'Cache size', $prefix . self::size( $size['bytes'] ) . ' in ' . self::counted( $size['files'], 'file' ), expSysCardFact::INFO, '', 'caches' );
        $entries = isset( $size['entries'] ) && is_array( $size['entries'] ) ? $size['entries'] : array();
        arsort( $entries );
        foreach ( array_slice( $entries, 0, self::LISTED, true ) as $name => $bytes )
            $facts[] = $this->fact( 'dir:' . $name, (string)$name, $prefix . self::size( $bytes ), expSysCardFact::INFO, '', 'caches' );
        return $facts;
    }
}
