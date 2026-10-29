<?php
/**
 * File containing the expUpgradeStepApiKeyTable class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The personal API keys (expapikey), through expApiKeySchema::install() as update/common/scripts/6.0/createapikeytable.php.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepApiKeyTable extends expUpgradeTableInstallerStep
{
    public function id()
    {
        return '6.0.15-api-key-table';
    }

    public function description()
    {
        return 'The personal API keys (table expapikey)';
    }

    public function tables()
    {
        return array( 'expapikey' );
    }

    protected function install( $db, array &$messages )
    {
        return expApiKeySchema::install( $db, $messages );
    }
}
