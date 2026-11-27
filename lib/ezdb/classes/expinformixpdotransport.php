<?php
/**
 * File containing the expInformixPDOTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * expInformixDB over pdo_ibm: IBM's Data Server Driver speaks DRDA, which Informix answers on a listener of the
 * drsoctcp protocol (port 9089 in IBM's container). The same PDO layer as Db2's, with Informix's default port.
 *
 * @since 6.0.15
 * @package lib
 */
class expInformixPDOTransport extends expDb2PDOTransport
{
    protected function dsn( array $config )
    {
        return expInformixDialect::pdoDSN( array( 'layer' => $this->layer ) + $config );
    }
}
