<?php
/**
 * PHP functions the kernel uses that are younger than the oldest PHP it supports.
 *
 * Exponential 6 runs on PHP 8.0 and later (8.0 is the stock PHP of Red Hat
 * Enterprise Linux 9). A function that only exists from 8.1 on is defined here,
 * and only when PHP does not have it, so on 8.1 and later nothing in this file
 * does anything and the built-in function is the one that runs.
 *
 * Loaded by autoload.php before anything else, so every entry point (index.php,
 * the CLI scripts, the cronjobs, the tests) has these functions.
 *
 * Add a function here only when it can be written in PHP with the same result.
 * A function that cannot (fsync(), memory_reset_peak_usage()) is guarded at the
 * call with function_exists() instead. See doc/bc/6.0/php-8.0-support.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package lib
 */

if ( !function_exists( 'array_is_list' ) )
{
    /**
     * PHP 8.1: whether the keys of $array are 0, 1, 2 ... in that order.
     *
     * @param array $array
     * @return bool
     */
    function array_is_list( array $array )
    {
        $i = 0;
        foreach ( $array as $key => $unused )
        {
            if ( $key !== $i++ )
                return false;
        }
        return true;
    }
}
