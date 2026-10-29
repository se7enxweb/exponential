<?php
/**
 * A site.ini fixture for the DB-less password tests (expWeakPasswordListTest, eZForgotPasswordExpiryTest).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/**
 * A site.ini with the test's own values only; nothing is read from disk.
 */
class expWeakPasswordTestINI extends eZINI
{
    private $values;

    public function __construct( array $values = array() )
    {
        $this->values = $values;
    }

    function hasVariable( $blockName, $varName )
    {
        return isset( $this->values[$blockName] ) && array_key_exists( $varName, $this->values[$blockName] );
    }

    function variable( $blockName, $varName )
    {
        return $this->hasVariable( $blockName, $varName ) ? $this->values[$blockName][$varName] : false;
    }
}
