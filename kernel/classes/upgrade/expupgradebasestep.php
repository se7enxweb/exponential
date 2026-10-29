<?php
/**
 * File containing the expUpgradeBaseStep class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The defaults of a step: every engine, no dependency, not optional, version 6.0.15, and the helpers to answer
 * detect() in one line.
 *
 * @since 6.0.15
 * @package kernel
 */
abstract class expUpgradeBaseStep implements expUpgradeStep
{
    public function version()
    {
        return '6.0.15';
    }

    public function engines()
    {
        return array( '*' );
    }

    public function after()
    {
        return array();
    }

    public function optional()
    {
        return false;
    }

    /**
     * @param string $effect
     * @param string $detail
     * @return array
     */
    protected static function effect( $effect, $detail = '' )
    {
        return array( 'effect' => $effect, 'detail' => (string)$detail );
    }

    /**
     * PRESENT for true, MISSING for false, UNKNOWN for null.
     *
     * @param bool|null $present
     * @param string $detail
     * @return array
     */
    protected static function fromBool( $present, $detail = '' )
    {
        if ( $present === null )
            return self::effect( self::UNKNOWN, $detail !== '' ? $detail : 'cannot tell from the database catalog' );
        return self::effect( $present ? self::PRESENT : self::MISSING, $detail );
    }
}
