<?php
/**
 * File containing the expUpgradeStepTrashColumns class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Who moved an object to the trash, and when: ezcontentobject_trash.trashed (there since 6.0.0 unless the database
 * came from 5.x), trashed_by, trashed_via and the index on trashed_by. Step 6.0.15-trash-records-to-columns fills
 * trashed_by from the old trash file afterwards.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepTrashColumns extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-trash-columns';
    }

    public function description()
    {
        return 'Who moved an object to the trash: ezcontentobject_trash.trashed, trashed_by, trashed_via and their index';
    }

    public function fields()
    {
        return array( 'ezcontentobject_trash' => array( 'trashed', 'trashed_by', 'trashed_via' ) );
    }

    public function indexes()
    {
        return array( 'ezcontentobject_trash' => array( 'ezcobj_trash_trashed_by' ) );
    }

    protected function afterApply( expUpgradeContext $context )
    {
        eZContentObjectTrashNode::hasTrashedByColumns( true );
    }
}
