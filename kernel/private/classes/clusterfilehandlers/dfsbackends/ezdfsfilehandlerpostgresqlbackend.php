<?php
/**
 * File containing the eZDFSFileHandlerPostgresqlBackend class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The earlier name of the kernel's PostgreSQL DFS backend, kept so that settings written for the ezpostgresqlcluster
 * extension ([eZDFSClusteringSettings] DBBackend=eZDFSFileHandlerPostgresqlBackend) work without the extension.
 * New settings name expDFSFileHandlerPostgresqlBackend. While the old extension is still installed and active, its
 * own class of this name is loaded instead (extension classes come before kernel classes in the autoload).
 *
 * @package kernel
 */
class eZDFSFileHandlerPostgresqlBackend extends expDFSFileHandlerPostgresqlBackend
{
}
