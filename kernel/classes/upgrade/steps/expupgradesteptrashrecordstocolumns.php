<?php
/**
 * File containing the expUpgradeStepTrashRecordsToColumns class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Who moved an object to the trash, copied from <VarDir>/trash/trashed.json into ezcontentobject_trash.trashed_by and
 * trashed_via, through Exponential\Service\TrashRecord as update/common/scripts/6.0/movetrashrecords.php. The file is
 * kept (movetrashrecords.php --remove-file removes it). Not applicable when there is no file.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepTrashRecordsToColumns extends expUpgradeScriptStep
{
    public function id()
    {
        return '6.0.15-trash-records-to-columns';
    }

    public function description()
    {
        return 'Who moved an object to the trash, copied from the old trash file into the trash table (update/common/scripts/6.0/movetrashrecords.php)';
    }

    public function after()
    {
        return array( '6.0.15-trash-columns' );
    }

    protected function changes( expUpgradeContext $context )
    {
        $file = \Exponential\Service\TrashRecord::file();
        if ( !is_file( $file ) )
        {
            $this->notApplicable = true;
            $this->reason = 'no trash file to copy';
            return null;
        }
        if ( \Exponential\Service\TrashRecord::isUnreadable( $file ) )
        {
            $this->reason = 'the trash file holds no JSON object; repair it from a backup or remove it by hand';
            return null;
        }
        if ( expUpgradeCatalog::hasColumn( $context->db(), 'ezcontentobject_trash', 'trashed_by' ) !== true )
            return array( 'copy the trash file into the trash table once the database stage added trashed_by' );
        $stats = \Exponential\Service\TrashRecord::moveToColumns( $context->db(), true );
        return $stats['moved'] ? array( $stats['moved'] . ' trash row(s) to give a trashed_by' ) : array();
    }

    protected function write( expUpgradeContext $context )
    {
        $stats = \Exponential\Service\TrashRecord::moveToColumns( $context->db(), false );
        return array( sprintf( '%d row(s) given a trashed_by, %d kept theirs, %d entries without a trash row', $stats['moved'], $stats['kept'], $stats['orphans'] ) );
    }
}
