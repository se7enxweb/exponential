<?php
/**
 * A database handler that runs nothing and only escapes, for the tests of SQL builders (eZContentPermissionSQLTest,
 * eZContentNodePathConditionTest). Set with eZDB::setInstance() and put back after the test.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZContentPermissionSQLTestDB extends eZNullDB
{
    public function __construct()
    {
    }

    function escapeString( $str )
    {
        return addslashes( $str );
    }
}
