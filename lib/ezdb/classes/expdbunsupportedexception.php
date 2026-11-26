<?php
/**
 * File containing the expDBUnsupportedException class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * Thrown by a driver or an SQL helper when the database's SQL has no way to say what was asked: MS Access has no
 * upsert, no GROUP_CONCAT, no MD5 and no OFFSET, for example. The message names the database and the missing piece,
 * so the caller can fall back to doing the work in PHP.
 *
 * @since 6.0.15
 * @package lib
 */
class expDBUnsupportedException extends RuntimeException
{
    /** @var string the dialect that cannot do it (msaccess ...) */
    protected $dialect = '';

    /** @var string what was asked (upsert, groupConcat ...) */
    protected $feature = '';

    /**
     * @param string $dialect
     * @param string $feature
     * @param string $why
     */
    public function __construct( $dialect, $feature, $why = '' )
    {
        $this->dialect = (string)$dialect;
        $this->feature = (string)$feature;
        parent::__construct( "The $dialect SQL dialect has no $feature" . ( $why !== '' ? ": $why" : '' ) );
    }

    /** @return string */
    public function dialect()
    {
        return $this->dialect;
    }

    /** @return string */
    public function feature()
    {
        return $this->feature;
    }
}
?>
