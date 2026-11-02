<?php
/**
 * File containing the expUpgradeStepContentObjectModifiedIndex class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The index ezcontentobject_modified on ezcontentobject.modified: Dashboard > Recent content (moderation/recent) lists the
 * objects by their modification date, a page at a time, and on a large database that read must be indexed (the
 * creation date has ezcontentobject_pub already).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepContentObjectModifiedIndex extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-contentobject-modified-index';
    }

    public function description()
    {
        return 'The index ezcontentobject_modified on ezcontentobject.modified, for Dashboard > Recent content';
    }

    public function indexes()
    {
        return array( 'ezcontentobject' => array( 'ezcontentobject_modified' ) );
    }
}
