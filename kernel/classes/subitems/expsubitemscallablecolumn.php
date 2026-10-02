<?php
/**
 * A subitems column whose value comes from a PHP callable named by Handler=class::method
 * in its [Column_<key>] block. The callable gets ( eZContentObjectTreeNode $node,
 * array $settings, expSubitemsColumn $column ) and returns the value.
 *
 * The handler name comes from the INI only, never from a request.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsCallableColumn extends expSubitemsColumn
{
    /** @var callable */
    protected $callable;

    /**
     * @param string $key
     * @param array $settings the column block; Handler= names the callable
     * @throws InvalidArgumentException when Handler= is not a callable class::method
     */
    public function __construct( $key, array $settings )
    {
        parent::__construct( $key, $settings );
        $this->callable = self::callableFromSetting( isset( $settings['Handler'] ) ? $settings['Handler'] : '' );
        if ( $this->callable === null )
            throw new InvalidArgumentException( "Column '$key': Handler is not a callable class::method" );
    }

    /**
     * The callable for a Handler= value, or null.
     *
     * @param string $handler "class::method"
     * @return callable|null
     */
    public static function callableFromSetting( $handler )
    {
        $handler = trim( (string)$handler );
        if ( !preg_match( '/^([A-Za-z_\\\\][A-Za-z0-9_\\\\]*)::([A-Za-z_][A-Za-z0-9_]*)$/', $handler, $m ) )
            return null;
        if ( !class_exists( $m[1] ) )
            return null;
        $callable = array( $m[1], $m[2] );
        return is_callable( $callable ) ? $callable : null;
    }

    public function value( eZContentObjectTreeNode $node )
    {
        return call_user_func( $this->callable, $node, $this->settings, $this );
    }
}
