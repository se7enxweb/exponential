<?php
/**
 * File containing the expUpgradeStepOrderSiteaccessColumn class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The siteaccess an order was placed in: ezorder.siteaccess. Step 6.0.15-order-siteaccess-backfill fills it for older
 * orders where it can be inferred.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepOrderSiteaccessColumn extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-order-siteaccess-column';
    }

    public function description()
    {
        return 'The siteaccess an order was placed in: ezorder.siteaccess';
    }

    public function fields()
    {
        return array( 'ezorder' => array( 'siteaccess' ) );
    }

    protected function afterApply( expUpgradeContext $context )
    {
        eZOrder::hasSiteaccessColumn( true );
    }
}
