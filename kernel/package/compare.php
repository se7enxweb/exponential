<?php
/**
 * Entry point of kernel/package/compare.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * package/compare/<PackageName>: what the package carries compared with the site's content tree and
 * classes, paginated, filtered and sorted, with a side by side word level difference of the item
 * opened (eZPackageComparison). The state is in view parameters, like the contents browser on
 * package/view/full:
 *   package/compare/<name>/(filter)/changed/(class)/slash_quote/(search)/abc/(sort)/name/(dir)/desc/(limit)/100/(offset)/200/(item)/17
 * each left out at its default (every status, every class, no search, the package's own order, 50
 * per page, offset 0, nothing opened). The search is written encoded twice, as on package/view/full,
 * because the kernel URL-decodes the whole path before it splits it.
 *
 * Reading needs the package read policy. Importing what the package brings for chosen items
 * (eZPackageComparisonImport) needs package install on top of it and always goes through a
 * confirmation that lists what will change: Import on a row, Import selected, Import all changes of
 * this filter, or Import on the opened item (with its values that may be unticked) lead to it;
 * Confirm imports, at most eZPackageComparisonImport::MAX_ITEMS items a request, and shows the
 * result. "Compare again" only rebuilds the comparison's own cache.
 */

// The code is in kernel/private/classes/views/package/compare.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Package\Compare::main( __FILE__, get_defined_vars() );
