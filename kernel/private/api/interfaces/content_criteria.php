<?php
/**
 * File containing the ezpContentCriteriaInterface interface.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package API
 */

/**
 * This interface is the base one when it comes to implementing content query criteria
 * @package API
 */
interface ezpContentCriteriaInterface
{
    /**
     * Return the criteria as a value usable by eZContentObjectTreeNode
     * Temporary method that needs to be refactored
     *
     * @return array
     */
    public function translate();
}
?>
