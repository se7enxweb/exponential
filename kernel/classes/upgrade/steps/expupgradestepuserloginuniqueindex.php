<?php
/**
 * File containing the expUpgradeStepUserLoginUniqueIndex class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One account per login: the unique index ezuser_login, through expUserLoginIntegrity as
 * ./console exp:user-login-check --fix-index. The SQL update file leaves it out because it fails on a database where
 * two accounts share a login; here it is added only when there are none. While duplicates exist the step is
 * "unknown" (skipped, never a failure): resolve them with ./console exp:user-login-check, then run again.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepUserLoginUniqueIndex extends expUpgradeBaseStep
{
    public function id()
    {
        return '6.0.15-user-login-unique-index';
    }

    public function stage()
    {
        return 'database';
    }

    public function description()
    {
        return 'One account per login: the unique index ezuser_login (only when no login is shared)';
    }

    public function engines()
    {
        return array( 'mysql', 'postgresql', 'sqlite', 'oracle' );
    }

    public function detect( expUpgradeContext $context )
    {
        $db = $context->db();
        $status = expUserLoginIntegrity::indexStatus( $db );
        if ( $status['unique'] )
            return self::effect( self::PRESENT, 'unique index ' . implode( ', ', $status['names'] ) );
        $duplicates = expUserLoginIntegrity::duplicates( $db );
        if ( $duplicates )
            return self::effect( self::UNKNOWN, count( $duplicates ) . ' login(s) belong to more than one account: resolve them with ./console exp:user-login-check, then run again' );
        return self::effect( self::MISSING, $status['exists'] ? 'the index on ezuser.login is not unique' : 'no index on ezuser.login' );
    }

    public function plan( expUpgradeContext $context )
    {
        return array( 'add the unique index ezuser_login on ezuser.login (no login is shared)' );
    }

    public function apply( expUpgradeContext $context )
    {
        $message = '';
        if ( !expUserLoginIntegrity::addUniqueIndex( $context->db(), $message ) )
            throw new RuntimeException( (string)$message );
        return array( (string)$message !== '' ? (string)$message : 'added the unique index ezuser_login' );
    }
}
