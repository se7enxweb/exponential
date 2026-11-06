<?php
/**
 * File containing the expUpgradeStepUrlMapTables class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * URL map import and export: the table exp_url_redirect (the redirects checked on the full request, path and query,
 * before the URL aliases: old addresses with a query, temporary redirects, redirects to another site) and the record
 * of imports, exp_urlmap_batch and exp_urlmap_batch_item, from which an import is undone. Without them the redirects
 * of the table are not answered (nothing is read) and nothing can be imported; global URL aliases and wildcards work
 * as before.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepUrlMapTables extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-url-map';
    }

    public function description()
    {
        return 'URL map import: the tables exp_url_redirect, exp_urlmap_batch and exp_urlmap_batch_item';
    }

    public function tables()
    {
        return array( expUrlRedirect::TABLE, expUrlMapBatch::TABLE, expUrlMapBatch::ITEM_TABLE );
    }

    protected function afterApply( expUpgradeContext $context )
    {
        parent::afterApply( $context );
        expUrlRedirect::expireCache();
    }
}
