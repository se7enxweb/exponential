<?php
/**
 * File containing the expUrlMapWriter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Writes rows (expUrlMapRow) as a URL map file.
 */
abstract class expUrlMapWriter
{
    /** @var array */
    protected $options;

    /**
     * @param array $options format options (see each writer)
     */
    function __construct( array $options = array() )
    {
        $this->options = $options;
    }

    /**
     * @param expUrlMapRow[] $rows
     * @return string the file's content
     * @throws expUrlMapFormatException
     */
    abstract function writeString( array $rows );

    /**
     * @param string $path
     * @param expUrlMapRow[] $rows
     * @throws expUrlMapFormatException
     */
    function writeFile( $path, array $rows )
    {
        if ( @file_put_contents( $path, $this->writeString( $rows ) ) === false )
            throw new expUrlMapFormatException( "The file $path cannot be written" );
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    protected function option( $name, $default )
    {
        return array_key_exists( $name, $this->options ) ? $this->options[$name] : $default;
    }
}
