<?php
/**
 * File containing the expUpgradeStepPasswordHashWidth class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * ezuser.password_hash takes the hashes Exponential writes: varchar(255). A database that came from 5.x may still
 * have varchar(50), too narrow for the bcrypt hash every user is given at the first sign-in. SQLite keeps no length
 * (not applicable there).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepPasswordHashWidth extends expUpgradeBaseStep
{
    const WIDTH = 255;

    public function id()
    {
        return '6.0.15-user-password-hash-width';
    }

    public function stage()
    {
        return 'database';
    }

    public function description()
    {
        return 'ezuser.password_hash is wide enough for the password hashes (varchar(255))';
    }

    public function engines()
    {
        return array( 'mysql', 'postgresql', 'oracle' );
    }

    public function detect( expUpgradeContext $context )
    {
        $length = expUpgradeCatalog::columnLength( $context->db(), 'ezuser', 'password_hash' );
        if ( $length === null )
            return self::effect( self::UNKNOWN, 'cannot read the length of ezuser.password_hash' );
        if ( $length === 0 )
            return self::effect( self::NOT_APPLICABLE, 'the engine keeps no length for text columns' );
        return $length >= self::WIDTH ? self::effect( self::PRESENT, "ezuser.password_hash is varchar($length)" )
                                      : self::effect( self::MISSING, "ezuser.password_hash is varchar($length)" );
    }

    /**
     * @param string $engine
     * @return string
     */
    public static function sql( $engine )
    {
        switch ( $engine )
        {
            case 'mysql':
                return 'ALTER TABLE ezuser CHANGE password_hash password_hash VARCHAR(255) default NULL';
            case 'postgresql':
                return 'ALTER TABLE ezuser ALTER COLUMN password_hash TYPE VARCHAR(255)';
            case 'oracle':
                return 'ALTER TABLE ezuser MODIFY ( password_hash VARCHAR2(255) )';
        }
        return '';
    }

    public function plan( expUpgradeContext $context )
    {
        return array( 'widen ezuser.password_hash: ' . self::sql( $context->engine() ) );
    }

    public function apply( expUpgradeContext $context )
    {
        $sql = self::sql( $context->engine() );
        if ( $sql === '' )
            throw new RuntimeException( 'no statement for ' . $context->engine() );
        expUpgradeCatalog::execute( $context->db(), $sql . ";\n" );
        return array( 'widened ezuser.password_hash to varchar(255)' );
    }
}
