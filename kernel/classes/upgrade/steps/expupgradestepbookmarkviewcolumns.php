<?php
/**
 * File containing the expUpgradeStepBookmarkViewColumns class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The address of a system page bookmark parsed: expbookmark_view.module_name, view_name, view_parameters and their
 * index, which a table made by an early 6.0.15 build does not have. Step 6.0.15-bookmark-view-fill fills them from
 * the address of the rows that exist.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepBookmarkViewColumns extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-bookmark-view-columns';
    }

    public function description()
    {
        return 'The address of a system page bookmark parsed: expbookmark_view.module_name, view_name, view_parameters';
    }

    public function fields()
    {
        return array( 'expbookmark_view' => expBookmarkView::PARSED_COLUMNS );
    }

    public function indexes()
    {
        return array( 'expbookmark_view' => array( 'expbookmark_view_module' ) );
    }

    public function after()
    {
        return array( '6.0.15-bookmark-view-table' );
    }

    protected function afterApply( expUpgradeContext $context )
    {
        expBookmarkView::hasParsedColumns( true );
    }
}
