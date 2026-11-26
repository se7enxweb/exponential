<?php
/**
 * Stands in for Composer in expUpgradeStagesTest (US-09): writes far more to stderr than a pipe holds, as Composer's
 * progress does, then a line to stdout, and exits with 3.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

for ( $i = 0; $i < 20000; $i++ )
    fwrite( STDERR, "  - Upgrading se7enxweb/package-$i (1.0.0 => 1.0.1)\n" );
fwrite( STDOUT, 'done ' . getenv( 'EXP_FIXTURE_MARK' ) . "\n" );
exit( 3 );
