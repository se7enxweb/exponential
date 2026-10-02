#!/usr/bin/env php
<?php
/**
 * File containing the clusterize.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @description Migrate binary files and images from local filesystem into database cluster storage
 * @long-description Reads existing binary and image files from the local filesystem and inserts them into the database cluster backend. Run once when enabling clustering on a site that previously used local file storage.
 */

/*

NOTE:

 Please read doc/features/3.8/clustering.txt and set up clustering
 before running this script.

*/

error_reporting( E_ALL | E_NOTICE );

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/clusterize.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Clusterize::main( __FILE__ );
