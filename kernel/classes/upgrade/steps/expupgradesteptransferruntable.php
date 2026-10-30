<?php
/**
 * File containing the expUpgradeStepTransferRunTable class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The run history of the import and export tools (exp_transfer_run), through expKernelTransferRunSchema::install() as
 * update/common/scripts/6.0/createtransferruntable.php.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepTransferRunTable extends expUpgradeTableInstallerStep
{
    public function id()
    {
        return '6.0.15-transfer-run-table';
    }

    public function description()
    {
        return 'The run history of the import and export tools (table exp_transfer_run)';
    }

    public function tables()
    {
        return array( 'exp_transfer_run' );
    }

    protected function install( $db, array &$messages )
    {
        return expKernelTransferRunSchema::install( $db, $messages );
    }
}
