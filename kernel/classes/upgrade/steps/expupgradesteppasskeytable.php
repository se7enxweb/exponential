<?php
/**
 * File containing the expUpgradeStepPasskeyTable class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The passkeys (exppasskey), through expPasskeySchema::install() as update/common/scripts/6.0/createpasskeytable.php.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepPasskeyTable extends expUpgradeTableInstallerStep
{
    public function id()
    {
        return '6.0.15-passkey-table';
    }

    public function description()
    {
        return 'The passkeys of the users (table exppasskey)';
    }

    public function tables()
    {
        return array( 'exppasskey' );
    }

    protected function install( $db, array &$messages )
    {
        return expPasskeySchema::install( $db, $messages );
    }
}
